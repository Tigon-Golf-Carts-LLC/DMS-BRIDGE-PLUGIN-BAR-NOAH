# Evolution - New Vehicle Manufacturer Mapping

> **Scope:** Every key from the global `Database_Object` is listed below.
> Values marked `inherit` are unchanged from [Global-New-Logic.md](../Global-New-Logic.md).
> Only non-inherit values are Evolution-specific overrides.

Source: `Abstract_Cart.php`, `New_Cart_Converter.php`

---

## posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | inherit (`EVOLUTION(R) {MODEL} {Color} In {City} {State}`) |
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
| `_yoast_wpseo_metadesc` | inherit (uses `EVOLUTION(R)` as make) |
| `_yoast_wpseo_primary_product_cat` | Term ID for `EVOLUTION(R)` category |
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
| `_yikes_woo_products_tabs` | **"EVolution Warranty"** tab (new carts only) + model-specific spec/image tabs (see Model-Logic files for each Evolution model) |

---

## postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit (`1`) |
| `_wcpa_product_meta` | Add-on list titled `EVolution(R) {Model} Add Ons`. **D5 models:** first space in model name becomes a hyphen (e.g. `D5 Maverick 4 Plus` -> `EVolution(R) D5-Maverick 4 Plus Add Ons`). Other examples: `EVolution(R) Carrier 6 Plus Add Ons`, `EVolution(R) Classic 4 Pro Add Ons` |

---

## postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit (`new`) |
| `_wc_gla_brand` | `EVOLUTION(R)` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | `EVOLUTION(R)` |
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
| `pa_battery-type` | `Lithium` | From shared default `battery.type = Lithium` |
| `pa_battery-warranty` | `5` | From shared default `battery.warrantyLength = 5` |
| `pa_brush-guard` | **YES** | Evolution override (Abstract_Cart.php:889) |
| `pa_cargo-rack` | inherit (`NO`) | |
| `pa_drivetrain` | inherit (`2X4`) | |
| `pa_electric-bed-lift` | inherit (`NO`) | |
| `pa_extended-top` | inherit | |
| `pa_fender-flares` | inherit (`YES`) | |
| `pa_led-accents` | **NO** | Evolution override (Abstract_Cart.php:947-951) |
| `pa_lift-kit` | inherit | |
| `pa_location` | inherit | |
| `pa_evolution-cart-colors` | `{cartColor}` from Evolution palette | Evolution-specific attribute slug |
| `pa_evolution-seat-colors` | `{seatColor}` from Evolution palette | Evolution-specific attribute slug |
| `pa_sound-system` | `EVOLUTION(R) SOUND SYSTEM` | Evolution override |
| `pa_passengers` | inherit | |
| `pa_receiver-hitch` | inherit (`NO`) | |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) | |
| `pa_rim-size` | `14 INCH` | From shared default `tireRimSize = 14` |
| `pa_shipping` | inherit | |
| `pa_street-legal` | `YES` | From shared default `title.isStreetLegal = true` |
| `pa_tire-profile` | inherit | |
| `pa_vehicle-class` | inherit | |
| `pa_vehicle-warranty` | Varies by model | `seatColor`, `ampHours`, `passengers`, `retailPrice`, `warrantyLength` vary by model (default `2`) |
| `pa_year-of-vehicle` | inherit | |

### Available Evolution Cart Colors

Artic grey, Black, Black sapphire, Blue, Candy apple, Copper, Flamenco red, Green, Lime green, Mediterranean blue, Midnight blue, Mineral white, Navy blue, Portimao blue, Red, Silver, Sky blue, White

### Available Evolution Seat Colors

Varies by model (see Model-Logic files)

---

## term_relationships - Categories

All Evolution models receive these categories (based on shared defaults: `isElectric=true`, `battery.type=Lithium`, `battery.packVoltage=48`, `isStreetLegal=true`):

| Category | Condition | Applied? |
|---|---|---|
| `EVOLUTION(R)` | Always | YES |
| `EVOLUTION(R) {MODEL}` | If exists in system | YES |
| `{N} SEATER` | From passenger count | YES |
| `LIFTED` | If `isLifted=true` | Per model |
| `NEW` | Always | YES |
| `ELECTRIC` | `isElectric=true` | YES |
| `GAS` | `isElectric=false` | NO |
| `ZERO EMISSION VEHICLES (ZEVS)` | Electric | YES |
| `LITHIUM` | `battery.type = Lithium` | YES |
| `LEAD-ACID` | `battery.type = Lead` | NO |
| `48 VOLT` | Electric, `packVoltage=48` | YES |
| `STREET LEGAL` | Electric + street legal | YES |
| `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)` | Electric + street legal | YES |
| `BATTERY ELECTRIC VEHICLES (BEVS)` | Electric + street legal | YES |
| `LOW SPEED VEHICLES (LSVS)` | Electric + street legal | YES |
| `MEDIUM SPEED VEHICLES (MSVS)` | Electric + street legal | YES |
| `PERSONAL TRANSPORTATION VEHICLES (PTVS)` | Gas | NO |
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
| Manufacturer | `EVOLUTION(R)` |
| Model | `EVOLUTION(R) {MODEL}` |
| Sound System | `EVOLUTION(R) SOUND SYSTEM` |
| Added Features | inherit |
| Vehicle Class | inherit |
| Inventory Status | inherit |
| Drivetrain | inherit (`2X4`) |
| Shipping Class | inherit (Term ID `665`) |

---

## term_relationships - Tags

| Tag | Condition |
|---|---|
| `EVOLUTION(R)` | Always |
| `EVOLUTION(R) {MODEL}` | Always |
| `EVOLUTION(R) {MODEL} {COLOR}` | Always |
| All other tags | inherit from global |

---

## Shared Defaults from Converter (All Evolution Models)

These values are set in `New_Cart_Converter.php` for every Evolution model and feed into the mappings above:

| Field | Value |
|---|---|
| `isElectric` | `true` |
| `battery.brand` | `HDK` |
| `battery.type` | `Lithium` |
| `battery.packVoltage` | `48` |
| `battery.warrantyLength` | `5` |
| `battery.isDC` | `false` |
| `title.isStreetLegal` | `true` |
| `tireRimSize` | `14` |
| `seatColor` | Varies by model |
| `battery.ampHours` | Varies by model |
| `passengers` | Varies by model |
| `retailPrice` | Varies by model |
| `warrantyLength` | `2` (default, varies by model) |

### Description Hyperlinks

- Make dealer format: `evolution` (standard)
- Links to: `https://tigongolfcarts.com/evolution`
