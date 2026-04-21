# Swift Models - Complete Database_Object Mapping Per Model

Source: `New_Cart_Converter.php`

All Swift models inherit from `../Manufacturer-Logic/Swift-EV.md` which inherits from `../Global-New-Logic.md`.
Below is the COMPLETE per-model mapping. Every meta key is listed with either the model-specific value or `inherit`.

Note: In `New_Cart_Converter.php` the make is stored as `Swift` (not `Swift EV`).
Battery brand is `ECO` (not CATL as mentioned in manufacturer doc - the converter has the actual values).

---

## Swift Mach 4

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `SWIFT EV(R) MACH 4 {Color} In {City} {State}` |
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
| `_regular_price` | `11500` |
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
| `_yikes_woo_products_tabs` | inherit (no Swift-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `SWIFT EV(R) Mach 4 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`SWIFT EV(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Mach 4` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`SWIFT EV(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Mach 4` |
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
| `pa_brush-guard` | inherit (`NO`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_swift-cart-colors` | `{cartColor}` |
| `pa_swift-seat-colors` | `2 Tone` |
| `pa_sound-system` | `NO` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `All-Terrain` |
| `tireRimSize` | `14` |
| `isLifted` | `true` |
| `hasSoundSystem` | `false` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |
| `isElectric` | `true` |
| `isStreetLegal` | `true` |
| `battery.brand` | `ECO` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `105` |
| `battery.packVoltage` | `48` |

### Available Colors

Black, Blue, Champagne, Red, White

---

## Swift Mach 4E

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `SWIFT EV(R) MACH 4E {Color} In {City} {State}` |
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
| `_regular_price` | `7500` |
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
| `_yikes_woo_products_tabs` | inherit (no Swift-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `SWIFT EV(R) Mach 4E Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`SWIFT EV(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Mach 4E` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`SWIFT EV(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Mach 4E` |
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
| `pa_brush-guard` | inherit (`NO`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_swift-cart-colors` | `{cartColor}` |
| `pa_swift-seat-colors` | `2 Tone` |
| `pa_sound-system` | `NO` |
| `pa_passengers` | `4 SEATER` |
| `pa_receiver-hitch` | inherit (`NO`) |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | `12 INCH` |
| `pa_shipping` | inherit |
| `pa_street-legal` | inherit (`YES`) |
| `pa_tire-profile` | `Street Tire` |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | `2` |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

| DMS Field | Default Value |
|---|---|
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `12` |
| `isLifted` | `false` |
| `hasSoundSystem` | `false` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |
| `isElectric` | `true` |
| `isStreetLegal` | `true` |
| `battery.brand` | `ECO` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `105` |
| `battery.packVoltage` | `48` |

### Available Colors

Black, Blue, Champagne, Red, Yellow

---

## Swift Mach 6

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `SWIFT EV(R) MACH 6 {Color} In {City} {State}` |
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
| `_regular_price` | `14000` |
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
| `_yikes_woo_products_tabs` | inherit (no Swift-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `SWIFT EV(R) Mach 6 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`SWIFT EV(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Mach 6` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`SWIFT EV(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Mach 6` |
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
| `pa_brush-guard` | inherit (`NO`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_swift-cart-colors` | `{cartColor}` |
| `pa_swift-seat-colors` | `2 Tone` |
| `pa_sound-system` | `NO` |
| `pa_passengers` | `6 SEATER` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `All-Terrain` |
| `tireRimSize` | `14` |
| `isLifted` | `true` |
| `hasSoundSystem` | `false` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `6 Passenger` |
| `isElectric` | `true` |
| `isStreetLegal` | `true` |
| `battery.brand` | `ECO` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `105` |
| `battery.packVoltage` | `48` |

### Available Colors

Black, Blue, Champagne, Red, Yellow

---

## Swift Mach 6E

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `SWIFT EV(R) MACH 6E {Color} In {City} {State}` |
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
| `_regular_price` | `9500` (approx - from converter) |
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
| `_yikes_woo_products_tabs` | inherit (no Swift-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `SWIFT EV(R) Mach 6E Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`SWIFT EV(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Mach 6E` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`SWIFT EV(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Mach 6E` |
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
| `pa_brush-guard` | inherit (`NO`) |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_swift-cart-colors` | `{cartColor}` |
| `pa_swift-seat-colors` | `2 Tone` |
| `pa_sound-system` | `NO` |
| `pa_passengers` | `6 SEATER` |
| `pa_receiver-hitch` | inherit (`NO`) |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | `12 INCH` |
| `pa_shipping` | inherit |
| `pa_street-legal` | inherit (`YES`) |
| `pa_tire-profile` | `Street Tire` |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | `2` |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

| DMS Field | Default Value |
|---|---|
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `12` |
| `isLifted` | `false` |
| `hasSoundSystem` | `false` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `6 Passenger` |
| `isElectric` | `true` |
| `isStreetLegal` | `true` |
| `battery.brand` | `ECO` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `105` |
| `battery.packVoltage` | `48` |

### Available Colors

Black, Blue, Champagne, Red, Yellow

---

## Pattern Summary

| Model | Passengers | Lifted | Tires | Rims | Price |
|---|---|---|---|---|---|
| Mach 4 | 4 | Yes | All-Terrain | 14" | $11,500 |
| Mach 4E | 4 | No | Street | 12" | $7,500 |
| Mach 6 | 6 | Yes | All-Terrain | 14" | $14,000 |
| Mach 6E | 6 | No | Street | 12" | ~$9,500 |

The "E" suffix = economy/entry-level (street tires, not lifted, smaller rims, lower price).
