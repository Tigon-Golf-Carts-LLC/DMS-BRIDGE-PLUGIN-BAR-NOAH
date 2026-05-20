<?php
/**
 * Cloudflare edge cache purging.
 *
 * Pairs with the Cache helper (which handles WP Rocket / the origin cache).
 * When DMS inventory changes, the affected URLs must also be invalidated at
 * Cloudflare's edge, or visitors keep seeing stale product pages until
 * Cloudflare's TTL expires.
 *
 * Per-product purges are COALESCED: changed URLs accumulate in an option and
 * a single scheduled flush (Action Scheduler) purges them in one API call a
 * few minutes later. This keeps the integration well below Cloudflare's
 * 1,000-purge-calls-per-day limit (Free / Pro / Business plans) even though
 * the DMS updates thousands of carts daily.
 *
 * Credentials can be supplied two ways — a wp-config.php constant always
 * wins over the database value:
 *
 *   1. wp-config.php constants: TIGON_CF_ZONE_ID / TIGON_CF_API_TOKEN.
 *   2. The plugin's Settings -> Cloudflare tab (stored in the config table).
 *
 * Use a scoped API token with only the Zone.Cache Purge permission.
 *
 * @package Tigon\DmsConnect\Includes
 */

namespace Tigon\DmsConnect\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Cloudflare
{
    /** Cloudflare API base for zone endpoints. */
    private const API_BASE = 'https://api.cloudflare.com/client/v4/zones/';

    /** Action Scheduler / WP-Cron hook that runs the coalesced flush. */
    public const FLUSH_HOOK = 'tigon_dms_cf_flush';

    /** Action Scheduler group. */
    private const AS_GROUP = 'tigon-dms-connect';

    /** Option holding the URLs awaiting the next flush. */
    private const PENDING_OPTION = 'tigon_dms_cf_pending_urls';

    /** Max URLs per purge-by-URL call (Cloudflare Free plan limit). */
    private const URL_PURGE_LIMIT = 30;

    /** Default delay before a coalesced flush runs, in seconds. */
    private const DEFAULT_FLUSH_DELAY = 180;

    /** Config-table keys for credentials managed via the settings page. */
    public const ZONE_OPTION  = 'cf_zone_id';
    public const TOKEN_OPTION = 'cf_api_token';

    /** Markers bounding the plugin-managed block in wp-config.php. */
    private const MARKER_BEGIN = '/* BEGIN Tigon DMS Connect - Cloudflare credentials */';
    private const MARKER_END   = '/* END Tigon DMS Connect - Cloudflare credentials */';

    /**
     * Register the flush hook. Called once on plugin load.
     */
    public static function init(): void
    {
        add_action(self::FLUSH_HOOK, [self::class, 'flush']);
    }

    /**
     * Whether Cloudflare credentials are configured (constant or database).
     */
    public static function is_configured(): bool
    {
        return self::zone_id() !== '' && self::api_token() !== '';
    }

    /**
     * The active Cloudflare zone ID — wp-config.php constant if defined,
     * otherwise the value saved on the settings page.
     */
    public static function zone_id(): string
    {
        if (defined('TIGON_CF_ZONE_ID') && TIGON_CF_ZONE_ID !== '') {
            return (string) TIGON_CF_ZONE_ID;
        }
        return self::config_value(self::ZONE_OPTION);
    }

    /**
     * The active Cloudflare API token — wp-config.php constant if defined,
     * otherwise the value saved on the settings page.
     */
    public static function api_token(): string
    {
        if (defined('TIGON_CF_API_TOKEN') && TIGON_CF_API_TOKEN !== '') {
            return (string) TIGON_CF_API_TOKEN;
        }
        return self::config_value(self::TOKEN_OPTION);
    }

    /**
     * Where the active credentials come from, for display on the settings
     * page: 'constant', 'database', or 'none'.
     */
    public static function credentials_source(): string
    {
        $from_constant = defined('TIGON_CF_ZONE_ID') && TIGON_CF_ZONE_ID !== ''
            && defined('TIGON_CF_API_TOKEN') && TIGON_CF_API_TOKEN !== '';
        if ($from_constant) {
            return 'constant';
        }
        return self::is_configured() ? 'database' : 'none';
    }

    /**
     * Read a credential from the plugin's config table.
     */
    private static function config_value(string $key): string
    {
        if (function_exists('tigon_dms_get_config')) {
            return (string) tigon_dms_get_config($key, '');
        }
        return '';
    }

    /**
     * Queue URLs for the next coalesced edge-cache flush.
     *
     * No HTTP happens here — URLs accumulate in an option and a single flush
     * is scheduled a few minutes out. The flush is debounced: further changes
     * inside that window join the same flush instead of scheduling another.
     *
     * @param string[] $urls Full URLs to invalidate.
     */
    public static function queue_urls(array $urls): void
    {
        if (!self::is_configured()) {
            return;
        }

        $urls = array_filter(array_map('strval', $urls));
        if (empty($urls)) {
            return;
        }

        $pending = get_option(self::PENDING_OPTION, []);
        if (!is_array($pending)) {
            $pending = [];
        }

        // Once the backlog is past the per-URL limit the flush will purge the
        // whole zone anyway, so there is no point tracking more individual URLs.
        if (count($pending) <= self::URL_PURGE_LIMIT) {
            $pending = array_values(array_unique(array_merge($pending, $urls)));
            update_option(self::PENDING_OPTION, $pending, false);
        }

        self::schedule_flush();
    }

    /**
     * Run the coalesced flush: purge everything pending in one API call.
     *
     * Invoked by Action Scheduler / WP-Cron via FLUSH_HOOK.
     */
    public static function flush(): void
    {
        $pending = get_option(self::PENDING_OPTION, []);
        delete_option(self::PENDING_OPTION);

        if (!self::is_configured() || empty($pending) || !is_array($pending)) {
            return;
        }

        // More than one call's worth of URLs changed — a single
        // purge-everything is cheaper and covers them all.
        if (count($pending) > self::URL_PURGE_LIMIT) {
            self::purge_everything();
        } else {
            self::purge_urls($pending);
        }
    }

    /**
     * Immediately purge the entire Cloudflare zone.
     *
     * Used at the end of a full DMS resync. Skipped when WP Rocket's
     * Cloudflare add-on is active, since it already purges the zone on
     * rocket_clean_domain().
     */
    public static function purge_everything_now(): void
    {
        if (!self::is_configured()) {
            return;
        }

        // A full purge supersedes any queued per-URL backlog.
        delete_option(self::PENDING_OPTION);

        if (self::addon_handles_domain_purges()) {
            return;
        }

        self::purge_everything();
    }

    /**
     * Purge specific URLs from the Cloudflare edge cache.
     *
     * @param string[] $urls Full URLs (max 30 per call on the Free plan).
     * @return true|\WP_Error
     */
    public static function purge_urls(array $urls)
    {
        if (!self::is_configured()) {
            return new \WP_Error('cf_no_credentials', 'Cloudflare credentials not configured');
        }

        $urls = array_slice(
            array_values(array_unique(array_filter(array_map('strval', $urls)))),
            0,
            self::URL_PURGE_LIMIT
        );
        if (empty($urls)) {
            return new \WP_Error('cf_bad_input', 'No URLs provided');
        }

        $response = wp_remote_post(self::endpoint(), [
            'timeout' => 15,
            'headers' => self::headers(),
            'body'    => wp_json_encode(['files' => $urls]),
        ]);

        return self::handle_response($response, '[Tigon CF Purge]');
    }

    /**
     * Purge the entire Cloudflare zone.
     *
     * @return true|\WP_Error
     */
    public static function purge_everything()
    {
        if (!self::is_configured()) {
            return new \WP_Error('cf_no_credentials', 'Cloudflare credentials not configured');
        }

        $response = wp_remote_post(self::endpoint(), [
            'timeout' => 30,
            'headers' => self::headers(),
            'body'    => wp_json_encode(['purge_everything' => true]),
        ]);

        return self::handle_response($response, '[Tigon CF Purge All]');
    }

    /**
     * Schedule the coalesced flush if one is not already pending (debounce).
     */
    private static function schedule_flush(): void
    {
        $delay = (int) apply_filters('tigon_dms_cf_flush_delay', self::DEFAULT_FLUSH_DELAY);
        $delay = max(0, $delay);

        if (function_exists('as_schedule_single_action') && function_exists('as_next_scheduled_action')) {
            if (as_next_scheduled_action(self::FLUSH_HOOK, null, self::AS_GROUP) !== false) {
                return; // already scheduled — debounced
            }
            as_schedule_single_action(time() + $delay, self::FLUSH_HOOK, [], self::AS_GROUP);
            return;
        }

        // Fallback when Action Scheduler is unavailable.
        if (!wp_next_scheduled(self::FLUSH_HOOK)) {
            wp_schedule_single_event(time() + $delay, self::FLUSH_HOOK);
        }
    }

    /**
     * Whether WP Rocket's Cloudflare add-on is active — it purges the zone
     * itself on rocket_clean_domain().
     */
    private static function addon_handles_domain_purges(): bool
    {
        return function_exists('get_rocket_option') && (bool) get_rocket_option('do_cloudflare');
    }

    /**
     * Zone purge endpoint URL.
     */
    private static function endpoint(): string
    {
        return self::API_BASE . rawurlencode(self::zone_id()) . '/purge_cache';
    }

    /**
     * Authenticated request headers.
     *
     * @return array<string,string>
     */
    private static function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . self::api_token(),
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Write the Cloudflare credential constants into wp-config.php.
     *
     * Replaces an existing plugin-managed block if present, otherwise inserts
     * one immediately after the opening PHP tag. The write is atomic (temp
     * file + rename) and the original contents are restored in-process if the
     * result fails a basic integrity check, so a botched write cannot leave a
     * broken wp-config.php behind.
     *
     * @param string $zone_id   Cloudflare zone ID.
     * @param string $api_token Cloudflare API token.
     * @return true|\WP_Error
     */
    public static function write_wp_config_constants(string $zone_id, string $api_token)
    {
        // Restrict to the characters Cloudflare actually uses so nothing can
        // break out of the single-quoted PHP strings we are about to write.
        $zone_id   = preg_replace('/[^A-Za-z0-9]/', '', $zone_id);
        $api_token = preg_replace('/[^A-Za-z0-9_\-]/', '', $api_token);
        if ($zone_id === '' || $api_token === '') {
            return new \WP_Error('cf_bad_input', 'A valid zone ID and API token are both required.');
        }

        $path = self::locate_wp_config();
        if ($path === '') {
            return new \WP_Error('cf_no_wpconfig', 'Could not locate wp-config.php.');
        }
        if (!is_writable($path) || !is_writable(dirname($path))) {
            return new \WP_Error(
                'cf_not_writable',
                'wp-config.php is not writable by the web server. Add the constants manually instead.'
            );
        }

        $original = file_get_contents($path);
        if ($original === false || strpos($original, '<?php') !== 0) {
            return new \WP_Error('cf_read_failed', 'Could not read a valid wp-config.php.');
        }

        $block = self::MARKER_BEGIN . "\n"
            . "define( 'TIGON_CF_ZONE_ID', '" . $zone_id . "' );\n"
            . "define( 'TIGON_CF_API_TOKEN', '" . $api_token . "' );\n"
            . self::MARKER_END;

        $pattern = '/' . preg_quote(self::MARKER_BEGIN, '/') . '.*?'
            . preg_quote(self::MARKER_END, '/') . '/s';
        if (preg_match($pattern, $original)) {
            $updated = preg_replace($pattern, $block, $original, 1);
        } else {
            $updated = preg_replace('/^<\?php/', "<?php\n" . $block, $original, 1);
        }
        if (!is_string($updated) || strpos($updated, $block) === false) {
            return new \WP_Error('cf_write_prep_failed', 'Could not prepare the updated wp-config.php contents.');
        }

        // Atomic replace: write a sibling temp file, then rename over the
        // original. The temp file ends in .php so it is never served as
        // plaintext if it lingers.
        $tmp = dirname($path) . '/wp-config-tigon-tmp.php';
        if (file_put_contents($tmp, $updated, LOCK_EX) === false) {
            return new \WP_Error('cf_write_failed', 'Could not write the temporary config file.');
        }
        if (!rename($tmp, $path)) {
            @unlink($tmp);
            return new \WP_Error('cf_write_failed', 'Could not replace wp-config.php.');
        }

        // Integrity check — restore the original if the result looks wrong.
        $verify = file_get_contents($path);
        if ($verify === false || strpos($verify, '<?php') !== 0 || strpos($verify, $block) === false) {
            file_put_contents($path, $original, LOCK_EX);
            return new \WP_Error('cf_verify_failed', 'wp-config.php failed verification and was restored.');
        }

        return true;
    }

    /**
     * Locate the site's wp-config.php — in ABSPATH, or one directory above it.
     */
    private static function locate_wp_config(): string
    {
        foreach ([ABSPATH . 'wp-config.php', dirname(ABSPATH) . '/wp-config.php'] as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }
        return '';
    }

    /**
     * Validate a Cloudflare API response, logging any failure.
     *
     * @param array|\WP_Error $response Result of wp_remote_post().
     * @param string          $context Log prefix.
     * @return true|\WP_Error
     */
    private static function handle_response($response, string $context)
    {
        if (is_wp_error($response)) {
            error_log($context . ' Request failed: ' . $response->get_error_message());
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($body['success'])) {
            $errors = isset($body['errors']) ? wp_json_encode($body['errors']) : 'unknown';
            error_log($context . ' API returned error: ' . $errors);
            return new \WP_Error('cf_api_error', 'Cloudflare API error', is_array($body) ? $body : []);
        }

        return true;
    }
}
