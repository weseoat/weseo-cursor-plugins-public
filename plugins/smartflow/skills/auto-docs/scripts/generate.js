/**
 * Generates one LLM-readable Markdown doc per ACF field group into docs/field-groups/.
 *
 * Legacy path: only for projects that still maintain an admin JSON export
 * (acf_export/acf-export-*.json) instead of tracked field-group sources.
 * Projects on the SmartFlow standard (ACF Local JSON under acf-json/)
 * document field groups through the auto-docs worker subagents instead.
 *
 * Source: a path passed as argument, otherwise the newest acf-export-*.json in acf_export/.
 * A passed path is resolved relative to the current working directory (like any CLI tool),
 * so it works no matter where you start node from. Absolute paths also work.
 *
 * Usage:
 *   # from repo root, newest export picked automatically:
 *   node docs/field-groups/generate.js
 *   # specific file, path relative to repo root:
 *   node docs/field-groups/generate.js acf_export/acf-export-2026-07-03.json
 *   # from inside docs/field-groups/ with a relative path:
 *   node generate.js ../../acf_export/acf-export.json
 *   # absolute path:
 *   node generate.js C:/path/to/acf-export.json
 */
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..', '..');
const OUT_DIR = __dirname;

/* ---------- resolve export file ---------- */
let exportFile;
if (process.argv[2]) {
  // Resolve the argument relative to where node was started (standard CLI behavior).
  exportFile = path.resolve(process.cwd(), process.argv[2]);
  if (!fs.existsSync(exportFile)) {
    console.error(`Datei nicht gefunden: ${process.argv[2]} (aufgelöst zu ${exportFile})`);
    process.exit(1);
  }
} else {
  const exportDir = path.join(ROOT, 'acf_export');
  const candidates = fs
    .readdirSync(exportDir)
    .filter((f) => /^acf-export-.*\.json$/.test(f))
    .sort();
  if (!candidates.length) {
    console.error('Kein acf-export-*.json in acf_export/ gefunden.');
    process.exit(1);
  }
  exportFile = path.join(exportDir, candidates[candidates.length - 1]);
}
const sourceRel = path.relative(ROOT, exportFile).replace(/\\/g, '/');
const generatedDate = new Date().toISOString().slice(0, 10);

const groups = JSON.parse(fs.readFileSync(exportFile, 'utf8'));

/* ---------- global lookup maps ---------- */
const fieldByKey = new Map(); // field_key -> { name, label, type }
const groupByKey = new Map(); // group_key -> group

function indexFields(fields) {
  for (const f of fields || []) {
    if (f.key) fieldByKey.set(f.key, { name: f.name, label: f.label, type: f.type });
    if (f.sub_fields) indexFields(f.sub_fields);
    if (f.layouts) {
      const layouts = Array.isArray(f.layouts) ? f.layouts : Object.values(f.layouts);
      for (const l of layouts) indexFields(l.sub_fields);
    }
  }
}
for (const g of groups) {
  groupByKey.set(g.key, g);
  indexFields(g.fields);
}

/* ---------- filename slugs (unique) ---------- */
function slugify(s) {
  return s
    .toLowerCase()
    .replace(/\[tmpl\]/g, 'tmpl')
    .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue').replace(/ß/g, 'ss')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}
const slugCount = new Map();
const slugByGroup = new Map();
for (const g of groups) {
  let slug = slugify(g.title);
  if (slugCount.has(slug)) {
    slugCount.set(slug, slugCount.get(slug) + 1);
    slug = `${slug}-${g.key.replace('group_', '')}`;
  } else {
    slugCount.set(slug, 1);
  }
  slugByGroup.set(g.key, slug);
}

/* ---------- helpers ---------- */
const STRUCTURAL_TYPES = new Set(['tab', 'acfe_column', 'accordion']);

const TYPE_HINTS = {
  text: 'Einzeiliger Text.',
  textarea: 'Mehrzeiliger Text (ohne Formatierung).',
  wysiwyg: 'Formatierter Text (HTML über den WordPress-Editor).',
  image: 'Bild aus der Mediathek auswählen bzw. Attachment-ID setzen.',
  gallery: 'Mehrere Bilder aus der Mediathek auswählen (Liste von Attachment-IDs).',
  file: 'Datei aus der Mediathek auswählen bzw. Attachment-ID setzen.',
  oembed: 'URL eines einbettbaren Mediums (z. B. YouTube/Vimeo) eintragen.',
  url: 'Vollständige URL inkl. https:// eintragen.',
  email: 'E-Mail-Adresse eintragen.',
  number: 'Zahl eintragen.',
  range: 'Zahl innerhalb des angegebenen Bereichs wählen.',
  select: 'Einen Wert (bzw. bei Mehrfachauswahl mehrere) aus den Auswahlmöglichkeiten wählen.',
  radio: 'Genau einen Wert aus den Auswahlmöglichkeiten wählen.',
  checkbox: 'Einen oder mehrere Werte aus den Auswahlmöglichkeiten wählen.',
  button_group: 'Genau einen Wert aus den Auswahlmöglichkeiten wählen.',
  true_false: 'Ja/Nein-Schalter: 1 = aktiviert, 0 = deaktiviert.',
  link: 'Link-Objekt mit url, title und target befüllen.',
  page_link: 'Interne Seite/Beitrag auswählen (liefert URL).',
  post_object: 'Einen (oder mehrere) Beiträge des angegebenen Post-Types auswählen (Post-ID).',
  relationship: 'Beiträge des angegebenen Post-Types auswählen (Liste von Post-IDs).',
  taxonomy: 'Begriff(e) der angegebenen Taxonomie auswählen.',
  user: 'Benutzer auswählen.',
  date_picker: 'Datum wählen.',
  date_time_picker: 'Datum und Uhrzeit wählen.',
  time_picker: 'Uhrzeit wählen.',
  color_picker: 'Farbwert wählen (Hex).',
  google_map: 'Adresse/Koordinaten über die Kartensuche setzen.',
  repeater: 'Wiederholbare Zeilen; jede Zeile enthält die unten aufgeführten Unterfelder.',
  group: 'Feldgruppe; enthält die unten aufgeführten Unterfelder.',
  flexible_content: 'Flexible Inhalte; beliebige Abfolge der unten aufgeführten Layouts.',
  clone: 'Eingebettete (geklonte) Feldgruppe – Felder siehe verlinkte Dokumentation.',
  message: 'Nur Hinweistext im Backend, kein Eingabefeld.',
  acfe_slug: 'Slug (URL-tauglicher Bezeichner) eintragen.',
  acfe_post_statuses: 'Post-Status auswählen.',
  acfe_taxonomy_terms: 'Taxonomie-Begriff(e) auswählen.',
};

function esc(s) {
  return String(s == null ? '' : s).replace(/\|/g, '\\|').replace(/\r?\n/g, ' ').trim();
}

function fieldRefByKey(key) {
  const f = fieldByKey.get(key);
  if (!f) return `\`${key}\``;
  return f.name ? `\`${f.name}\` („${f.label}“)` : `„${f.label}“`;
}

function conditionalLogicText(cl) {
  if (!cl || !Array.isArray(cl) || cl.length === 0) return null;
  const orParts = cl.map((andGroup) => {
    const andParts = andGroup.map((rule) => {
      const op = { '==': '=', '!=': '≠', '==empty': 'ist leer', '!=empty': 'ist nicht leer', '==contains': 'enthält', '==pattern': 'entspricht Muster' }[rule.operator] || rule.operator;
      const ref = fieldRefByKey(rule.field);
      if (rule.operator === '==empty' || rule.operator === '!=empty') return `${ref} ${op}`;
      return `${ref} ${op} \`${rule.value}\``;
    });
    return andParts.join(' UND ');
  });
  return orParts.join(' ODER ');
}

function choicesLines(field) {
  if (!field.choices || Object.keys(field.choices).length === 0) return [];
  const lines = ['- **Auswahlmöglichkeiten:**'];
  for (const [val, label] of Object.entries(field.choices)) {
    lines.push(`  - \`${val}\` = ${label}`);
  }
  return lines;
}

function detailLines(f) {
  const d = [];
  if (f.default_value !== undefined && f.default_value !== '' && f.default_value !== null && !(Array.isArray(f.default_value) && f.default_value.length === 0)) {
    d.push(`- **Standardwert:** \`${JSON.stringify(f.default_value).replace(/^"|"$/g, '')}\``);
  }
  if (f.placeholder) d.push(`- **Platzhalter:** ${esc(f.placeholder)}`);
  if (f.maxlength) d.push(`- **Max. Länge:** ${f.maxlength}`);
  const isRows = f.type === 'repeater' || f.type === 'flexible_content' || f.type === 'gallery';
  if (f.min !== undefined && f.min !== '' && f.min !== 0) d.push(`- **${isRows ? 'Min. Einträge' : 'Min'}:** ${f.min}`);
  if (f.max !== undefined && f.max !== '' && f.max !== 0) d.push(`- **${isRows ? 'Max. Einträge' : 'Max'}:** ${f.max}`);
  if (f.return_format) d.push(`- **Rückgabeformat:** \`${f.return_format}\``);
  if (f.multiple) d.push('- **Mehrfachauswahl:** ja');
  if (f.allow_null) d.push('- **Leerer Wert erlaubt:** ja');
  if (f.post_type && f.post_type.length) d.push(`- **Post-Type(s):** ${[].concat(f.post_type).map((p) => `\`${p}\``).join(', ')}`);
  if (f.taxonomy && typeof f.taxonomy === 'string') d.push(`- **Taxonomie:** \`${f.taxonomy}\``);
  if (f.mime_types) d.push(`- **Erlaubte Dateitypen:** ${esc(f.mime_types)}`);
  if (f.type === 'repeater' && f.button_label) {
    d.push(`- **Button-Beschriftung:** „${esc(f.button_label)}“`);
  }
  if (f.type === 'true_false') {
    if (f.ui_on_text || f.ui_off_text) d.push(`- **Schalter-Texte:** an = „${esc(f.ui_on_text)}“, aus = „${esc(f.ui_off_text)}“`);
  }
  return d;
}

function cloneTargets(f) {
  const targets = [].concat(f.clone || []);
  return targets.map((t) => {
    if (t.startsWith('group_')) {
      const g = groupByKey.get(t);
      if (g) return `Feldgruppe „${g.title}“ → siehe \`${slugByGroup.get(t)}.md\``;
      return `Feldgruppe \`${t}\` (nicht im Export enthalten)`;
    }
    if (t.startsWith('field_')) {
      const ref = fieldByKey.get(t);
      return ref ? `Feld ${fieldRefByKey(t)}` : `Feld \`${t}\``;
    }
    return `\`${t}\``;
  });
}

/* ---------- field rendering (recursive) ---------- */
function renderFields(fields, depth, numberPrefix, out, ctx) {
  let n = 0;
  let currentTab = ctx.tab || null;

  for (const f of fields || []) {
    if (f.type === 'tab') {
      currentTab = f.label;
      continue;
    }
    if (f.type === 'acfe_column') continue;

    n += 1;
    const num = numberPrefix ? `${numberPrefix}.${n}` : String(n);
    const hLevel = Math.min(3 + depth, 6);
    const h = '#'.repeat(hLevel);
    const nameStr = f.name ? `\`${f.name}\`` : '_(ohne Feldname)_';
    out.push('', `${h} ${num}. ${nameStr} – ${f.label || '(ohne Label)'}`, '');

    out.push(`- **Feld-Key:** \`${f.key}\``);
    out.push(`- **Typ:** \`${f.type}\``);
    if (currentTab) out.push(`- **Tab:** ${currentTab}`);
    out.push(`- **Pflichtfeld:** ${f.required ? 'ja' : 'nein'}`);

    const cond = conditionalLogicText(f.conditional_logic);
    if (cond) out.push(`- **Nur sichtbar wenn:** ${cond}`);

    if (f.instructions) out.push(`- **Anweisung im Backend:** ${esc(f.instructions)}`);

    out.push(...detailLines(f));
    out.push(...choicesLines(f));

    if (f.type === 'clone') {
      const targets = cloneTargets(f);
      out.push(`- **Klont:** ${targets.join('; ')}`);
      if (f.prefix_name) out.push(`- **Feldnamen-Präfix:** ja – geklonte Felder werden als \`${f.name}_<feldname>\` gespeichert`);
    }

    if (f.type === 'message' && f.message) {
      out.push(`- **Hinweistext:** ${esc(f.message)}`);
    }

    const hint = TYPE_HINTS[f.type];
    if (hint) out.push(`- **Befüllung:** ${hint}`);

    if (f.sub_fields && f.sub_fields.length) {
      out.push('', `${'#'.repeat(Math.min(hLevel + 1, 6))} Unterfelder von \`${f.name || f.label}\``);
      renderFields(f.sub_fields, depth + 1, num, out, { tab: null });
    }

    if (f.layouts) {
      const layouts = Array.isArray(f.layouts) ? f.layouts : Object.values(f.layouts);
      let li = 0;
      for (const layout of layouts) {
        li += 1;
        const lh = '#'.repeat(Math.min(hLevel + 1, 6));
        out.push('', `${lh} Layout ${num}.L${li}: \`${layout.name}\` – ${layout.label}`, '');
        out.push(`- **Layout-Key:** \`${layout.key}\``);
        if (layout.min !== '' && layout.min !== undefined) out.push(`- **Min. Verwendungen:** ${layout.min}`);
        if (layout.max !== '' && layout.max !== undefined) out.push(`- **Max. Verwendungen:** ${layout.max}`);
        if (layout.sub_fields && layout.sub_fields.length) {
          renderFields(layout.sub_fields, depth + 2, `${num}.L${li}`, out, { tab: null });
        } else {
          out.push('', '_Dieses Layout hat keine eigenen Felder._');
        }
      }
    }
  }
  return n;
}

/* ---------- flat overview table ---------- */
function flattenForTable(fields, prefix, tab, rows) {
  let currentTab = tab;
  for (const f of fields || []) {
    if (f.type === 'tab') { currentTab = f.label; continue; }
    if (f.type === 'acfe_column') continue;
    const pathName = f.name ? (prefix ? `${prefix}.${f.name}` : f.name) : '—';
    rows.push({ path: pathName, label: f.label || '', type: f.type, required: f.required ? 'ja' : 'nein', tab: currentTab || '—' });
    if (f.sub_fields) flattenForTable(f.sub_fields, f.name ? (prefix ? `${prefix}.${f.name}` : f.name) : prefix, null, rows);
    if (f.layouts) {
      const layouts = Array.isArray(f.layouts) ? f.layouts : Object.values(f.layouts);
      for (const l of layouts) {
        const lPrefix = `${f.name ? (prefix ? `${prefix}.${f.name}` : f.name) : prefix}[${l.name}]`;
        flattenForTable(l.sub_fields, lPrefix, null, rows);
      }
    }
  }
}

/* ---------- location rules ---------- */
function locationText(location) {
  if (!location || !location.length) return ['_Keine Zuordnung definiert._'];
  const opMap = { '==': 'ist', '!=': 'ist nicht' };
  return location.map((andGroup) => {
    const parts = andGroup.map((r) => `\`${r.param}\` ${opMap[r.operator] || r.operator} \`${r.value}\``);
    return `- ${parts.join(' UND ')}`;
  });
}

/* ---------- generate ---------- */
fs.mkdirSync(OUT_DIR, { recursive: true });

const indexRows = [];

for (const g of groups) {
  const slug = slugByGroup.get(g.key);
  const out = [];

  out.push('---');
  out.push(`title: "${g.title.replace(/"/g, '\\"')}"`);
  out.push('category: field-groups');
  out.push(`slug: ${slug}`);
  out.push('source_files:');
  out.push(`  - ${sourceRel}`);
  out.push('related_docs: []');
  out.push(`generated: ${generatedDate}`);
  out.push('status: complete');
  out.push(`acf_group_key: ${g.key}`);
  out.push('---');
  out.push('');
  out.push(`# Feldgruppe: ${g.title}`);
  out.push('');
  out.push('## Übersicht');
  out.push('');
  out.push(`- **Gruppen-Key:** \`${g.key}\``);
  out.push('- **Registrierung:** Admin-JSON-Export (Legacy-Pfad)');
  out.push(`- **Aktiv:** ${g.active ? 'ja' : 'nein'}`);
  if (g.description) out.push(`- **Beschreibung:** ${esc(g.description)}`);
  out.push(`- **Anzahl Felder (oberste Ebene, ohne Struktur-Felder):** ${(g.fields || []).filter((f) => !STRUCTURAL_TYPES.has(f.type)).length}`);
  out.push('');
  out.push('## Wo wird die Feldgruppe angezeigt?');
  out.push('');
  out.push(...locationText(g.location));
  out.push('');

  const rows = [];
  flattenForTable(g.fields, '', null, rows);

  out.push('## Felder auf einen Blick');
  out.push('');
  if (rows.length) {
    out.push('| Feld (Pfad) | Label | Typ | Pflicht | Tab |');
    out.push('|---|---|---|---|---|');
    for (const r of rows) {
      out.push(`| \`${esc(r.path)}\` | ${esc(r.label)} | \`${r.type}\` | ${r.required} | ${esc(r.tab)} |`);
    }
  } else {
    out.push('_Diese Feldgruppe enthält keine Eingabefelder._');
  }
  out.push('');
  out.push('## Felder im Detail');

  const body = [];
  const count = renderFields(g.fields, 0, '', body, { tab: null });
  if (count === 0) {
    out.push('', '_Diese Feldgruppe enthält keine Eingabefelder (nur Struktur- oder Hinweis-Elemente)._');
  } else {
    out.push(...body);
  }

  out.push('');
  fs.writeFileSync(path.join(OUT_DIR, `${slug}.md`), out.join('\n'), 'utf8');
  indexRows.push({ slug, title: g.title, key: g.key, fields: rows.length });
}

/* ---------- index file ---------- */
const idx = [];
idx.push('# ACF-Feldgruppen – Dokumentationsindex');
idx.push('');
idx.push('Eine Datei pro Feldgruppe. Jede Datei beschreibt, wie die Feldgruppe zu befüllen ist.');
idx.push('');
idx.push('Neu generieren (aus dem Repo-Root, nimmt den neuesten Export aus `acf_export/`):');
idx.push('');
idx.push('```sh');
idx.push('node docs/field-groups/generate.js');
idx.push('```');
idx.push('');
idx.push('Oder eine bestimmte Export-Datei angeben (Pfad relativ zum aktuellen Verzeichnis):');
idx.push('');
idx.push('```sh');
idx.push('node docs/field-groups/generate.js acf_export/acf-export-2026-07-03.json');
idx.push('```');
idx.push('');
idx.push('| Datei | Feldgruppe | Gruppen-Key | Felder |');
idx.push('|---|---|---|---|');
for (const r of indexRows) {
  idx.push(`| [\`${r.slug}.md\`](./${r.slug}.md) | ${esc(r.title)} | \`${r.key}\` | ${r.fields} |`);
}
idx.push('');
fs.writeFileSync(path.join(OUT_DIR, 'README.md'), idx.join('\n'), 'utf8');

console.log(`Fertig: ${indexRows.length} Feldgruppen-Dateien + README.md in ${path.relative(ROOT, OUT_DIR)} (Quelle: ${sourceRel})`);
