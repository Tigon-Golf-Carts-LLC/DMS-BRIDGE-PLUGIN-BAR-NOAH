# Icon - New Vehicle Manufacturer Mapping

> **Scope:** Every key from the global `Database_Object` is listed below.
> Values marked `inherit` are unchanged from [Global-New-Logic.md](../Global-New-Logic.md).
> Only non-inherit values are Icon-specific overrides.
>
> **Important:** Icon has three distinct series with different defaults:
> - **C-Series** (commercial) -- NOT street legal, Brown seats, White only
> - **i-Series** (consumer) -- Street legal, 2 Tone seats, full color palette
> - **G-Series** (gas) -- GAS powered, Black seats, 4 colors only

Source: `Abstract_Cart.php`, `New_Cart_Converter.php`

---

## posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | inherit (`ICON(R) {MODEL} {Color} In {City} {State}`) |
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
| `_yoast_wpseo_metadesc` | inherit (uses `ICON(R)` as make) |
| `_yoast_wpseo_primary_product_cat` | Term ID for `ICON(R)` category |
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
| `_yikes_woo_products_tabs` | **No Icon-specific warranty or spec tabs.** No tabs are injected for Icon models. |

---

## postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit (`1`) |
| `_wcpa_product_meta` | Add-on list titled `ICON(R) {Model} Add Ons` (e.g. `ICON(R) i40 Add Ons`, `ICON(R) i40L Add Ons`, `ICON(R) i60L Add Ons`) |

---

## postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | inherit |
| `_wc_gla_condition` | inherit (`new`) |
| `_wc_gla_brand` | `ICON(R)` |
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
| `_wc_facebook_enhanced_catalog_attributes_brand` | `ICON(R)` |
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
| `pa_battery-type` | Varies by series: `AGM` (most electric C/i-Series), `null` (G-Series gas) | |
| `pa_battery-warranty` | Varies by model | |
| `pa_brush-guard` | **NO** | Icon override (Abstract_Cart.php:896) |
| `pa_cargo-rack` | inherit (`NO`) | |
| `pa_drivetrain` | inherit (`2X4`) | |
| `pa_electric-bed-lift` | inherit (`NO`) | |
| `pa_extended-top` | inherit | |
| `pa_fender-flares` | inherit (`YES`) | |
| `pa_led-accents` | **NO** | Icon override (Abstract_Cart.php:947-951) |
| `pa_lift-kit` | inherit | |
| `pa_location` | inherit | |
| `pa_icon-cart-colors` | `{cartColor}` from Icon palette | Icon-specific attribute slug |
| `pa_icon-seat-colors` | `{seatColor}` from Icon palette | Icon-specific attribute slug |
| `pa_sound-system` | `ICON(R) SOUND SYSTEM` | Icon override |
| `pa_passengers` | inherit | |
| `pa_receiver-hitch` | inherit (`NO`) | |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) | |
| `pa_rim-size` | inherit | |
| `pa_shipping` | inherit | |
| `pa_street-legal` | Varies: `YES` (i-Series), `NO` (C-Series, G-Series) | |
| `pa_tire-profile` | inherit | |
| `pa_vehicle-class` | inherit | |
| `pa_vehicle-warranty` | Varies by model | |
| `pa_year-of-vehicle` | inherit | |

### Available Icon Cart Colors by Series

**i-Series (consumer, street legal):**
Black, Caribbean, Champagne, Forest, Indigo, Lime, Orange, Purple, Sangria, Silver, Torch, White, Yellow

**C-Series (commercial, NOT street legal):**
White

**G-Series (gas):**
Black, Forest, Indigo, White

### Available Icon Seat Colors by Series

| Series | Seat Color |
|---|---|
| C-Series | Brown |
| i-Series | 2 Tone |
| G-Series | Black |

---

## term_relationships - Categories

Icon categories vary by series. The table below shows which categories apply to each:

| Category | Condition | i-Series (Electric) | C-Series (Electric) | G-Series (Gas) |
|---|---|---|---|---|
| `ICON(R)` | Always | YES | YES | YES |
| `ICON(R) {MODEL}` | If exists in system | YES | YES | YES |
| `{N} SEATER` | From passenger count | YES | YES | YES |
| `LIFTED` | If `isLifted=true` | Per model | Per model | Per model |
| `NEW` | Always | YES | YES | YES |
| `ELECTRIC` | `isElectric=true` | YES | YES | NO |
| `GAS` | `isElectric=false` | NO | NO | YES |
| `ZERO EMISSION VEHICLES (ZEVS)` | Electric | YES | YES | NO |
| `LITHIUM` | `battery.type = Lithium` | Per model | Per model | NO |
| `LEAD-ACID` | `battery.type = Lead` | Per model | Per model | NO |
| `48 VOLT` | Electric, `packVoltage=48` | YES | YES | NO |
| `STREET LEGAL` | Electric + street legal | YES | **NO** | NO |
| `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)` | Electric + street legal | YES | **NO** | NO |
| `BATTERY ELECTRIC VEHICLES (BEVS)` | Electric + street legal | YES | **NO** | NO |
| `LOW SPEED VEHICLES (LSVS)` | Electric + street legal | YES | **NO** | NO |
| `MEDIUM SPEED VEHICLES (MSVS)` | Electric + street legal | YES | **NO** | NO |
| `PERSONAL TRANSPORTATION VEHICLES (PTVS)` | Gas | NO | NO | YES |
| `LOCAL NEW ACTIVE INVENTORY` | If not rental | YES | YES | YES |
| `LOCAL NEW RENTAL INVENTORY` | If rental | Per cart | Per cart | Per cart |
| `RENTAL` | If rental | Per cart | Per cart | Per cart |
| `GOLF CARTS` | Always | YES | YES | YES |
| `2X4` | Always | YES | YES | YES |
| `TIGON DEALERSHIP` | Always | YES | YES | YES |
| `TIGON GOLF CARTS {CITY} {STATE}` | Always | YES | YES | YES |

---

## term_relationships - Custom Taxonomies

| Taxonomy | Value |
|---|---|
| Location (city) | inherit |
| Location (state) | inherit |
| Manufacturer | `ICON(R)` |
| Model | `ICON(R) {MODEL}` |
| Sound System | `ICON(R) SOUND SYSTEM` |
| Added Features | inherit |
| Vehicle Class | inherit (includes PTV for G-Series gas models) |
| Inventory Status | inherit |
| Drivetrain | inherit (`2X4`) |
| Shipping Class | inherit (Term ID `665`) |

---

## term_relationships - Tags

| Tag | Condition |
|---|---|
| `ICON(R)` | Always |
| `ICON(R) {MODEL}` | Always |
| `ICON(R) {MODEL} {COLOR}` | Always |
| All other tags | inherit from global (electric vs gas tags vary by series) |

---

## Shared Defaults from Converter (All Icon Models)

These values are set in `New_Cart_Converter.php` and vary by series:

### All Icon Models (shared)

| Field | Value |
|---|---|
| `battery.packVoltage` | `48` (electric models only) |
| `battery.isDC` | `false` |

### i-Series Defaults (consumer, street legal, electric)

| Field | Value |
|---|---|
| `isElectric` | `true` |
| `battery.type` | Varies (AGM for most) |
| `title.isStreetLegal` | `true` |
| `seatColor` | `2 Tone` |

### C-Series Defaults (commercial, NOT street legal, electric)

| Field | Value |
|---|---|
| `isElectric` | `true` |
| `battery.type` | Varies (AGM for most) |
| `title.isStreetLegal` | `false` |
| `seatColor` | `Brown` |

### G-Series Defaults (gas)

| Field | Value |
|---|---|
| `isElectric` | `false` |
| `battery.type` | `null` |
| `title.isStreetLegal` | `false` |
| `seatColor` | `Black` |

> **Note:** `battery.brand`, `battery.ampHours`, `battery.warrantyLength`, `tireType`, `passengers`, `retailPrice`, and `warrantyLength` all vary by individual model.

### Description Hyperlinks

- Make dealer format: `icon` (standard)
- Links to: `https://tigongolfcarts.com/icon`
