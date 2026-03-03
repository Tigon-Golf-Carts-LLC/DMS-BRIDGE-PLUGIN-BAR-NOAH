<?php

namespace Tigon\DmsConnect\Admin;

/**
 * Field Mapping — persistence layer for DMS → WooCommerce field mappings.
 *
 * Mappings are stored in the `{prefix}tigon_dms_field_mappings` table.
 * Each row maps a DMS JSON path to a WooCommerce target (postmeta key,
 * taxonomy, or post column) with an optional transform.
 */
class Field_Mapping
{
    /** @var string Table name (set in constructor / static helpers) */
    private static $table;

    private function __construct() {}

    /**
     * Get the fully-qualified table name.
     */
    public static function table_name(): string
    {
        if (self::$table) {
            return self::$table;
        }
        global $wpdb;
        self::$table = $wpdb->prefix . 'tigon_dms_field_mappings';
        return self::$table;
    }

    // ------------------------------------------------------------------
    //  Schema / Installation
    // ------------------------------------------------------------------

    /**
     * Create the field_mappings table (idempotent via dbDelta).
     */
    public static function install(): void
    {
        global $wpdb;
        $table   = self::table_name();
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            mapping_id    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            dms_path      VARCHAR(255)    NOT NULL DEFAULT '',
            woo_target    VARCHAR(255)    NOT NULL DEFAULT '',
            target_type   VARCHAR(32)     NOT NULL DEFAULT 'postmeta',
            transform     VARCHAR(64)     NOT NULL DEFAULT 'direct',
            transform_cfg TEXT            NOT NULL,
            is_enabled    TINYINT(1)      NOT NULL DEFAULT 1,
            sort_order    INT             NOT NULL DEFAULT 0,
            PRIMARY KEY   (mapping_id),
            KEY idx_enabled (is_enabled)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        ob_start();
        dbDelta($sql);
        ob_end_clean();
    }

    // ------------------------------------------------------------------
    //  CRUD
    // ------------------------------------------------------------------

    /**
     * Get all mappings, ordered by sort_order.
     *
     * @param bool $enabled_only Return only enabled rows.
     * @return array
     */
    public static function get_all(bool $enabled_only = false): array
    {
        global $wpdb;
        $table = self::table_name();

        $where = $enabled_only ? 'WHERE is_enabled = 1' : '';
        $rows  = $wpdb->get_results(
            "SELECT * FROM {$table} {$where} ORDER BY sort_order ASC, mapping_id ASC",
            ARRAY_A
        );
        return $rows ?: [];
    }

    /**
     * Get a single mapping by ID.
     */
    public static function get(int $mapping_id): ?array
    {
        global $wpdb;
        $table = self::table_name();

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE mapping_id = %d", $mapping_id),
            ARRAY_A
        );
    }

    /**
     * Insert a new mapping.
     *
     * @return int|false  The new mapping_id, or false on failure.
     */
    public static function insert(array $data)
    {
        global $wpdb;
        $result = $wpdb->insert(self::table_name(), self::sanitize_row($data));
        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update an existing mapping.
     */
    public static function update(int $mapping_id, array $data): bool
    {
        global $wpdb;
        return (bool) $wpdb->update(
            self::table_name(),
            self::sanitize_row($data),
            ['mapping_id' => $mapping_id]
        );
    }

    /**
     * Delete a mapping.
     */
    public static function delete(int $mapping_id): bool
    {
        global $wpdb;
        return (bool) $wpdb->delete(self::table_name(), ['mapping_id' => $mapping_id]);
    }

    /**
     * Bulk-replace all mappings (transactional).
     *
     * @param array $rows Array of mapping row arrays.
     */
    public static function replace_all(array $rows): void
    {
        global $wpdb;
        $table = self::table_name();

        $wpdb->query('START TRANSACTION');
        $wpdb->query("DELETE FROM {$table}");
        foreach ($rows as $i => $row) {
            $row['sort_order'] = $i;
            $wpdb->insert($table, self::sanitize_row($row));
        }
        $wpdb->query('COMMIT');
    }

    // ------------------------------------------------------------------
    //  Application — resolve DMS payload using stored mappings
    // ------------------------------------------------------------------

    /**
     * Apply all enabled mappings to a DMS cart payload and return the
     * resulting WooCommerce field values.
     *
     * Return structure:
     * [
     *   'postmeta'  => [ '_meta_key' => 'value', … ],
     *   'post'      => [ 'post_title' => 'value', … ],
     *   'taxonomy'  => [ 'product_cat' => [ term_ids… ], … ],
     * ]
     *
     * @param array $cart_data  Full DMS cart payload.
     * @return array
     */
    public static function apply(array $cart_data): array
    {
        $mappings = self::get_all(true);
        $result   = [
            'postmeta' => [],
            'post'     => [],
            'taxonomy' => [],
        ];

        foreach ($mappings as $mapping) {
            $raw = self::resolve_dms_path($cart_data, $mapping['dms_path']);
            if ($raw === null) {
                continue;
            }

            $value = self::transform($raw, $mapping['transform'], $mapping['transform_cfg']);

            switch ($mapping['target_type']) {
                case 'postmeta':
                    $result['postmeta'][$mapping['woo_target']] = $value;
                    break;
                case 'post':
                    $result['post'][$mapping['woo_target']] = $value;
                    break;
                case 'taxonomy':
                    if (!isset($result['taxonomy'][$mapping['woo_target']])) {
                        $result['taxonomy'][$mapping['woo_target']] = [];
                    }
                    $result['taxonomy'][$mapping['woo_target']][] = $value;
                    break;
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------
    //  Known DMS fields (for the UI dropdown)
    // ------------------------------------------------------------------

    /**
     * Return a flat list of known DMS payload paths based on the
     * Abstract_Cart::$defaults structure and common top-level fields.
     */
    public static function get_known_dms_fields(): array
    {
        return [
            // ── Top-level identifiers ───────────────────────────────
            '_id',
            'pid',
            'serialNo',
            'vinNo',

            // ── Cart Type ───────────────────────────────────────────
            'cartType.make',
            'cartType.model',
            'cartType.year',

            // ── Cart Attributes ─────────────────────────────────────
            'cartAttributes.cartColor',
            'cartAttributes.seatColor',
            'cartAttributes.tireRimSize',
            'cartAttributes.tireType',
            'cartAttributes.hasSoundSystem',
            'cartAttributes.isLifted',
            'cartAttributes.liftKit',
            'cartAttributes.hasHitch',
            'cartAttributes.hitch',
            'cartAttributes.hasExtendedTop',
            'cartAttributes.passengers',
            'cartAttributes.hasBed',
            'cartAttributes.hasBedLift',
            'cartAttributes.hasFenderFlares',
            'cartAttributes.cargoRack',
            'cartAttributes.LEDs',
            'cartAttributes.returnPolicy',
            'cartAttributes.shipping',
            'cartAttributes.sideStep',
            'cartAttributes.stakeSide',
            'cartAttributes.utilityBed',
            'cartAttributes.driveTrain',

            // ── Added Features ──────────────────────────────────────
            'addedFeatures.stockOptions',
            'addedFeatures.brushGuard',
            'addedFeatures.clayBasket',
            'addedFeatures.fenderFlares',
            'addedFeatures.LEDs',
            'addedFeatures.lightBar',
            'addedFeatures.underGlow',
            'addedFeatures.staticStock',

            // ── Options (cart add-ons) ──────────────────────────────
            'options.denagoRoverXLaddOns',
            'options.denagoNomadXLAddOns',
            'options.denagoNomadAddOns',
            'options.golfBagEvoD3',
            'options.golfBagEvo',
            'options.120voltInverter',
            'options.D5enclosure',
            'options.D5hitch',
            'options.5passengerEnclosure',
            'options.4passengerEnclosure',
            'options.4passengerStorageCoverD5',
            'options.4passengerStorageCoverEpic',
            'options.6passengerStorageCoverEpic',
            'options.armrest4fwd',
            'options.armrest4',
            'options.armrest6',
            'options.interiorBasket',
            'options.lightBar',
            'options.subWoofer',
            'options.programmer',
            'options.cargoCaddie',
            'options.newCartHitch',
            'options.newClayBasket',
            'options.seatBelts',
            'options.gtwCupHolders',
            'options.chargerUpgrade',
            'options.6passengerStorageCover',
            'options.fanSystem',
            'options.soundBar',
            'options.gtwGolfBag',
            'options.gtwGrabBar',
            'options.gtwHitch',
            'options.underBodyLights',
            'options.sideMirrors',
            'options.4passengerStorageCover',
            'options.soundSystem',
            'options.customCart',

            // ── Battery ─────────────────────────────────────────────
            'battery.type',
            'battery.brand',
            'battery.year',
            'battery.serialNo',
            'battery.ampHours',
            'battery.batteryVoltage',
            'battery.packVoltage',
            'battery.warrantyLength',
            'battery.isDC',

            // ── Engine ──────────────────────────────────────────────
            'engine.make',
            'engine.model',
            'engine.horsepower',
            'engine.stroke',

            // ── Location ────────────────────────────────────────────
            'cartLocation.locationId',
            'cartLocation.locationDescription',
            'cartLocation.latestStoreId',

            // ── Title / Legal ───────────────────────────────────────
            'title.isStreetLegal',
            'title.isTitleInPossession',
            'title.storeID',

            // ── Advertising ─────────────────────────────────────────
            'advertising.websiteUrl',
            'advertising.onWebsite',
            'advertising.needOnWebsite',
            'advertising.facebookAccounts',

            // ── RFS Status ──────────────────────────────────────────
            'rfsStatus.isRFS',
            'rfsStatus.notRFSOption',
            'rfsStatus.notRFSDescription',

            // ── Floor Plan ──────────────────────────────────────────
            'floorPlanned.isFloorPlanned',
            'floorPlanned.floorPlannedTimestamp',

            // ── Pricing & Status ────────────────────────────────────
            'retailPrice',
            'salePrice',
            'overheadCost',
            'isElectric',
            'isUsed',
            'isInStock',
            'isInBoneyard',
            'isOnLot',
            'isDelivered',
            'isService',
            'isComplete',
            'odometer',
            'warrantyLength',
            'status',
            'hour',

            // ── Timestamps ──────────────────────────────────────────
            'lastInteractionTimestamp',
            'inventoryTimestamp',
            'serviceTimestamp',

            // ── Misc ────────────────────────────────────────────────
            'transferLocation',
            'invoiceNo',
            'currentOwner',
            'tradeInInfo',
            'categories',

            // ── Images ──────────────────────────────────────────────
            'imageUrls',
            'internalCartImageUrls',
            'cartWindowStickerUrl',
        ];
    }

    /**
     * Return known WooCommerce target keys grouped by target_type.
     */
    public static function get_known_woo_targets(): array
    {
        return [
            'postmeta' => [
                // WooCommerce Core
                '_sku',
                '_global_unique_id',
                '_regular_price',
                '_price',
                '_sale_price',
                '_sale_price_dates_from',
                '_sale_price_dates_to',
                '_stock_status',
                '_stock',
                '_manage_stock',
                '_tax_status',
                '_tax_class',
                '_virtual',
                '_downloadable',
                '_download_limit',
                '_download_expiry',
                '_sold_individually',
                '_backorders',
                '_weight',
                '_length',
                '_width',
                '_height',
                '_thumbnail_id',
                '_product_image_gallery',
                '_product_attributes',
                '_featured',
                '_purchase_note',
                // DMS Bridge
                '_dms_cart_id',
                '_dms_payload',
                '_dms_cart_specs',
                '_dms_cart_images',
                '_dms_cart_warranty',
                'monroney_sticker',
                // Yoast SEO
                '_yoast_wpseo_title',
                '_yoast_wpseo_metadesc',
                '_yoast_wpseo_focuskw',
                '_yoast_wpseo_focus_keywords',
                '_yoast_wpseo_bctitle',
                '_yoast_wpseo_primary_product_cat',
                '_yoast_wpseo_primary_added-features',
                '_yoast_wpseo_opengraph-title',
                '_yoast_wpseo_opengraph-description',
                '_yoast_wpseo_opengraph-image',
                '_yoast_wpseo_twitter-image',
                // Google for WooCommerce
                '_wc_gla_brand',
                '_wc_gla_color',
                '_wc_gla_pattern',
                '_wc_gla_condition',
                '_wc_gla_mpn',
                '_wc_gla_sizeSystem',
                '_wc_gla_gtin',
                '_wc_gla_ageGroup',
                '_wc_gla_gender',
                '_wc_gla_material',
                '_wc_gla_productType',
                // Facebook for WooCommerce
                '_wc_facebook_sync_enabled',
                '_wc_fb_visibility',
                '_wc_facebook_enhanced_catalog_attributes_brand',
                '_wc_facebook_enhanced_catalog_attributes_color',
                '_wc_facebook_enhanced_catalog_attributes_pattern',
                '_wc_facebook_enhanced_catalog_attributes_size',
                '_wc_facebook_google_product_category',
                // Pinterest for WooCommerce
                '_wc_pinterest_condition',
                '_wc_pinterest_google_product_category',
                '_wc_pinterest_brand',
                '_wc_pinterest_color',
            ],
            'post' => [
                'post_title',
                'post_name',
                'post_content',
                'post_excerpt',
                'post_status',
                'menu_order',
            ],
            'taxonomy' => [
                'product_cat',
                'product_tag',
                'product_type',
                'product_visibility',
                'manufacturers',
                'models',
                'location',
                'sound-systems',
                'added-features',
                'tires',
                'vehicle-class',
                'drivetrain',
                'inventory-status',
                'rims',
                'pa_make',
                'pa_model',
                'pa_color',
                'pa_seats',
                'pa_year',
                'pa_battery-type',
                'pa_passenger-count',
                'pa_lift-kit',
                'pa_tire-type',
                'pa_rim-size',
                'pa_transmission',
                'pa_horsepower',
            ],
        ];
    }

    // ------------------------------------------------------------------
    //  Internal helpers
    // ------------------------------------------------------------------

    /**
     * Walk a dot-separated path into a nested array.
     *
     * @param array  $data  The DMS payload.
     * @param string $path  Dot-separated (e.g. "cartType.make").
     * @return mixed|null   The resolved value, or null if the path doesn't exist.
     */
    public static function resolve_dms_path(array $data, string $path)
    {
        $keys    = explode('.', $path);
        $current = $data;

        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return null;
            }
            $current = $current[$key];
        }

        return $current;
    }

    /**
     * Apply a transform to a raw value.
     *
     * Supported transforms:
     *   direct       — pass-through
     *   uppercase    — strtoupper
     *   lowercase    — strtolower
     *   ucwords      — ucwords(strtolower())
     *   boolean_yesno — true→"Yes", false→"No"
     *   boolean_label — true→$cfg[0], false→$cfg[1]
     *   prefix       — prepend $cfg
     *   suffix       — append $cfg
     *   template     — evaluate {value} placeholder in $cfg
     *   static       — always return $cfg regardless of raw value
     *
     * @param mixed  $raw       The DMS field value.
     * @param string $transform Transform name.
     * @param string $cfg       JSON or plain-text configuration string.
     * @return mixed
     */
    public static function transform($raw, string $transform, string $cfg = '')
    {
        $value = is_scalar($raw) ? (string) $raw : $raw;

        switch ($transform) {
            case 'uppercase':
                return strtoupper($value);

            case 'lowercase':
                return strtolower($value);

            case 'ucwords':
                return ucwords(strtolower($value));

            case 'boolean_yesno':
                return $raw ? 'Yes' : 'No';

            case 'boolean_label':
                $labels = json_decode($cfg, true);
                if (!is_array($labels) || count($labels) < 2) {
                    return $raw ? 'Yes' : 'No';
                }
                return $raw ? $labels[0] : $labels[1];

            case 'prefix':
                return $cfg . $value;

            case 'suffix':
                return $value . $cfg;

            case 'template':
                return str_replace('{value}', $value, $cfg);

            case 'static':
                return $cfg;

            case 'direct':
            default:
                return $value;
        }
    }

    /**
     * Sanitize a row before insert/update.
     */
    private static function sanitize_row(array $data): array
    {
        $clean = [];

        if (isset($data['dms_path'])) {
            $clean['dms_path'] = sanitize_text_field($data['dms_path']);
        }
        if (isset($data['woo_target'])) {
            $clean['woo_target'] = sanitize_text_field($data['woo_target']);
        }
        if (isset($data['target_type'])) {
            $allowed = ['postmeta', 'post', 'taxonomy'];
            $clean['target_type'] = in_array($data['target_type'], $allowed, true)
                ? $data['target_type']
                : 'postmeta';
        }
        if (isset($data['transform'])) {
            $clean['transform'] = sanitize_text_field($data['transform']);
        }
        if (isset($data['transform_cfg'])) {
            $clean['transform_cfg'] = wp_kses_post($data['transform_cfg']);
        } else {
            $clean['transform_cfg'] = '';
        }
        if (isset($data['is_enabled'])) {
            $clean['is_enabled'] = (int) (bool) $data['is_enabled'];
        }
        if (isset($data['sort_order'])) {
            $clean['sort_order'] = (int) $data['sort_order'];
        }

        return $clean;
    }
}
