# Used Vehicle Manufacturer Logic - Complete Meta Key Overrides

> **How to read:** Each manufacturer section lists which meta keys from `Global-Used-Logic.md` it overrides. All unlisted keys inherit the global used value.

Source: `Abstract_Cart.php` (shared base), `Used/Cart.php` (used overrides)

---

## Important Note

Used vehicles have **NO manufacturer-specific templates** like new vehicles do. There is no `Used_Cart_Converter.php` equivalent. All used cart data comes directly from the DMS.

The manufacturer-specific logic that applies to used carts is **identical** to new carts because it lives in `Abstract_Cart.php`, which is shared. The overrides below are computed at runtime from the make name.

---

## Denago (Used)

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | Denago Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `YES` |
| `pa_led-accents` | make-dependent | `YES` + `LIGHT BAR` |
| `pa_{make}-cart-colors` | generic fallback | `pa_denago-cart-colors` = `{cartColor}` from DMS |
| `pa_{make}-seat-colors` | generic fallback | `pa_denago-seat-colors` = `{seatColor}` from DMS |
| `pa_sound-system` | make-dependent | `DENAGO(R) SOUND SYSTEM` |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | `TIGON Warranty (USED GOLF CARTS)` (first tab) + model-specific spec tabs (see Model-Logic) |
| `_wcpa_product_meta` | individual from DMS `cartAddOns` | Individual add-ons from DMS `cart.advertising.cartAddOns` (same as global used) |

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with Denago overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `DENAGO(R)` term ID | Computed from make |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `DENAGO(R)` | Computed from make |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `DENAGO(R)` | Computed from make |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### Denago Categories

| Category | Condition |
|---|---|
| `DENAGO(R)` | Always |
| `DENAGO(R) {MODEL}` | If exists in system |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

### Denago Attributes Not Overridden

| Attribute | Value | Notes |
|---|---|---|
| `pa_battery-type` | From DMS | Inherited |
| `pa_battery-warranty` | From DMS | Inherited |
| `pa_cargo-rack` | `NO` | Inherited |
| `pa_drivetrain` | `2X4` | Inherited |
| `pa_electric-bed-lift` | `NO` | Inherited |
| `pa_extended-top` | From DMS | Inherited |
| `pa_fender-flares` | `YES` | Inherited |
| `pa_lift-kit` | From DMS `isLifted` | Inherited |
| `pa_location` | `{City} {State}` | Inherited |
| `pa_passengers` | `{N} SEATER` | Inherited |
| `pa_receiver-hitch` | `NO` | Inherited |
| `pa_return-policy` | `90 DAY` + `YES` | Inherited |
| `pa_rim-size` | From DMS | Inherited |
| `pa_shipping` | Standard 3-tier | Inherited |
| `pa_street-legal` | From DMS | Inherited |
| `pa_tire-profile` | From DMS | Inherited |
| `pa_vehicle-class` | Computed | Inherited |
| `pa_vehicle-warranty` | From DMS | Inherited |
| `pa_year-of-vehicle` | From DMS | Inherited |

---

## Evolution (Used)

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | Evolution Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `YES` |
| `pa_led-accents` | make-dependent | `NO` |
| `pa_{make}-cart-colors` | generic fallback | `pa_evolution-cart-colors` = `{cartColor}` from DMS |
| `pa_{make}-seat-colors` | generic fallback | `pa_evolution-seat-colors` = `{seatColor}` from DMS |
| `pa_sound-system` | make-dependent | `EVOLUTION(R) SOUND SYSTEM` |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | `TIGON Warranty (USED GOLF CARTS)` (first tab) + model-specific spec/image tabs (see Model-Logic) |
| `_wcpa_product_meta` | individual from DMS `cartAddOns` | Individual add-ons from DMS `cart.advertising.cartAddOns` (same as global used) |

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with Evolution overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `EVOLUTION(R)` term ID | Computed from make |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `EVOLUTION(R)` | Computed from make |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `EVOLUTION(R)` | Computed from make |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### Evolution Categories

| Category | Condition |
|---|---|
| `EVOLUTION(R)` | Always |
| `EVOLUTION(R) {MODEL}` | If exists in system |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

---

## Epic (Used)

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | Epic Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `NO` |
| `pa_led-accents` | make-dependent | `NO` |
| `pa_{make}-cart-colors` | generic fallback | `pa_epic-cart-colors` = `{cartColor}` from DMS |
| `pa_{make}-seat-colors` | generic fallback | `pa_epic-seat-colors` = `{seatColor}` from DMS |
| `pa_sound-system` | make-dependent | `EPIC(R) SOUND SYSTEM` |

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with Epic overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `EPIC(R)` term ID | Computed from make |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wcpa_product_meta` | Individual from DMS `cartAddOns` | Inherited |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | Inherited (no model-specific tabs) |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `EPIC(R)` | Computed from make |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `EPIC(R)` | Computed from make |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### Epic Categories

| Category | Condition |
|---|---|
| `EPIC(R)` | Always |
| `EPIC(R) {MODEL}` | If exists in system |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

---

## Icon (Used)

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | Icon Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `NO` |
| `pa_led-accents` | make-dependent | `NO` |
| `pa_{make}-cart-colors` | generic fallback | `pa_icon-cart-colors` = `{cartColor}` from DMS |
| `pa_{make}-seat-colors` | generic fallback | `pa_icon-seat-colors` = `{seatColor}` from DMS |
| `pa_sound-system` | make-dependent | `ICON(R) SOUND SYSTEM` |

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with Icon overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `ICON(R)` term ID | Computed from make |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wcpa_product_meta` | Individual from DMS `cartAddOns` | Inherited |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | Inherited (no model-specific tabs) |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `ICON(R)` | Computed from make |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `ICON(R)` | Computed from make |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### Icon Categories

| Category | Condition |
|---|---|
| `ICON(R)` | Always |
| `ICON(R) {MODEL}` | If exists in system |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

---

## Swift EV (Used)

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | Swift Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `NO` |
| `pa_led-accents` | make-dependent | `NO` |
| `pa_{make}-cart-colors` | generic fallback | `pa_swift-cart-colors` = `{cartColor}` from DMS |
| `pa_{make}-seat-colors` | generic fallback | `pa_swift-seat-colors` = `{seatColor}` from DMS |
| `pa_sound-system` | make-dependent | `SWIFT(R) SOUND SYSTEM` |
| Manufacturer taxonomy | make name | `SWIFT` (alias: Swift EV -> SWIFT) |

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with Swift overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `SWIFT(R)` term ID | Computed from make |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wcpa_product_meta` | Individual from DMS `cartAddOns` | Inherited |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | Inherited (no model-specific tabs) |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `SWIFT(R)` | Computed from make |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `SWIFT(R)` | Computed from make |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### Swift Categories

| Category | Condition |
|---|---|
| `SWIFT(R)` | Always |
| `SWIFT(R) {MODEL}` | If exists in system |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

---

## Club Car (Used)

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | Club Car Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `NO` |
| `pa_led-accents` | make-dependent | `NO` |
| `pa_{make}-cart-colors` | generic fallback | `pa_club-car-cart-colors` = `{cartColor}` from DMS |
| `pa_{make}-seat-colors` | generic fallback | `pa_club-car-seat-colors` = `{seatColor}` from DMS |
| `pa_sound-system` | make-dependent | `CLUB CAR(R) SOUND SYSTEM` |
| Model taxonomy | `{MAKE} {MODEL}` | Model aliases: DS -> `CLUB CAR(R) DS ELECTRIC`, Precedent -> `CLUB CAR(R) PRECEDENT ELECTRIC` |

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with Club Car overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `CLUB CAR(R)` term ID | Computed from make |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wcpa_product_meta` | Individual from DMS `cartAddOns` | Inherited |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | Inherited (no model-specific tabs) |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `CLUB CAR(R)` | Computed from make |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `CLUB CAR(R)` | Computed from make |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### Club Car Model Aliases

| DMS Model | Taxonomy Term |
|---|---|
| `DS` | `CLUB CAR(R) DS ELECTRIC` |
| `Precedent` | `CLUB CAR(R) PRECEDENT ELECTRIC` |

### Club Car Categories

| Category | Condition |
|---|---|
| `CLUB CAR(R)` | Always |
| `CLUB CAR(R) {MODEL}` | If exists in system (with aliases above) |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

---

## EZGO (Used)

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | EZGO Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `NO` |
| `pa_led-accents` | make-dependent | `NO` |
| `pa_{make}-cart-colors` | generic fallback | `pa_ezgo-cart-colors` = `{cartColor}` from DMS |
| `pa_{make}-seat-colors` | generic fallback | `pa_ezgo-seat-colors` = `{seatColor}` from DMS |
| `pa_sound-system` | make-dependent | `EZ-GO(R) SOUND SYSTEM` |
| Category/taxonomy name | `{MAKE(R)}` | `EZ-GO(R)` (NOT `EZGO(R)`) |

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with EZGO overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `EZ-GO(R)` term ID | Name alias applied |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wcpa_product_meta` | Individual from DMS `cartAddOns` | Inherited |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | Inherited (no model-specific tabs) |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `EZ-GO(R)` | Name alias applied |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `EZ-GO(R)` | Name alias applied |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### EZGO Name Alias

All category, tag, taxonomy, attribute, and brand references use `EZ-GO(R)` instead of `EZGO(R)`. This applies to:
- `_yoast_wpseo_primary_product_cat` -> `EZ-GO(R)` term ID
- `_wc_gla_brand` -> `EZ-GO(R)`
- `_wc_facebook_enhanced_catalog_attributes_brand` -> `EZ-GO(R)`
- Category -> `EZ-GO(R)`
- Tags -> `EZ-GO(R)`, `EZ-GO(R) {MODEL}`, etc.
- Manufacturer taxonomy -> `EZ-GO(R)`
- Model taxonomy -> `EZ-GO(R) {MODEL}`

### EZGO Categories

| Category | Condition |
|---|---|
| `EZ-GO(R)` | Always (name alias) |
| `EZ-GO(R) {MODEL}` | If exists in system |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

---

## Yamaha (Used)

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | Yamaha Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `NO` |
| `pa_led-accents` | make-dependent | `NO` |
| `pa_{make}-cart-colors` | generic fallback | `pa_yamaha-cart-colors` = `{cartColor}` from DMS |
| `pa_{make}-seat-colors` | generic fallback | `pa_yamaha-seat-colors` = `{seatColor}` from DMS |
| `pa_sound-system` | make-dependent | `YAMAHA(R) SOUND SYSTEM` |
| Model taxonomy | `{MAKE} {MODEL}` | Model aliases (see below) |

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with Yamaha overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `YAMAHA(R)` term ID | Computed from make |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wcpa_product_meta` | Individual from DMS `cartAddOns` | Inherited |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | Inherited (no model-specific tabs) |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `YAMAHA(R)` | Computed from make |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `YAMAHA(R)` | Computed from make |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### Yamaha Model Aliases

| DMS Model | Taxonomy Term |
|---|---|
| `Drive 2` | `YAMAHA(R) DRIVE2` |
| `4L` | `YAMAHA(R) CROWN 4 LIFTED` |
| `6L` | `YAMAHA(R) CROWN 6 LIFTED` |

### Yamaha Categories

| Category | Condition |
|---|---|
| `YAMAHA(R)` | Always |
| `YAMAHA(R) {MODEL}` | If exists in system (with aliases above) |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

---

## All Other Brands (Used)

Applies to any make not listed above (e.g., Bintelli, Navitas, Polaris, Royal EV, Star EV, Tomberlin, and any unknown brands).

### Meta Keys Overridden from Global

| Meta Key | Global Used Value | Others Override |
|---|---|---|
| `pa_brush-guard` | make-dependent | `NO` |
| `pa_led-accents` | make-dependent | `NO` |
| `pa_{make}-cart-colors` | make-specific palette | `pa_cart-color` = `{cartColor}` (generic fallback if make is not in known brands list) |
| `pa_{make}-seat-colors` | make-specific palette | `pa_seat-color` = `{seatColor}` (generic fallback if make is not in known brands list) |
| `pa_sound-system` | make-dependent | `{MAKE(R)} SOUND SYSTEM` or `YES` |

**Note:** If the make IS in the known brands list (`bintelli, club-car, denago, epic, evolution, ezgo, icon, navitas, polaris, royal-ev, star-ev, swift, tomberlin, yamaha`), it uses `pa_{make}-cart-colors` / `pa_{make}-seat-colors`. If the make is NOT in this list, it falls back to generic `pa_cart-color` / `pa_seat-color`.

### All Other Meta Keys (Inherited from Global Used)

| Meta Key | Value | Notes |
|---|---|---|
| `post_status` | `publish` | Inherited |
| `_sku` | VIN > Serial (no generated) | Inherited |
| `_stock_status` | From DMS `isInStock` | Inherited |
| `_thumbnail_id` | First sideloaded image | Inherited |
| `_product_image_gallery` | Remaining sideloaded images | Inherited |
| `_regular_price` | `cart.retailPrice` | Inherited |
| `_price` | `cart.salePrice` | Inherited |
| `_tax_status` | `taxable` | Inherited |
| `_tax_class` | `standard` | Inherited |
| `_manage_stock` | `no` | Inherited |
| `_backorders` | `no` | Inherited |
| `_sold_individually` | `no` | Inherited |
| `_virtual` | `no` | Inherited |
| `_downloadable` | `no` | Inherited |
| `_download_limit` | `-1` | Inherited |
| `_download_expiry` | `-1` | Inherited |
| `_stock` | `10000` | Inherited |
| `_global_unique_id` | Auto from SKU | Inherited |
| `_product_attributes` | Serialized pa_* array | Inherited (with Others overrides above) |
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` | Inherited |
| `_yoast_wpseo_metadesc` | Standard format | Inherited |
| `_yoast_wpseo_primary_product_cat` | `{MAKE(R)}` term ID | Computed from make |
| `_yoast_wpseo_primary_location` | City term ID | Inherited |
| `_yoast_wpseo_primary_models` | `null` | Inherited |
| `_yoast_wpseo_primary_added-features` | `null` | Inherited |
| `_yoast_wpseo_is_cornerstone` | `1` | Inherited |
| `_yoast_wpseo_focus_kw` | Same as `post_title` | Inherited |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` | Inherited |
| `_yoast_wpseo_bctitle` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` | Inherited |
| `_yoast_wpseo_opengraph-description` | Same as metadesc | Inherited |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_opengraph-image` | Featured image URL | Inherited |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` | Inherited |
| `_yoast_wpseo_twitter-image` | Featured image URL | Inherited |
| `wcpa_exclude_global_forms` | `1` | Inherited |
| `_wcpa_product_meta` | Individual from DMS `cartAddOns` | Inherited |
| `_yikes_woo_products_tabs` | `TIGON Warranty (USED GOLF CARTS)` only | Inherited (no model-specific tabs) |
| `_wc_gla_mpn` | Same as `_global_unique_id` | Inherited |
| `_wc_gla_condition` | `used` | Inherited |
| `_wc_gla_brand` | `{MAKE(R)}` | Computed from make |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_gla_pattern` | `{model}` original case | Inherited |
| `_wc_gla_gender` | `unisex` | Inherited |
| `_wc_gla_sizeSystem` | `US` | Inherited |
| `_wc_gla_adult` | `no` | Inherited |
| `_wc_pinterest_condition` | `used` | Inherited |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_brand` | `{MAKE(R)}` | Computed from make |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` | Inherited |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` | Inherited |
| `_wc_facebook_product_image_source` | `product` | Inherited |
| `_wc_facebook_sync_enabled` | `yes` | Inherited |
| `_wc_fb_visibility` | `yes` | Inherited |
| `monroney_sticker` | Sideloaded from DMS | Inherited |
| `_monroney_sticker` | `field_66e3332abf481` | Inherited |
| `_tigonwm` | `{City Short} {ST}` | Inherited |

### Other Brand Categories

| Category | Condition |
|---|---|
| `{MAKE(R)}` | Always |
| `{MAKE(R)} {MODEL}` | If exists in system |
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| All other categories | Same as Global Used |

---

## Categories Specific to Used (All Manufacturers)

| Category | Applied When |
|---|---|
| `USED` | Always |
| `LOCAL USED ACTIVE INVENTORY` | Default |
| `LOCAL USED RENTAL INVENTORY` | If `isRental=true` |
| `RENTAL` | If `isRental=true` |

All other categories (make, electric/gas, seating, lifted, street legal, voltage, vehicle class, etc.) are computed from the DMS data exactly like new vehicles.

---

## Quick Reference: Manufacturer Override Summary

| Make | `pa_brush-guard` | `pa_led-accents` | Color Attributes | Tabs | Add-Ons | Special |
|---|---|---|---|---|---|---|
| Denago | `YES` | `YES` + `LIGHT BAR` | `pa_denago-cart/seat-colors` | TIGON Warranty (USED) + model specs | Individual from DMS | -- |
| Evolution | `YES` | `NO` | `pa_evolution-cart/seat-colors` | TIGON Warranty (USED) + model specs/images | Individual from DMS | -- |
| Epic | `NO` | `NO` | `pa_epic-cart/seat-colors` | TIGON Warranty (USED) only | Individual from DMS | -- |
| Icon | `NO` | `NO` | `pa_icon-cart/seat-colors` | TIGON Warranty (USED) only | Individual from DMS | -- |
| Swift EV | `NO` | `NO` | `pa_swift-cart/seat-colors` | TIGON Warranty (USED) only | Individual from DMS | Manufacturer alias: Swift EV -> SWIFT |
| Club Car | `NO` | `NO` | `pa_club-car-cart/seat-colors` | TIGON Warranty (USED) only | Individual from DMS | Model aliases: DS->DS ELECTRIC, Precedent->PRECEDENT ELECTRIC |
| EZGO | `NO` | `NO` | `pa_ezgo-cart/seat-colors` | TIGON Warranty (USED) only | Individual from DMS | Name alias: EZGO -> EZ-GO(R) |
| Yamaha | `NO` | `NO` | `pa_yamaha-cart/seat-colors` | TIGON Warranty (USED) only | Individual from DMS | Model aliases: Drive 2->DRIVE2, 4L->CROWN 4 LIFTED, 6L->CROWN 6 LIFTED |
| Others | `NO` | `NO` | `pa_cart-color`/`pa_seat-color` (generic fallback) | TIGON Warranty (USED) only | Individual from DMS | -- |
