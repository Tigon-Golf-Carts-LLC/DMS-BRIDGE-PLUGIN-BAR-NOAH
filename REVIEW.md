# TIGON DMS Connect — Change Log

**Updated:** 2026-03-06
**Plugin:** TIGON DMS Connect v2.0.0

---

## Implemented Changes

### 1. `.gitignore` Added

A comprehensive `.gitignore` has been added that excludes `node_modules/`, `.env` / `.env.*`, `*.log`, `error_log`, `debug.log`, OS files (`.DS_Store`, `Thumbs.db`), IDE/editor directories (`.idea/`, `.vscode/`), swap files, and build artifacts (`*.map`). `vendor/` is intentionally kept tracked as it contains only the Composer autoloader required for PSR-4 class loading in production.

### 2. Placeholder Image for Products with No DMS Images

Products arriving from the DMS with empty or null `imageUrls` are automatically assigned the coming-soon placeholder image (attachment ID **204304** — `coming-soon.jpg`) as the featured image. When real images later arrive via a DMS payload update, the placeholder is automatically replaced with the actual product images.

**Files changed:**
- `dms-bridge-plugin.php` — `tigon_dms_download_and_attach_images()`: sets placeholder when no images, clears it when real images arrive
- `includes/class-dms-sync.php` — `DMS_Sync::sync_product_images()`: sets placeholder during scheduled cron sync and selective sync
- `src/Abstracts/Abstract_Cart.php` — `Abstract_Cart::fetch_images()`: sets placeholder for REST push and import controller paths
- `src/Includes/Product_Media.php` — `Product_Media::delete_product_media()`: protects shared placeholder (ID 204304) from being permanently deleted during media cleanup

**All sync paths covered:** REST API push, Sync Mapped Inventory (AJAX), Scheduled Cron Sync, Selective Sync, and Lazy WooCommerce product creation.

### 3. Product Readiness Validation (Draft Until Fully Mapped)

Products arriving from the DMS that cannot map to all required WooCommerce fields are automatically set to **draft** status instead of being published with incomplete data. Once a subsequent sync provides the missing data, the product is automatically promoted to **publish**.

**Required mappings (all must be present for publish):**
- **SKU** — `vinNo`, `serialNo`, or enough data (make + model + color) for a generated fallback
- **Price** — `retailPrice` must be a positive number
- **Categories** — `cartType.make` must be non-empty
- **Location** — `cartLocation.locationId` must resolve to a known store
- **Manufacturers** — derived from `cartType.make` (must be non-empty)
- **Vehicle Class** — `cartType.model` must be non-empty
- **Inventory Status** — `isUsed` flag must be explicitly present
- **Brands** — derived from `cartType.make` (must be non-empty)

Products with all mappings but **no images** are still **published** (images are not a required mapping). Missing fields are tracked in `_dms_readiness_missing` postmeta for debugging.

**Files changed:**
- `src/Includes/Product_Readiness.php` — central validation class with `evaluate()` method
- `src/Abstracts/Abstract_Cart.php` — calls `Product_Readiness::evaluate()` in `convert()` to set draft when mappings are incomplete
- `src/Admin/Database_Object.php` — added `_dms_readiness_missing` postmeta field
- `dms-bridge-plugin.php` — `tigon_dms_create_woo_product()` and `tigon_dms_update_woo_product()` both evaluate readiness
- `includes/class-dms-sync.php` — `detect_sold_products()` now includes draft products so draft DMS products aren't mistakenly deleted as "sold"
- `src/Core.php` — `ajax_publish_synced_batch()` skips products with `_dms_readiness_missing` meta

### 4. Fix Product Images Disappearing After Page Load

The JavaScript injection script (`dms-woo-inject.js`) was hiding locally-downloaded WooCommerce images and trying to replace them with S3 URLs. Now when WooCommerce already has local images attached to the product (featured + gallery), the JS skips injection entirely and lets the native WooCommerce gallery render.

**Files changed:**
- `assets/js/dms-woo-inject.js` — added `hasLocalImages` check to skip S3 injection when local images exist
- `dms-bridge-plugin.php` — `tigon_dms_enqueue_woo_inject_script()` now passes `localImageUrls` array to JavaScript

### 5. WooCommerce Placeholder Image Fallback for Display Pages

When DMS has no images for a product, the display layer now falls back to the WooCommerce placeholder image configured in CMS settings (via `wc_placeholder_img_src()`), with the coming-soon image as a last resort only if no WooCommerce placeholder is set.

**Image priority chain:**
1. DMS/DBS `imageUrls` (S3 images from the API payload)
2. Default cart images for new carts (location-specific, then national)
3. WooCommerce placeholder image (configured in WooCommerce > Settings)
4. Coming-soon image (hardcoded fallback, only used if no WC placeholder is set)

**Files changed:**
- `includes/class-dms-display.php` — cart listing cards and single cart detail page now use `wc_placeholder_img_src()` instead of hardcoded coming-soon URL
- `includes/class-dms-api.php` — `resolve_cart_image_urls()` tries WooCommerce placeholder before falling back to coming-soon image

### 6. Brand Taxonomy Mapping (product_brand)

Brands now map from the DMS `cartType.make` field to the `product_brand` taxonomy, matching the manufacturer. If a brand term doesn't exist in WordPress, it is auto-created.

**Files changed:**
- `src/Admin/Attributes.php` — added `brands_taxonomy` property and `ai_get_brands()` loader
- `src/Abstracts/Abstract_Cart.php` — `attach_taxonomies()` maps brand from resolved manufacturer name with auto-create
- `dms-bridge-plugin.php` — brand assignment in rich mapping with auto-create, Yoast primary brand meta

**All sync paths covered:** REST API push, AJAX import, Scheduled Cron Sync, Selective Sync, and Lazy WooCommerce product creation.

---

## CDN & Caching Configuration

### CDN Exclusions (files that should NOT be served via CDN)

```
/wp-json/tigon-dms-connect/(.*)
/wp-admin/admin-ajax.php
/dms/cart/(.*)
/wp-cron.php
```

### Never Cache URLs

```
/dms/cart/(.*)
/wp-json/tigon-dms-connect/(.*)
/wp-admin/admin-ajax.php
/wp-cron.php
/inventory/(.*)
```

### Always Purge URLs

Purged from cache whenever any post or page is updated:

```
/inventory/(.*)
/golf-cart-inventory/(.*)
/hatfield-pa/(.*)
/ocean-view-nj/(.*)
/long-pond-pa/(.*)
/dover-de/(.*)
/scranton-pa/(.*)
/raleigh-nc/(.*)
/south-bend-in/(.*)
/gloucester-va/(.*)
/bayville-nj/(.*)
/waretown-nj/(.*)
/orangeburg-sc/(.*)
/swanton-oh/(.*)
/lecanto-fl/(.*)
/golf-cart-services/(.*)
/manufacturers/(.*)
/brands/(.*)
/product/(.*)
/product-category/(.*)
/shop/(.*)
```

### Cache Query Strings

```
pa_location
pa_model
pa_battery-type
pa_drivetrain
pa_brush-guard
pa_cargo-rack
pa_electric-bed-lift
pa_electric-range
pa_extended-top
pa_fender-flares
pa_led-accents
pa_lift-kit
pa_mileage
pa_seats
pa_speed
pa_street-legal
pa_fuel-type
pa_gifted
min_price
max_price
orderby
product_cat
product_tag
product_brand
manufacturers
models
```

### Never Cache Cookies

```
wordpress_logged_in_
wordpress_sec_
wp-settings-
wp-postpass_
woocommerce_cart_hash
woocommerce_items_in_cart
wp_woocommerce_session_
woocommerce_recently_viewed
comment_author_
tk_ai
```

### Never Cache User Agents

```
(.*)wp-cron(.*)
(.*)WordPress(.*)
(.*)DMS(.*)
(.*)Jetstress(.*)
(.*)check_http(.*)
(.*)nagios(.*)
(.*)Pingdom(.*)
(.*)UptimeRobot(.*)
(.*)StatusCake(.*)
(.*)GTmetrix(.*)
(.*)Google-Site-Verification(.*)
```

### JavaScript & CSS Optimization

| Setting | Safe? | Notes |
|---------|-------|-------|
| Minify CSS | Yes | Safe — removes whitespace/comments |
| Optimize CSS delivery (Remove Unused CSS) | Careful | Can break Elementor styling. Use "Load CSS Asynchronously" instead |
| Minify JavaScript | Yes | Safe — removes whitespace/comments |
| Combine JavaScript | **No** | Breaks plugin — scripts depend on specific load order and `wp_localize_script` data |
| Load JavaScript Deferred | Careful | Test inventory pages + admin sync. Exclude: `/wp-content/plugins/tigon-dms-connect/assets/js/(.*)` |
| Delay JavaScript | **No** | Breaks `dms-woo-inject.js` — images won't load until user interaction |

#### Excluded JavaScript Files

Specify URLs of JavaScript files to be excluded from minification and concatenation (one per line).

```
/wp-content/plugins/tigon-dms-connect/assets/js/(.*).js
```

#### Excluded CSS Files

Specify URLs of CSS files to be excluded from minification (one per line).

```
/wp-content/plugins/tigon-dms-connect/assets/css/(.*).css
```
