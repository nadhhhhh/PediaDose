# PediaDose AI - Design System

## 1. Brand Identity
**Name:** PediaDose (PediaDose)
**Vibe:** Professional, Medical, Pediatric, Friendly, Clean, Modern, Vibrant.
**Target Audience:** Healthcare professionals, pediatricians, pharmacists.

## 2. Color Palette
We use a vibrant, tropical gradient transitioning from Emerald Green to Sunset Coral to convey a friendly pediatric feel while maintaining medical trust.

### Primary Colors
- **Emerald Green:** `#059669` (Main brand color, conveys health and safety)
- **Sunset Coral:** `#ff7f50` (Secondary brand color, friendly and pediatric)
- **Amber Gold:** `#f59e0b` (Accent color, used for active states, borders, and highlights)

### Gradients
- **Primary Gradient:** `linear-gradient(135deg, #059669, #eab308, #ff7f50)` (Used for active sidebar menus, primary buttons, and mobile top bar)

### Backgrounds & Neutrals
- **App Background (Light Mint):** `#f0fdf4` (Very soft, eye-relaxing tint)
- **Card/Sidebar Background:** `#ffffff` (Clean white for content separation)
- **Text Dark:** `#2c3e50` (Main typography)
- **Text Muted:** `#6c757d` (Secondary typography, labels, metadata)
- **Border Color:** `#e0e6ed` (Soft dividers and borders)

### Semantic Colors (Alerts)
- **Success (Normal Dose):** Background `#d4edda`, Text `#155724`
- **Warning (Underdose):** Background `#fff3cd`, Text `#856404`
- **Danger (Overdose):** Background `#f8d7da`, Text `#721c24`

## 3. Typography
- **Font Family:** 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
- **Base Size:** `16px` (1rem)
- **Headings:** Bold, colored in `Emerald Green` or `White` when on gradient background.
- **Page Titles:** Bold, `Emerald Green` text with an `Amber Gold` 3px bottom border.

## 4. Layout & Spacing
- **Desktop Strategy:** 250px fixed left Sidebar, fluid main content area.
- **Mobile Strategy:** Mobile-first approach with a sticky Top App Bar and a fixed Bottom Navigation Bar. Sidebar is hidden.
- **Card Padding:** `30px` on desktop, `20px 15px` on mobile.
- **Card Radius:** `10px` on desktop, `12px` on mobile.
- **Shadows:** Soft drop shadow `0 4px 6px rgba(0,0,0,0.05)` for floating elements.

## 5. UI Components

### Buttons
- **Primary Button:** Uses the `Primary Gradient` background with a 3px `Amber Gold` bottom border. White text, bold. Border radius `6px`.
- **Hover State:** Slightly darker or shifts gradient (currently falls back to solid in legacy, but Stitch can enhance this with CSS animations).

### Forms
- **Inputs & Selects:** 100% width, 1px solid border (`#e0e6ed`), rounded `6px`.
- **Focus State:** Border changes to `Sunset Coral` or `Emerald Green` with a soft box-shadow ring.
- **Labels:** Muted dark gray, font-weight 500, placed above the input field.

### Bottom Navigation (Mobile)
- Fixed at the bottom, white background, top border `#e0e6ed`.
- **Active Item:** Text color `Emerald Green`, with a 3px `Amber Gold` top border marker.

## 6. Directions for Google Stitch
When generating new screens or components for PediaDose:
1. Always utilize the `Primary Gradient` for primary call-to-action buttons or prominent headers.
2. Keep the background clean (`#f0fdf4`) and place content inside white (`#ffffff`) cards with soft shadows.
3. Maintain the pediatric-friendly but professional medical vibe. Avoid overly cartoonish elements; rely on the vibrant color palette to provide the friendly aesthetic.
