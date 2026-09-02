# Viewport Measurement Reference

The standard measurement pass for the `project-css-setup` viewport ladder. Every project measures the same way with this snippet, so results are comparable across rungs, blocks, and projects, and known pitfalls are solved once instead of per project.

Prerequisites: Playwright MCP is running against the served target URL (claim a browser per the `playwright-browser-claim` Rule when the project runs parallel servers), and the container selector is known from `PROJECT-CONTEXT.md` (common WESEO Astra default: `.ast-container` — non-normative, read the project value).

## The Ladder

Walk the full ladder from the `frontend-section-qa-tablet-band` Rule, or the project's recorded rungs when `PROJECT-CONTEXT.md` documents different values:

```text
1920 -> 1440 -> 1024 -> 991 -> 921 -> 767 -> 575 -> 375
```

- 1920 and 375 are the Figma design anchors — compare the measured values against the design frames (±2px).
- All other rungs are "no broken layout, deliberate result" checks — measure, do not calculate.

## The 991 -> 990 Caveat (Playwright MCP Only)

At exactly 991px some Playwright browsers report a fractional `devicePixelRatio`, so neither `max-width: 991px` nor `min-width: 992px` media queries match — the rung silently measures a state no real device ever renders. Keep the 991 row in the results table, but **set the viewport to 990 in Playwright MCP** for that row. Real devices at 991px are unaffected. The snippet returns `dpr` so a fractional value is visible in the evidence.

## Per-Rung Procedure

For each rung:

1. `browser_resize` to `<rung-width> x 1080` (990 for the 991 row, see above).
2. `browser_evaluate` with the snippet below.
3. Add the returned object as one row to the results table.

## The Snippet

Replace `<container-selector>` with the project's container selector before running. The optional `extra` map takes block-specific probes (a headline, a button, a section wrapper) as `label: selector` pairs.

```js
() => {
  const containerSel = '<container-selector>';
  const extra = {
    // 'h1': 'h1',
    // 'button': '.wso-button',
  };
  const round1 = (v) => Math.round(v * 10) / 10;
  const el = document.querySelector(containerSel);
  const cs = el ? getComputedStyle(el) : null;
  const rect = el ? el.getBoundingClientRect() : null;
  const innerWidth = el
    ? rect.width - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight)
    : null;
  const result = {
    viewport: window.innerWidth,
    dpr: window.devicePixelRatio,
    remBasisPx: round1(parseFloat(getComputedStyle(document.documentElement).fontSize)),
    bodyFontPx: round1(parseFloat(getComputedStyle(document.body).fontSize)),
    containerSelector: containerSel,
    containerFound: !!el,
    containerOuterPx: el ? round1(rect.width) : null,
    containerInnerPx: el ? round1(innerWidth) : null,
    sideMarginPx: el ? round1((window.innerWidth - rect.width) / 2) : null,
  };
  for (const [label, sel] of Object.entries(extra)) {
    const node = document.querySelector(sel);
    if (!node) { result[label] = null; continue; }
    const ncs = getComputedStyle(node);
    result[label] = {
      widthPx: round1(node.getBoundingClientRect().width),
      fontSizePx: round1(parseFloat(ncs.fontSize)),
      lineHeight: ncs.lineHeight,
    };
  }
  return result;
};
```

What the fields answer:

- `remBasisPx` — the fluid rem basis at this rung. This is what makes one anchor rem value scale; if a rung's px result is off, check this first before adding an override.
- `containerInnerPx` — the content width Sections actually get (outer width minus the container's own padding). This is the value compared against the design's content width at the anchors.
- `sideMarginPx` — the symmetric margin outside the container; compare against the design's side margins at the anchors.
- `dpr` — evidence for the 991 caveat; a fractional value at a tablet rung means the measurement must move to 990.
- `extra` probes — per-block spot measures (typography on the text node, button geometry) without changing the snippet's core shape.

## Results Table

Collect one row per rung:

| viewport | rem basis | container inner | side margin | notes |
|---|---|---|---|---|
| 1920 | `<px>` | `<px>` | `<px>` | design anchor, compare ±2px |
| 1440 | `<px>` | `<px>` | `<px>` | |
| ... | | | | |
| 991 (at 990) | `<px>` | `<px>` | `<px>` | Playwright caveat |
| ... | | | | |
| 375 | `<px>` | `<px>` | `<px>` | mobile design anchor, compare ±2px |

Record the filled table (or its anchor rows plus deviations) in `PROJECT-CONTEXT.md` with the value block it verified, and per-breakpoint overrides with their reasons next to it.
