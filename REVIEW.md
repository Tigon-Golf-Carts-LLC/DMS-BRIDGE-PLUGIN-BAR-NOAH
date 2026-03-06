# TIGON DMS Connect — Change Log

**Updated:** 2026-03-06
**Plugin:** TIGON DMS Connect v2.0.0

---

## Implemented Changes

### 1. `.gitignore` Added

A comprehensive `.gitignore` has been added that excludes `node_modules/`, `.env` / `.env.*`, `*.log`, `error_log`, `debug.log`, OS files (`.DS_Store`, `Thumbs.db`), IDE/editor directories (`.idea/`, `.vscode/`), swap files, and build artifacts (`*.map`). `vendor/` is intentionally kept tracked as it contains only the Composer autoloader required for PSR-4 class loading in production.

### 2. Placeholder Image for Products with No DMS Images

Products arriving from the DMS with empty or null `imageUrls` are automatically assigned the WooCommerce placeholder image (attachment ID **70055**) as the featured image. When real images later arrive via a DMS payload update, the placeholder is automatically replaced with the actual product images.

**Files changed:**
- `dms-bridge-plugin.php` — `tigon_dms_download_and_attach_images()`: sets placeholder when no images, clears it when real images arrive
- `includes/class-dms-sync.php` — `DMS_Sync::sync_product_images()`: sets placeholder during scheduled cron sync and selective sync
- `src/Abstracts/Abstract_Cart.php` — `Abstract_Cart::fetch_images()`: sets placeholder for REST push and import controller paths
- `src/Includes/Product_Media.php` — `Product_Media::delete_product_media()`: protects shared placeholder (ID 70055) from being permanently deleted during media cleanup

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
