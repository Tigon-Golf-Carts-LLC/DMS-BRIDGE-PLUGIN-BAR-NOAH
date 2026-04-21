# Royal EV Models - Complete Database_Object Mapping Per Model

Source: `Abstract_Cart.php`

All Royal EV models inherit from `../Manufacturer-Logic/Royal-EV.md` which inherits from `../Global-New-Logic.md`.
Royal EV has NO converter defaults — all values come directly from the DMS / API payload.
Royal EV HAS dedicated color palette attributes (`pa_royal-ev-cart-colors` / `pa_royal-ev-seat-colors`).

---

## Royal EV Majesty

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ROYAL EV(R) MAJESTY {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| `post_status` | inherit |
| `comment_status` | inherit |
| `ping_status` | inherit |
| `menu_order` | inherit |
| `post_type` | inherit |
| `comment_count` | inherit |
| `post_author` | inherit |
| `post_name` | inherit |

### postmeta - WooCommerce

| Meta Key | Value |
|---|---|
| `_sku` | inherit |
| `_tax_status` | inherit |
| `_tax_class` | inherit |
| `_manage_stock` | inherit |
| `_backorders` | inherit |
| `_sold_individually` | inherit |
| `_virtual` | inherit |
| `_downloadable` | inherit |
| `_download_limit` | inherit |
| `_download_expiry` | inherit |
| `_stock` | inherit |
| `_stock_status` | inherit |
| `_global_unique_id` | inherit |
| `_product_attributes` | See Attributes below |
| `_thumbnail_id` | inherit |
| `_product_image_gallery` | inherit |
| `_regular_price` | From API payload |
| `_price` | inherit |

### postmeta - Yoast SEO

All values inherit.

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | **No Royal EV-specific tabs.** No tabs are injected for Royal EV models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ROYAL EV(R) Majesty Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_brand` | `ROYAL EV(R)` |
| `_wc_gla_pattern` | `Majesty` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `ROYAL EV(R)` |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Majesty` |
| All other keys | inherit |

### postmeta - Pinterest / Tigon Specific

All values inherit.

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | From API payload |
| `pa_battery-warranty` | From API payload |
| `pa_brush-guard` | **NO** |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | From API payload |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | **NO** |
| `pa_lift-kit` | From API payload |
| `pa_location` | inherit |
| `pa_royal-ev-cart-colors` | From API payload |
| `pa_royal-ev-seat-colors` | From API payload |
| `pa_sound-system` | `ROYAL EV(R) SOUND SYSTEM` |
| `pa_passengers` | From API payload |
| `pa_receiver-hitch` | From API payload |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | From API payload |
| `pa_shipping` | inherit |
| `pa_street-legal` | From API payload |
| `pa_tire-profile` | From API payload |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | From API payload |
| `pa_year-of-vehicle` | inherit |

### DMS Defaults

All values from API payload.

### Available Colors

From API payload

---

## Royal EV Crown 4

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ROYAL EV(R) CROWN 4 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ROYAL EV(R) Crown 4 Add Ons` |

### postmeta - Google / Facebook pattern

`Crown 4`

### All other postmeta

Inherit from Royal EV manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Royal EV models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ROYAL EV(R) SOUND SYSTEM`. Uses `pa_royal-ev-cart-colors` / `pa_royal-ev-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Royal EV Crown 4L

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ROYAL EV(R) CROWN 4L {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ROYAL EV(R) Crown 4L Add Ons` |

### postmeta - Google / Facebook pattern

`Crown 4L`

### All other postmeta

Inherit from Royal EV manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Royal EV models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ROYAL EV(R) SOUND SYSTEM`. Uses `pa_royal-ev-cart-colors` / `pa_royal-ev-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Royal EV Crown 6

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ROYAL EV(R) CROWN 6 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ROYAL EV(R) Crown 6 Add Ons` |

### postmeta - Google / Facebook pattern

`Crown 6`

### All other postmeta

Inherit from Royal EV manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Royal EV models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ROYAL EV(R) SOUND SYSTEM`. Uses `pa_royal-ev-cart-colors` / `pa_royal-ev-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Royal EV Crown 6L

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ROYAL EV(R) CROWN 6L {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ROYAL EV(R) Crown 6L Add Ons` |

### postmeta - Google / Facebook pattern

`Crown 6L`

### All other postmeta

Inherit from Royal EV manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Royal EV models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ROYAL EV(R) SOUND SYSTEM`. Uses `pa_royal-ev-cart-colors` / `pa_royal-ev-seat-colors`.

### DMS Defaults

All values from API payload.
