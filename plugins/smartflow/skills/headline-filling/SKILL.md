---
name: headline-filling
description: Rules for correctly filling title and subtitle fields in ACF content blocks (WST Flexible Content). Separates the semantic heading hierarchy (Format field = HTML tag) from the look (Style field = wso- class) so that no heading levels are skipped (e.g. no h6 directly after h2). Use when filling page content with title/subtitle fields, when setting title_format/subtitle_format/title_style/subtitle_style, for headline or heading hierarchy questions, and for accessibility checks of the heading structure.
---

# Headline Filling

Most WST content blocks (Flexible Content sections) have two select fields each for title and subtitle:

| Field | ACF name | Meaning | Choices |
|---|---|---|---|
| Format | `title_format` / `subtitle_format` | **HTML tag** (semantics) | `h1`-`h6`, `p` |
| Style | `title_style` / `subtitle_style` | **Look** (CSS class) | `wso-auto`, `wso-h1`-`wso-h6`, `wso-subline`, `wso-p` |

A typical WST element template renders:

```html
<{{title_format}} class="wso-{{title_format}} … wso-title">…</{{title_format}}>
```

If a style is set, its class replaces the automatic `wso-{format}` class. **Format only determines the tag, Style only the look — both are chosen independently.**

## Core Rule

The page's heading hierarchy must follow the correct order:

- Levels must not be skipped downward (after `h2` an `h3` may follow, but no `h4`-`h6`).
- Exactly one `h1` per page (usually the intro/hero title).
- Subtitles, sublines, and purely decorative text lines are semantically **not headings** -> Format `p`.

When the design shows a look that would violate the hierarchy, **never bend the Format** — solve the look through Style:

> The design shows a title in h2 look and, directly after it in the same section, a subtitle in h6 look -> title: Format `h2`; subtitle: Format `p`, Style `wso-h6`.

## Filling Procedure

1. Think through the heading structure of the whole page, not just the single section: which level is semantically correct next?
2. Set **Format** by semantics (document outline), regardless of how large the text looks in the design.
3. Set **Style** by design:
   - The text looks like its format -> leave Style at `wso-auto` (default); the look then follows the format tag (an empty style alternatively sets `wso-{format}` automatically).
   - The look deviates -> pick the matching `wso-h1`-`wso-h6`, `wso-subline`, or `wso-p` class.
4. Subtitles: the Format default is `p`, the Style default `wso-subline`. Change the Format to `hX` only when the subtitle really is its own outline level — the ACF instruction explicitly warns that values other than "Paragraph" can cause accessibility problems.

## Examples

| Design shows | Format | Style |
|---|---|---|
| Section title in h2 look, semantically level 2 | `h2` | empty / `wso-auto` |
| Subtitle in h6 look directly after an h2 title | `p` | `wso-h6` |
| Small kicker (subline) above the title | `p` | `wso-subline` |
| Card title in h5 look, last heading was h2 | `h3` | `wso-h5` |
| Hero title in h1 look on a subpage without another h1 | `h1` | empty / `wso-auto` |

## Do Not

- Choose the Format by look ("looks like h6 -> Format h6").
- Skip levels because the section "works on its own".
- Set multiple `h1` on one page.
- Hardcode styles or invent new classes — use only the existing `wso-*` choices.

Writing these fields onto a live page or post goes through the bundled `content` Skill (REST, backup, full Flexible Content round-trip). This Skill only decides Format vs Style.
