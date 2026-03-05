# Repository Review — DMS Bridge Plugin (Tigon DMS Connect)

**Reviewed:** 2026-02-25
**Plugin:** TIGON DMS Connect v2.0.0
**Purpose:** WordPress/WooCommerce plugin that fetches, imports, maps, and displays golf carts from the Tigon DMS (Dealer Management System).

---

## Overview

This is a WordPress plugin that bridges a DMS backend with WooCommerce. It provides:

- REST API endpoints for DMS push (used/new carts, product grids)
- Admin UI (diagnostics, import wizard, settings)
- Rich field mapping (50+ WooCommerce fields, 10+ taxonomies, 30+ attributes)
- Direct database writes for high-performance imports
- New cart template system (40+ pre-configured golf cart models)
- Featured product grid management (Elementor integration)
- Lazy WooCommerce product creation via `/dms/cart/{id}` route
- GitHub auto-updater

### File Structure

| Path | Purpose |
|------|---------|
| `dms-bridge-plugin.php` | Main plugin entry point (~2,900 lines) |
| `src/` | PSR-4 autoloaded classes (Core, Admin, Abstracts, Includes) |
| `includes/` | Legacy classes (DMS_API, DMS_Display, DMS_Sync) |
| `templates/` | Frontend templates (single cart, inventory, homepage) |
| `assets/` | CSS, JS, images |
| `mapped-logic/` | Markdown docs for manufacturer/model mapping logic |
| `tigon-dms-connect/` | **Duplicate copy** of the entire plugin (see issue #1) |
| `vendor/` | Composer autoloader |

---

## Critical Issues

### 1. Full Plugin Duplication — `tigon-dms-connect/` directory

The entire plugin is duplicated inside `tigon-dms-connect/`. Both copies have diverged slightly (9+ files differ). This adds ~1.5MB of redundant code to the repo. The root `src/`, `includes/`, `assets/`, `templates/`, and even `vendor/` are all copied. Only the root-level files are actually loaded by WordPress.

**Recommendation:** Remove the `tigon-dms-connect/` directory entirely, or if it served as an older version, archive it to a separate branch.

### 2. REST Routes Have No `permission_callback` (CRITICAL)

**File:** `src/Core.php:247-268`

All 5 `register_rest_route()` calls are missing the required `permission_callback` parameter. This means **any unauthenticated user** can:
- Create/update/delete used carts
- Push new cart updates
- Modify product showcase grids

```php
// Current (INSECURE):
register_rest_route('tigon-dms-connect', 'used', [
    'methods' => \WP_REST_Server::CREATABLE,
    'callback' => '...'
]);

// Should be:
register_rest_route('tigon-dms-connect', 'used', [
    'methods' => \WP_REST_Server::CREATABLE,
    'callback' => '...',
    'permission_callback' => function() {
        return current_user_can('manage_options');
    }
]);
```

WordPress 5.5+ logs a `_doing_it_wrong` notice when `permission_callback` is omitted. This is the single most critical security gap in the plugin.

### 3. AJAX Handlers Missing Nonce Verification and Capability Checks

**File:** `src/Admin/Ajax_Import_Controller.php`

All 7 AJAX handlers (`query_dms`, `ajax_import_convert`, `ajax_new_import_convert`, `ajax_import_create`, `ajax_import_update`, `ajax_import_delete`, `ajax_import_new`) are missing:
- `check_ajax_referer()` / nonce verification
- `current_user_can()` capability checks

They only check for the `HTTP_X_REQUESTED_WITH` header, which is trivially spoofable.

**File:** `src/Admin/Ajax_Settings_Controller.php` — `save_settings()` and `get_dms_props()` also lack nonce and capability checks.

**Contrast:** `Core::ajax_sync_mapped_inventory()` correctly implements both — use it as a pattern for the others.

### 4. SQL Injection Vulnerabilities

Multiple files use direct string concatenation in SQL queries instead of `$wpdb->prepare()`:

| File | Line(s) | Query |
|------|---------|-------|
| `src/Core.php` | 69, 81, 279-285 | `SHOW TABLES LIKE`, `SELECT ... FROM`, `RENAME TABLE` |
| `src/Admin/Ajax_Settings_Controller.php` | 25 | `SELECT * FROM $table_name` |
| `src/Admin/Database_Write_Controller.php` | 25 | `SELECT meta_id ... post_id = ' . $post_id` |
| `src/Includes/Product_Archive_Extension.php` | 20-24, 42, 106 | `SELECT list FROM` with direct concatenation |
| `src/Admin/Admin_Page.php` | 112-145 | Hardcoded `term_taxonomy_id` values in SQL |

While table names derived from `$wpdb->prefix` are generally safe, the pattern is risky and inconsistent with the rest of the codebase where `$wpdb->prepare()` is correctly used.

### 5. Unsafe `unserialize()` on User Input

**File:** `src/Admin/Ajax_Import_Controller.php:144, 192, 229`

```php
$data = stripcslashes($_REQUEST['data']);
$data = unserialize($data);
```

PHP object injection via `unserialize()` on untrusted input is a well-known critical vulnerability. Use `json_decode()` instead, or at minimum use `unserialize($data, ['allowed_classes' => false])`.

---

## High-Priority Issues

### 6. XSS — Missing Output Escaping in Admin Pages

**File:** `src/Admin/Admin_Page.php`

- Lines 255-273: HTML with unescaped SKU data and URLs output via `echo`
- Lines 259-261: Link `href` built without `esc_url()`
- Lines 588-600: Form input `placeholder` attributes with raw DB values (need `esc_attr()`)
- Lines 290-316: Raw echo of user-controlled HTML

**File:** `src/Admin/Ajax_Settings_Controller.php:136-257` — Large HTML block echoed without escaping.

### 7. Constructor Typo

**File:** `src/Core.php:8`

```php
private function __contruct()  // Missing 's' — should be __construct()
```

This is harmless because the class only uses static methods, but it's a code quality issue that could confuse future developers.

### 8. Undefined Variable Bug

**File:** `src/Admin/REST_Routes.php:150`

```php
return new \WP_Error(500, ['pid' => 0, 'error' => 'Deletion failure', $converted->get_value()]);
```

`$converted` is never defined in `delete_used_cart()`. This will cause a fatal error if the deletion failure path is hit.

### 9. Undefined Variables in `tigon_dms_create_woo_product()`

**File:** `dms-bridge-plugin.php:612-615`

```php
$slug_tpl = isset($templates['schema_slug']) && $templates['schema_slug'] !== ''
    ? $templates['schema_slug']
    : '{make}-{model}-{cartColor}-seat-{seatColor}-{city}-{state}';
$slug = tigon_dms_evaluate_template($slug_tpl, $vars, true);
```

`$templates` and `$vars` are used but **never defined** in this function. They are defined in the caller `tigon_dms_ensure_woo_product()` but not passed through. This means every new product gets the fallback slug template with empty variables, producing broken slugs.

### 10. Null Safety — `get_page_by_path()` Result

**File:** `src/Admin/Admin_Page.php:215`

```php
$extra_new_pids = array_map(function($slug) {
    return get_page_by_path($slug, OBJECT, 'product')->ID;
}, $extra_new_pids);
```

`get_page_by_path()` can return `null`, causing a "member access on null" fatal error.

### 11. Committed `node_modules` Directories

Four `node_modules/` directories are committed to Git totaling ~240KB:

- `assets/js/node_modules/` (21KB)
- `assets/js/tigon-dms/node_modules/` (99KB)
- `tigon-dms-connect/assets/js/node_modules/` (21KB)
- `tigon-dms-connect/assets/js/tigon-dms/node_modules/` (99KB)

These should be in `.gitignore`. The packages (`php-serialize`, `base64-js`, `buffer`, `ieee754`) should be managed via `package.json` and installed at build time.

### 12. Hardcoded Page/Term IDs

**File:** `src/Admin/REST_Routes.php:209-240` and `src/Admin/Admin_Page.php:118, 141`

Location page IDs (741, 59477, 59498, etc.), archive IDs, and `term_taxonomy_id` values (4549, 4553) are hardcoded. If the database is migrated, restored, or if these posts/terms are deleted, the plugin will break silently.

**Recommendation:** Store these as plugin settings or use slug-based lookups.

### 13. Disabled Caching

**File:** `includes/class-dms-api.php:51-84`

Transient caching is commented out. Every page load makes fresh API calls to the DMS backend. This could be a performance bottleneck, especially on the frontend cart display pages.

---

## Medium-Priority Issues

### 14. Monolithic Main Plugin File

`dms-bridge-plugin.php` is 2,897 lines. It contains the entire "lazy WooCommerce product creation" system — template evaluation, spec parsing, product creation/update, taxonomy mapping, image sideloading, and more. This logic should be moved into `src/` classes to match the existing architecture.

### 15. Code Duplication in REST Routes

**File:** `src/Admin/REST_Routes.php`

`push_used_cart()` (lines 26-97) and `delete_used_cart()` (lines 105-159) contain nearly identical image/monroney deletion logic. This should be extracted into a shared helper.

### 16. Code Duplication in Import Controllers

**File:** `src/Admin/Ajax_Import_Controller.php`

- `ajax_import_convert()` and `ajax_new_import_convert()` are structurally identical
- `ajax_import_create()` and `ajax_import_update()` are structurally identical

### 17. Empty Deactivation/Uninstall Hooks

**File:** `src/Core.php:506-513`

```php
public static function deactivate() {}
public static function uninstall() {}
```

The plugin creates custom database tables on activation but never cleans them up. Consider implementing `uninstall()` to drop `tigon_dms_config` and `tigon_dms_cart_lists` tables.

### 18. ~~Missing `.gitignore`~~ — RESOLVED

~~No `.gitignore` file exists.~~

**Fixed:** A comprehensive `.gitignore` has been added that excludes `node_modules/`, `.env` / `.env.*`, `*.log`, `error_log`, `debug.log`, OS files (`.DS_Store`, `Thumbs.db`), IDE/editor directories (`.idea/`, `.vscode/`), swap files, and build artifacts (`*.map`). `vendor/` is intentionally kept tracked as it contains only the Composer autoloader required for PSR-4 class loading in production.

### 19. Placeholder Image for Products with No DMS Images — IMPLEMENTED

Products arriving from the DMS with empty or null `imageUrls` previously had no featured image on the WooCommerce site. Now, when `imageUrls` is empty/null, the WooCommerce placeholder image (attachment ID **70055**) is automatically assigned as the featured image. When real images later arrive via a DMS payload update, the placeholder is automatically replaced with the actual product images.

**Files changed:**
- `dms-bridge-plugin.php` — `tigon_dms_download_and_attach_images()`: sets placeholder when no images, clears it when real images arrive
- `includes/class-dms-sync.php` — `DMS_Sync::sync_product_images()`: sets placeholder during scheduled cron sync and selective sync
- `src/Abstracts/Abstract_Cart.php` — `Abstract_Cart::fetch_images()`: sets placeholder for REST push and import controller paths
- `src/Includes/Product_Media.php` — `Product_Media::delete_product_media()`: protects shared placeholder (ID 70055) from being permanently deleted during media cleanup

**All sync paths covered:** REST API push, Sync Mapped Inventory (AJAX), Scheduled Cron Sync, Selective Sync, and Lazy WooCommerce product creation.

### 20. Product Readiness Validation (Draft Until Fully Mapped) — IMPLEMENTED

Products arriving from the DMS that cannot map to all required WooCommerce fields are now automatically set to **draft** status instead of being published with incomplete data. Once a subsequent sync provides the missing data, the product is automatically promoted to **publish**.

**Required mappings (all must be present for publish):**
- **SKU** — `vinNo`, `serialNo`, or enough data (make + model + color) for a generated fallback
- **Price** — `retailPrice` must be a positive number
- **Categories** — `cartType.make` must be non-empty
- **Location** — `cartLocation.locationId` must resolve to a known store
- **Manufacturers** — derived from `cartType.make` (must be non-empty)
- **Vehicle Class** — `cartType.model` must be non-empty
- **Inventory Status** — `isUsed` flag must be explicitly present
- **Brands** — derived from `cartType.make` (must be non-empty)

**Key behavior:** Products with all mappings but **no images** are still **published** (images are not a required mapping). Missing fields are tracked in `_dms_readiness_missing` postmeta for debugging.

**Files changed:**
- `src/Includes/Product_Readiness.php` — **NEW** central validation class with `evaluate()` method
- `src/Abstracts/Abstract_Cart.php` — calls `Product_Readiness::evaluate()` in `convert()` to set `$this->published = 'draft'` when mappings are incomplete
- `src/Admin/Database_Object.php` — added `_dms_readiness_missing` postmeta field and `dms_readiness_missing` constructor parameter
- `dms-bridge-plugin.php` — `tigon_dms_create_woo_product()` and `tigon_dms_update_woo_product()` both evaluate readiness; updates re-evaluate so draft products get promoted to publish
- `includes/class-dms-sync.php` — `detect_sold_products()` now includes draft products (not just published) so draft DMS products aren't mistakenly deleted as "sold"
- `src/Core.php` — `ajax_publish_synced_batch()` skips products with `_dms_readiness_missing` meta to avoid force-publishing incomplete products

**All sync paths covered:** REST API push, Sync Mapped Inventory (AJAX), Scheduled Cron Sync, Selective Sync, Lazy WooCommerce product creation, and Publish All batch action.

---

## Summary

| Category | Count | Severity |
|----------|-------|----------|
| Missing REST permission callbacks | 5 routes | CRITICAL |
| Missing AJAX nonce/capability checks | 9 handlers | CRITICAL |
| SQL injection (string concatenation) | 7 locations | CRITICAL |
| Unsafe unserialize | 3 locations | CRITICAL |
| XSS / missing output escaping | 5+ locations | HIGH |
| Undefined variable bug (`$templates`/`$vars`) | 1 | HIGH |
| Undefined variable bug (`$converted`) | 1 | HIGH |
| Null safety bugs | 2 | HIGH |
| Full plugin duplication | 1 directory | HIGH |
| Committed node_modules | 4 directories | MEDIUM |
| Hardcoded IDs | 10+ values | MEDIUM |
| Code duplication | 4 areas | MEDIUM |
| Monolithic entry file | 2,897 lines | MEDIUM |
| Disabled caching | 1 | MEDIUM |
| ~~Missing .gitignore~~ | ~~1~~ | ~~LOW~~ RESOLVED |
| Constructor typo | 1 | LOW |

### Recommended Priority Order

1. **Add `permission_callback` to all REST routes** — unauthenticated users can currently modify products
2. **Add nonce verification and capability checks** to all AJAX handlers
3. **Replace `unserialize()` with `json_decode()`** or restrict allowed classes
4. **Use `$wpdb->prepare()`** for all SQL queries
5. **Add output escaping** (`esc_html()`, `esc_attr()`, `esc_url()`) in admin templates
6. **Fix the undefined `$converted` variable** in `delete_used_cart()`
7. **Remove the `tigon-dms-connect/` duplicate** directory
8. ~~**Add `.gitignore`**~~ (DONE) and remove committed `node_modules/`
9. **Refactor `dms-bridge-plugin.php`** — move logic into `src/` classes
10. **Replace hardcoded IDs** with configurable settings
