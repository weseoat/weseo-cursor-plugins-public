---
title: "<Element-Name>"
category: elements
slug: <slug>
source_files:
  - themes/<child-theme>/smart-template-builder/elements/<slug>.php
  - themes/<child-theme>/styles/elements/<slug>.css
related_docs: []
generated: <YYYY-MM-DD>
status: <complete|partial|todo>
css_hooks: ["<.wso-...>"]
used_by: [<sections/post-types/templates, die das Element nutzen>]
---

# Element: <Name>

## Zweck

<1–3 Sätze: Was ist das Element (z. B. Footer, Header, Button, Image-Box),
wo taucht es im Frontend auf.>

## Aufbau

- **Template:** [`<slug>.php`](<relativer Pfad>) <oder „nur CSS, kein eigenes Template">
- **Einbindung:** <Hook, Shortcode, Theme-Template oder Section, die es rendert>

### CSS-Hooks

| Selektor | Zweck |
|---|---|
| `<selektor>` | <Zweck> |

## Styling

- **CSS/SCSS:** [`<datei>.css`](<relativer Pfad>)
- **Registrierung:** Eintrag in `styles.json`: <ja/nein/Pfad>
- **Verwendete Tokens/Variablen:** <globale bzw. scoped Variablen>
- **Responsive:** <Breakpoint-Verhalten in 1–2 Sätzen>

## Verwendung

| Kontext | Datei / Ort |
|---|---|
| <Section/Template/Seite> | `<pfad oder url>` |

## Relevante Dateien

| Pfad | Rolle |
|---|---|
| `<pfad>` | <Rolle> |

## Offene Punkte / TODOs

- <unbekannte oder zu prüfende Werte — oder „keine">
