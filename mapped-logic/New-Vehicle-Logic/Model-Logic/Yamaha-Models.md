# Yamaha Models - Complete Database_Object Mapping Per Model

Source: `Abstract_Cart.php`

All Yamaha models inherit from `../Manufacturer-Logic/Yamaha.md` which inherits from `../Global-New-Logic.md`.
Yamaha has NO converter defaults — all values come directly from the DMS / API payload.
Yamaha HAS dedicated color palette attributes (`pa_yamaha-cart-colors` / `pa_yamaha-seat-colors`).

---

## Yamaha Drive2 PTV

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `YAMAHA(R) DRIVE2 PTV {Color} In {City} {State}` |
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
| `_yikes_woo_products_tabs` | **No Yamaha-specific tabs.** No tabs are injected for Yamaha models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `YAMAHA(R) Drive2 PTV Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_brand` | `YAMAHA(R)` |
| `_wc_gla_pattern` | `Drive2 PTV` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `YAMAHA(R)` |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `Drive2 PTV` |
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
| `pa_yamaha-cart-colors` | From API payload |
| `pa_yamaha-seat-colors` | From API payload |
| `pa_sound-system` | `YAMAHA(R) SOUND SYSTEM` |
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

## Yamaha Drive2 Concierge 4

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `YAMAHA(R) DRIVE2 CONCIERGE 4 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `YAMAHA(R) Drive2 Concierge 4 Add Ons` |

### postmeta - Google / Facebook pattern

`Drive2 Concierge 4`

### All other postmeta

Inherit from Yamaha manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Yamaha models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `YAMAHA(R) SOUND SYSTEM`. Uses `pa_yamaha-cart-colors` / `pa_yamaha-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Yamaha UMAX

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `YAMAHA(R) UMAX {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `YAMAHA(R) UMAX Add Ons` |

### postmeta - Google / Facebook pattern

`UMAX`

### All other postmeta

Inherit from Yamaha manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Yamaha models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `YAMAHA(R) SOUND SYSTEM`. Uses `pa_yamaha-cart-colors` / `pa_yamaha-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Yamaha UMAX Rally

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `YAMAHA(R) UMAX RALLY {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `YAMAHA(R) UMAX Rally Add Ons` |

### postmeta - Google / Facebook pattern

`UMAX Rally`

### All other postmeta

Inherit from Yamaha manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Yamaha models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `YAMAHA(R) SOUND SYSTEM`. Uses `pa_yamaha-cart-colors` / `pa_yamaha-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Yamaha UMAX Rally 2+2

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `YAMAHA(R) UMAX RALLY 2+2 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `YAMAHA(R) UMAX Rally 2+2 Add Ons` |

### postmeta - Google / Facebook pattern

`UMAX Rally 2+2`

### All other postmeta

Inherit from Yamaha manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Yamaha models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `YAMAHA(R) SOUND SYSTEM`. Uses `pa_yamaha-cart-colors` / `pa_yamaha-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Yamaha PilotCar PC-2

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `YAMAHA(R) PILOTCAR PC-2 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `YAMAHA(R) PilotCar PC-2 Add Ons` |

### postmeta - Google / Facebook pattern

`PilotCar PC-2`

### All other postmeta

Inherit from Yamaha manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Yamaha models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `YAMAHA(R) SOUND SYSTEM`. Uses `pa_yamaha-cart-colors` / `pa_yamaha-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Yamaha PilotCar PC-4

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `YAMAHA(R) PILOTCAR PC-4 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `YAMAHA(R) PilotCar PC-4 Add Ons` |

### postmeta - Google / Facebook pattern

`PilotCar PC-4`

### All other postmeta

Inherit from Yamaha manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Yamaha models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `YAMAHA(R) SOUND SYSTEM`. Uses `pa_yamaha-cart-colors` / `pa_yamaha-seat-colors`.

### DMS Defaults

All values from API payload.

---

## Yamaha PilotCar PC-4 Lifted

> **No converter defaults.** All values populated dynamically from the API payload.

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `YAMAHA(R) PILOTCAR PC-4 LIFTED {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `YAMAHA(R) PilotCar PC-4 Lifted Add Ons` |

### postmeta - Google / Facebook pattern

`PilotCar PC-4 Lifted`

### All other postmeta

Inherit from Yamaha manufacturer defaults. All dynamic values from API payload.

### Attributes (pa_*)

Same as other API-driven Yamaha models. All dynamic values from API payload. Inherits: `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `YAMAHA(R) SOUND SYSTEM`. Uses `pa_yamaha-cart-colors` / `pa_yamaha-seat-colors`.

### DMS Defaults

All values from API payload.
