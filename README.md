# TIGON DMS Connect

**The enterprise WordPress plugin that bridges your Tigon Dealer Management System with WooCommerce** — automatically syncing golf cart inventory, images, pricing, attributes, and product data in real time.

**Author:** Noah Jaslow &amp; [Jaslow Digital](https://jaslowdigital.com)
**Version:** 2.0.0
**Requires:** WordPress 6.0+ &middot; WooCommerce 8.0+ &middot; PHP 8.1+

---

## What It Does

TIGON DMS Connect takes your entire golf cart inventory from the Tigon DMS and publishes it as fully-featured WooCommerce products — complete with images, pricing, specifications, categories, and SEO-ready attributes. It supports both **push** (DMS sends changes in real time) and **pull** (WordPress fetches inventory on demand) sync models.

Every product gets:

- **Title &amp; slug** generated from configurable schema templates
- **Images** downloaded from S3 and attached as WooCommerce featured + gallery images
- **Pricing** (retail, sale) synced to WooCommerce price fields
- **30+ product attributes** (make, model, color, drivetrain, passengers, battery, etc.)
- **Categories** auto-assigned (make, model, new/used, lifted, seating, location, etc.)
- **Google, Facebook &amp; Pinterest** product feed metadata
- **SKU** from VIN, serial number, or auto-generated fallback

---

## Key Features

### Multi-Path Inventory Sync

| Sync Method | Description |
|-------------|-------------|
| **REST API Push** | DMS pushes individual cart changes in real time via REST endpoints |
| **Mapped Sync** | Fetches all active inventory from DMS and creates/updates WooCommerce products in batches |
| **Selective Sync** | Admin picks specific carts to sync from the DMS inventory list |
| **Lazy Sync** | On-demand product creation when a cart URL is visited (`/dms/cart/{id}`) |

All sync methods support **batched processing** to prevent timeouts on large inventories, with progress tracking in the admin UI.

### Schema Templates

Control how every product field is generated using template variables pulled from DMS cart data:

```
Title:    {^make}® {^model} {cartColor} in {city}, {stateAbbr}
Slug:     {make}-{model}-{cartColor}-seat-{seatColor}-{city}-{state}
Image:    {^make}® {^model} {cartColor} in {city}, {stateAbbr} image
```

Available variables include `{make}`, `{model}`, `{year}`, `{cartColor}`, `{seatColor}`, `{city}`, `{state}`, `{stateAbbr}`, `{retailPrice}`, `{passengers}`, `{driveTrain}`, and many more.

### Field Mapping System

A visual admin interface for mapping any DMS JSON path to any WooCommerce field:

- **Post meta** — Map to any `_postmeta` key
- **Taxonomies** — Map to product categories, tags, or custom taxonomies
- **Post columns** — Map to `post_title`, `post_content`, `post_status`, etc.
- **Transforms** — Direct copy, template interpolation, lookup tables, or custom functions

### Image Handling

- Images downloaded from S3 bucket and sideloaded into the WordPress media library
- First image set as **featured image**, rest added to **product gallery**
- Monroney window sticker PDFs attached as product meta
- Configurable image name templates for SEO
- Existing images cleaned up on re-sync to prevent duplicates

### 7-Layer Duplicate Prevention

Multiple safeguards ensure inventory syncs never create duplicate products:

1. **PID from DMS** — WordPress post ID stored in DMS and sent back on subsequent syncs
2. **`_dms_cart_id` meta lookup** — Finds existing products by DMS cart ID
3. **SKU matching** — `wc_get_product_id_by_sku()` catches products by VIN/serial
4. **Create/update routing** — Existing product ID routes to update, not create
5. **Write-time guards** — Database layer refuses to create if ID exists, or update if ID is missing
6. **In-memory dedup key** — Prevents identical new carts within a single sync batch
7. **PID report-back** — After creation, WordPress ID is written back to DMS for future syncs

*See [DUPLICATE-PREVENTION-AUDIT.md](DUPLICATE-PREVENTION-AUDIT.md) for the full technical breakdown.*

### Product Feed Integration

Every synced product is automatically tagged with metadata for:

- **Google Listings &amp; Ads** — Condition, brand, color, pattern, MPN, size system
- **Facebook Catalog** — Brand, color, condition, pattern, image source
- **Pinterest** — Condition, Google product category
- **WooCommerce product feeds** — All standard fields populated

### Multi-Location Support

- Store locations fetched from DMS API with city, state, and coordinates
- Products categorized by location automatically
- Featured product grids managed per location (8 new, 8 used, 4 popular)
- Location-specific Elementor widgets for dealership pages

---

## Admin Pages

| Page | Description |
|------|-------------|
| **Diagnostics** | Inventory health dashboard — shows sync accuracy, missing/extra products, and DMS vs. WooCommerce comparison with pie charts |
| **Settings** | DMS API connection (URL, auth token), S3 file source, GitHub update token, REST endpoint reference, and schema template configuration |
| **Field Mapping** | Visual CRUD interface for custom DMS → WooCommerce field mappings with drag-and-drop ordering |
| **Inventory Sync** | Trigger mapped or selective syncs with real-time batch progress bars and status logging |

---

## REST API Endpoints

The plugin registers the following endpoints for DMS-to-WordPress communication:

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/wp-json/tigon-dms-connect/v1/push` | POST | Push a single cart (create or update) |
| `/wp-json/tigon-dms-connect/used` | POST | Create/update a used cart |
| `/wp-json/tigon-dms-connect/used` | DELETE | Remove a used cart and its media |
| `/wp-json/tigon-dms-connect/new/update` | POST | Create/update a new cart |
| `/wp-json/tigon-dms-connect/new/pid` | POST | Look up product ID by URL slug |
| `/wp-json/tigon-dms-connect/showcase` | POST | Set featured product grid for a location |

All endpoints require `manage_options` capability (WordPress admin authentication).

---

## Shortcodes

```
[tigon_dms_carts type="all"]
```
Renders a grid of DMS cart cards. Options: `type="all"`, `type="new"`, `type="used"`.

```
[tigon_dms_inventory_filtered show_filters="yes" show_pagination="yes" per_page="20"]
```
Renders the full inventory browser with sidebar filters for make, model, color, location, price range, and more.

---

## Elementor Widgets

| Widget | Description |
|--------|-------------|
| **DMS Carts** | Configurable cart grid — display all, new, used, or popular carts with a type selector in the Elementor editor |
| **DMS Inventory (Filtered)** | Full inventory browser with optional filter sidebar, pagination, and sorting — embeddable on any Elementor page |

---

## WooCommerce Customizations

- **"See Details" buttons** — DMS-imported products show "See Details" instead of "Add to Cart" and link to the product page
- **Native attribute tabs** — Product specifications display in WooCommerce's built-in "Additional Information" tab
- **Product ordering** — Custom sort-by-price with menu order priority for catalog pages
- **Image zoom disabled** — Product gallery zoom removed for cleaner cart image display
- **20 products per page** — Catalog set to 5 rows of 4 products

---

## Database Tables

The plugin creates five custom tables on activation:

| Table | Purpose |
|-------|---------|
| `{prefix}tigon_dms_config` | Key-value configuration store (DMS URL, tokens, schema templates) |
| `{prefix}tigon_dms_cart_lists` | Featured product grids per location (JSON arrays of cart IDs) |
| `{prefix}tigon_dms_field_mappings` | Custom DMS → WooCommerce field mapping rules |
| `{prefix}tigon_dms_carts` | Full DMS inventory staging cache (100+ columns, denormalized) |

---

## Auto-Updates

The plugin checks for updates from its private GitHub repository on every admin page load. Configure a GitHub personal access token in **Settings → General** to enable automatic one-click updates from the WordPress plugins screen.

---

## Architecture

```
TIGON-DMS-Connect/
├── dms-bridge-plugin.php          # Main plugin file — bridge sync, shortcodes, WooCommerce hooks
├── src/
│   ├── Core.php                   # Admin init, AJAX handlers, REST route registration
│   ├── Admin/
│   │   ├── Admin_Page.php         # Admin page HTML rendering (diagnostics, settings, sync)
│   │   ├── REST_Routes.php        # REST API endpoint handlers
│   │   ├── Database_Object.php    # Product data model for mapped sync
│   │   ├── Database_Write_Controller.php  # Direct DB writes (bypasses WP for speed)
│   │   ├── Ajax_Settings_Controller.php   # Settings AJAX handlers
│   │   ├── Field_Mapping.php      # Field mapping CRUD
│   │   ├── CartModel.php          # Staging table upsert logic
│   │   ├── Attributes.php         # Location/store data, manufacturer lookup
│   │   ├── New/Cart.php           # New cart converter
│   │   └── Used/Cart.php          # Used cart converter
│   ├── Abstracts/
│   │   └── Abstract_Cart.php      # Base cart conversion — images, templates, validation
│   └── Includes/
│       ├── DMS_Connector.php      # Authenticated DMS API client
│       ├── Somatic.php            # Image sideload utility
│       ├── WP_GitHub_Updater.php  # GitHub auto-update checker
│       └── Product_Archive_Extension.php  # Custom product ordering
├── includes/
│   ├── class-dms-api.php          # DMS API calls, S3 URLs, transient caching
│   ├── class-dms-sync.php         # Legacy paginated inventory sync
│   ├── class-dms-display.php      # Frontend cart rendering
│   ├── class-dms-elementor-widget.php     # Elementor "DMS Carts" widget
│   └── class-dms-inventory-widget.php     # Elementor "DMS Inventory" widget
├── assets/
│   ├── css/                       # Admin + frontend stylesheets
│   └── js/                        # Settings, diagnostics, API service scripts
└── templates/                     # Cart display templates
```

---

## Requirements

- **WordPress** 6.0 or higher
- **WooCommerce** 8.0 or higher
- **PHP** 8.1 or higher
- **Tigon DMS** API access (URL + authentication token)
- **S3 bucket** configured for cart images and window stickers

---

## License

Proprietary. All rights reserved.

&copy; Jaslow Digital &mdash; [jaslowdigital.com](https://jaslowdigital.com)
