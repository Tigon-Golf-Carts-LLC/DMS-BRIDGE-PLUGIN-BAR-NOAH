# Icon Models - Complete Database_Object Mapping Per Model

Source: `Abstract_Cart.php`, `New_Cart_Converter.php`

All Icon models inherit from `../Manufacturer-Logic/Icon.md` which inherits from `../Global-New-Logic.md`.
Icon HAS converter defaults that vary by series. Icon HAS dedicated color palette attributes (`pa_icon-cart-colors` / `pa_icon-seat-colors`).

**Series breakdown:**
- **i-Series** (consumer) — Electric, street legal, 2 Tone seats, full 13-color palette
- **C-Series** (commercial) — Electric, NOT street legal, Brown seats, White only
- **G-Series** (gas) — Gas powered, NOT street legal, Black seats, 4 colors only
- **HD variants** — Heavy-duty models, i-prefix = i-Series defaults

---

## Icon i20X

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I20X {Color} In {City} {State}` |
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
| `_regular_price` | From converter / API payload |
| `_price` | inherit |

### postmeta - Yoast SEO

All values inherit.

### postmeta - Product Tabs

| Meta Key | Value |
|---|---|
| `_yikes_woo_products_tabs` | **No Icon-specific tabs.** No tabs are injected for Icon models. |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i20X Add Ons` |

### postmeta - Google for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_gla_brand` | `ICON(R)` |
| `_wc_gla_pattern` | `i20X` |
| All other keys | inherit |

### postmeta - Facebook for WooCommerce

| Meta Key | Value |
|---|---|
| `_wc_facebook_enhanced_catalog_attributes_brand` | `ICON(R)` |
| `_wc_facebook_enhanced_catalog_attributes_pattern` | `i20X` |
| All other keys | inherit |

### postmeta - Pinterest / Tigon Specific

All values inherit.

### Attributes (pa_*)

| Attribute | Value |
|---|---|
| `pa_battery-type` | `AGM` |
| `pa_battery-warranty` | From converter |
| `pa_brush-guard` | **NO** |
| `pa_cargo-rack` | inherit (`NO`) |
| `pa_drivetrain` | inherit (`2X4`) |
| `pa_electric-bed-lift` | inherit (`NO`) |
| `pa_extended-top` | From converter |
| `pa_fender-flares` | inherit (`YES`) |
| `pa_led-accents` | **NO** |
| `pa_lift-kit` | From converter |
| `pa_location` | inherit |
| `pa_icon-cart-colors` | From i-Series palette (13 colors) |
| `pa_icon-seat-colors` | `2 Tone` |
| `pa_sound-system` | `ICON(R) SOUND SYSTEM` |
| `pa_passengers` | From converter |
| `pa_receiver-hitch` | inherit (`NO`) |
| `pa_return-policy` | inherit (`90 DAY` + `YES`) |
| `pa_rim-size` | From converter |
| `pa_shipping` | inherit |
| `pa_street-legal` | `YES` |
| `pa_tire-profile` | From converter |
| `pa_vehicle-class` | inherit |
| `pa_vehicle-warranty` | From converter |
| `pa_year-of-vehicle` | inherit |

### Converter Defaults

| Field | Value |
|---|---|
| `isElectric` | `true` |
| `battery.packVoltage` | `48` |
| `battery.isDC` | `false` |
| `title.isStreetLegal` | `true` |
| `seatColor` | `2 Tone` |

### Available Colors

Black, Caribbean, Champagne, Forest, Indigo, Lime, Orange, Purple, Sangria, Silver, Torch, White, Yellow

---

## Icon i40X

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I40X {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i40X Add Ons` |

### postmeta - Google / Facebook pattern

`i40X`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i60LX

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I60LX {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i60LX Add Ons` |

### postmeta - Google / Facebook pattern

`i60LX`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i40-ECO

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I40-ECO {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i40-ECO Add Ons` |

### postmeta - Google / Facebook pattern

`i40-ECO`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i20

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I20 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i20 Add Ons` |

### postmeta - Google / Facebook pattern

`i20`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i40

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I40 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i40 Add Ons` |

### postmeta - Google / Facebook pattern

`i40`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i40L

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I40L {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i40L Add Ons` |

### postmeta - Google / Facebook pattern

`i40L`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i60

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I60 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i60 Add Ons` |

### postmeta - Google / Facebook pattern

`i60`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i60L

> **Series:** i-Series (consumer, electric, street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I60L {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i60L Add Ons` |

### postmeta - Google / Facebook pattern

`i60L`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon G40

> **Series:** G-Series (gas, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) G40 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) G40 Add Ons` |

### postmeta - Google / Facebook pattern

`G40`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

G-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `null` (gas), `pa_icon-seat-colors` = `Black`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (4-color palette).

### Converter Defaults

G-Series shared defaults. `isElectric` = `false`, `title.isStreetLegal` = `false`, `seatColor` = `Black`, `battery.type` = `null`.

### Available Colors

Black, Forest, Indigo, White

---

## Icon G40L

> **Series:** G-Series (gas, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) G40L {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) G40L Add Ons` |

### postmeta - Google / Facebook pattern

`G40L`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

G-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `null` (gas), `pa_icon-seat-colors` = `Black`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (4-color palette).

### Converter Defaults

G-Series shared defaults. `isElectric` = `false`, `title.isStreetLegal` = `false`, `seatColor` = `Black`, `battery.type` = `null`.

### Available Colors

Black, Forest, Indigo, White

---

## Icon G60

> **Series:** G-Series (gas, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) G60 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) G60 Add Ons` |

### postmeta - Google / Facebook pattern

`G60`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

G-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `null` (gas), `pa_icon-seat-colors` = `Black`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (4-color palette).

### Converter Defaults

G-Series shared defaults. `isElectric` = `false`, `title.isStreetLegal` = `false`, `seatColor` = `Black`, `battery.type` = `null`.

### Available Colors

Black, Forest, Indigo, White

---

## Icon G60L

> **Series:** G-Series (gas, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) G60L {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) G60L Add Ons` |

### postmeta - Google / Facebook pattern

`G60L`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

G-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `null` (gas), `pa_icon-seat-colors` = `Black`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (4-color palette).

### Converter Defaults

G-Series shared defaults. `isElectric` = `false`, `title.isStreetLegal` = `false`, `seatColor` = `Black`, `battery.type` = `null`.

### Available Colors

Black, Forest, Indigo, White

---

## Icon C20S

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C20S {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C20S Add Ons` |

### postmeta - Google / Facebook pattern

`C20S`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C20U

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C20U {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C20U Add Ons` |

### postmeta - Google / Facebook pattern

`C20U`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C20UL

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C20UL {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C20UL Add Ons` |

### postmeta - Google / Facebook pattern

`C20UL`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C20V

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C20V {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C20V Add Ons` |

### postmeta - Google / Facebook pattern

`C20V`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C30AMB

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C30AMB {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C30AMB Add Ons` |

### postmeta - Google / Facebook pattern

`C30AMB`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C30AMBL

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C30AMBL {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C30AMBL Add Ons` |

### postmeta - Google / Facebook pattern

`C30AMBL`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C40FS

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C40FS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C40FS Add Ons` |

### postmeta - Google / Facebook pattern

`C40FS`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C60FS

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C60FS {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C60FS Add Ons` |

### postmeta - Google / Facebook pattern

`C60FS`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C70W

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C70W {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C70W Add Ons` |

### postmeta - Google / Facebook pattern

`C70W`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon C80

> **Series:** C-Series (commercial, electric, NOT street legal)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) C80 {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) C80 Add Ons` |

### postmeta - Google / Facebook pattern

`C80`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

C-Series defaults. `pa_street-legal` = `NO`, `pa_battery-type` = `AGM`, `pa_icon-seat-colors` = `Brown`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (White only).

### Converter Defaults

C-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `false`, `seatColor` = `Brown`, `battery.packVoltage` = `48`.

### Available Colors

White

---

## Icon i20S-HD

> **Series:** i-Series HD variant (consumer, electric, street legal, heavy-duty)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I20S-HD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i20S-HD Add Ons` |

### postmeta - Google / Facebook pattern

`i20S-HD`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i20U-HD

> **Series:** i-Series HD variant (consumer, electric, street legal, heavy-duty)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I20U-HD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i20U-HD Add Ons` |

### postmeta - Google / Facebook pattern

`i20U-HD`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i20UL-HD

> **Series:** i-Series HD variant (consumer, electric, street legal, heavy-duty)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I20UL-HD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i20UL-HD Add Ons` |

### postmeta - Google / Facebook pattern

`i20UL-HD`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i40FS-HD

> **Series:** i-Series HD variant (consumer, electric, street legal, heavy-duty)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I40FS-HD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i40FS-HD Add Ons` |

### postmeta - Google / Facebook pattern

`i40FS-HD`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i60-HD

> **Series:** i-Series HD variant (consumer, electric, street legal, heavy-duty)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I60-HD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i60-HD Add Ons` |

### postmeta - Google / Facebook pattern

`i60-HD`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.

---

## Icon i60FS-HD

> **Series:** i-Series HD variant (consumer, electric, street legal, heavy-duty)

### posts (wp_posts)

| Column | Value |
|---|---|
| `ID` | inherit |
| `post_title` | `ICON(R) I60FS-HD {Color} In {City} {State}` |
| `post_excerpt` | inherit |
| `post_content` | inherit |
| All other columns | inherit |

### postmeta - Custom Product Add-Ons

| Meta Key | Value |
|---|---|
| `wcpa_exclude_global_forms` | inherit |
| `_wcpa_product_meta` | `ICON(R) i60FS-HD Add Ons` |

### postmeta - Google / Facebook pattern

`i60FS-HD`

### All other postmeta

Inherit from Icon manufacturer defaults.

### Attributes (pa_*)

i-Series defaults. `pa_street-legal` = `YES`, `pa_icon-seat-colors` = `2 Tone`, `pa_brush-guard` = `NO`, `pa_led-accents` = `NO`, `pa_sound-system` = `ICON(R) SOUND SYSTEM`. Uses `pa_icon-cart-colors` (13-color palette).

### Converter Defaults

i-Series shared defaults. `isElectric` = `true`, `title.isStreetLegal` = `true`, `seatColor` = `2 Tone`, `battery.packVoltage` = `48`.
