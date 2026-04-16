# Global New Vehicle Logic - Complete Database_Object Mapping

> **How to edit:** Change any `Value` cell below. Manufacturer and Model files override values listed here.

Source: `Abstract_Cart.php`, `New/Cart.php`, `Database_Object.php`

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
| `post_status` | `draft` |
| `comment_status` | `open` |
| `ping_status` | `closed` |
| `menu_order` | `0` |
| `post_type` | `product` |
| `comment_count` | `0` |
| `post_author` | `3` |
| `post_name` | DMS `websiteUrl` last segment OR `{make}-{model}-{color}-seat-{seat}-{city}-{state}` |

---

## postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | VIN > Serial > Generated `{MAKE3}{MODEL3}{COLOR3}{SEAT3}{CITY3}` |
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
| `_stock_status` | `outofstock` (template/no serial) OR `instock`/`outofstock` from DMS |
| `_global_unique_id` | Auto from SKU: last 14 chars of letter-to-digit conversion, left-padded 0s |
| `_product_attributes` | Serialized `pa_*` attribute array (see Attributes section) |
| `_thumbnail_id` | `null` (new templates have no images) |
| `_product_image_gallery` | `null` (new templates have no images) |
| `_regular_price` | `cart.retailPrice` |
| `_price` | `cart.salePrice` |

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

---

## postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | Serialized `[{name, id, title, content}]` - varies by make/model (see Manufacturer/Model files) |

---

## postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | `1` |
| `_wcpa_product_meta` | Serialized - single model add-on list (see Manufacturer files for format) |

---

## postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | Same as `_global_unique_id` |
| `_wc_gla_condition` | `new` |
| `_wc_gla_brand` | `{MAKE(R)}` UPPERCASED |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_gla_pattern` | `{model}` original case |
| `_wc_gla_gender` | `unisex` |
| `_wc_gla_sizeSystem` | `US` |
| `_wc_gla_adult` | `no` |

---

## postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | `new` |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` |

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

---

## postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | `[pdf-embedder url="{url}"]` or `[pdf-embedder url=""]` for templates |
| `_monroney_sticker` | `field_66e3332abf481` |
| `_tigonwm` | `{City Short} {ST}` or `TIGON(R) RENTALS` if rental |

---

## term_relationships - Categories

| Category | Condition |
|---|---|
| `{MAKE(R)}` | Always |
| `{MAKE(R)} {MODEL}` | If exists in system |
| `{N} SEATER` | From passenger count |
| `LIFTED` | If `isLifted=true` |
| `NEW` | Always |
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
| `LOCAL NEW ACTIVE INVENTORY` | If not rental |
| `LOCAL NEW RENTAL INVENTORY` | If rental |
| `RENTAL` | If rental |
| `GOLF CARTS` | Always |
| `2X4` | Always |
| `TIGON DEALERSHIP` | Always |
| `TIGON GOLF CARTS {CITY} {STATE}` | Always |

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
| `NEW` | Always |
| `{CITY}`, `{CITY STATE}`, `{STATE}` | Always |
| `{CITY} GOLF CART DEALERSHIP` | Always |
| `{STATE} GOLF CART DEALERSHIP` | Always |
| `{CITY STATE} STREET LEGAL DEALERSHIP` | Always |
| `GOLF CART` | Always |
| `ELECTRIC` / `GAS` | From `isElectric` |
| `NEV`, `LSV`, `MSV`, `STREET LEGAL` | If electric + street legal |
| `PTV` | If gas |
| `TIGON`, `TIGON GOLF CARTS` | Always |

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
| Inventory Status | `LOCAL NEW ACTIVE INVENTORY` / `LOCAL NEW RENTAL INVENTORY` |
| Drivetrain | `2X4` (or from `driveTrain`) |
| Shipping Class | Term ID `665` |
