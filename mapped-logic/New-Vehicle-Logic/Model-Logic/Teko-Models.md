# TEKO EV Models - Complete Database_Object Mapping Per Model

Source: `Abstract_Cart.php`

All TEKO EV models inherit from `../Manufacturer-Logic/Teko.md` which inherits from `../Global-New-Logic.md`.
TEKO EV has NO converter defaults — all values come directly from the DMS / API payload.

---

## TEKO EV Turbo

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TEKO EV(R) TURBO {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | **No TEKO EV-specific tabs.** No tabs are injected for TEKO EV models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TEKO EV(R) Turbo Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_brand` | `TEKO EV(R)` |
| `_wc_gla_pattern` | `Turbo` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `TEKO EV(R)` |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Turbo` |
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
| `pa_cart-color` | From API payload |
| `pa_seat-color` | From API payload |
| `pa_sound-system` | `TEKO EV(R) SOUND SYSTEM` |
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

## TEKO EV Trophy

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TEKO EV(R) TROPHY {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TEKO EV(R) Trophy Add Ons` |

### postmeta - Google / Facebook pattern

`Trophy`

### All other postmeta

Inherit from TEKO EV manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven TEKO EV models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TEKO EV(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## TEKO EV Trophy Plus

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TEKO EV(R) TROPHY PLUS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TEKO EV(R) Trophy Plus Add Ons` |

### postmeta - Google / Facebook pattern

`Trophy Plus`

### All other postmeta

Inherit from TEKO EV manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven TEKO EV models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TEKO EV(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.

---

## TEKO EV Triumph

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `TEKO EV(R) TRIUMPH {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `TEKO EV(R) Triumph Add Ons` |

### postmeta - Google / Facebook pattern

`Triumph`

### All other postmeta

Inherit from TEKO EV manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven TEKO EV models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `TEKO EV(R) SOUND SYSTEM`. Uses generic `pa_cart-color` / `pa_seat-color`.

### DMS Defaults

All values from API payload.
