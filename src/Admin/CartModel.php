<?php

namespace Tigon\DmsConnect\Admin;

/**
 * CartModel — staging table for DMS inventory data.
 *
 * Stores all DMS cart data in `{prefix}tigon_dms_carts` as a local cache
 * before syncing into WooCommerce products. This provides:
 * - Full data preservation (even fields not yet mapped to WooCommerce)
 * - Resilience if WooCommerce product creation fails
 * - Store-based queries without API calls
 * - Complete JSON backup of each cart payload
 */
class CartModel
{
    /**
     * Insert or update a cart from API data
     *
     * @param array  $cart        Raw cart array from DMS API
     * @param string $store_name  Store name (used as group_key)
     * @param string $store_slug  Store slug (auto-derived from store_name if empty)
     * @param string $groupKey    Grouping key for batch operations
     */
    public static function upsert_from_api(array $cart, string $store_name, string $store_slug = '', string $groupKey = ''): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'tigon_dms_carts';

        $base_image_url = \DMS_API::get_s3_carts_url();

        // Generate cart_id
        if (empty($store_slug)) {
            $store_slug = strtolower(str_replace(' ', '_', $store_name));
        }
        $cart_id = $cart['_id'] ?? $cart['pid'] ?? 'temp_' . uniqid('cart_', true);
        $cart_id = sanitize_text_field($cart_id);

        // Images — preserve all, even empty
        $image_urls = [];
        if (!empty($cart['imageUrls']) && is_array($cart['imageUrls'])) {
            foreach ($cart['imageUrls'] as $img) {
                if (is_string($img) && trim($img) !== '') {
                    $image_urls[] = $base_image_url . sanitize_text_field($img);
                }
            }
        }
        $primary_image = !empty($image_urls) ? $image_urls[0] : null;

        // Location & Store
        $location_id          = $cart['cartLocation']['locationId'] ?? null;
        $location_description = $cart['cartLocation']['locationDescription'] ?? null;
        $latest_store_id      = $cart['cartLocation']['latestStoreId'] ?? null;

        // === FULL DATA ARRAY — EVERY FIELD INCLUDED ===
        $data = [
            'cart_id'               => $cart_id,
            'group_key'             => sanitize_text_field($groupKey),

            // Store & Location
            'store_name'            => sanitize_text_field($store_name),
            'store_slug'            => sanitize_title($store_slug),
            'location_id'           => sanitize_text_field($location_id),
            'location_description'  => sanitize_text_field($location_description),
            'latest_store_id'       => sanitize_text_field($latest_store_id),
            'transfer_location'     => sanitize_text_field($cart['transferLocation'] ?? null),

            // Cart Type
            'make'                  => self::v($cart, 'cartType.make'),
            'model'                 => self::v($cart, 'cartType.model'),
            'year'                  => self::v($cart, 'cartType.year', ''),

            // Attributes
            'color'                 => self::v($cart, 'cartAttributes.cartColor'),
            'seat_color'            => self::v($cart, 'cartAttributes.seatColor'),
            'passengers'            => self::v($cart, 'cartAttributes.passengers'),
            'drive_train'           => self::v($cart, 'cartAttributes.driveTrain'),
            'tire_rim_size'         => self::v($cart, 'cartAttributes.tireRimSize'),
            'tire_type'             => self::v($cart, 'cartAttributes.tireType'),
            'has_sound_system'      => self::b($cart, 'cartAttributes.hasSoundSystem'),
            'is_lifted'             => self::b($cart, 'cartAttributes.isLifted'),
            'has_hitch'             => self::b($cart, 'cartAttributes.hasHitch'),
            'has_extended_top'      => self::b($cart, 'cartAttributes.hasExtendedTop'),

            // Battery — ALL fields
            'battery_year'          => self::v($cart, 'battery.year'),
            'battery_brand'         => self::v($cart, 'battery.brand'),
            'battery_type'          => self::v($cart, 'battery.type'),
            'battery_serial'        => self::v($cart, 'battery.serialNo'),
            'amp_hours'             => self::v($cart, 'battery.ampHours'),
            'battery_voltage'       => self::v($cart, 'battery.batteryVoltage'),
            'pack_voltage'          => self::v($cart, 'battery.packVoltage'),
            'battery_warranty'      => self::v($cart, 'battery.warrantyLength'),
            'battery_is_dc'         => self::b($cart, 'battery.isDC'),

            // Engine — ALL fields
            'engine_make'           => self::v($cart, 'engine.make'),
            'horsepower'            => self::v($cart, 'engine.horsepower'),
            'engine_stroke'         => self::v($cart, 'engine.stroke'),

            // Title
            'is_street_legal'       => self::b($cart, 'title.isStreetLegal'),
            'title_in_possession'   => self::b($cart, 'title.isTitleInPossession'),
            'title_store_id'        => self::v($cart, 'title.storeID'),

            // RFS & Floor Plan
            'is_rfs'                => self::b($cart, 'rfsStatus.isRFS'),
            'not_rfs_option'        => self::v($cart, 'rfsStatus.notRFSOption'),
            'not_rfs_description'   => self::v($cart, 'rfsStatus.notRFSDescription'),
            'is_floor_planned'      => self::b($cart, 'floorPlanned.isFloorPlanned'),
            'floor_planned_timestamp' => $cart['floorPlanned']['floorPlannedTimestamp'] ?? null,

            // Pricing & Costs
            'retail_price'          => $cart['retailPrice'] !== null && $cart['retailPrice'] !== '' ? (float) $cart['retailPrice'] : null,
            'overhead_cost'         => $cart['overheadCost'] ?? null,

            // Status Flags
            'is_electric'           => $cart['isElectric'] ?? null,
            'is_used'               => $cart['isUsed'] ?? null,
            'is_on_lot'             => $cart['isOnLot'] ?? null,
            'is_in_stock'           => $cart['isInStock'] ?? null,
            'is_service'            => $cart['isService'] ?? null,
            'is_in_boneyard'        => $cart['isInBoneyard'] ?? null,
            'is_sold_by_tigon'      => $cart['isSoldByTigon'] ?? null,
            'is_complete'           => $cart['isComplete'] ?? null,

            // Warranty
            'warranty_start_date'   => $cart['warrantyStartDate'] ?? null,
            'warranty_length'       => self::v($cart, 'warrantyLength'),

            // Odometer & Hours
            'odometer'              => isset($cart['odometer']) ? (int) $cart['odometer'] : null,
            'hour'                  => isset($cart['hour']) ? (int) $cart['hour'] : null,

            // IDs & Serials
            'serial_no'             => self::v($cart, 'serialNo'),
            'vin_no'                => self::v($cart, 'vinNo'),
            'pid'                   => self::v($cart, 'pid'),
            'invoice_no'            => self::v($cart, 'invoiceNo'),
            'current_owner'         => self::v($cart, 'currentOwner'),

            // Advertising
            'website_url'           => self::v($cart, 'advertising.websiteUrl'),
            'on_website'            => $cart['advertising']['onWebsite'] ?? null,
            'is_draft'              => $cart['advertising']['isDraft'] ?? null,
            'need_on_website'       => $cart['advertising']['needOnWebsite'] ?? null,

            // Images
            'primary_image_url'     => $primary_image,
            'image_urls'            => !empty($image_urls) ? wp_json_encode($image_urls) : null,
            'internal_image_urls'   => !empty($cart['internalCartImageUrls']) ? wp_json_encode($cart['internalCartImageUrls']) : null,

            // Categories & Notes
            'categories'            => !empty($cart['categories']) ? wp_json_encode($cart['categories']) : null,
            'notes'                 => self::v($cart, 'notes'),
            'is_reviewed'           => !empty($cart['isReviewed']) ? wp_json_encode($cart['isReviewed']) : null,
            'trade_in_info'         => !empty($cart['tradeInInfo']) ? wp_json_encode($cart['tradeInInfo']) : null,

            // Timestamps
            'inventory_timestamp'        => $cart['inventoryTimestamp'] ?? null,
            'service_timestamp'          => $cart['serviceTimestamp'] ?? null,
            'creation_timestamp'         => $cart['creationTimestamp'] ?? null,
            'last_interaction_timestamp' => $cart['lastInteractionTimestamp'] ?? null,
            'last_update_timestamp'      => $cart['lastUpdateTimestamp'] ?? null,

            // Metadata
            'created_by'            => self::v($cart, 'createdBy'),
            'last_updated_by'       => self::v($cart, 'lastUpdatedBy'),
            'version'               => $cart['__v'] ?? null,

            // Full backup
            'json_data'             => wp_json_encode($cart),
            'updated_at'            => current_time('mysql'),
        ];

        // Check if cart already exists
        $exists = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE cart_id = %s LIMIT 1",
            $cart_id
        ));

        if ($exists) {
            $wpdb->update($table, $data, ['cart_id' => $cart_id]);
        } else {
            $data['created_at'] = current_time('mysql');
            $wpdb->insert($table, $data);
        }
    }

    /**
     * Get all carts (paginated)
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function get_all(int $limit = 50, int $offset = 0): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'tigon_dms_carts';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY id DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        )) ?: [];
    }

    /**
     * Get carts for a single store by slug
     *
     * @param string $store_slug
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public static function get_by_store_slug(string $store_slug, int $limit = 50, int $offset = 0): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'tigon_dms_carts';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE store_slug = %s ORDER BY id DESC LIMIT %d OFFSET %d",
            $store_slug,
            $limit,
            $offset
        )) ?: [];
    }

    /**
     * Get a single cart by DMS cart_id
     *
     * @param string $cart_id
     * @return object|null
     */
    public static function get_by_cart_id(string $cart_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'tigon_dms_carts';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE cart_id = %s LIMIT 1",
            $cart_id
        ));
    }

    /**
     * Get total cart count (optionally filtered by store)
     *
     * @param string|null $store_slug
     * @return int
     */
    public static function count(?string $store_slug = null): int
    {
        global $wpdb;
        $table = $wpdb->prefix . 'tigon_dms_carts';

        if ($store_slug) {
            return (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE store_slug = %s",
                $store_slug
            ));
        }

        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    }

    /**
     * Create the tigon_dms_carts table (idempotent via dbDelta).
     */
    public static function install(): void
    {
        global $wpdb;
        $table   = $wpdb->prefix . 'tigon_dms_carts';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            cart_id VARCHAR(100) NOT NULL DEFAULT '',
            group_key VARCHAR(100) NOT NULL DEFAULT '',

            store_name VARCHAR(200) NOT NULL DEFAULT '',
            store_slug VARCHAR(200) NOT NULL DEFAULT '',
            location_id VARCHAR(20) DEFAULT NULL,
            location_description VARCHAR(255) DEFAULT NULL,
            latest_store_id VARCHAR(20) DEFAULT NULL,
            transfer_location VARCHAR(100) DEFAULT NULL,

            make VARCHAR(100) DEFAULT NULL,
            model VARCHAR(100) DEFAULT NULL,
            year VARCHAR(10) DEFAULT NULL,

            color VARCHAR(100) DEFAULT NULL,
            seat_color VARCHAR(100) DEFAULT NULL,
            passengers VARCHAR(50) DEFAULT NULL,
            drive_train VARCHAR(50) DEFAULT NULL,
            tire_rim_size VARCHAR(50) DEFAULT NULL,
            tire_type VARCHAR(50) DEFAULT NULL,
            has_sound_system TINYINT(1) DEFAULT NULL,
            is_lifted TINYINT(1) DEFAULT NULL,
            has_hitch TINYINT(1) DEFAULT NULL,
            has_extended_top TINYINT(1) DEFAULT NULL,

            battery_year VARCHAR(10) DEFAULT NULL,
            battery_brand VARCHAR(100) DEFAULT NULL,
            battery_type VARCHAR(50) DEFAULT NULL,
            battery_serial VARCHAR(100) DEFAULT NULL,
            amp_hours VARCHAR(50) DEFAULT NULL,
            battery_voltage VARCHAR(50) DEFAULT NULL,
            pack_voltage VARCHAR(50) DEFAULT NULL,
            battery_warranty VARCHAR(50) DEFAULT NULL,
            battery_is_dc TINYINT(1) DEFAULT NULL,

            engine_make VARCHAR(100) DEFAULT NULL,
            horsepower VARCHAR(50) DEFAULT NULL,
            engine_stroke VARCHAR(10) DEFAULT NULL,

            is_street_legal TINYINT(1) DEFAULT NULL,
            title_in_possession TINYINT(1) DEFAULT NULL,
            title_store_id VARCHAR(20) DEFAULT NULL,

            is_rfs TINYINT(1) DEFAULT NULL,
            not_rfs_option VARCHAR(100) DEFAULT NULL,
            not_rfs_description TEXT DEFAULT NULL,
            is_floor_planned TINYINT(1) DEFAULT NULL,
            floor_planned_timestamp VARCHAR(50) DEFAULT NULL,

            retail_price DECIMAL(10,2) DEFAULT NULL,
            overhead_cost DECIMAL(10,2) DEFAULT NULL,

            is_electric TINYINT(1) DEFAULT NULL,
            is_used TINYINT(1) DEFAULT NULL,
            is_on_lot TINYINT(1) DEFAULT NULL,
            is_in_stock TINYINT(1) DEFAULT NULL,
            is_service TINYINT(1) DEFAULT NULL,
            is_in_boneyard TINYINT(1) DEFAULT NULL,
            is_sold_by_tigon TINYINT(1) DEFAULT NULL,
            is_complete TINYINT(1) DEFAULT NULL,

            warranty_start_date VARCHAR(50) DEFAULT NULL,
            warranty_length VARCHAR(50) DEFAULT NULL,

            odometer INT DEFAULT NULL,
            hour INT DEFAULT NULL,

            serial_no VARCHAR(100) DEFAULT NULL,
            vin_no VARCHAR(100) DEFAULT NULL,
            pid VARCHAR(100) DEFAULT NULL,
            invoice_no VARCHAR(100) DEFAULT NULL,
            current_owner VARCHAR(200) DEFAULT NULL,

            website_url VARCHAR(500) DEFAULT NULL,
            on_website TINYINT(1) DEFAULT NULL,
            is_draft TINYINT(1) DEFAULT NULL,
            need_on_website TINYINT(1) DEFAULT NULL,

            primary_image_url VARCHAR(500) DEFAULT NULL,
            image_urls LONGTEXT DEFAULT NULL,
            internal_image_urls LONGTEXT DEFAULT NULL,

            categories TEXT DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            is_reviewed TEXT DEFAULT NULL,
            trade_in_info TEXT DEFAULT NULL,

            inventory_timestamp VARCHAR(50) DEFAULT NULL,
            service_timestamp VARCHAR(50) DEFAULT NULL,
            creation_timestamp VARCHAR(50) DEFAULT NULL,
            last_interaction_timestamp VARCHAR(50) DEFAULT NULL,
            last_update_timestamp VARCHAR(50) DEFAULT NULL,

            created_by VARCHAR(200) DEFAULT NULL,
            last_updated_by VARCHAR(200) DEFAULT NULL,
            version INT DEFAULT NULL,

            json_data LONGTEXT DEFAULT NULL,
            created_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT NULL,

            PRIMARY KEY  (id),
            UNIQUE KEY idx_cart_id (cart_id),
            KEY idx_store_slug (store_slug),
            KEY idx_location_id (location_id),
            KEY idx_is_in_stock (is_in_stock)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        ob_start();
        dbDelta($sql);
        ob_end_clean();
    }

    /**
     * Walk a dot-separated path into a nested array and sanitize.
     *
     * @param array  $arr     Source array
     * @param string $path    Dot-separated path (e.g. "cartType.make")
     * @param mixed  $default Default value if path doesn't exist
     * @return string|null
     */
    private static function v(array $arr, string $path, $default = null)
    {
        $keys = explode('.', $path);
        $val  = $arr;
        foreach ($keys as $k) {
            if (is_array($val) && array_key_exists($k, $val)) {
                $val = $val[$k];
            } else {
                return $default;
            }
        }
        return $val === null ? null : sanitize_text_field($val);
    }

    /**
     * Resolve a boolean value from a dot-path → 1/0/null
     *
     * @param array  $arr  Source array
     * @param string $path Dot-separated path
     * @return int|null
     */
    private static function b(array $arr, string $path)
    {
        $val = self::v($arr, $path, 'NOT_SET');
        if ($val === 'NOT_SET') {
            return null;
        }
        return $val === true || $val === '1' || $val === 1 ? 1 : ($val === false || $val === '0' || $val === 0 ? 0 : null);
    }
}
