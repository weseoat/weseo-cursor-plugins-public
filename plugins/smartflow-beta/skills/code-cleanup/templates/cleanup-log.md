---
title: "Cleanup-Log"
category: maintenance
slug: cleanup-log
generated: <YYYY-MM-DD>
status: complete
---

# Cleanup-Log

Gedächtnis des `code-cleanup`-Skills zwischen den Läufen. Neue Läufe werden oben angefügt. Abgelehnte Befunde werden mit Grund geführt, damit sie nicht erneut vorgeschlagen werden; `deferred`-Befunde sind die Startliste des nächsten Laufs; drei Vorkommen desselben Kriteriums sind eine Konventionslücke (Vorschlag für `docs/coding-standard/`).

## Offene Konventionen

<Konventionen, die weder `docs/coding-standard/` noch die Rules beantworten - Entscheidung steht aus, bis dahin kein Befund.>

- <Sprache>: <Frage> - vorgeschlagen: <Antwort> - Status: offen | entschieden (<Datum>)

## Wiederkehrende Befunde

| Kriterium | Vorkommen (Läufe) | Zuletzt | Vorschlag | Status |
|---|---|---|---|---|
| <ID aus dem Kriterienkatalog> | <n> | <YYYY-MM-DD> | <Satz für docs/coding-standard/<sprache>.md oder Rule-Vorschlag> | offen | übernommen (<Datum>) |

## Deferred

| Lauf | Kriterium | Datei:Zeile | Befund | Grund |
|---|---|---|---|---|
| <YYYY-MM-DD> | <ID> | <pfad:zeile> | <Kurzbefund> | Budget | kein Beweis möglich | Route: <skill> |

## Abgelehnt

| Lauf | Kriterium | Datei:Zeile | Grund |
|---|---|---|---|
| <YYYY-MM-DD> | <ID> | <pfad:zeile> | <Entscheidung des Users> |

---

## Lauf <YYYY-MM-DD>

- Modus: post-run | recurring | scoped (<Ziel>)
- Scope: <n> Dateien (<n> PHP, <n> CSS, <n> Snippets, <n> ACF JSON), Anker: cleanup/<letzter-Tag> | letzte 30 Tage | <explizit>
- Hotspots: <pfad> (<n>), <pfad> (<n>), <pfad> (<n>)
- Ausgeklammert: <pfade oder keine>
- Tooling: <cleanup_tooling-Wert> | agent checks only
- Budget: <n> Judgment-Befunde, <n> Minuten - genutzt: <n> / <n>
- Commit-Mandat: ja | nein | prepare-only (master - user commits)

| Pass | Befunde | angewendet | abgelehnt | deferred | zurückgenommen | Commit |
|---|---|---|---|---|---|---|
| 1 Format | | | | | | <hash> |
| 2 Leftovers | | | | | | <hash> |
| 3 Kommentare | | | | | | <hash> |
| 4 Konventionen | | | | | | <hash> |
| 5 Duplikate | | | | | | <hash> |
| 6 Vereinfachung | | | | | | <hash> |

- Beweise: <Preview-URLs / Served-URLs, Rungs, Screenshot-/HTML-Snapshot-Ablage unter tmp/>
- Außerhalb des Scopes gesehen: <Kurzliste oder keine>
- Kandidaten ohne Beweis: <L-07/L-08-Kandidaten mit fehlendem Nachweis>
- Routen: <Befund -> frontend-section-qa | wst-section-workflow | wst-new-post-type | project-css-setup>
- Tag: cleanup/<YYYY-MM-DD>
- Deploy: implementation pass, deployed verification pending | bridge-verified (<deployed_commit>)
- Offen: <Punkte für den nächsten Lauf>
