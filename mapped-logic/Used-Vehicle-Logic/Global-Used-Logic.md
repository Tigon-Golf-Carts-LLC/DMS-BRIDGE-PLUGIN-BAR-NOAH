# Global Used Vehicle Logic - Complete Database_Object Mapping

> **How to edit:** Change any `Value` cell below. Manufacturer and Model files override values listed here.

Source: `Abstract_Cart.php`, `Used/Cart.php`, `Database_Object.php`

---

## method

| Condition | Value |
|---|---|
| `isInStock` AND `!isInBoneyard` AND `needOnWebsite` AND product exists | `update` |
| `isInStock` AND `!isInBoneyard` AND `needOnWebsite` AND no product | `create` |
| All other | `delete` |

---

## posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | Existing product ID or auto-generated |
| `post_title` | `{MAKE(R)} {MODEL} {Color} In {City} {State}` |
| `post_excerpt` | Auto-generated HTML short description (only on create, null on update) |
| `post_content` | Auto-generated HTML spec table + call shortcode |
| `post_status` | `publish` |
| `comment_status` | `open` |
| `ping_status` | `closed` |
| `menu_order` | `0` |
| `post_type` | `product` |
| `comment_count` | `0` |
| `post_author` | `3` |
| `post_name` | DMS `websiteUrl` last segment OR `{make}-{model}-{color}-seat-{seat}-{city}-{state}` |

**Key difference from new:** `post_status` = `publish` (used carts go live immediately; new carts start as `draft`).

---

## postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | VIN (`vinNo`) preferred, fallback Serial (`serialNo`). No generated/synthetic SKU - if neither exists AND no existing product, the cart is rejected with `WP_Error` |
| `_tax_status` | `taxable` |
| `_tax_class` | `standard` |
| `_manage_stock` | `no` |
| `_backorders` | `no` |
| `_sold_individually` | `no` |
| `_virtual` | `no` |
| `_downloadable` | `no` |
| `_download_limit` | `-1` |
| `_download_expiry` | `-1` |
| `_stock` | `10000` |
| `_stock_status` | From DMS `isInStock`: `instock` if true, `outofstock` if false (NOT forced to `outofstock` like new templates) |
| `_global_unique_id` | Auto from SKU: last 14 chars of letter-to-digit conversion, left-padded 0s |
| `_product_attributes` | Serialized `pa_*` attribute array (see Attributes section) |
| `_thumbnail_id` | Attachment ID of the first sideloaded image from DMS `imageUrls` array |
| `_product_image_gallery` | Comma-separated attachment IDs of remaining sideloaded images from DMS `imageUrls` array |
| `_regular_price` | `cart.retailPrice` |
| `_price` | `cart.salePrice` |

**Key differences from new:**
- `_sku`: No generated fallback. VIN > Serial only. Rejected if neither exists.
- `_stock_status`: Reflects actual DMS `isInStock` value (not forced to `outofstock`).
- `_thumbnail_id`: First sideloaded image from DMS `imageUrls` (new templates set this to `null`).
- `_product_image_gallery`: Remaining sideloaded images (new templates set this to `null`).

### Image Sideloading

Used carts inherit `Abstract_Cart.fetch_images()` which:
- Loops through `cart.imageUrls`
- Sideloads each image from `{file_source}/carts/{filename}`
- Image name format: `{full product name} For Sale{SKU} {index+1}`
- First image becomes `_thumbnail_id` (featured image)
- All subsequent images become `_product_image_gallery`

---

## postmeta - Yoast SEO

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | `{post_title} - Tigon Golf Carts` |
| `_yoast_wpseo_metadesc` | `{MAKE MODEL COLOR} At TIGON Golf Carts in {Location}. Call Now {Phone} Get 0% Financing, and Shipping Options Today!` |
| `_yoast_wpseo_primary_product_cat` | Term ID for `{MAKE(R)}` category |
| `_yoast_wpseo_primary_location` | Term ID for location city |
| `_yoast_wpseo_primary_models` | `null` |
| `_yoast_wpseo_primary_added-features` | `null` |
| `_yoast_wpseo_is_cornerstone` | `1` |
| `_yoast_wpseo_focus_kw` | Same as `post_title` |
| `_yoast_wpseo_focus_keywords` | Same as `post_title` |
| `_yoast_wpseo_bctitle` | Same as `post_title` |
| `_yoast_wpseo_opengraph-title` | Same as `post_title` |
| `_yoast_wpseo_opengraph-description` | Same as `_yoast_wpseo_metadesc` |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` |
| `_yoast_wpseo_opengraph-image` | Featured image URL via `wp_get_attachment_image_url` |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` |
| `_yoast_wpseo_twitter-image` | Featured image URL |

Identical to new vehicles.

---

## postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Serialized `[{name, id, title, content}]` array. First tab is always `TIGON Warranty (USED GOLF CARTS)`. Additional model-specific tabs for Denago and Evolution only (see Manufacturer/Model files). |

**Key differences from new:**
- First tab: `TIGON Warranty (USED GOLF CARTS)` (new carts get the manufacturer warranty tab, e.g., "DENAGO Warranty" or "EVolution Warranty").
- Used carts do NOT get the manufacturer warranty tab.
- Model-specific spec/image tabs are still appended for Denago and Evolution models.
- All other makes: only the single `TIGON Warranty (USED GOLF CARTS)` tab.

---

## postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | `1` |
| `_wcpa_product_meta` | Serialized - individual add-ons from `cart.advertising.cartAddOns` array (see full mapping table below) |

**Key difference from new:** New carts get a single model-level add-on list. Used carts get individual add-ons matched from the DMS `cart.advertising.cartAddOns` array.

### Full Add-On Mapping Table

Each DMS add-on string is checked individually. If present in `cartAddOns`, the corresponding WooCommerce option is included:

| DMS Add-On Key | WooCommerce Option | Seating Condition |
|---|---|---|
| `Golf cart enclosure 2 passenger 600` | 2 Passenger Golf Cart Enclosure | 2-seater only |
| `Golf cart enclosure 4 Passenger 800` | 4 Passenger Golf Cart Enclosure | 4-seater only |
| `Golf cart enclosure 6 passenger 1200` | 6 Passenger Golf Cart Enclosure | 6-seater only |
| `120 Volt inverter 500` | 120 Volt Inverter | Any |
| `32 inch light bar 220` | 32in LED Light Bar | Any |
| `Cargo caddie 250` | Cargo Caddie | Any |
| `Rear seat cupholders 80` | Rear Seat Cupholders | Any |
| `Upgraded charger 210` | Upgraded Charger | Any |
| `Breezeasy Fan System 400` | Breezeasy 3 Fan System | Any |
| `Golf bag attachment 120` | Golf Bag Attachment | Any |
| `Led light kit 350` | LED Cart Light Kit | Any |
| `Led light kit with signals and horn 495` | LED Cart Light Kit With Signals & Horn | Any |
| `Led under glow 400` | LED Under Glow Lights | Any |
| `Led roof lights 400` | LED Roof Lights | Any |
| `Rear seat kit 385` | Rear Seat Kit | Any |
| `Basic 4 Passenger storage cover 150` | Basic 4 Passenger Storage Cover | 4-seater only |
| `Premium 4 Passenger storage cover 300` | Premium 4 Passenger Storage Cover | 4-seater only |
| `Premium 6 Passenger storage cover 385` | Premium 6 Passenger Storage Cover | 6-seater only |
| `26 in sound bar 500` | 26" Sound Bar | Any |
| `32 in Sound bar 600` | 32" Sound Bar | Any |
| `EcoXGear subwoofer 745` | EcoXGear Subwoofer | Any |
| `New tinted windshield 210` | Tinted Windshield | Any |
| `Hitch 80` | Hitch Bolt On | Any |
| `Hitch 300` | Basic Hitch Weld On | Any |
| `Hitch 500` | Premium Hitch Weld On | Any |
| `Seat belts 4 Passenger 160` | Seat Belts 4 Passenger | 4-seater only |
| `Seat belts 6 Passenger 240` | Seat Belts 6 Passenger | 6-seater only |
| `Grab bar 85` | Grab Bar | Any |
| `Deluxe Grab Bar 150` | Deluxe Grab Bar | Any |
| `Side mirrors 65` | Side Mirrors | Any |
| `Extended roof 500` | Extended Roof 84" | Any |

---

## postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | Same as `_global_unique_id` |
| `_wc_gla_condition` | `used` |
| `_wc_gla_brand` | `{MAKE(R)}` UPPERCASED |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_gla_pattern` | `{model}` original case |
| `_wc_gla_gender` | `unisex` |
| `_wc_gla_sizeSystem` | `US` |
| `_wc_gla_adult` | `no` |

**Key difference from new:** `_wc_gla_condition` = `used` (new = `new`).

---

## postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | `used` |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` |

**Key difference from new:** `_wc_pinterest_condition` = `used` (new = `new`).

---

## postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `{MAKE(R)}` UPPERCASED |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `{model}` original case |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` |
| `_wc_facebook_product_image_source` | `product` |
| `_wc_facebook_sync_enabled` | `yes` |
| `_wc_fb_visibility` | `yes` |

Identical to new vehicles.

---

## postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | `[pdf-embedder url="{url}"]` - sideloaded from DMS (NOT forced to empty like new templates) |
| `_monroney_sticker` | `field_66e3332abf481` |
| `_tigonwm` | `{City Short} {ST}` or `TIGON(R) RENTALS` if rental |

**Key difference from new:** `monroney_sticker` is sideloaded from the DMS and populated with the actual PDF URL. New templates force this to `[pdf-embedder url=""]` (empty).

---

## term_relationships - Categories

| Category | Condition |
|---|---|
| `{MAKE(R)}` | Always |
| `{MAKE(R)} {MODEL}` | If exists in system |
| `{N} SEATER` | From passenger count |
| `LIFTED` | If `isLifted=true` |
| `USED` | Always |
| `ELECTRIC` | If `isElectric=true` |
| `GAS` | If `isElectric=false` |
| `ZERO EMISSION VEHICLES (ZEVS)` | If electric |
| `LITHIUM` | If battery.type = Lithium |
| `LEAD-ACID` | If battery.type = Lead |
| `{voltage} VOLT` | If electric |
| `STREET LEGAL` | If electric + street legal |
| `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)` | If electric + street legal |
| `BATTERY ELECTRIC VEHICLES (BEVS)` | If electric + street legal |
| `LOW SPEED VEHICLES (LSVS)` | If electric + street legal |
| `MEDIUM SPEED VEHICLES (MSVS)` | If electric + street legal |
| `PERSONAL TRANSPORTATION VEHICLES (PTVS)` | If gas |
| `LOCAL USED ACTIVE INVENTORY` | If not rental |
| `LOCAL USED RENTAL INVENTORY` | If rental |
| `RENTAL` | If rental |
| `GOLF CARTS` | Always |
| `2X4` | Always |
| `TIGON DEALERSHIP` | Always |
| `TIGON GOLF CARTS {CITY} {STATE}` | Always |

**Key differences from new:**
- `USED` instead of `NEW`
- `LOCAL USED ACTIVE INVENTORY` instead of `LOCAL NEW ACTIVE INVENTORY`
- `LOCAL USED RENTAL INVENTORY` instead of `LOCAL NEW RENTAL INVENTORY`

## term_relationships - Tags

| Tag | Condition |
|---|---|
| `{MAKE(R)}` | Always |
| `{MAKE(R)} {MODEL}` | Always |
| `{MAKE(R)} {MODEL} {COLOR}` | Always |
| `{full name}` | Always |
| `{COLOR}` | Always |
| `{N} SEATS` | Always |
| `LIFTED` / `NON LIFTED` | From `isLifted` |
| `USED` | Always |
| `{CITY}`, `{CITY STATE}`, `{STATE}` | Always |
| `{CITY} GOLF CART DEALERSHIP` | Always |
| `{STATE} GOLF CART DEALERSHIP` | Always |
| `{CITY STATE} STREET LEGAL DEALERSHIP` | Always |
| `GOLF CART` | Always |
| `ELECTRIC` / `GAS` | From `isElectric` |
| `NEV`, `LSV`, `MSV`, `STREET LEGAL` | If electric + street legal |
| `PTV` | If gas |
| `TIGON`, `TIGON GOLF CARTS` | Always |

**Key difference from new:** `USED` tag instead of `NEW` tag.

## term_relationships - Product Attributes (pa_*)

| Attribute | Value | Condition |
|---|---|---|
| `pa_battery-type` | `{battery.type}` | If electric |
| `pa_battery-warranty` | `{battery.warrantyLength}` | If electric |
| `pa_brush-guard` | `YES` / `NO` | YES: Denago, Evolution. NO: all others |
| `pa_cargo-rack` | `NO` | Always |
| `pa_drivetrain` | `2X4` | Always |
| `pa_electric-bed-lift` | `NO` | Always |
| `pa_extended-top` | `YES` / `NO` | From `hasExtendedTop` |
| `pa_fender-flares` | `YES` | Always |
| `pa_led-accents` | `YES` + `LIGHT BAR` / `NO` | YES: Denago. NO: all others |
| `pa_lift-kit` | `3 INCH` / `NO` | From `isLifted` |
| `pa_location` | `{City} {State}` | Always |
| `pa_{make}-cart-colors` | `{cartColor}` | Known brands only |
| `pa_{make}-seat-colors` | `{seatColor}` | Known brands only |
| `pa_cart-color` | `{cartColor}` | Unknown brands fallback |
| `pa_seat-color` | `{seatColor}` | Unknown brands fallback |
| `pa_sound-system` | `{MAKE(R)} SOUND SYSTEM` / `YES` | Brand-specific |
| `pa_passengers` | `{N} SEATER` | Always |
| `pa_receiver-hitch` | `NO` | Always |
| `pa_return-policy` | `90 DAY` + `YES` | Always |
| `pa_rim-size` | `{tireRimSize} INCH` | From DMS |
| `pa_shipping` | `1 TO 3 DAYS LOCAL`, `3 TO 7 DAYS OTR`, `5 TO 9 DAYS NATIONAL` | Always |
| `pa_street-legal` | `YES` / `NO` | From `isStreetLegal` |
| `pa_tire-profile` | `{tireType}` | From DMS |
| `pa_vehicle-class` | Golf Cart + NEV/ZEV/LSV/MSV/PTV/UTV | Computed |
| `pa_vehicle-warranty` | `{warrantyLength}` | From DMS |
| `pa_year-of-vehicle` | `{year}` | From DMS |

Known brands with `pa_{make}-*` palettes: `bintelli, club-car, denago, epic, evolution, ezgo, icon, navitas, polaris, royal-ev, star-ev, swift, tomberlin, yamaha`

Identical rules to new vehicles. Same make-based logic for all attributes.

## term_relationships - Custom Taxonomies

| Taxonomy | Value |
|---|---|
| Location (city) | City term ID |
| Location (state) | State term ID |
| Manufacturer | Make term (aliases: Swift EV->SWIFT, Star->STAR EV) |
| Model | `{MAKE} {MODEL}` term (aliases: DS->DS ELECTRIC, etc.) |
| Sound System | `{MAKE} SOUND SYSTEM` term |
| Added Features | From `addedFeatures` flags: staticStock, brushGuard, clayBasket, fenderFlares, LEDs, lightBar, underGlow, liftKit, towHitch, stockOptions |
| Vehicle Class | Golf Cart + NEV/ZEV/LSV/MSV/PTV/UTV |
| Inventory Status | `LOCAL USED ACTIVE INVENTORY` / `LOCAL USED RENTAL INVENTORY` |
| Drivetrain | `2X4` (or from `driveTrain`) |
| Shipping Class | Term ID `665` |

**Key difference from new:** Inventory Status uses `LOCAL USED ACTIVE INVENTORY` / `LOCAL USED RENTAL INVENTORY` (new uses `LOCAL NEW ACTIVE INVENTORY` / `LOCAL NEW RENTAL INVENTORY`).

---

## Summary of All Differences: Used vs New

| Meta Key / Field | New Value | Used Value |
|---|---|---|
| `post_status` | `draft` | `publish` |
| `_sku` | VIN > Serial > Generated fallback | VIN > Serial only (no generated, rejected if neither) |
| `_stock_status` | Forced `outofstock` for templates | From DMS `isInStock` |
| `_thumbnail_id` | `null` (no images) | First sideloaded image from DMS `imageUrls` |
| `_product_image_gallery` | `null` (no images) | Remaining sideloaded images from DMS `imageUrls` |
| `_wc_gla_condition` | `new` | `used` |
| `_wc_pinterest_condition` | `new` | `used` |
| `monroney_sticker` | `[pdf-embedder url=""]` (forced empty) | `[pdf-embedder url="{url}"]` (sideloaded from DMS) |
| `_yikes_woo_products_tabs` | Manufacturer warranty tab first | `TIGON Warranty (USED GOLF CARTS)` first |
| `_wcpa_product_meta` | Single model-level add-on list | Individual add-ons from `cart.advertising.cartAddOns` |
| Categories | `NEW`, `LOCAL NEW ACTIVE INVENTORY` | `USED`, `LOCAL USED ACTIVE INVENTORY` |
| Tags | `NEW` | `USED` |
| Inventory Status taxonomy | `LOCAL NEW ACTIVE INVENTORY` | `LOCAL USED ACTIVE INVENTORY` |
