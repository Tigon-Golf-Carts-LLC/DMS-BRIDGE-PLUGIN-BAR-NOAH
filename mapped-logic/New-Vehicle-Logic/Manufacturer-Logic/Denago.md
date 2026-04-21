# Denago - New Vehicle Manufacturer Mapping

> **How to edit:** Values here override Global-New-Logic.md for ALL Denago vehicles.
> `inherit` = uses the global value. Model files can further override.

Source: `Abstract_Cart.php`, `New_Cart_Converter.php`

---

## posts (wp_posts) - Denago Overrides

| Column | Value |
|---|---|
| `post_title` | `DENAGO(R) {MODEL} {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | `draft` (inherit) |
| `comment_status` | `open` (inherit) |
| `ping_status` | `closed` (inherit) |
| `menu_order` | `0` (inherit) |
| `post_type` | `product` (inherit) |
| `comment_count` | `0` (inherit) |
| `post_author` | `3` (inherit) |
| `post_name` | inherit |

---

## postmeta - WooCommerce - Denago Overrides

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | `taxable` (inherit) |
| `_tax_class` | `standard` (inherit) |
| `_manage_stock` | `no` (inherit) |
| `_backorders` | `no` (inherit) |
| `_sold_individually` | `no` (inherit) |
| `_virtual` | `no` (inherit) |
| `_downloadable` | `no` (inherit) |
| `_download_limit` | `-1` (inherit) |
| `_download_expiry` | `-1` (inherit) |
| `_stock` | `10000` (inherit) |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | Model-specific (see Model files) |
| `_price` | inherit |

---

## postmeta - Yoast SEO - Denago Overrides

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | inherit |
| `_yoast_wpseo_metadesc` | inherit |
| `_yoast_wpseo_primary_product_cat` | Term ID for `DENAGO(R)` category |
| `_yoast_wpseo_primary_location` | inherit |
| `_yoast_wpseo_primary_models` | inherit |
| `_yoast_wpseo_primary_added-features` | inherit |
| `_yoast_wpseo_is_cornerstone` | `1` (inherit) |
| `_yoast_wpseo_focus_kw` | inherit |
| `_yoast_wpseo_focus_keywords` | inherit |
| `_yoast_wpseo_bctitle` | inherit |
| `_yoast_wpseo_opengraph-title` | inherit |
| `_yoast_wpseo_opengraph-description` | inherit |
| `_yoast_wpseo_opengraph-image-id` | inherit |
| `_yoast_wpseo_opengraph-image` | inherit |
| `_yoast_wpseo_twitter-image-id` | inherit |
| `_yoast_wpseo_twitter-image` | inherit |

---

## postmeta - Product Tabs - Denago Overrides

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Tab 1: `DENAGO Warranty` + Model-specific spec tab + Year-specific video/image tabs |

---

## postmeta - Custom Product Add-Ons - Denago Overrides

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | `1` (inherit) |
| `_wcpa_product_meta` | `Denago(R) EV {Model} Add Ons` |

---

## postmeta - Google for WooCommerce - Denago Overrides

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | `new` (inherit) |
| `_wc_gla_brand` | `DENAGO(R)` |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED (inherit) |
| `_wc_gla_pattern` | Model-specific |
| `_wc_gla_gender` | `unisex` (inherit) |
| `_wc_gla_sizeSystem` | `US` (inherit) |
| `_wc_gla_adult` | `no` (inherit) |

---

## postmeta - Pinterest for WooCommerce - Denago Overrides

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | `new` (inherit) |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` (inherit) |

---

## postmeta - Facebook for WooCommerce - Denago Overrides

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `DENAGO(R)` |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | Model-specific |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` (inherit) |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` (inherit) |
| `_wc_facebook_product_image_source` | `product` (inherit) |
| `_wc_facebook_sync_enabled` | `yes` (inherit) |
| `_wc_fb_visibility` | `yes` (inherit) |

---

## postmeta - Tigon Specific - Denago Overrides

| Meta Key | Value |
|---|---|
| `monroney_sticker` | inherit |
| `_monroney_sticker` | `field_66e3332abf481` (inherit) |
| `_tigonwm` | inherit |

---

## term_relationships - Denago Attribute Overrides

| Attribute | Value |
|---|---|
| `pa_battery-type` | `Lithium` |
| `pa_battery-warranty` | `5` |
| `pa_brush-guard` | **`YES`** |
| `pa_cargo-rack` | `NO` (inherit) |
| `pa_drivetrain` | `2X4` (inherit) |
| `pa_electric-bed-lift` | `NO` (inherit) |
| `pa_extended-top` | Model-specific |
| `pa_fender-flares` | `YES` (inherit) |
| `pa_led-accents` | **`YES` + `LIGHT BAR`** |
| `pa_lift-kit` | Model-specific |
| `pa_location` | inherit |
| `pa_denago-cart-colors` | `{cartColor}` from Denago palette: Black, Blue, Champagne, Gray, Lava, White, Verdant |
| `pa_denago-seat-colors` | `{seatColor}` (default: Stone) |
| `pa_sound-system` | `DENAGO(R) SOUND SYSTEM` |
| `pa_passengers` | Model-specific |
| `pa_receiver-hitch` | `NO` (inherit) |
| `pa_return-policy` | `90 DAY` + `YES` (inherit) |
| `pa_rim-size` | `14 INCH` (default) |
| `pa_shipping` | inherit |
| `pa_street-legal` | `YES` |
| `pa_tire-profile` | Model-specific |
| `pa_vehicle-class` | Golf Cart, NEV, ZEV, LSV, MSV |
| `pa_vehicle-warranty` | `2` (default) |
| `pa_year-of-vehicle` | inherit |

---

## term_relationships - Denago Categories

All global categories apply, PLUS because all Denago are electric + street legal:
- `DENAGO(R)` (always)
- `ELECTRIC`, `ZERO EMISSION VEHICLES (ZEVS)`, `LITHIUM`, `48 VOLT`
- `STREET LEGAL`, `NEVS`, `BEVS`, `LSVS`, `MSVS`
- `NEW`, `GOLF CARTS`, `2X4`, `TIGON DEALERSHIP`

---

## Denago Shared Defaults (from New_Cart_Converter)

| DMS Field | Default Value |
|---|---|
| `isElectric` | `true` |
| `battery.brand` | `Denago` |
| `battery.type` | `Lithium` |
| `battery.ampHours` | `105` |
| `battery.packVoltage` | `48` |
| `battery.warrantyLength` | `5` |
| `battery.isDC` | `false` |
| `title.isStreetLegal` | `true` |
| `cartAttributes.seatColor` | `Stone` |
| `cartAttributes.tireRimSize` | `14` |
| `warrantyLength` | `2` |
