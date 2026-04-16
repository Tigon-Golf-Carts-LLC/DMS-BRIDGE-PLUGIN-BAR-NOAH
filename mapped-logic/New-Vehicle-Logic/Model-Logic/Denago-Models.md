# Denago Models - Complete Database_Object Mapping Per Model

Source: `New_Cart_Converter.php`

All Denago models inherit from `../Manufacturer-Logic/Denago.md` which inherits from `../Global-New-Logic.md`.
Below is the COMPLETE per-model mapping. Every meta key is listed with either the model-specific value or `inherit`.

---

## Denago Nomad

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `DENAGO(R) NOMAD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | `7995` |
| `_price` | inherit |

### postmeta - Yoast SEO

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | inherit |
| `_yoast_wpseo_metadesc` | inherit |
| `_yoast_wpseo_primary_product_cat` | inherit |
| `_yoast_wpseo_primary_location` | inherit |
| `_yoast_wpseo_primary_models` | inherit |
| `_yoast_wpseo_primary_added-features` | inherit |
| `_yoast_wpseo_is_cornerstone` | inherit |
| `_yoast_wpseo_focus_kw` | inherit |
| `_yoast_wpseo_focus_keywords` | inherit |
| `_yoast_wpseo_bctitle` | inherit |
| `_yoast_wpseo_opengraph-title` | inherit |
| `_yoast_wpseo_opengraph-description` | inherit |
| `_yoast_wpseo_opengraph-image-id` | inherit |
| `_yoast_wpseo_opengraph-image` | inherit |
| `_yoast_wpseo_twitter-image-id` | inherit |
| `_yoast_wpseo_twitter-image` | inherit |

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` + Tab 2: `Denago(R) Nomad Vehicle Specs` + Tab 3 (if year=2024): `VIDEO DENAGO 2024` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `Denago(R) EV Nomad Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Nomad` |
| `_wc_gla_gender` | inherit |
| `_wc_gla_sizeSystem` | inherit |
| `_wc_gla_adult` | inherit |

### postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | inherit |
| `_wc_pinterest_google_product_category` | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Nomad` |
| `_wc_facebook_enhanced_catalog_attributes_gender` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | inherit |
| `_wc_facebook_product_image_source` | inherit |
| `_wc_facebook_sync_enabled` | inherit |
| `_wc_fb_visibility` | inherit |

### postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | inherit |
| `_monroney_sticker` | inherit |
| `_tigonwm` | inherit |

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | inherit (`Lithium`) |
| `pa_battery-warranty` | inherit (`5`) |
| `pa_brush-guard` | inherit (`YES`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`YES` + `LIGHT BAR`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | `{cartColor}` |
| `pa_denago-seat-colors` | inherit (`Stone`) |
| `pa_sound-system` | inherit (`DENAGO(R) SOUND SYSTEM`) |
| `pa_passengers` | `4 SEATER` |
| `pa_receiver-hitch` | inherit (`NO`) |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | `14 INCH` |
| `pa_shipping` | inherit |
| `pa_street-legal` | inherit (`YES`) |
| `pa_tire-profile` | `Street Tire` |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | `2` |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

| DMS Field | Default Value |
|---|---|
| `seatColor` | `Stone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `false` |
| `hasSoundSystem` | `false` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |

### Available Colors

Black, Blue, Champagne, Gray, Lava, White

---

## Denago Nomad XL

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `DENAGO(R) NOMAD XL {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | `7995` |
| `_price` | inherit |

### postmeta - Yoast SEO

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | inherit |
| `_yoast_wpseo_metadesc` | inherit |
| `_yoast_wpseo_primary_product_cat` | inherit |
| `_yoast_wpseo_primary_location` | inherit |
| `_yoast_wpseo_primary_models` | inherit |
| `_yoast_wpseo_primary_added-features` | inherit |
| `_yoast_wpseo_is_cornerstone` | inherit |
| `_yoast_wpseo_focus_kw` | inherit |
| `_yoast_wpseo_focus_keywords` | inherit |
| `_yoast_wpseo_bctitle` | inherit |
| `_yoast_wpseo_opengraph-title` | inherit |
| `_yoast_wpseo_opengraph-description` | inherit |
| `_yoast_wpseo_opengraph-image-id` | inherit |
| `_yoast_wpseo_opengraph-image` | inherit |
| `_yoast_wpseo_twitter-image-id` | inherit |
| `_yoast_wpseo_twitter-image` | inherit |

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` + Tab 2: `Denago(R) Nomad XL Vehicle Specs` + Tab 3: `Denago Nomad XL User Manual` + Tab 4 (if year=2024): `VIDEO DENAGO 2024` + Tab 5 (if year=2024): `PICS DENAGO NOMAD XL 2024` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `Denago(R) EV Nomad XL Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Nomad XL` |
| `_wc_gla_gender` | inherit |
| `_wc_gla_sizeSystem` | inherit |
| `_wc_gla_adult` | inherit |

### postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | inherit |
| `_wc_pinterest_google_product_category` | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Nomad XL` |
| `_wc_facebook_enhanced_catalog_attributes_gender` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | inherit |
| `_wc_facebook_product_image_source` | inherit |
| `_wc_facebook_sync_enabled` | inherit |
| `_wc_fb_visibility` | inherit |

### postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | inherit |
| `_monroney_sticker` | inherit |
| `_tigonwm` | inherit |

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | inherit (`Lithium`) |
| `pa_battery-warranty` | inherit (`5`) |
| `pa_brush-guard` | inherit (`YES`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`YES` + `LIGHT BAR`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | `{cartColor}` |
| `pa_denago-seat-colors` | inherit (`Stone`) |
| `pa_sound-system` | inherit (`DENAGO(R) SOUND SYSTEM`) |
| `pa_passengers` | `4 SEATER` |
| `pa_receiver-hitch` | inherit (`NO`) |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | `14 INCH` |
| `pa_shipping` | inherit |
| `pa_street-legal` | inherit (`YES`) |
| `pa_tire-profile` | `All-Terrain` |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | `2` |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

| DMS Field | Default Value |
|---|---|
| `seatColor` | `Stone` |
| `tireType` | `All-Terrain` |
| `tireRimSize` | `14` |
| `isLifted` | `true` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |

### Available Colors

Black, Blue, Gray, Lava, White, Verdant

---

## Denago Rover XL

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `DENAGO(R) ROVER XL {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | `9995` |
| `_price` | inherit |

### postmeta - Yoast SEO

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | inherit |
| `_yoast_wpseo_metadesc` | inherit |
| `_yoast_wpseo_primary_product_cat` | inherit |
| `_yoast_wpseo_primary_location` | inherit |
| `_yoast_wpseo_primary_models` | inherit |
| `_yoast_wpseo_primary_added-features` | inherit |
| `_yoast_wpseo_is_cornerstone` | inherit |
| `_yoast_wpseo_focus_kw` | inherit |
| `_yoast_wpseo_focus_keywords` | inherit |
| `_yoast_wpseo_bctitle` | inherit |
| `_yoast_wpseo_opengraph-title` | inherit |
| `_yoast_wpseo_opengraph-description` | inherit |
| `_yoast_wpseo_opengraph-image-id` | inherit |
| `_yoast_wpseo_opengraph-image` | inherit |
| `_yoast_wpseo_twitter-image-id` | inherit |
| `_yoast_wpseo_twitter-image` | inherit |

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` + Tab 2: `Denago(R) Rover XL Vehicle Specs` + Tab 3 (if year=2024): `VIDEO DENAGO 2024` + Tab 4 (if year=2024): `PICS DENAGO ROVER XL 2024` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `Denago(R) EV Rover XL Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Rover XL` |
| `_wc_gla_gender` | inherit |
| `_wc_gla_sizeSystem` | inherit |
| `_wc_gla_adult` | inherit |

### postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | inherit |
| `_wc_pinterest_google_product_category` | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Rover XL` |
| `_wc_facebook_enhanced_catalog_attributes_gender` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | inherit |
| `_wc_facebook_product_image_source` | inherit |
| `_wc_facebook_sync_enabled` | inherit |
| `_wc_fb_visibility` | inherit |

### postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | inherit |
| `_monroney_sticker` | inherit |
| `_tigonwm` | inherit |

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | inherit (`Lithium`) |
| `pa_battery-warranty` | inherit (`5`) |
| `pa_brush-guard` | inherit (`YES`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`YES` + `LIGHT BAR`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | `{cartColor}` |
| `pa_denago-seat-colors` | inherit (`Stone`) |
| `pa_sound-system` | inherit (`DENAGO(R) SOUND SYSTEM`) |
| `pa_passengers` | `4 SEATER` |
| `pa_receiver-hitch` | inherit (`NO`) |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | `14 INCH` |
| `pa_shipping` | inherit |
| `pa_street-legal` | inherit (`YES`) |
| `pa_tire-profile` | `All-Terrain` |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | `2` |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

| DMS Field | Default Value |
|---|---|
| `seatColor` | `Stone` |
| `tireType` | `All-Terrain` |
| `tireRimSize` | `14` |
| `isLifted` | `true` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |

### Available Colors

Black, Blue, Gray, Lava, White, Verdant

---

## Denago Rover XXL

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `DENAGO(R) ROVER XXL {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | From API payload |
| `_price` | inherit |

### postmeta - Yoast SEO

All values inherit.

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `Denago(R) EV Rover XXL Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `Rover XXL` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Rover XXL` |
| All other keys | inherit |

### postmeta - Pinterest / Tigon Specific

All values inherit.

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | From API payload |
| `pa_battery-warranty` | From API payload |
| `pa_brush-guard` | inherit (`YES`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | From API payload |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`YES` + `LIGHT BAR`) |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | From API payload |
| `pa_denago-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`DENAGO(R) SOUND SYSTEM`) |
| `pa_passengers` | From API payload |
| `pa_receiver-hitch` | From API payload |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | From API payload |
| `pa_shipping` | inherit |
| `pa_street-legal` | From API payload |
| `pa_tire-profile` | From API payload |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | From API payload |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

All values from API payload.

### Available Colors

From API payload

---

## Denago Rover XL6

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `DENAGO(R) ROVER XL6 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | From API payload |
| `_price` | inherit |

### postmeta - Yoast SEO

All values inherit.

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `Denago(R) EV Rover XL6 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `Rover XL6` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Rover XL6` |
| All other keys | inherit |

### postmeta - Pinterest / Tigon Specific

All values inherit.

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | From API payload |
| `pa_battery-warranty` | From API payload |
| `pa_brush-guard` | inherit (`YES`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | From API payload |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`YES` + `LIGHT BAR`) |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | From API payload |
| `pa_denago-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`DENAGO(R) SOUND SYSTEM`) |
| `pa_passengers` | From API payload |
| `pa_receiver-hitch` | From API payload |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | From API payload |
| `pa_shipping` | inherit |
| `pa_street-legal` | From API payload |
| `pa_tire-profile` | From API payload |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | From API payload |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

All values from API payload.

### Available Colors

From API payload

---

## Denago Rover XL Ultra

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `DENAGO(R) ROVER XL ULTRA {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | From API payload |
| `_price` | inherit |

### postmeta - Yoast SEO

All values inherit.

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `Denago(R) EV Rover XL Ultra Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `Rover XL Ultra` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Rover XL Ultra` |
| All other keys | inherit |

### postmeta - Pinterest / Tigon Specific

All values inherit.

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | From API payload |
| `pa_battery-warranty` | From API payload |
| `pa_brush-guard` | inherit (`YES`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | From API payload |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`YES` + `LIGHT BAR`) |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | From API payload |
| `pa_denago-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`DENAGO(R) SOUND SYSTEM`) |
| `pa_passengers` | From API payload |
| `pa_receiver-hitch` | From API payload |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | From API payload |
| `pa_shipping` | inherit |
| `pa_street-legal` | From API payload |
| `pa_tire-profile` | From API payload |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | From API payload |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

All values from API payload.

### Available Colors

From API payload

---

## Denago Scout

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `DENAGO(R) SCOUT {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | From API payload |
| `_price` | inherit |

### postmeta - Yoast SEO

All values inherit.

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `Denago(R) EV Scout Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `Scout` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Scout` |
| All other keys | inherit |

### postmeta - Pinterest / Tigon Specific

All values inherit.

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | From API payload |
| `pa_battery-warranty` | From API payload |
| `pa_brush-guard` | inherit (`YES`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | From API payload |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`YES` + `LIGHT BAR`) |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | From API payload |
| `pa_denago-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`DENAGO(R) SOUND SYSTEM`) |
| `pa_passengers` | From API payload |
| `pa_receiver-hitch` | From API payload |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | From API payload |
| `pa_shipping` | inherit |
| `pa_street-legal` | From API payload |
| `pa_tire-profile` | From API payload |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | From API payload |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

All values from API payload.

### Available Colors

From API payload

---

## Denago Oxen

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `DENAGO(R) OXEN {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | From API payload |
| `_price` | inherit |

### postmeta - Yoast SEO

All values inherit.

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `Denago(R) EV Oxen Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `Oxen` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Oxen` |
| All other keys | inherit |

### postmeta - Pinterest / Tigon Specific

All values inherit.

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | From API payload |
| `pa_battery-warranty` | From API payload |
| `pa_brush-guard` | inherit (`YES`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | From API payload |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`YES` + `LIGHT BAR`) |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | From API payload |
| `pa_denago-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`DENAGO(R) SOUND SYSTEM`) |
| `pa_passengers` | From API payload |
| `pa_receiver-hitch` | From API payload |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | From API payload |
| `pa_shipping` | inherit |
| `pa_street-legal` | From API payload |
| `pa_tire-profile` | From API payload |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | From API payload |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

All values from API payload.

### Available Colors

From API payload
