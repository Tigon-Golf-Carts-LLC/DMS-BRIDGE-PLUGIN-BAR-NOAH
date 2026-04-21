# Bintelli - New Vehicle Manufacturer Mapping

> **Scope:** Every key from the global `Database_Object` is listed below.
> Values marked `inherit` are unchanged from [Global-New-Logic.md](../Global-New-Logic.md).
> Only non-inherit values are Bintelli-specific overrides.
>
> **Important:** Bintelli does NOT have a template in `New_Cart_Converter.php`.
> All data comes from the DMS. Bintelli HAS dedicated color palette attributes.

Source: `Abstract_Cart.php`

---

## posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | inherit (`BINTELLI(R) {MODEL} {Color} In {City} {State}`) |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit (`draft`) |
| `comment_status` | inherit (`open`) |
| `ping_status` | inherit (`closed`) |
| `menu_order` | inherit (`0`) |
| `post_type` | inherit (`product`) |
| `comment_count` | inherit (`0`) |
| `post_author` | inherit (`3`) |
| `post_name` | inherit |

---

## postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit (`taxable`) |
| `_tax_class` | inherit (`standard`) |
| `_manage_stock` | inherit (`no`) |
| `_backorders` | inherit (`no`) |
| `_sold_individually` | inherit (`no`) |
| `_virtual` | inherit (`no`) |
| `_downloadable` | inherit (`no`) |
| `_download_limit` | inherit (`-1`) |
| `_download_expiry` | inherit (`-1`) |
| `_stock` | inherit (`10000`) |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | inherit (serialized `pa_*` array) |
| `_thumbnail_id` | inherit (`null`) |
| `_product_image_gallery` | inherit (`null`) |
| `_regular_price` | inherit (`cart.retailPrice`) |
| `_price` | inherit (`cart.salePrice`) |

---

## postmeta - Yoast SEO

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | inherit (`{post_title} - Tigon Golf Carts`) |
| `_yoast_wpseo_metadesc` | inherit |
| `_yoast_wpseo_primary_product_cat` | Term ID for `BINTELLI(R)` category |
| `_yoast_wpseo_primary_location` | inherit |
| `_yoast_wpseo_primary_models` | inherit (`null`) |
| `_yoast_wpseo_primary_added-features` | inherit (`null`) |
| `_yoast_wpseo_is_cornerstone` | inherit (`1`) |
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

## postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | **No Bintelli-specific warranty or spec tabs.** No tabs are injected for Bintelli models. |

---

## postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit (`1`) |
| `_wcpa_product_meta` | Add-on list titled `BINTELLI(R) {Model} Add Ons` (follows global format) |

---

## postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit (`new`) |
| `_wc_gla_brand` | `BINTELLI(R)` |
| `_wc_gla_color` | inherit (`{CART_COLOR}` uppercased) |
| `_wc_gla_pattern` | inherit (`{model}`) |
| `_wc_gla_gender` | inherit (`unisex`) |
| `_wc_gla_sizeSystem` | inherit (`US`) |
| `_wc_gla_adult` | inherit (`no`) |

---

## postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | inherit (`new`) |
| `_wc_pinterest_google_product_category` | inherit (`Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts`) |

---

## postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `BINTELLI(R)` |
| `_wc_facebook_enhanced_catalog_attributes_color` | inherit (`{CART_COLOR}` uppercased) |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | inherit (`{model}`) |
| `_wc_facebook_enhanced_catalog_attributes_gender` | inherit (`unisex`) |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | inherit (`all ages`) |
| `_wc_facebook_product_image_source` | inherit (`product`) |
| `_wc_facebook_sync_enabled` | inherit (`yes`) |
| `_wc_fb_visibility` | inherit (`yes`) |

---

## postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | inherit |
| `_monroney_sticker` | inherit (`field_66e3332abf481`) |
| `_tigonwm` | inherit |

---

## term_relationships - Attribute Overrides

These override the global attribute defaults from Global-New-Logic.md:

| Attribute | Value | Notes |
|---|---|---|
| `pa_battery-type` | inherit (from DMS data) | |
| `pa_battery-warranty` | inherit (from DMS data) | |
| `pa_brush-guard` | **NO** | Non-Denago/non-Evolution brand |
| `pa_cargo-rack` | inherit (`NO`) | |
| `pa_drivetrain` | inherit (`2X4`) | |
| `pa_electric-bed-lift` | inherit (`NO`) | |
| `pa_extended-top` | inherit | |
| `pa_fender-flares` | inherit (`YES`) | |
| `pa_led-accents` | **NO** | Non-Denago brand |
| `pa_lift-kit` | inherit | |
| `pa_location` | inherit | |
| `pa_bintelli-cart-colors` | `{cartColor}` from Bintelli palette | Bintelli-specific attribute slug |
| `pa_bintelli-seat-colors` | `{seatColor}` from Bintelli palette | Bintelli-specific attribute slug |
| `pa_sound-system` | `BINTELLI(R) SOUND SYSTEM` | Brand-specific |
| `pa_passengers` | inherit | |
| `pa_receiver-hitch` | inherit (`NO`) | |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) | |
| `pa_rim-size` | inherit | |
| `pa_shipping` | inherit | |
| `pa_street-legal` | inherit (from DMS data) | |
| `pa_tire-profile` | inherit | |
| `pa_vehicle-class` | inherit | |
| `pa_vehicle-warranty` | inherit (from DMS data) | |
| `pa_year-of-vehicle` | inherit | |

---

## term_relationships - Categories

Categories are determined by DMS data (no converter defaults):

| Category | Condition | Applied? |
|---|---|---|
| `BINTELLI(R)` | Always | YES |
| `BINTELLI(R) {MODEL}` | If exists in system | YES |
| `{N} SEATER` | From passenger count | From DMS |
| `LIFTED` | If `isLifted=true` | From DMS |
| `NEW` | Always | YES |
| `ELECTRIC` | `isElectric=true` | From DMS |
| `GAS` | `isElectric=false` | From DMS |
| `ZERO EMISSION VEHICLES (ZEVS)` | Electric | From DMS |
| `LITHIUM` | `battery.type = Lithium` | From DMS |
| `LEAD-ACID` | `battery.type = Lead` | From DMS |
| `{voltage} VOLT` | Electric | From DMS |
| `STREET LEGAL` | Electric + street legal | From DMS |
| `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)` | Electric + street legal | From DMS |
| `BATTERY ELECTRIC VEHICLES (BEVS)` | Electric + street legal | From DMS |
| `LOW SPEED VEHICLES (LSVS)` | Electric + street legal | From DMS |
| `MEDIUM SPEED VEHICLES (MSVS)` | Electric + street legal | From DMS |
| `PERSONAL TRANSPORTATION VEHICLES (PTVS)` | Gas | From DMS |
| `LOCAL NEW ACTIVE INVENTORY` | If not rental | YES |
| `LOCAL NEW RENTAL INVENTORY` | If rental | Per cart |
| `RENTAL` | If rental | Per cart |
| `GOLF CARTS` | Always | YES |
| `2X4` | Always | YES |
| `TIGON DEALERSHIP` | Always | YES |
| `TIGON GOLF CARTS {CITY} {STATE}` | Always | YES |

---

## term_relationships - Custom Taxonomies

| Taxonomy | Value |
|---|---|
| Location (city) | inherit |
| Location (state) | inherit |
| Manufacturer | `BINTELLI(R)` |
| Model | `BINTELLI(R) {MODEL}` |
| Sound System | `BINTELLI(R) SOUND SYSTEM` |
| Added Features | inherit |
| Vehicle Class | inherit |
| Inventory Status | inherit |
| Drivetrain | inherit (`2X4`) |
| Shipping Class | inherit (Term ID `665`) |

---

## term_relationships - Tags

| Tag | Condition |
|---|---|
| `BINTELLI(R)` | Always |
| `BINTELLI(R) {MODEL}` | Always |
| `BINTELLI(R) {MODEL} {COLOR}` | Always |
| All other tags | inherit from global |

---

## Shared Defaults from Converter

**Bintelli has NO converter defaults.** All field values come directly from the DMS data:

| Field | Value |
|---|---|
| `isElectric` | From DMS |
| `battery.brand` | From DMS |
| `battery.type` | From DMS |
| `battery.ampHours` | From DMS |
| `battery.packVoltage` | From DMS |
| `battery.warrantyLength` | From DMS |
| `battery.isDC` | From DMS |
| `title.isStreetLegal` | From DMS |
| `seatColor` | From DMS |
| `tireRimSize` | From DMS |
| `warrantyLength` | From DMS |
| `passengers` | From DMS |
| `retailPrice` | From DMS |

All other attributes follow the global logic in [Global-New-Logic.md](../Global-New-Logic.md).
