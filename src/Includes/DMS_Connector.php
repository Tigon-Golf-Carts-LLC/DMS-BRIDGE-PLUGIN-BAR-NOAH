<?php

namespace Tigon\DmsConnect\Includes;

final class DMS_Connector {

    /**
     * Public API base URL (no authentication required)
     */
    private const PUBLIC_API_URL = 'https://api.tigondms.com/wp-website';

    /**
     * Endpoint mapping: authenticated endpoint → public API equivalent.
     * Only lookup-style reads are available on the public API.
     */
    private static $public_endpoint_map = [
        '/chimera/lookup' => '/get-carts',
    ];

    /**
     * Sends an HTTP request to the DMS using the auth data from the database.
     * Falls back to the public API when auth credentials are not configured.
     *
     * @param string $query
     * @param string $endpoint
     * @param string $method GET, PUT, POST, DELETE
     * @return string|false DMS response body, or false on failure
     */
    public static function request(string $query, string $endpoint, string $method)
    {
        $query = stripcslashes($query);
        global $wpdb;

        $table_name = $wpdb->prefix . 'tigon_dms_config';

        $dms_url    = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'dms_url'");
        $user_token = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'user_token'");

        // If authenticated DMS URL or user token is not configured,
        // try the public API for supported endpoints
        if (empty($dms_url) || empty($user_token)) {
            return self::request_public($query, $endpoint, $method);
        }

        $url = $dms_url . $endpoint;

        // Validate auth and reacquire if needed
        $auth_token = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'auth_token'");
        if (!$auth_token) {
            try {
                $auth_token = DMS_Connector::get_auth($user_token);
                $wpdb->update($table_name, ['option_value' => $auth_token], ['option_name' => 'auth_token']);
            } catch (\Throwable $e) {
                // Auth failed — try public API
                return self::request_public($query, $endpoint, $method);
            }
        }

        // Get data from DMS
        $options = [
            'http' => [
                'header' => ["x-auth-token:" . $auth_token, 'Content-type: application/json'],
                'method' => $method,
                'content' => $query
            ]
        ];
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);

        // On failure, reobtain auth and retry
        if (!$result) {
            try {
                $retry_auth_token = DMS_Connector::get_auth($user_token, $auth_token);
                $wpdb->update($table_name, ['option_value' => $retry_auth_token], ['option_name' => 'auth_token']);

                $options['http']['header'] = ["x-auth-token:" . $retry_auth_token, 'Content-type: application/json'];

                $retry_context = stream_context_create($options);
                $result = @file_get_contents($url, false, $retry_context);
            } catch (\Throwable $e) {
                // Auth retry failed — fall through
            }

            // If still no result, try public API as last resort
            if (!$result) {
                return self::request_public($query, $endpoint, $method);
            }
        }
        return $result;
    }

    /**
     * Make a request to the public DMS API (no authentication).
     * Only supports read endpoints (lookup).
     *
     * @param string $query    JSON body
     * @param string $endpoint The authenticated endpoint path (will be mapped)
     * @param string $method   HTTP method
     * @return string Response body or empty JSON array on failure
     */
    private static function request_public(string $query, string $endpoint, string $method): string
    {
        // Map authenticated endpoint to public equivalent
        $public_endpoint = self::$public_endpoint_map[$endpoint] ?? null;
        if ($public_endpoint === null) {
            // No public equivalent for write endpoints — return empty
            return '[]';
        }

        $url = self::PUBLIC_API_URL . $public_endpoint;

        // Translate the query format: /chimera/lookup filters → /get-carts format
        $body = self::translate_query_for_public_api($query, $endpoint);

        $response = wp_remote_post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => $body,
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return '[]';
        }

        $result = wp_remote_retrieve_body($response);
        $decoded = json_decode($result, true);

        // Public API returns { carts: [...], totalCarts: N }
        // Convert to flat array for compatibility with existing code
        if (is_array($decoded) && isset($decoded['carts'])) {
            return wp_json_encode($decoded['carts']);
        }

        return $result ?: '[]';
    }

    /**
     * Translate authenticated API query format to public API format.
     *
     * The authenticated /chimera/lookup uses filters like:
     *   { "isUsed": true, "isInStock": true, "needOnWebsite": true }
     *
     * The public /get-carts uses:
     *   { "pageNumber": 0, "pageSize": 100, "isUsed": true }
     */
    private static function translate_query_for_public_api(string $query, string $endpoint): string
    {
        $input = json_decode($query, true) ?? [];
        $output = [
            'pageNumber' => 0,
            'pageSize'   => 100,
        ];

        // Pass through common filters
        if (isset($input['isUsed'])) {
            if ($input['isUsed'] === true) {
                $output['isUsed'] = true;
            } elseif ($input['isUsed'] === false) {
                $output['isNew'] = true;
            }
        }
        if (isset($input['isElectric'])) {
            $output['isElectric'] = (bool) $input['isElectric'];
        }

        return wp_json_encode($output);
    }

    public static function get_auth(string $amplify_id, string $old_token = null)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'tigon_dms_config';

        $dms_url = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'dms_url'");
        if (empty($dms_url)) {
            error_log('[DMS Connector] Error: dms_url is not configured for auth');
            return false;
        }
        $url = $dms_url . '/auth';

        if($old_token !== null)
        {
            $url = $url . '/get-refresh-token';
            $body = [
                'amplifyId' => $amplify_id,
                'oldToken' => $old_token
            ];
        } else {
            $url = $url . '/get-token';
            $body = [
                'amplifyUserId' => $amplify_id
            ];
        }

        $response = wp_remote_post($url, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body'    => $body,
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            error_log('[DMS Connector] Auth error: ' . $response->get_error_message());
            return false;
        }

        $result = wp_remote_retrieve_body($response);
        $decoded = json_decode($result);

        if (empty($decoded->token)) {
            error_log('[DMS Connector] Auth failed: no token in response');
            return false;
        }

        return $decoded->token;
    }
}
