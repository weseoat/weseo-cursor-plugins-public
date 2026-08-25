---
title: "<Section-Name>"
category: sections
slug: <slug>
source_files:
  - themes/<child-theme>/smart-template-builder/sections/<slug>.php
  - themes/<child-theme>/styles/sections/<slug>.css
related_docs:
  - ../field-groups/section-<slug>.md
generated: <YYYY-MM-DD>
status: <complete|partial|todo>
acf_layout_key: <layout_...|unbekannt>
css_hooks: [".wso-section-<slug>"]
frontend_urls: [<url|unbekannt>]
preview_urls: [<url|n/a>]
---

# Section: <Name>

## Zweck

<1–3 Sätze: Was zeigt die Section, wofür wird sie eingesetzt, auf welchen
Seiten ist sie im Einsatz.>

## Aufbau

- **Template:** [`<slug>.php`](<relativer Pfad>)
- **Flexible-Content-Layout:** `<layout-name>` (Key: `<layout_...|unbekannt>`)
- **ACF-JSON-Feldgruppe:** [`<datei>.json`](<relativer Pfad>) — Doku: [`section-<slug>.md`](../field-groups/section-<slug>.md)
- **Registrierung:** <flexible-content.php-Eintrag oder `unbekannt`>

### Markup-Struktur

<Kurzbeschreibung der DOM-Struktur: Wrapper, Grid, wichtige Kind-Elemente.>

### CSS-Hooks

| Selektor | Zweck |
|---|---|
| `.wso-section-<slug>` | Section-Wrapper |
| `<weitere>` | <Zweck> |

## ACF-Felder

Vollständige Felddoku: [`section-<slug>.md`](../field-groups/section-<slug>.md)

Im Template verwendete Felder (aus WST-Shortcodes abgeleitet):

| Feld | Typ | Verwendung im Template |
|---|---|---|
| `<name>` | `<type>` | <z. B. Headline, Bild, Button-Link> |

## Styling

- **CSS/SCSS:** [`<datei>.css`](<relativer Pfad>) <ggf. SCSS-Quelle verlinken>
- **Registrierung:** Eintrag in `styles.json`: <ja/nein/Pfad>
- **Scoped Variablen:** `<--section-...-*>` <oder „keine">
- **Responsive:** <Breakpoint-Verhalten in 1–2 Sätzen>

## Varianten / Optionen

<Auswahlfelder, Modifier-Klassen, Layout-Varianten — oder „keine".>

## Visual QA Targets

<!-- Work-Record-Abschnitt: wird von wst-section-workflow befüllt und von
frontend-section-qa je Zeile mit Result beschrieben. auto-docs legt nur das
leere Gerüst an und ändert befüllte Zeilen nie. -->

Viewport-Zuordnung (Pixelwerte aus `PROJECT-CONTEXT.md`):

| Rolle | Breite (px) |
|---|---|
| desktop | <1920|unbekannt> |
| tablet | <768–991|unbekannt> |
| mobile | <375|unbekannt> |

Eine Zeile = eine ja/nein-prüfbare Erwartung. Pflicht-Basisvarianten
(default, lange Headline/Copy, optionales Feld leer, viele Wiederholungen,
Mobile-Stack, Interaktionszustände) beantworten oder als `n/a: <Grund>`
eintragen. Mobile-Zeilen stammen aus dem Design-Mobile-Frame
(`no-mobile-design: derived-from-desktop` markiert Interpretationsspielraum).

| Variante | Viewport | Erwartung | Result |
|---|---|---|---|
| default | desktop | <ja/nein-prüfbare Erwartung mit stabilem Selektor> | <pass|fail: Notiz|offen> |
| <variante> | <viewport> | <Erwartung> | <Result> |

## QA-Notizen

<!-- Work-Record-Abschnitt: Statusfelder, Deploy-Stand (Commit-Hash,
Bridge-Verifikation), Injection-Proof- und Served-Check-Ergebnisse,
offene Fragen, Blocker. Gehört den WST-/Frontend-QA-Workflows. -->

<Statusfelder und Ergebnisse des letzten Durchlaufs — oder „noch kein Durchlauf".>

## Relevante Dateien

| Pfad | Rolle |
|---|---|
| `smart-template-builder/sections/<slug>.php` | Section-Template |
| `acf-json/<datei>.json` | ACF-JSON-Feldgruppe |
| `styles/sections/<slug>.css` | Frontend-CSS |
| `<weitere>` | <Rolle> |

## Offene Punkte / TODOs

- <unbekannte oder zu prüfende Werte — oder „keine">
