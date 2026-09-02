---
title: "<Feldgruppen-Titel>"
category: field-groups
slug: <slug>
source_files:
  - themes/<child-theme>/acf-json/<datei>.json
related_docs:
  - ../sections/<slug>.md
generated: <YYYY-MM-DD>
status: <complete|partial|todo>
acf_group_key: <group_...>
---

# Feldgruppe: <Titel>

## Übersicht

- **Gruppen-Key:** `<group_...>`
- **Registrierung:** Local JSON ([`<datei>.json`](<relativer Pfad>) in `acf-json/`; Sync per Admin-Klick)
- **Aktiv:** <ja|nein>
- **Beschreibung:** <aus der Registrierung — oder „keine">
- **Anzahl Felder (oberste Ebene, ohne Struktur-Felder):** <n>
- **Bridge-Abgleich:** <in GET /status mit local: json gelistet | nicht gelistet — offener Punkt | nicht geprüft>

## Wo wird die Feldgruppe angezeigt?

<Location-Regeln aus der Registrierung, eine Zeile pro UND-Gruppe:>

- `<param>` ist `<wert>` <UND `<param>` ist `<wert>`>

## Felder auf einen Blick

| Feld (Pfad) | Label | Typ | Pflicht | Tab |
|---|---|---|---|---|
| `<name>` | <Label> | `<type>` | <ja|nein> | <Tab|—> |

## Felder im Detail

### 1. `<name>` – <Label>

- **Feld-Key:** `<field_...>`
- **Typ:** `<type>`
- **Pflichtfeld:** <ja|nein>
- **Nur sichtbar wenn:** <Conditional Logic — oder Zeile weglassen>
- **Anweisung im Backend:** <instructions — oder Zeile weglassen>
- **Auswahlmöglichkeiten:** <bei select/radio/checkbox: `wert` = Label — oder Zeile weglassen>
- **Rückgabeformat:** `<return_format>` <oder Zeile weglassen>
- **Befüllung:** <kurzer Hinweis, wie das Feld im Backend befüllt wird>

<Unterfelder von Repeater/Group/Flexible-Content-Feldern rekursiv als
nummerierte Unterabschnitte (1.1, 1.2, …; Layouts als 1.L1, 1.L2, …) mit
denselben Angaben. Bei Clone-Feldern das Ziel benennen:>

- **Klont:** Feldgruppe „<Titel>" → siehe [`<slug>.md`](<relativer Pfad>)
- **Feldnamen-Präfix:** <ja — geklonte Felder werden als `<name>_<feldname>` gespeichert | nein>
- **`parent_layout`:** `<layout_...>` <bei Clone-Kindern im Flexible-Content-Container>

## Offene Punkte / TODOs

- <unbekannte oder zu prüfende Werte — oder „keine">
