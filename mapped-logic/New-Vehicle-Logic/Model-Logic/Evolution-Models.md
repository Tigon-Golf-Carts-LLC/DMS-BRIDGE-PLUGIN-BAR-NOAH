# Evolution Models - Complete Database_Object Mapping Per Model

Source: `New_Cart_Converter.php`

All Evolution models inherit from `../Manufacturer-Logic/Evolution.md` which inherits from `../Global-New-Logic.md`.
Below is the COMPLETE per-model mapping. Every meta key is listed with either the model-specific value or `inherit`.

---

## Evolution Carrier 6 PLUS

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) CARRIER 6 PLUS {Color} In {City} {State}` |
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
| `_regular_price` | varies |
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
| `_yikes_woo_products_tabs` | inherit (no Carrier 6 PLUS-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Carrier 6 Plus Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Carrier 6 PLUS` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Carrier 6 PLUS` |
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
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `6 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Mineral white

---

## Evolution CLASSIC 2 PLUS

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) CLASSIC 2 PLUS {Color} In {City} {State}` |
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
| `_regular_price` | `6795` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` + Tab 2: `EVolution Classic 2 Plus Images` + Tab 3: `EVolution Classic 2 Plus Specs` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Classic 2 Plus Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `CLASSIC 2 PLUS` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `CLASSIC 2 PLUS` |
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
| `pa_extended-top` | `NO` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
| `pa_passengers` | `2 SEATER` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `false` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `false` |
| `passengers` | `2 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Mineral white, Sky blue

---

## Evolution CLASSIC 2 PRO

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) CLASSIC 2 PRO {Color} In {City} {State}` |
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
| `_regular_price` | `6995` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` + Tab 2: `EVolution Classic 2 Pro Images` + Tab 3: `EVolution Classic 2 Pro Specs` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Classic 2 Pro Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `CLASSIC 2 PRO` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `CLASSIC 2 PRO` |
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
| `pa_extended-top` | `NO` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
| `pa_passengers` | `2 SEATER` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `false` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `false` |
| `passengers` | `2 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Black, Blue, Candy apple, Lime green, Navy blue, Red, Silver, White

---

## Evolution CLASSIC 4 PLUS

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) CLASSIC 4 PLUS {Color} In {City} {State}` |
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
| `_regular_price` | `6995` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` + Tab 2: `EVolution Classic 4 Plus Images` + Tab 3: `EVolution Classic 4 Plus Specs` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Classic 4 Plus Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `CLASSIC 4 PLUS` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `CLASSIC 4 PLUS` |
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
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `false` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Mineral white, Sky blue

---

## Evolution CLASSIC 4 PRO

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) CLASSIC 4 PRO {Color} In {City} {State}` |
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
| `_regular_price` | `6995` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` + Tab 2: `EVolution Classic 4 Pro Images` + Tab 3: `EVolution Classic 4 Pro Specs` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Classic 4 Pro Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `CLASSIC 4 PRO` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `CLASSIC 4 PRO` |
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
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `false` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Black, Blue, Candy apple, Lime green, Navy blue, Red, Silver, White

---

## Evolution D3

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D3 {Color} In {City} {State}` |
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
| `_regular_price` | `17500` |
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
| `_yikes_woo_products_tabs` | inherit (no D3-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D3 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `D3` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D3` |
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
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `true` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Mediterranean blue, Mineral white, Portimao blue

---

## Evolution D5 Maverick 2+2

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 MAVERICK 2+2 {Color} In {City} {State}` |
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
| `_regular_price` | `8995` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` + Tab 2: `EVolution D5-Maverick 2+2` + Tab 3: `EVolution D5-Maverick 2+2 Images` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Maverick 2+2 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `D5 Maverick 2+2` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D5 Maverick 2+2` |
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
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Mediterranean blue, Mineral white, Sky blue

---

## Evolution D5 MAVERICK 6

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 MAVERICK 6 {Color} In {City} {State}` |
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
| `_regular_price` | `12995` |
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
| `_yikes_woo_products_tabs` | inherit (no D5 MAVERICK 6-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-MAVERICK 6 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `D5 MAVERICK 6` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D5 MAVERICK 6` |
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
| `pa_extended-top` | `NO` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `false` |
| `passengers` | `6 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Mediterranean blue, Mineral white, Portimao blue

---

## Evolution D5 Ranger 2+2

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 RANGER 2+2 {Color} In {City} {State}` |
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
| `_regular_price` | `8595` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` + Tab 2: `EVOLUTION D5 RANGER 2+2 IMAGES` + Tab 3: `EVOLUTION D5 RANGER 2+2 SPECS` |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Ranger 2+2 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `D5 Ranger 2+2` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D5 Ranger 2+2` |
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
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Lime green, Mediterranean blue, Mineral white, Portimao blue, Sky blue

---

## Evolution D5-C6 RANGER

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5-C6 RANGER {Color} In {City} {State}` |
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
| `_regular_price` | `11995` |
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
| `_yikes_woo_products_tabs` | inherit (no D5-C6 RANGER-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-C6 RANGER Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `D5-C6 RANGER` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D5-C6 RANGER` |
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
| `pa_extended-top` | `NO` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
| `pa_passengers` | `6 SEATER` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `false` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `false` |
| `passengers` | `6 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Mineral white

---

## Evolution D5-F4 MAVERICK

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5-F4 MAVERICK {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | inherit (no D5-F4 MAVERICK-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-F4 MAVERICK Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `D5-F4 MAVERICK` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D5-F4 MAVERICK` |
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
| `pa_extended-top` | `NO` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `false` |
| `passengers` | `4 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Mediterranean blue, Mineral white

---

## Evolution D5-C4 RANGER

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5-C4 RANGER {Color} In {City} {State}` |
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
| `_regular_price` | `9595` |
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
| `_yikes_woo_products_tabs` | inherit (no D5-C4 RANGER-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-C4 RANGER Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `D5-C4 RANGER` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D5-C4 RANGER` |
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
| `pa_extended-top` | `NO` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `false` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `false` |
| `passengers` | `4 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Lime green, Mediterranean blue, Mineral white, Portimao blue, Sky blue

---

## Evolution Forester 4+

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) FORESTER 4+ {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | inherit (no Forester 4+-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Forester 4+ Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Forester 4+` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Forester 4+` |
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
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `4 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Flamenco red, Mineral white, Mediterranean Blue, Blue

---

## Evolution Forester 6+

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) FORESTER 6+ {Color} In {City} {State}` |
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
| `_regular_price` | `10995` |
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
| `_yikes_woo_products_tabs` | inherit (no Forester 6+-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Forester 6+ Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Forester 6+` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Forester 6+` |
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
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
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
| `hasSoundSystem` | `true` |
| `hasHitch` | `false` |
| `hasExtendedTop` | `true` |
| `passengers` | `6 Passenger` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Available Colors

Artic grey, Black sapphire, Mineral white

---

## Evolution Turfman 200

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) TURFMAN 200 {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | inherit (no Turfman 200-specific tabs defined) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Turfman 200 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit |
| `_wc_gla_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_gla_color` | inherit |
| `_wc_gla_pattern` | `Turfman 200` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | inherit (`EVOLUTION(R)`) |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Turfman 200` |
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
| `pa_extended-top` | `NO` |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | inherit (`NO`) |
| `pa_lift-kit` | `NO` |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | `{cartColor}` |
| `pa_evolution-seat-colors` | `2 Tone` |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` |
| `pa_passengers` | `2 SEATER` |
| `pa_receiver-hitch` | `YES` |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | `14 INCH` |
| `pa_shipping` | inherit |
| `pa_street-legal` | **`NO`** |
| `pa_tire-profile` | `Street Tire` |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | `2` |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

| DMS Field | Default Value |
|---|---|
| `seatColor` | `2 Tone` |
| `tireType` | `Street Tire` |
| `tireRimSize` | `14` |
| `isLifted` | `false` |
| `hasSoundSystem` | `true` |
| `hasHitch` | `true` |
| `hasExtendedTop` | `false` |
| `passengers` | `2 Passenger` |
| `isStreetLegal` | `false` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `110` |
| `battery.packVoltage` | `48` |

### Special Notes
- This is one of the only Evolution models that is NOT street legal
- Has `pa_receiver-hitch` = `YES` (unique among Evolution models)
- Description hyperlink uses special format: `turfman/u-200` (utility prefix)

### Available Colors

White

---

## Evolution Carrier 8 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) CARRIER 8 PLUS {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Carrier 8 Plus Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `Carrier 8 Plus` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Carrier 8 Plus` |
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
| `pa_led-accents` | **NO** |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | From API payload |
| `pa_evolution-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`EVOLUTION(R) SOUND SYSTEM`) |
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

## Evolution D3 Standard

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D3 STANDARD {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D3 Standard Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `D3 Standard` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D3 Standard` |
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
| `pa_led-accents` | **NO** |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | From API payload |
| `pa_evolution-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`EVOLUTION(R) SOUND SYSTEM`) |
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

## Evolution D3 Lifted

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D3 LIFTED {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D3 Lifted Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `D3 Lifted` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D3 Lifted` |
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
| `pa_led-accents` | **NO** |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | From API payload |
| `pa_evolution-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`EVOLUTION(R) SOUND SYSTEM`) |
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

## Evolution D5 Maverick 2+2 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 MAVERICK 2+2 PLUS {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | Tab 1: `EVolution Warranty` (if applicable from API payload) |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Maverick 2+2 Plus Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_pattern` | `D5 Maverick 2+2 Plus` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `D5 Maverick 2+2 Plus` |
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
| `pa_led-accents` | **NO** |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_evolution-cart-colors` | From API payload |
| `pa_evolution-seat-colors` | From API payload |
| `pa_sound-system` | inherit (`EVOLUTION(R) SOUND SYSTEM`) |
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

## Evolution D5 Maverick 4 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 MAVERICK 4 PLUS {Color} In {City} {State}` |
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

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Maverick 4 Plus Add Ons` |

### postmeta - Google / Facebook pattern

`D5 Maverick 4 Plus`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D5 Maverick 4+2 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 MAVERICK 4+2 PLUS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Maverick 4+2 Plus Add Ons` |

### postmeta - Google / Facebook pattern

`D5 Maverick 4+2 Plus`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D5 Maverick 6 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 MAVERICK 6 PLUS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Maverick 6 Plus Add Ons` |

### postmeta - Google / Facebook pattern

`D5 Maverick 6 Plus`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D5 Ranger 2+2 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 RANGER 2+2 PLUS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Ranger 2+2 Plus Add Ons` |

### postmeta - Google / Facebook pattern

`D5 Ranger 2+2 Plus`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D5 Ranger 4 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 RANGER 4 PLUS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Ranger 4 Plus Add Ons` |

### postmeta - Google / Facebook pattern

`D5 Ranger 4 Plus`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D5 Ranger 4+2 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 RANGER 4+2 PLUS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Ranger 4+2 Plus Add Ons` |

### postmeta - Google / Facebook pattern

`D5 Ranger 4+2 Plus`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D5 Ranger 6 Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D5 RANGER 6 PLUS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D5-Ranger 6 Plus Add Ons` |

### postmeta - Google / Facebook pattern

`D5 Ranger 6 Plus`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution Turfman 800

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) TURFMAN 800 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Turfman 800 Add Ons` |

### postmeta - Google / Facebook pattern

`Turfman 800`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution Turfman 1000

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) TURFMAN 1000 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) Turfman 1000 Add Ons` |

### postmeta - Google / Facebook pattern

`Turfman 1000`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D-Max GT4

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D-MAX GT4 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D-Max GT4 Add Ons` |

### postmeta - Google / Facebook pattern

`D-Max GT4`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D-Max GT6

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D-MAX GT6 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D-Max GT6 Add Ons` |

### postmeta - Google / Facebook pattern

`D-Max GT6`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D-Max XT4

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D-MAX XT4 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D-Max XT4 Add Ons` |

### postmeta - Google / Facebook pattern

`D-Max XT4`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.

---

## Evolution D-Max XT6

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `EVOLUTION(R) D-MAX XT6 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `EVolution(R) D-Max XT6 Add Ons` |

### postmeta - Google / Facebook pattern

`D-Max XT6`

### All other postmeta

Inherit from Evolution manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Evolution models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `YES`, `pa_led-accents` = `NO`, `pa_sound-system` = `EVOLUTION(R) SOUND SYSTEM`.

### DMS Defaults

All values from API payload.
