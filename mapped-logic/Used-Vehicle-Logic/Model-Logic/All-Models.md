# Used Vehicle Model Logic - Complete Meta Key Reference

> **Important:** Used vehicles have NO pre-defined model templates. All data comes entirely from the DMS.

Source: `Abstract_Cart.php`, `Used/Cart.php`

---

## No Model Templates for Used

Unlike new vehicles (which have hardcoded defaults in `New_Cart_Converter.php`), all used vehicle specs come **entirely from the DMS cart document**. There is no template system -- every field is populated from what the DMS sends.

For used vehicles, the model name is essentially just a string that flows through to a handful of downstream lookups. It does NOT determine default specs, pricing, or attribute values.

---

## What the Model Name Affects

The DMS `cartType.model` field influences exactly four things for used vehicles:

### 1. Product Title (`post_title`)

Format: `{MAKE(R)} {MODEL} {Color} In {City} {State}`

The model name is inserted directly into the product title as received from the DMS.

### 2. Category and Taxonomy Lookups

- **Category:** If `{MAKE(R)} {MODEL}` exists as a WooCommerce product category, it is assigned.
- **Model Taxonomy:** `{MAKE(R)} {MODEL}` is assigned as a model taxonomy term, with special aliases applied:
  - Club Car DS -> `CLUB CAR(R) DS ELECTRIC`
  - Club Car Precedent -> `CLUB CAR(R) PRECEDENT ELECTRIC`
  - Yamaha Drive 2 -> `YAMAHA(R) DRIVE2`
  - Yamaha 4L -> `YAMAHA(R) CROWN 4 LIFTED`
  - Yamaha 6L -> `YAMAHA(R) CROWN 6 LIFTED`
  - Star models -> `STAR EV(R) {MODEL}`
  - EZGO models -> `EZ-GO(R) {MODEL}`

### 3. Tab Selection (Denago and Evolution Only)

Only Denago and Evolution used carts get model-specific tabs appended after the `TIGON Warranty (USED GOLF CARTS)` tab. All other makes get only the single warranty tab.

### 4. Description Hyperlinks

The model name determines the hyperlink URLs embedded in the product description:
- Make link: `https://tigongolfcarts.com/{make-hyphenated}` (with `denago-ev` for Denago)
- Model link: `https://tigongolfcarts.com/{make-hyphenated}/{model-hyphenated}`
- Special Turfman model URL format: `turfman/u-{number}` (utility prefix)

---

## Model-Specific Tab Mappings

### Denago Models (Used) - `_yikes_woo_products_tabs`

All Denago used carts get `TIGON Warranty (USED GOLF CARTS)` as the first tab. The following model-specific tabs are appended:

| DMS Model | Tab 2 | Tab 3 | Tab 4 |
|---|---|---|---|
| Nomad | `Denago(R) Nomad Vehicle Specs` | -- | -- |
| Nomad XL | `Denago(R) Nomad XL Vehicle Specs` | `Denago Nomad XL User Manual` | Year-specific image tabs |
| Rover XL | `Denago(R) Rover XL Vehicle Specs` | Year-specific image tabs | -- |
| All other Denago | (no additional tabs) | -- | -- |

### Evolution Models (Used) - `_yikes_woo_products_tabs`

All Evolution used carts get `TIGON Warranty (USED GOLF CARTS)` as the first tab. The following model-specific tabs are appended:

| DMS Model | Tab 2 | Tab 3 |
|---|---|---|
| Classic 2 Pro | `EVolution Classic 2 Pro Images` | `EVolution Classic 2 Pro Specs` |
| Classic 2 Plus | `EVolution Classic 2 Plus Images` | `EVolution Classic 2 Plus Specs` |
| Classic 4 Pro | `EVolution Classic 4 Pro Images` | `EVolution Classic 4 Pro Specs` |
| Classic 4 Plus | `EVolution Classic 4 Plus Images` | `EVolution Classic 4 Plus Specs` |
| D5 Maverick 2+2 | `EVolution D5-Maverick 2+2` | `EVolution D5-Maverick 2+2 Images` |
| D5 Ranger 2+2 | `EVOLUTION D5 RANGER 2+2 IMAGES` | `EVOLUTION D5 RANGER 2+2 SPECS` |
| D5 Ranger 4 | `EVOLUTION D5 RANGER 4 IMAGES` | `EVOLUTION D5 RANGER 4 SPEC` |
| D5 Ranger 4 Plus | `EVOLUTION D5 RANGER 4 PLUS IMAGES` | `EVOLUTION D5 RANGER 4 PLUS SPECS` |
| D5 Ranger 6 | `EVOLUTION D5 RANGER 6 IMAGES` | `EVOLUTION D5 RANGER 6 SPECS` |
| All other Evolution | (no additional tabs) | -- |

### All Other Makes - `_yikes_woo_products_tabs`

Only the single `TIGON Warranty (USED GOLF CARTS)` tab. No model-specific tabs are added.

---

## Full Meta Key Table: Model-Affected vs Inherited

The table below lists every meta key in the Database_Object and indicates whether it is affected by the model name or inherited directly from Global-Used-Logic / Manufacturer-Logic.

### posts (wp_posts)

| Column | Model-Affected? | Value for Used |
|---|---|---|
| `ID` | No | Existing product ID or auto-generated |
| `post_title` | **YES** | `{MAKE(R)} {MODEL} {Color} In {City} {State}` - model name inserted |
| `post_excerpt` | **YES** | Auto-generated HTML short description includes model in text (only on create) |
| `post_content` | **YES** | Auto-generated HTML spec table includes model name + model hyperlink |
| `post_status` | No | `publish` |
| `comment_status` | No | `open` |
| `ping_status` | No | `closed` |
| `menu_order` | No | `0` |
| `post_type` | No | `product` |
| `comment_count` | No | `0` |
| `post_author` | No | `3` |
| `post_name` | **YES** | DMS `websiteUrl` last segment OR `{make}-{model}-{color}-seat-{seat}-{city}-{state}` |

### postmeta - WooCommerce

| Meta Key | Model-Affected? | Value for Used |
|---|---|---|
| `_sku` | No | VIN > Serial (no generated fallback) |
| `_tax_status` | No | `taxable` |
| `_tax_class` | No | `standard` |
| `_manage_stock` | No | `no` |
| `_backorders` | No | `no` |
| `_sold_individually` | No | `no` |
| `_virtual` | No | `no` |
| `_downloadable` | No | `no` |
| `_download_limit` | No | `-1` |
| `_download_expiry` | No | `-1` |
| `_stock` | No | `10000` |
| `_stock_status` | No | From DMS `isInStock` |
| `_global_unique_id` | No | Auto from SKU |
| `_product_attributes` | No | Serialized pa_* array (make-driven, not model-driven) |
| `_thumbnail_id` | No | First sideloaded image |
| `_product_image_gallery` | No | Remaining sideloaded images |
| `_regular_price` | No | `cart.retailPrice` |
| `_price` | No | `cart.salePrice` |

### postmeta - Yoast SEO

| Meta Key | Model-Affected? | Value for Used |
|---|---|---|
| `_yoast_wpseo_title` | **YES** | `{post_title} - Tigon Golf Carts` - includes model via post_title |
| `_yoast_wpseo_metadesc` | **YES** | Includes model name in description text |
| `_yoast_wpseo_primary_product_cat` | No | Term ID for `{MAKE(R)}` category |
| `_yoast_wpseo_primary_location` | No | Term ID for location city |
| `_yoast_wpseo_primary_models` | No | `null` |
| `_yoast_wpseo_primary_added-features` | No | `null` |
| `_yoast_wpseo_is_cornerstone` | No | `1` |
| `_yoast_wpseo_focus_kw` | **YES** | Same as `post_title` (includes model) |
| `_yoast_wpseo_focus_keywords` | **YES** | Same as `post_title` (includes model) |
| `_yoast_wpseo_bctitle` | **YES** | Same as `post_title` (includes model) |
| `_yoast_wpseo_opengraph-title` | **YES** | Same as `post_title` (includes model) |
| `_yoast_wpseo_opengraph-description` | **YES** | Same as metadesc (includes model) |
| `_yoast_wpseo_opengraph-image-id` | No | Same as `_thumbnail_id` |
| `_yoast_wpseo_opengraph-image` | No | Featured image URL |
| `_yoast_wpseo_twitter-image-id` | No | Same as `_thumbnail_id` |
| `_yoast_wpseo_twitter-image` | No | Featured image URL |

### postmeta - Product Tabs

| Meta Key | Model-Affected? | Value for Used |
|---|---|---|
| `_yikes_woo_products_tabs` | **YES** (Denago + Evolution only) | First tab: `TIGON Warranty (USED GOLF CARTS)`. Denago/Evolution: additional model-specific tabs appended. All others: warranty tab only. |

### postmeta - Custom Product Add-Ons

| Meta Key | Model-Affected? | Value for Used |
|---|---|---|
| `wcpa_exclude_global_forms` | No | `1` |
| `_wcpa_product_meta` | No | Individual add-ons from DMS `cart.advertising.cartAddOns` (not model-driven) |

### postmeta - Google for WooCommerce

| Meta Key | Model-Affected? | Value for Used |
|---|---|---|
| `_wc_gla_mpn` | No | Same as `_global_unique_id` |
| `_wc_gla_condition` | No | `used` |
| `_wc_gla_brand` | No | `{MAKE(R)}` UPPERCASED |
| `_wc_gla_color` | No | `{CART_COLOR}` UPPERCASED |
| `_wc_gla_pattern` | **YES** | `{model}` original case |
| `_wc_gla_gender` | No | `unisex` |
| `_wc_gla_sizeSystem` | No | `US` |
| `_wc_gla_adult` | No | `no` |

### postmeta - Pinterest for WooCommerce

| Meta Key | Model-Affected? | Value for Used |
|---|---|---|
| `_wc_pinterest_condition` | No | `used` |
| `_wc_pinterest_google_product_category` | No | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` |

### postmeta - Facebook for WooCommerce

| Meta Key | Model-Affected? | Value for Used |
|---|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | No | `{MAKE(R)}` UPPERCASED |
| `_wc_facebook_enhanced_catalog_attributes_color` | No | `{CART_COLOR}` UPPERCASED |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | **YES** | `{model}` original case |
| `_wc_facebook_enhanced_catalog_attributes_gender` | No | `unisex` |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | No | `all ages` |
| `_wc_facebook_product_image_source` | No | `product` |
| `_wc_facebook_sync_enabled` | No | `yes` |
| `_wc_fb_visibility` | No | `yes` |

### postmeta - Tigon Specific

| Meta Key | Model-Affected? | Value for Used |
|---|---|---|
| `monroney_sticker` | No | `[pdf-embedder url="{url}"]` sideloaded from DMS |
| `_monroney_sticker` | No | `field_66e3332abf481` |
| `_tigonwm` | No | `{City Short} {ST}` or `TIGON(R) RENTALS` |

### term_relationships - Categories

| Category | Model-Affected? | Condition |
|---|---|---|
| `{MAKE(R)}` | No | Always |
| `{MAKE(R)} {MODEL}` | **YES** | If exists in system (model name determines which category) |
| `{N} SEATER` | No | From passenger count |
| `LIFTED` | No | If `isLifted=true` |
| `USED` | No | Always |
| `ELECTRIC` / `GAS` | No | From `isElectric` |
| `ZERO EMISSION VEHICLES (ZEVS)` | No | If electric |
| `LITHIUM` / `LEAD-ACID` | No | From battery type |
| `{voltage} VOLT` | No | If electric |
| `STREET LEGAL` | No | If electric + street legal |
| `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)` | No | If electric + street legal |
| `BATTERY ELECTRIC VEHICLES (BEVS)` | No | If electric + street legal |
| `LOW SPEED VEHICLES (LSVS)` | No | If electric + street legal |
| `MEDIUM SPEED VEHICLES (MSVS)` | No | If electric + street legal |
| `PERSONAL TRANSPORTATION VEHICLES (PTVS)` | No | If gas |
| `LOCAL USED ACTIVE INVENTORY` | No | If not rental |
| `LOCAL USED RENTAL INVENTORY` | No | If rental |
| `RENTAL` | No | If rental |
| `GOLF CARTS` | No | Always |
| `2X4` | No | Always |
| `TIGON DEALERSHIP` | No | Always |
| `TIGON GOLF CARTS {CITY} {STATE}` | No | Always |

### term_relationships - Tags

| Tag | Model-Affected? | Condition |
|---|---|---|
| `{MAKE(R)}` | No | Always |
| `{MAKE(R)} {MODEL}` | **YES** | Always (model name in tag) |
| `{MAKE(R)} {MODEL} {COLOR}` | **YES** | Always (model name in tag) |
| `{full name}` | **YES** | Always (model name in full name) |
| `{COLOR}` | No | Always |
| `{N} SEATS` | No | Always |
| `LIFTED` / `NON LIFTED` | No | From `isLifted` |
| `USED` | No | Always |
| `{CITY}`, `{CITY STATE}`, `{STATE}` | No | Always |
| `{CITY} GOLF CART DEALERSHIP` | No | Always |
| `{STATE} GOLF CART DEALERSHIP` | No | Always |
| `{CITY STATE} STREET LEGAL DEALERSHIP` | No | Always |
| `GOLF CART` | No | Always |
| `ELECTRIC` / `GAS` | No | From `isElectric` |
| `NEV`, `LSV`, `MSV`, `STREET LEGAL` | No | If electric + street legal |
| `PTV` | No | If gas |
| `TIGON`, `TIGON GOLF CARTS` | No | Always |

### term_relationships - Product Attributes (pa_*)

| Attribute | Model-Affected? | Value |
|---|---|---|
| `pa_battery-type` | No | From DMS |
| `pa_battery-warranty` | No | From DMS |
| `pa_brush-guard` | No | Make-driven (YES: Denago, Evolution. NO: all others) |
| `pa_cargo-rack` | No | `NO` |
| `pa_drivetrain` | No | `2X4` |
| `pa_electric-bed-lift` | No | `NO` |
| `pa_extended-top` | No | From DMS `hasExtendedTop` |
| `pa_fender-flares` | No | `YES` |
| `pa_led-accents` | No | Make-driven (YES: Denago. NO: all others) |
| `pa_lift-kit` | No | From DMS `isLifted` |
| `pa_location` | No | `{City} {State}` |
| `pa_{make}-cart-colors` | No | From DMS `cartColor` |
| `pa_{make}-seat-colors` | No | From DMS `seatColor` |
| `pa_cart-color` | No | From DMS (unknown brands fallback) |
| `pa_seat-color` | No | From DMS (unknown brands fallback) |
| `pa_sound-system` | No | Make-driven |
| `pa_passengers` | No | `{N} SEATER` |
| `pa_receiver-hitch` | No | `NO` |
| `pa_return-policy` | No | `90 DAY` + `YES` |
| `pa_rim-size` | No | From DMS |
| `pa_shipping` | No | Standard 3-tier |
| `pa_street-legal` | No | From DMS |
| `pa_tire-profile` | No | From DMS |
| `pa_vehicle-class` | No | Computed |
| `pa_vehicle-warranty` | No | From DMS |
| `pa_year-of-vehicle` | No | From DMS |

### term_relationships - Custom Taxonomies

| Taxonomy | Model-Affected? | Value |
|---|---|---|
| Location (city) | No | City term ID |
| Location (state) | No | State term ID |
| Manufacturer | No | Make term |
| Model | **YES** | `{MAKE} {MODEL}` term (with aliases) |
| Sound System | No | `{MAKE} SOUND SYSTEM` term |
| Added Features | No | From `addedFeatures` flags |
| Vehicle Class | No | Golf Cart + NEV/ZEV/LSV/MSV/PTV/UTV |
| Inventory Status | No | `LOCAL USED ACTIVE INVENTORY` / `LOCAL USED RENTAL INVENTORY` |
| Drivetrain | No | `2X4` |
| Shipping Class | No | Term ID `665` |

---

## Summary

For used vehicles, the model name is a passthrough string from the DMS that affects:

1. **`post_title`** -- model name inserted into product title
2. **Category/taxonomy lookups** -- `{MAKE(R)} {MODEL}` matched against existing terms (with aliases for Club Car, Yamaha, EZGO, Star)
3. **Tab selection** -- Denago and Evolution only get model-specific spec/image tabs appended after the TIGON Warranty tab
4. **Description hyperlinks** -- model name determines the `tigongolfcarts.com` URL path
5. **SEO/feed fields** -- model flows through to `_wc_gla_pattern`, `_wc_facebook_enhanced_catalog_attributes_pattern`, Yoast title/description/focus keywords, and tags

All other meta keys (WooCommerce, pricing, stock, images, add-ons, attributes, Google/Pinterest/Facebook condition, Tigon-specific fields) are **not model-affected** -- they inherit from Global-Used-Logic and Manufacturer-Logic without any model-specific variation. There is no model-level spec template for used vehicles.
