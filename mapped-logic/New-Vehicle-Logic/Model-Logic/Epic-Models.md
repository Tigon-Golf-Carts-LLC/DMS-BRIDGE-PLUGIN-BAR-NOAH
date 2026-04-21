# Epic Models - Complete Database_Object Mapping Per Model

Source: `Abstract_Cart.php`, `New_Cart_Converter.php`

All Epic models inherit from `../Manufacturer-Logic/Epic.md` which inherits from `../Global-New-Logic.md`.
Epic HAS dedicated color palette attributes (`pa_epic-cart-colors` / `pa_epic-seat-colors`).

**Models:** E40L, E60, E60L

---

## Epic E40L

### Converter Defaults (from New_Cart_Converter.php)

| Field | Value |
|---|---|
| `cartType.make` | `Epic` |
| `cartType.model` | `E40L` |
| `isElectric` | `true` |
| `battery.brand` | `Leoch` |
| `battery.type` | `AGM` |
| `battery.ampHours` | `210` |
| `battery.packVoltage` | `36` |
| `battery.warrantyLength` | `2` |
| `battery.isDC` | `false` |
| `engine.make` | `null` |
| `engine.horsepower` | `null` |
| `engine.stroke` | `null` |
| `title.isStreetLegal` | `true` |
| `cartAttributes.seatColor` | `Black` |
| `cartAttributes.tireRimSize` | `14` |
| `cartAttributes.tireType` | `All-Terrain` |
| `cartAttributes.hasSoundSystem` | `true` |
| `cartAttributes.isLifted` | `true` |
| `cartAttributes.hasHitch` | `false` |
| `cartAttributes.hasExtendedTop` | `true` |
| `cartAttributes.passengers` | `4 Passenger` |
| `retailPrice` | `14500` |
| `warrantyLength` | `3` |

### Available Colors

Black, Charcoal Gray, Dark Blue, Light Blue, Matte Black, Red Pearl, Silver, White Pearl

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | Existing product ID or auto-generated |
| `post_title` | `EPIC(R) E40L {Color} In {City} {State}` |
| `post_excerpt` | Auto-generated HTML short description (only on create, null on update) |
| `post_content` | Auto-generated HTML spec table + call shortcode |
| `post_status` | `draft` |
| `comment_status` | `open` |
| `ping_status` | `closed` |
| `menu_order` | `0` |
| `post_type` | `product` |
| `comment_count` | `0` |
| `post_author` | `3` |
| `post_name` | DMS `websiteUrl` last segment OR `epic-e40l-{color}-seat-black-{city}-{state}` |

### postmeta - WooCommerce

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
| `_thumbnail_id` | `null` |
| `_product_image_gallery` | `null` |
| `_regular_price` | `14500` |
| `_price` | `cart.salePrice` |

### postmeta - Yoast SEO

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | `EPIC(R) E40L {Color} In {City} {State} - Tigon Golf Carts` |
| `_yoast_wpseo_metadesc` | `EPIC E40L {COLOR} At TIGON Golf Carts in {Location}. Call Now {Phone} Get 0% Financing, and Shipping Options Today!` |
| `_yoast_wpseo_primary_product_cat` | Term ID for `EPIC(R)` category |
| `_yoast_wpseo_primary_location` | Term ID for location city |
| `_yoast_wpseo_primary_models` | `null` |
| `_yoast_wpseo_primary_added-features` | `null` |
| `_yoast_wpseo_is_cornerstone` | `1` |
| `_yoast_wpseo_focus_kw` | `EPIC(R) E40L {Color} In {City} {State}` |
| `_yoast_wpseo_focus_keywords` | `EPIC(R) E40L {Color} In {City} {State}` |
| `_yoast_wpseo_bctitle` | `EPIC(R) E40L {Color} In {City} {State}` |
| `_yoast_wpseo_opengraph-title` | `EPIC(R) E40L {Color} In {City} {State}` |
| `_yoast_wpseo_opengraph-description` | Same as `_yoast_wpseo_metadesc` |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` |
| `_yoast_wpseo_opengraph-image` | Featured image URL via `wp_get_attachment_image_url` |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` |
| `_yoast_wpseo_twitter-image` | Featured image URL |

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | No Epic-specific tabs. No tabs are injected for Epic models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | `1` |
| `_wcpa_product_meta` | `EPIC(R) E40L Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | Same as `_global_unique_id` |
| `_wc_gla_condition` | `new` |
| `_wc_gla_brand` | `EPIC(R)` |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_gla_pattern` | `E40L` |
| `_wc_gla_gender` | `unisex` |
| `_wc_gla_sizeSystem` | `US` |
| `_wc_gla_adult` | `no` |

### postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | `new` |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `EPIC(R)` |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `E40L` |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` |
| `_wc_facebook_product_image_source` | `product` |
| `_wc_facebook_sync_enabled` | `yes` |
| `_wc_fb_visibility` | `yes` |

### postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | `[pdf-embedder url="{url}"]` or `[pdf-embedder url=""]` for templates |
| `_monroney_sticker` | `field_66e3332abf481` |
| `_tigonwm` | `{City Short} {ST}` or `TIGON(R) RENTALS` if rental |

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | `AGM` |
| `pa_battery-warranty` | `2` |
| `pa_brush-guard` | `NO` |
| `pa_cargo-rack` | `NO` |
| `pa_drivetrain` | `2X4` |
| `pa_electric-bed-lift` | `NO` |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | `YES` |
| `pa_led-accents` | `NO` |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | `{City} {State}` |
| `pa_epic-cart-colors` | `{CART_COLOR}` UPPERCASED |
| `pa_epic-seat-colors` | `BLACK` |
| `pa_sound-system` | `EPIC(R) SOUND SYSTEM` |
| `pa_passengers` | `4 SEATER` |
| `pa_receiver-hitch` | `NO` |
| `pa_return-policy` | `90 DAY` + `YES` |
| `pa_rim-size` | `14 INCH` |
| `pa_shipping` | `1 TO 3 DAYS LOCAL`, `3 TO 7 DAYS OTR`, `5 TO 9 DAYS NATIONAL` |
| `pa_street-legal` | `YES` |
| `pa_tire-profile` | `ALL TERRAIN` |
| `pa_vehicle-class` | `GOLF CART`, `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)`, `ZERO EMISSION VEHICLES (ZEVS)`, `LOW SPEED VEHICLE (LSVS)`, `MEDIUM SPEED VEHICLE (MSVS)`, `PERSONAL TRANSPORTATION VEHICLES (PTVS)` |
| `pa_vehicle-warranty` | `3` |
| `pa_year-of-vehicle` | `{year}` from DMS |

### term_relationships - Categories

| Category | Applied? |
|---|---|
| `EPIC(R)` | YES |
| `EPIC(R) E40L` | YES |
| `4 SEATER` | YES |
| `LIFTED` | YES |
| `NEW` | YES |
| `ELECTRIC` | YES |
| `GAS` | NO |
| `ZERO EMISSION VEHICLES (ZEVS)` | YES |
| `LITHIUM` | NO (AGM) |
| `LEAD-ACID` | NO (AGM) |
| `36 VOLT` | YES |
| `STREET LEGAL` | YES |
| `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)` | YES |
| `BATTERY ELECTRIC VEHICLES (BEVS)` | YES |
| `LOW SPEED VEHICLES (LSVS)` | YES |
| `MEDIUM SPEED VEHICLES (MSVS)` | YES |
| `PERSONAL TRANSPORTATION VEHICLES (PTVS)` | NO (electric) |
| `LOCAL NEW ACTIVE INVENTORY` | YES (if not rental) |
| `LOCAL NEW RENTAL INVENTORY` | Per cart |
| `RENTAL` | Per cart |
| `GOLF CARTS` | YES |
| `2X4` | YES |
| `TIGON DEALERSHIP` | YES |
| `TIGON GOLF CARTS {CITY} {STATE}` | YES |

### term_relationships - Tags

| Tag | Applied? |
|---|---|
| `EPIC(R)` | YES |
| `EPIC(R) E40L` | YES |
| `EPIC(R) E40L {COLOR}` | YES |
| `{full name}` | YES |
| `{COLOR}` | YES |
| `4 SEATS` | YES |
| `LIFTED` | YES |
| `NON LIFTED` | NO |
| `NEW` | YES |
| `{CITY}` | YES |
| `{CITY STATE}` | YES |
| `{STATE}` | YES |
| `{CITY} GOLF CART DEALERSHIP` | YES |
| `{STATE} GOLF CART DEALERSHIP` | YES |
| `{CITY STATE} STREET LEGAL DEALERSHIP` | YES |
| `GOLF CART` | YES |
| `ELECTRIC` | YES |
| `GAS` | NO |
| `NEV` | YES |
| `LSV` | YES |
| `MSV` | YES |
| `STREET LEGAL` | YES |
| `PTV` | NO (electric) |
| `TIGON` | YES |
| `TIGON GOLF CARTS` | YES |

### term_relationships - Custom Taxonomies

| Taxonomy | Value |
|---|---|
| Location (city) | City term ID |
| Location (state) | State term ID |
| Manufacturer | `EPIC(R)` |
| Model | `EPIC(R) E40L` |
| Sound System | `EPIC(R) SOUND SYSTEM` |
| Added Features | From `addedFeatures` flags |
| Vehicle Class | `GOLF CART`, `NEVS`, `ZEVS`, `LSVS`, `MSVS`, `PTVS` |
| Inventory Status | `LOCAL NEW ACTIVE INVENTORY` |
| Drivetrain | `2X4` |
| Shipping Class | Term ID `665` |

### post_content Description Features

| Feature | Included? |
|---|---|
| `3 Inch Lift Kit` | YES (`isLifted` = true) |
| `EPIC(R) Sound System` | YES (`hasSoundSystem` = true) |

---

## Epic E60

### Converter Defaults (from New_Cart_Converter.php)

| Field | Value |
|---|---|
| `cartType.make` | `Epic` |
| `cartType.model` | `E60` |
| `isElectric` | `true` |
| `battery.brand` | `Leoch` |
| `battery.type` | `AGM` |
| `battery.ampHours` | `210` |
| `battery.packVoltage` | `36` |
| `battery.warrantyLength` | `2` |
| `battery.isDC` | `false` |
| `engine.make` | `null` |
| `engine.horsepower` | `null` |
| `engine.stroke` | `null` |
| `title.isStreetLegal` | `true` |
| `cartAttributes.seatColor` | `Black` |
| `cartAttributes.tireRimSize` | `12` |
| `cartAttributes.tireType` | `Street Tire` |
| `cartAttributes.hasSoundSystem` | `false` |
| `cartAttributes.isLifted` | `false` |
| `cartAttributes.hasHitch` | `false` |
| `cartAttributes.hasExtendedTop` | `true` |
| `cartAttributes.passengers` | `6 Passenger` |
| `retailPrice` | `14500` |
| `warrantyLength` | `3` |

### Available Colors

Black, Charcoal Gray, Dark Blue, Light Blue, Matte Black, Red Pearl, Silver, White Pearl

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | Existing product ID or auto-generated |
| `post_title` | `EPIC(R) E60 {Color} In {City} {State}` |
| `post_excerpt` | Auto-generated HTML short description (only on create, null on update) |
| `post_content` | Auto-generated HTML spec table + call shortcode |
| `post_status` | `draft` |
| `comment_status` | `open` |
| `ping_status` | `closed` |
| `menu_order` | `0` |
| `post_type` | `product` |
| `comment_count` | `0` |
| `post_author` | `3` |
| `post_name` | DMS `websiteUrl` last segment OR `epic-e60-{color}-seat-black-{city}-{state}` |

### postmeta - WooCommerce

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
| `_thumbnail_id` | `null` |
| `_product_image_gallery` | `null` |
| `_regular_price` | `14500` |
| `_price` | `cart.salePrice` |

### postmeta - Yoast SEO

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | `EPIC(R) E60 {Color} In {City} {State} - Tigon Golf Carts` |
| `_yoast_wpseo_metadesc` | `EPIC E60 {COLOR} At TIGON Golf Carts in {Location}. Call Now {Phone} Get 0% Financing, and Shipping Options Today!` |
| `_yoast_wpseo_primary_product_cat` | Term ID for `EPIC(R)` category |
| `_yoast_wpseo_primary_location` | Term ID for location city |
| `_yoast_wpseo_primary_models` | `null` |
| `_yoast_wpseo_primary_added-features` | `null` |
| `_yoast_wpseo_is_cornerstone` | `1` |
| `_yoast_wpseo_focus_kw` | `EPIC(R) E60 {Color} In {City} {State}` |
| `_yoast_wpseo_focus_keywords` | `EPIC(R) E60 {Color} In {City} {State}` |
| `_yoast_wpseo_bctitle` | `EPIC(R) E60 {Color} In {City} {State}` |
| `_yoast_wpseo_opengraph-title` | `EPIC(R) E60 {Color} In {City} {State}` |
| `_yoast_wpseo_opengraph-description` | Same as `_yoast_wpseo_metadesc` |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` |
| `_yoast_wpseo_opengraph-image` | Featured image URL via `wp_get_attachment_image_url` |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` |
| `_yoast_wpseo_twitter-image` | Featured image URL |

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | No Epic-specific tabs. No tabs are injected for Epic models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | `1` |
| `_wcpa_product_meta` | `EPIC(R) E60 Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | Same as `_global_unique_id` |
| `_wc_gla_condition` | `new` |
| `_wc_gla_brand` | `EPIC(R)` |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_gla_pattern` | `E60` |
| `_wc_gla_gender` | `unisex` |
| `_wc_gla_sizeSystem` | `US` |
| `_wc_gla_adult` | `no` |

### postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | `new` |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `EPIC(R)` |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `E60` |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` |
| `_wc_facebook_product_image_source` | `product` |
| `_wc_facebook_sync_enabled` | `yes` |
| `_wc_fb_visibility` | `yes` |

### postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | `[pdf-embedder url="{url}"]` or `[pdf-embedder url=""]` for templates |
| `_monroney_sticker` | `field_66e3332abf481` |
| `_tigonwm` | `{City Short} {ST}` or `TIGON(R) RENTALS` if rental |

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | `AGM` |
| `pa_battery-warranty` | `2` |
| `pa_brush-guard` | `NO` |
| `pa_cargo-rack` | `NO` |
| `pa_drivetrain` | `2X4` |
| `pa_electric-bed-lift` | `NO` |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | `YES` |
| `pa_led-accents` | `NO` |
| `pa_lift-kit` | `NO` |
| `pa_location` | `{City} {State}` |
| `pa_epic-cart-colors` | `{CART_COLOR}` UPPERCASED |
| `pa_epic-seat-colors` | `BLACK` |
| `pa_sound-system` | `EPIC(R) SOUND SYSTEM` |
| `pa_passengers` | `6 SEATER` |
| `pa_receiver-hitch` | `NO` |
| `pa_return-policy` | `90 DAY` + `YES` |
| `pa_rim-size` | `12 INCH` |
| `pa_shipping` | `1 TO 3 DAYS LOCAL`, `3 TO 7 DAYS OTR`, `5 TO 9 DAYS NATIONAL` |
| `pa_street-legal` | `YES` |
| `pa_tire-profile` | `STREET TIRE` |
| `pa_vehicle-class` | `GOLF CART`, `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)`, `ZERO EMISSION VEHICLES (ZEVS)`, `LOW SPEED VEHICLE (LSVS)`, `MEDIUM SPEED VEHICLE (MSVS)`, `PERSONAL TRANSPORTATION VEHICLES (PTVS)` |
| `pa_vehicle-warranty` | `3` |
| `pa_year-of-vehicle` | `{year}` from DMS |

### term_relationships - Categories

| Category | Applied? |
|---|---|
| `EPIC(R)` | YES |
| `EPIC(R) E60` | YES |
| `6 SEATER` | YES |
| `LIFTED` | NO |
| `NEW` | YES |
| `ELECTRIC` | YES |
| `GAS` | NO |
| `ZERO EMISSION VEHICLES (ZEVS)` | YES |
| `LITHIUM` | NO (AGM) |
| `LEAD-ACID` | NO (AGM) |
| `36 VOLT` | YES |
| `STREET LEGAL` | YES |
| `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)` | YES |
| `BATTERY ELECTRIC VEHICLES (BEVS)` | YES |
| `LOW SPEED VEHICLES (LSVS)` | YES |
| `MEDIUM SPEED VEHICLES (MSVS)` | YES |
| `PERSONAL TRANSPORTATION VEHICLES (PTVS)` | NO (electric) |
| `LOCAL NEW ACTIVE INVENTORY` | YES (if not rental) |
| `LOCAL NEW RENTAL INVENTORY` | Per cart |
| `RENTAL` | Per cart |
| `GOLF CARTS` | YES |
| `2X4` | YES |
| `TIGON DEALERSHIP` | YES |
| `TIGON GOLF CARTS {CITY} {STATE}` | YES |

### term_relationships - Tags

| Tag | Applied? |
|---|---|
| `EPIC(R)` | YES |
| `EPIC(R) E60` | YES |
| `EPIC(R) E60 {COLOR}` | YES |
| `{full name}` | YES |
| `{COLOR}` | YES |
| `6 SEATS` | YES |
| `LIFTED` | NO |
| `NON LIFTED` | YES |
| `NEW` | YES |
| `{CITY}` | YES |
| `{CITY STATE}` | YES |
| `{STATE}` | YES |
| `{CITY} GOLF CART DEALERSHIP` | YES |
| `{STATE} GOLF CART DEALERSHIP` | YES |
| `{CITY STATE} STREET LEGAL DEALERSHIP` | YES |
| `GOLF CART` | YES |
| `ELECTRIC` | YES |
| `GAS` | NO |
| `NEV` | YES |
| `LSV` | YES |
| `MSV` | YES |
| `STREET LEGAL` | YES |
| `PTV` | NO (electric) |
| `TIGON` | YES |
| `TIGON GOLF CARTS` | YES |

### term_relationships - Custom Taxonomies

| Taxonomy | Value |
|---|---|
| Location (city) | City term ID |
| Location (state) | State term ID |
| Manufacturer | `EPIC(R)` |
| Model | `EPIC(R) E60` |
| Sound System | `EPIC(R) SOUND SYSTEM` |
| Added Features | From `addedFeatures` flags |
| Vehicle Class | `GOLF CART`, `NEVS`, `ZEVS`, `LSVS`, `MSVS`, `PTVS` |
| Inventory Status | `LOCAL NEW ACTIVE INVENTORY` |
| Drivetrain | `2X4` |
| Shipping Class | Term ID `665` |

### post_content Description Features

| Feature | Included? |
|---|---|
| `3 Inch Lift Kit` | NO (`isLifted` = false) |
| `EPIC(R) Sound System` | NO (`hasSoundSystem` = false) |

---

## Epic E60L

### Converter Defaults (from New_Cart_Converter.php)

| Field | Value |
|---|---|
| `cartType.make` | `Epic` |
| `cartType.model` | `E60L` |
| `isElectric` | `true` |
| `battery.brand` | `Leoch` |
| `battery.type` | `AGM` |
| `battery.ampHours` | `210` |
| `battery.packVoltage` | `36` |
| `battery.warrantyLength` | `2` |
| `battery.isDC` | `false` |
| `engine.make` | `null` |
| `engine.horsepower` | `null` |
| `engine.stroke` | `null` |
| `title.isStreetLegal` | `true` |
| `cartAttributes.seatColor` | `Black` |
| `cartAttributes.tireRimSize` | `14` |
| `cartAttributes.tireType` | `All-Terrain` |
| `cartAttributes.hasSoundSystem` | `false` |
| `cartAttributes.isLifted` | `true` |
| `cartAttributes.hasHitch` | `false` |
| `cartAttributes.hasExtendedTop` | `true` |
| `cartAttributes.passengers` | `6 Passenger` |
| `retailPrice` | `15500` |
| `warrantyLength` | `3` |

### Available Colors

Black, Charcoal Gray, Dark Blue, Light Blue, Matte Black, Red Pearl, Silver, White Pearl

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | Existing product ID or auto-generated |
| `post_title` | `EPIC(R) E60L {Color} In {City} {State}` |
| `post_excerpt` | Auto-generated HTML short description (only on create, null on update) |
| `post_content` | Auto-generated HTML spec table + call shortcode |
| `post_status` | `draft` |
| `comment_status` | `open` |
| `ping_status` | `closed` |
| `menu_order` | `0` |
| `post_type` | `product` |
| `comment_count` | `0` |
| `post_author` | `3` |
| `post_name` | DMS `websiteUrl` last segment OR `epic-e60l-{color}-seat-black-{city}-{state}` |

### postmeta - WooCommerce

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
| `_thumbnail_id` | `null` |
| `_product_image_gallery` | `null` |
| `_regular_price` | `15500` |
| `_price` | `cart.salePrice` |

### postmeta - Yoast SEO

| Meta Key | Value |
|---|---|
| `_yoast_wpseo_title` | `EPIC(R) E60L {Color} In {City} {State} - Tigon Golf Carts` |
| `_yoast_wpseo_metadesc` | `EPIC E60L {COLOR} At TIGON Golf Carts in {Location}. Call Now {Phone} Get 0% Financing, and Shipping Options Today!` |
| `_yoast_wpseo_primary_product_cat` | Term ID for `EPIC(R)` category |
| `_yoast_wpseo_primary_location` | Term ID for location city |
| `_yoast_wpseo_primary_models` | `null` |
| `_yoast_wpseo_primary_added-features` | `null` |
| `_yoast_wpseo_is_cornerstone` | `1` |
| `_yoast_wpseo_focus_kw` | `EPIC(R) E60L {Color} In {City} {State}` |
| `_yoast_wpseo_focus_keywords` | `EPIC(R) E60L {Color} In {City} {State}` |
| `_yoast_wpseo_bctitle` | `EPIC(R) E60L {Color} In {City} {State}` |
| `_yoast_wpseo_opengraph-title` | `EPIC(R) E60L {Color} In {City} {State}` |
| `_yoast_wpseo_opengraph-description` | Same as `_yoast_wpseo_metadesc` |
| `_yoast_wpseo_opengraph-image-id` | Same as `_thumbnail_id` |
| `_yoast_wpseo_opengraph-image` | Featured image URL via `wp_get_attachment_image_url` |
| `_yoast_wpseo_twitter-image-id` | Same as `_thumbnail_id` |
| `_yoast_wpseo_twitter-image` | Featured image URL |

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | No Epic-specific tabs. No tabs are injected for Epic models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | `1` |
| `_wcpa_product_meta` | `EPIC(R) E60L Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_mpn` | Same as `_global_unique_id` |
| `_wc_gla_condition` | `new` |
| `_wc_gla_brand` | `EPIC(R)` |
| `_wc_gla_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_gla_pattern` | `E60L` |
| `_wc_gla_gender` | `unisex` |
| `_wc_gla_sizeSystem` | `US` |
| `_wc_gla_adult` | `no` |

### postmeta - Pinterest for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_pinterest_condition` | `new` |
| `_wc_pinterest_google_product_category` | `Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts` |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `EPIC(R)` |
| `_wc_facebook_enhanced_catalog_attributes_color` | `{CART_COLOR}` UPPERCASED |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `E60L` |
| `_wc_facebook_enhanced_catalog_attributes_gender` | `unisex` |
| `_wc_facebook_enhanced_catalog_attributes_age_group` | `all ages` |
| `_wc_facebook_product_image_source` | `product` |
| `_wc_facebook_sync_enabled` | `yes` |
| `_wc_fb_visibility` | `yes` |

### postmeta - Tigon Specific

| Meta Key | Value |
|---|---|
| `monroney_sticker` | `[pdf-embedder url="{url}"]` or `[pdf-embedder url=""]` for templates |
| `_monroney_sticker` | `field_66e3332abf481` |
| `_tigonwm` | `{City Short} {ST}` or `TIGON(R) RENTALS` if rental |

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | `AGM` |
| `pa_battery-warranty` | `2` |
| `pa_brush-guard` | `NO` |
| `pa_cargo-rack` | `NO` |
| `pa_drivetrain` | `2X4` |
| `pa_electric-bed-lift` | `NO` |
| `pa_extended-top` | `YES` |
| `pa_fender-flares` | `YES` |
| `pa_led-accents` | `NO` |
| `pa_lift-kit` | `3 INCH` |
| `pa_location` | `{City} {State}` |
| `pa_epic-cart-colors` | `{CART_COLOR}` UPPERCASED |
| `pa_epic-seat-colors` | `BLACK` |
| `pa_sound-system` | `EPIC(R) SOUND SYSTEM` |
| `pa_passengers` | `6 SEATER` |
| `pa_receiver-hitch` | `NO` |
| `pa_return-policy` | `90 DAY` + `YES` |
| `pa_rim-size` | `14 INCH` |
| `pa_shipping` | `1 TO 3 DAYS LOCAL`, `3 TO 7 DAYS OTR`, `5 TO 9 DAYS NATIONAL` |
| `pa_street-legal` | `YES` |
| `pa_tire-profile` | `ALL TERRAIN` |
| `pa_vehicle-class` | `GOLF CART`, `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)`, `ZERO EMISSION VEHICLES (ZEVS)`, `LOW SPEED VEHICLE (LSVS)`, `MEDIUM SPEED VEHICLE (MSVS)`, `PERSONAL TRANSPORTATION VEHICLES (PTVS)` |
| `pa_vehicle-warranty` | `3` |
| `pa_year-of-vehicle` | `{year}` from DMS |

### term_relationships - Categories

| Category | Applied? |
|---|---|
| `EPIC(R)` | YES |
| `EPIC(R) E60L` | YES |
| `6 SEATER` | YES |
| `LIFTED` | YES |
| `NEW` | YES |
| `ELECTRIC` | YES |
| `GAS` | NO |
| `ZERO EMISSION VEHICLES (ZEVS)` | YES |
| `LITHIUM` | NO (AGM) |
| `LEAD-ACID` | NO (AGM) |
| `36 VOLT` | YES |
| `STREET LEGAL` | YES |
| `NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)` | YES |
| `BATTERY ELECTRIC VEHICLES (BEVS)` | YES |
| `LOW SPEED VEHICLES (LSVS)` | YES |
| `MEDIUM SPEED VEHICLES (MSVS)` | YES |
| `PERSONAL TRANSPORTATION VEHICLES (PTVS)` | NO (electric) |
| `LOCAL NEW ACTIVE INVENTORY` | YES (if not rental) |
| `LOCAL NEW RENTAL INVENTORY` | Per cart |
| `RENTAL` | Per cart |
| `GOLF CARTS` | YES |
| `2X4` | YES |
| `TIGON DEALERSHIP` | YES |
| `TIGON GOLF CARTS {CITY} {STATE}` | YES |

### term_relationships - Tags

| Tag | Applied? |
|---|---|
| `EPIC(R)` | YES |
| `EPIC(R) E60L` | YES |
| `EPIC(R) E60L {COLOR}` | YES |
| `{full name}` | YES |
| `{COLOR}` | YES |
| `6 SEATS` | YES |
| `LIFTED` | YES |
| `NON LIFTED` | NO |
| `NEW` | YES |
| `{CITY}` | YES |
| `{CITY STATE}` | YES |
| `{STATE}` | YES |
| `{CITY} GOLF CART DEALERSHIP` | YES |
| `{STATE} GOLF CART DEALERSHIP` | YES |
| `{CITY STATE} STREET LEGAL DEALERSHIP` | YES |
| `GOLF CART` | YES |
| `ELECTRIC` | YES |
| `GAS` | NO |
| `NEV` | YES |
| `LSV` | YES |
| `MSV` | YES |
| `STREET LEGAL` | YES |
| `PTV` | NO (electric) |
| `TIGON` | YES |
| `TIGON GOLF CARTS` | YES |

### term_relationships - Custom Taxonomies

| Taxonomy | Value |
|---|---|
| Location (city) | City term ID |
| Location (state) | State term ID |
| Manufacturer | `EPIC(R)` |
| Model | `EPIC(R) E60L` |
| Sound System | `EPIC(R) SOUND SYSTEM` |
| Added Features | From `addedFeatures` flags |
| Vehicle Class | `GOLF CART`, `NEVS`, `ZEVS`, `LSVS`, `MSVS`, `PTVS` |
| Inventory Status | `LOCAL NEW ACTIVE INVENTORY` |
| Drivetrain | `2X4` |
| Shipping Class | Term ID `665` |

### post_content Description Features

| Feature | Included? |
|---|---|
| `3 Inch Lift Kit` | YES (`isLifted` = true) |
| `EPIC(R) Sound System` | NO (`hasSoundSystem` = false) |
