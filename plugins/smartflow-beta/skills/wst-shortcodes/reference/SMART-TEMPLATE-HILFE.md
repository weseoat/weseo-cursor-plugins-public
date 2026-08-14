# WESEO Smart Template & Smart Template Builder – Gesamthilfe

> Konsolidierter Dokumentations-Snapshot der installierten WordPress-Plugins. Enthalten sind die statische Admin-Hilfe, der vollständige dynamische Hilfe-Katalog inklusive ausgeblendeter und optional nicht registrierter Einträge, sämtliche dort hinterlegten Parameter und Optionen, die Builder-Ergänzungen sowie der vollständige Changelog.

- **Smart Template:** 6.19.4
- **Smart Template Builder:** 1.6.2
- **WordPress:** 6.9.4
- **Locale:** `de_DE_formal`
- **Exportiert:** 2026-07-16 12:48:43 CEST
- **Hilfe-Kategorien:** 30
- **Dokumentierte Hilfe-Einträge:** 184 (4 davon in der aktuellen Admin-Oberfläche abhängigkeitsbedingt ausgeblendet; 24 weitere CMB2-Einträge werden bei inaktivem CMB2 gar nicht registriert)

## Verwendung dieses Dokuments

Dieses Dokument ist eine lesbare Momentaufnahme der installierten Versionen. Maßgeblich für das Laufzeitverhalten bleibt der PHP-Code. Dynamische Standardwerte spiegeln den Stand dieser WordPress-Installation zum Exportzeitpunkt wider. Einträge mit dem Hinweis „aktuell ausgeblendet“ existieren im Plugin, werden in der Admin-Hilfe aber wegen einer fehlenden oder inaktiven Integration nicht angezeigt.

## Inhaltsübersicht

- Überblick, Installation und Konfiguration
- Smart Templates und Post-Type-Überschreibung
- Vollständiger Shortcode- und SmartTag-Katalog
- Helper-Funktionen und Integrationen
- Smart Template Builder: Rendering, Einstellungen, ACF, Polylang und Frontend-Bearbeitung
- Vollständiger Smart-Template-Changelog

### Dynamische Hilfe-Kategorien

- **Allgemein** (`general`): 21 Einträge, davon 21 aktuell sichtbar
- **Mehrsprachigkeit** (`multilingual`): 3 Einträge, davon 3 aktuell sichtbar
- **Formatierung** (`formatting`): 8 Einträge, davon 8 aktuell sichtbar
- **Lightbox** (`lightbox`): 2 Einträge, davon 2 aktuell sichtbar
- **Benutzer** (`user`): 3 Einträge, davon 3 aktuell sichtbar
- **Account** (`account`): 5 Einträge, davon 5 aktuell sichtbar
- **Fernsteuern** (`remote`): 5 Einträge, davon 5 aktuell sichtbar
- **Beitrags Felder** (`post`): 18 Einträge, davon 18 aktuell sichtbar
- **Wetter** (`weather`): 19 Einträge, davon 19 aktuell sichtbar
- **Page Builder** (`page_builder`): 1 Einträge, davon 0 aktuell sichtbar
- **Grid Plugins** (`grid`): 3 Einträge, davon 1 aktuell sichtbar
- **ACF Grundlage** (`acf_general`): 5 Einträge, davon 5 aktuell sichtbar
- **ACF Inhalt** (`acf_content`): 8 Einträge, davon 8 aktuell sichtbar
- **ACF Auswahl** (`acf_selection`): 4 Einträge, davon 4 aktuell sichtbar
- **ACF Relation** (`acf_relation`): 5 Einträge, davon 5 aktuell sichtbar
- **ACF Erweitert** (`acf_advanced`): 8 Einträge, davon 8 aktuell sichtbar
- **ACF Layout** (`acf_layout`): 2 Einträge, davon 1 aktuell sichtbar
- **ACF Formular** (`acf_form`): 1 Einträge, davon 1 aktuell sichtbar
- **Bedingte Logik** (`condition`): 8 Einträge, davon 8 aktuell sichtbar
- **Lesezeichen** (`bookmark`): 5 Einträge, davon 5 aktuell sichtbar
- **Gefällt mir** (`like`): 3 Einträge, davon 3 aktuell sichtbar
- **WooCommerce** (`woocommerce`): 14 Einträge, davon 14 aktuell sichtbar
- **Befehle** (`command`): 8 Einträge, davon 8 aktuell sichtbar
- **SmartTags** (`smarttag`): 1 Einträge, davon 1 aktuell sichtbar
- **CMB2 Grundlage** (`cmb2_general`): 4 Einträge, bei aktivem CMB2 registriert
- **CMB2 Inhalt** (`cmb2_content`): 8 Einträge, bei aktivem CMB2 registriert
- **CMB2 Auswahl** (`cmb2_selection`): 4 Einträge, bei aktivem CMB2 registriert
- **CMB2 Relation** (`cmb2_relation`): 2 Einträge, bei aktivem CMB2 registriert
- **CMB2 Erweitert** (`cmb2_advanced`): 5 Einträge, bei aktivem CMB2 registriert
- **CMB2 Layout** (`cmb2_layout`): 1 Einträge, bei aktivem CMB2 registriert

---

## 1. Überblick

WESEO Smart Template ist ein WordPress-Toolkit für wiederverwendbare Smart Templates, Shortcodes, SmartTags und Integrationen. Das separate Plugin Smart Template Builder ergänzt ACF Flexible Content um einen seitenorientierten Section-Builder.

### Kernfunktionen

- Wiederverwendbare Smart Templates für Beiträge, Seiten und Custom Post Types
- Post-Type-weite oder seitenindividuelle Template-Überschreibung
- Bookmark-/Merklisten und Like-System
- Swiper-Slider und Fancybox-Lightboxen
- ACF- und CMB2-Feldausgabe einschließlich Repeater und Flexible Content
- ACF Google Maps mit Routing, Suche, Clustering und Markern
- Bedingte Logik, Schleifen, Variablen, SmartTags und Konvertierungsbefehle
- Wetterbasierte Inhaltssteuerung via OpenWeatherMap
- PDF-, QR-Code-, ZIP-, vCard-, ICS- und Google-Kalender-Ausgaben
- Polylang-/WPML-Mehrsprachigkeit
- WooCommerce-, WP Grid Builder-, BeTheme-, Elementor-, WP Rocket- und Yoast-Integrationen
- Dynamische AJAX-Container sowie Typing- und Counting-Animationen

### Allgemeine Shortcode-Regeln

- Shortcodes können in Smart Templates, Beiträgen, Seiten und unterstützten Template-Kontexten eingesetzt werden.
- Der optionale Parameter `id="123"` kann grundsätzlich verwendet werden, um eine konkrete Post-ID anzusprechen.
- Werte einer ACF-Optionsseite sind über `id="options"` erreichbar.
- Verschachtelte Inhalte verwenden öffnende und schließende Shortcode-Tags.
- SmartTags werden beim Rendern durch kontextbezogene Werte ersetzt und können Konvertierungsbefehle enthalten.

## 2. Installation und Anforderungen

### Anforderungen

- WordPress 5.1.0 oder höher
- PHP 8.0 oder höher
- Optional: ACF Pro, ACF Extended, CMB2, WP Grid Builder und WooCommerce

### Installation

1. Plugin-Ordner nach `/wp-content/plugins/weseo-smart-template/` hochladen.
2. Plugin unter **WordPress → Plugins** aktivieren.
3. Unter **Smart Template → Einstellungen** konfigurieren.
4. Shortcodes in Beiträgen, Seiten oder Templates verwenden.
5. Für den ACF-Section-Builder zusätzlich `weseo-smart-template-builder` aktivieren.

## 3. Konfiguration

- **Allgemein:** Grundeinstellungen und Post-Type-Konfiguration.
- **Bookmarks:** Merklisten, Cookie-Laufzeiten und Zielseiten.
- **Likes:** Like-System, Speicherung und Darstellung.
- **Copyright:** Automatische Copyright-Ausgabe.
- **Wetter:** OpenWeatherMap-Konfiguration, Standort und Wetterlogik.
- **ACF:** Google-Maps-API-Key, Kartenstile und ACF-bezogene Optionen.
- **Integrationen:** Externe Dienste, E-Mail-Zustellung und E-Mail-Beschränkungen.
- **Mehrsprachigkeit:** Sprachumschalter und Übersetzungsintegration.
- **WooCommerce:** Shop- und Produktintegration.
- **Wartungsmodus:** Website temporär offline schalten und Inhalte ein-/ausschließen.
- **HTML Minify:** HTML-Ausgabe komprimieren.
- **Erweitert:** Seiteneinrichtung, Admin-Funktionen, Logs und technische Optionen.
- **Aktualisierungen:** Automatische Plugin-Updates konfigurieren.

## 4. Post-Type-Template-Überschreibung

Smart Template kann die vollständige Frontend-Ausgabe eines Post Types durch ein Smart Template ersetzen. So erhalten alle Einzelbeiträge dieses Typs ein einheitliches Layout, während Titel, Datum, Metafelder und andere Post-Daten über Shortcodes und SmartTags verfügbar bleiben.

### Einrichtung

1. **Smart Template → Einstellungen → Post Types** öffnen.
2. Gewünschten Post Type auswählen.
3. Unter **Template Assignment** ein Smart Template zuweisen.
4. Optional bei Seiten: **Override** aktivieren, um pro Seite ein abweichendes Template auszuwählen oder die globale Zuordnung zu deaktivieren.
5. Unter **Metadata** Metafelder definieren, die nicht überschrieben werden sollen, beispielsweise SEO- oder Plugin-Metadaten.

### Beispiel

```html
<article class="post-template">
    <header>
        <h1>[wst_post_title]</h1>
        <p class="meta">[wst_post_date] | [wst_post_author field="display_name"]</p>
    </header>

    <div class="featured-image">
        [wst_post_thumbnail size="large"]
    </div>

    <div class="content">
        [wst_post_content]
    </div>

    <footer>
        <div class="categories">[wst_post_terms taxonomy="category"]</div>
        <div class="tags">[wst_post_terms taxonomy="post_tag"]</div>
    </footer>
</article>
```

---

## 5. Vollständiger Shortcode- und SmartTag-Katalog

Die folgenden Daten stammen aus `wst_get_all_help_tabs()` und den sechs zusätzlich im Plugin vorhandenen CMB2-Hilfeklassen. Anders als die Admin-Oberfläche enthält dieser Export auch abhängigkeitsbedingt ausgeblendete sowie bei inaktiver Integration nicht registrierte Einträge.

### 5.1 Allgemein

- **Interne ID:** `general`
- **Einträge:** 21

#### [wst_copyright]

- **Shortcode:** `wst_copyright`

Gibt automatisch das richtige [Copyright HTML](https://naturelhotels.com.weseo.dev/wp-admin/edit.php?post_type=smart_template&page=wst-settings&tab=copyright) für die jeweilige Seite (Startseite oder Unterseite) zurück.

**Grundsyntax**

```text
[wst_copyright]
```

**Parameter und Optionen**

- `output` — Bestimmte Ausgabe des Copyright HTML: `front_page` oder `sub_page`. *(optional)*

#### [wst_back_button]

- **Shortcode:** `wst_back_button`

Erstellt einen **Zurück Button**, welcher automatisch den richtigen zurück Link ermittelt.

**Grundsyntax**

```text
[wst_back_button]
```

**Parameter und Optionen**

- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Zurück`)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional; Standard: `Zurück`)*
- `href` — Das href Attribut des Elements. *(optional; Standard: `Zurück`)*
- `target` — Die Zielfensterbasis des Elements: `_self | _blank | _parent | _top`. *(optional; Standard: `_self`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*

#### [wst_posts]

- **Shortcode:** `wst_posts`

Gibt jeden **Beitrag** eines bestimmten Inhaltstyps als Schleife zurück.

**Grundsyntax**

```text
[wst_posts]row_content[/wst_posts]
```

**Parameter und Optionen**

- `post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes. *(optional; Standard: `post`)*
- `post_status` — Name oder Liste (durch Komma getrennt) des Beitragsstatus. *(optional)*
- `posts_per_page` — Die Anzahl der anzuzeigenden Beiträge pro Seite. *(optional)*
- `posts_per_page_tablet` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem Tablet. *(optional)*
- `posts_per_page_mobile` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem mobilen Gerät (Smartphone). *(optional)*
- `offset` — Die Anzahl der zu versetzenden oder zu übergehenden Beiträgen. *(optional)*
- `related` — Den eigenen Beitrag in der Query nicht ausgegeben. *(optional)*
- `post_ids` — Komma getrennte Beitrag-IDs, um nur diese Beiträge in der Query anzuzeigen. *(optional)*
- `post_names` — Durch Komma getrennte Beitrags-Titelformen, sodass nur diese Beiträge in der Abfrage angezeigt werden. *(optional)*
- `post_parent` — Gibt nur untergeordnete Beiträge von einem übergeordneten Beitrag aus. *(optional)*
  - `current` — Gibt die ID des Beitrages zurück.
  - `parent` — Gibt die ID des übergeordneten Beitrags zurück.
  - `parent|level=1` — Gibt die ID eines spezifischen (Level) übergeordneten Beitrags zurück.
  - `post_id` — Benutzerdefinierte ID eines Beitrages

**Taxonomie Parameter**

- `taxonomy_relation` — Beziehung der verschachtelten Taxonomy Blöcke. *(optional; Standard: `OR`)*
- `reset_taxonomy_query` — Setzt die aktuelle Taxonomie Abfrage zurück. *(optional; Standard: `0`)*
- `reset_posts_query` — Setzt die aktuelle Beitrags (post__in) Abfrage zurück. *(optional; Standard: `0`)*

**Spezifische Taxonomie-Begriffe**

- `my_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Kategorien einer bestimmten Taxonomie. *(optional; Beispiel: `product_cat="clothes"`)*
- `my_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `my_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `my_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Spezifische Begriffe**

- `term_list` — Name oder Liste (durch Komma getrennt) der eingeschränkten Kategorien. *(optional; Beispiel: `term_list="1234,4321"`)*
- `term_list_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `term_list_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `term_list_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Selbe Beitrags Taxonomy**

- `same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional)*
- `same_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `same_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `same_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Primäre Beitrags Taxonomie**

- `primary_post_taxonomy` — Name oder Liste (durch ein Komma getrennt) der eingeschränkten Taxonomie, welche die Primäre Kategorie enthält. *(optional)*
- `primary_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `primary_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `primary_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Advanced Custom Fields**

- `acf_id` — Die ACF ID, unter welcher der Wert gespeichert wird. *(optional)*
- `acf_post_field` — Name des zu einschränkenden ACF Beitrag Objekt Feldes. *(optional)*
- `acf_taxonomy_field` — Name des zu einschränkenden ACF Taxonomy Feldes. *(optional)*
- `acf_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `acf_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `acf_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Lesezeichen**

- `user_bookmark_posts` — Name der Merkliste, um alle gespeicherten Beiträge des Benutzers in der Query auszugeben. *(optional)*

**Gefällt mir**

- `user_like_posts` — Alle gespeicherten Like-Beiträge des Benutzers in der Abfrage ausgeben. *(optional; Standard: `0`)*

**WooCommerce**

- `wc_product_upsell_products` — Alle Zusatzverkäufe (UpSells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_product_cross_sell_products` — Alle Querverkäufe (Cross-Sells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_cart_cross_sell_products` — Ausgabe von Cross-Sell-Produkten basierend auf den Artikeln im Warenkorb. *(optional; Standard: `0`)*
- `wc_product_gallery_images` — Alle Galerie Bilder eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `user_wc_recently_viewed_products` — Gibt alle kürzlich angesehenen WooCommerce-Produkte des Benutzers in der Abfrage aus. *(optional; Standard: `0`)*

**Wetter**

- `weather` — Name oder Liste (Beistrich getrennt) der einschränkenden Wetter Kategorien. *(optional)*
  - `live` — Kategorie der aktuellen Wetterabfrage (Keine Daten bereitgestellt.).
  - `thunderstorm` — **Gewitter** (Gewitter mit leichtem Regen, Gewitter mit Regen, Gewitter mit starkem Regen, Leichtes Gewitter, Gewitter, Starkes Gewitter, Wiederkehrende Gewitter, Gewitter mit leichtem Nieselregen, Gewitter mit Nieselregen, Gewitter mit starkem Nieselregen)
  - `drizzle` — **Nieselregen** (Leichter Nieselregen, Nieselregen, Starker Nieselregen, Leichter nieselartiger Niederschlag, Nieselartiger Niederschlag, Starker nieselartiger Niederschlag, Kurzer Regenschauer und Nieselregen, Starker Regenschauer und Nieselregen, Kurzer, nieselartiger Schauer)
  - `rain` — **Regen** (Leichter Regen, Mäßiger Regen, Starker Regen, Sehr starker Regen, Starkregen, Eisregen, Kurzer, leichter Regenschauer, Kurzer Regenschauer, Kurzer, starker Regenschauer, Wiederkehrende kurze Regenschauer)
  - `snow` — **Schneefall** (Leichter Schneefall, Schneefall, Starker Schneefall, Graupel, Kurzer Graupelschauer, Leichter Schneeregen, Schneeregen, Kurzer, leichter Schneeschauer, Kurzer Schneeschauer, Kurzer, starker Schneeschauer)
  - `atmosphere` — **Atmosphäre** (Dunst, Nebel, Rauch, Dunst, trübe Sicht, Staub und Sandwirbel, Nebel, Sand, Staub, Vulkanasche, Windböen, Tornado)
  - `clear` — **Heiter** (Klarer Himmel)
  - `clouds` — **Wolken** (Leicht bewölkt, Wolkig, Stark bewölkt, Bedeckt)
- `weather_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `weather_forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `weather_forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*

**Datumsfelder**

- `date_year` — 4-stelliges Jahr (z. B. 2011). *(optional)*
- `date_month` — Monatsnummer (von 1 bis 12). *(optional)*
- `date_week` — Woche des Jahres (von 0 bis 53). *(optional)*
- `date_day` — Tag des Monats (von 1 bis 31). *(optional)*
- `date_hour` — Stunde (von 0 bis 23). *(optional)*
- `date_minute` — Minute (von 0 bis 59). *(optional)*
- `date_second` — Sekunde (0 bis 59). *(optional)*
- `date_after` — Datum, nach dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatiblen String. *(optional)*
- `date_before` — Datum, vor dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatible Strings. *(optional)*
- `date_inclusive` — Für after/before, ob der genaue Wert abgeglichen werden soll oder nicht. *(optional; Standard: `0`)*
- `date_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `date_column` — Beitrags-Spalte, nach welcher abgefragt werden soll. *(optional; Standard: `post_date`)*
- `date_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Individuelle Felder**

- `meta_key` — Der Name des Post Metafeldes. *(optional)*
- `meta_value` — Der zu überprüfende Wert. *(optional)*
  - `post_id` — Gibt die ID des Beitrages zurück.
  - `my_meta_key` — Gibt den Wert eines übergebenen Beitrag-Metafeldes zurück.
  - `today` — Gibt das aktuelle Datum (2026-07-16) zurück.
  - `this_weekday` — Gibt den numerischer Tag (4), der aktuellen Woche zurück.
  - `this_week` — Gibt die Wochennummer (29), des aktuellen Jahres zurück (Woche beginnt am Montag).
  - `this_day` — Gibt die aktuelle Tageszahl (16), ohne führende Nullen zurück.
  - `this_month` — Gibt die aktuelle Monatszahl (7), ohne führende Nullen zurück.
  - `this_year` — Gibt das aktuelle Jahr (2026), als vierstellige Zahl zurück.
  - `page_on_front` — Gibt die **ID (10)** der Seite, welche als **Startseite (Startseite)** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `page_for_posts` — Gibt die **ID (0)** der Seite, welche als **Beitragsseite ()** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
- `meta_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `meta_type` — Der Feldtyp: `NUMERIC, BINARY, CHAR, DATE, DATETIME, DECIMAL, SIGNED, TIME, UNSIGNED`. *(optional; Standard: `CHAR`)*
- `meta_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Reihenfolge**

- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional)*
- `orderby_meta_key` — Name des benutzerdefinierten Feldes nach dem sortiert werden soll. *(optional)*
- `orderby_post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes, nach dem sortiert werden soll. *(optional)*
- `orderby_taxonomy_term` — Name oder Liste (durch Pipe getrennt) des Taxonomie Kategorie, nach dem sortiert werden soll. *(optional; Beispiel: `category(category_1,category_2)=date/DESC|post_tag(tag_1,tag_2)=title/ASC`)*
- `orderby_same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der sortierten Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `orderby_primary_post_taxonomy` — Name oder Liste (durch Pipe getrennt) der sortierten Taxonomie mit der primären Beitragskategorie. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*

**Zusätzliche Informationen**

###### Beiträge als Schleife verwenden.

Folgender Shortcode generiert eine Schleife, welche alle Beiträge der Reihe nach abarbeitet.

```text
[wst_posts post_type='post']
	[wst_post_title id='{{post_id/wst_posts}}']
[/wst_posts]
```

Jeder Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{post_id/name_of_the_loop}}` angegeben werden (siehe Beispiel).

#### [wst_terms]

- **Shortcode:** `wst_terms`

Gibt jeden **Begriff** aus einer gegebenen Taxonomie als Schleife zurück.

**Grundsyntax**

```text
[wst_terms taxonomy='category']row_content[/wst_terms]
```

**Parameter und Optionen**

- `taxonomy` — Name der Taxonomie. Wenn kein Name anagegeben ist, werden alle Taxonomie des selben Inhaltstypes einbezogen. *(Pflicht; Standard: `category`)*
- `orderby` — Name des Feldes `name | count | slug | term_group | term_order | term_id | none` nach dem sortiert werden soll. *(optional)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `ASC`)*
- `hide_empty` — Gibt an, ob Begriffe ausgeblendet werden sollen, die keinem Beitrag zugeordnet sind. *(optional; Standard: `0`)*
- `include` — Komma / Leerzeichen getrennte Zeichenfolge der einzuschließenden Term-IDs. *(optional)*
- `exclude` — Komma / Leerzeichen getrennte Zeichenfolge der auszuschließenden Term-IDs. *(optional)*
- `exclude_tree` — Komma / Leerzeichen getrennte Zeichenfolge der auszuschließenden Term-IDs, zusammen mit allen Nachkommen. *(optional)*
- `number` — Maximale Anzahl der zurückgegebenen Begriffe. *(optional)*
- `offset` — Die Nummer, um die die Abfrage der Begriffe versetzt werden soll. *(optional)*
- `name` — Name oder durch Komma / Leerzeichen getrennte Zeichenfolge von Namen, für die Begriffe zurückgegeben werden sollen. *(optional)*
- `slug` — Titelform oder durch Komma / Leerzeichen getrennte Zeichenfolge von Titelformen, für die der Begriff zurückgegeben werden soll. *(optional)*
- `hierarchical` — Gibt an, ob Begriffe mit nicht leeren Nachkommen eingeschlossen werden sollen. *(optional)*
- `search` — Suchkriterien zur Übereinstimmung von Begriffen Wird vorher und nachher mit Platzhaltern formatiert. *(optional)*
- `name__like` — Begriffe mit Kriterien abrufen, bei denen ein Begriff LIKE "name__like" lautet. *(optional)*
- `description__like` — Begriffe abrufen, bei denen die Beschreibung LIKE "description__like" lautet. *(optional)*
- `pad_counts` — Gibt an, ob die Anzahl der Kind-Begriffe in der Menge der Objektvariablen "count" aufgefüllt werden soll. *(optional; Standard: `0`)*
- `get` — Gibt an, ob Begriffe unabhängig von ihrer Herkunft zurückgegeben werden sollen oder ob die Begriffe leer sind. Akzeptiert "all" oder leer. *(optional)*
- `child_of` — Term-ID, um Kind-Begriffe abzurufen. Wenn mehrere Taxonomien übergeben werden, wird "child_of" ignoriert. *(optional; Standard: `0`)*
- `parent` — Übergeordnete Term-ID, um direkt untergeordnete Begriffe abzurufen. *(optional)*
- `childless` — Auf Begriffe einschränken, die keine Kinder haben. Dieser Parameter hat keine Auswirkung auf nicht hierarchische Taxonomien. *(optional; Standard: `0`)*
- `meta_key` — Begrenzen der Begriffe auf einem bestimmten Metadaten-Schlüssel. Kann zusammen mit meta_value verwendet werden. *(optional)*
- `meta_value` — Begrenzen der Begriffe auf einem bestimmten Metadaten-Wert. Kann zusammen mit meta_key verwendet werden. *(optional)*
- `meta_type` — MySQL-Datentyp, in den der meta_value für Vergleiche umgewandelt wird. *(optional)*
- `meta_compare` — Vergleichsoperator zum Testen von "meta_value". *(optional)*

**Zusätzliche Informationen**

###### Die Beitrags Begriffe als Schleife verwenden

Der folgender Shortcode verwandelt das Feld post terms in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_terms taxonomy='category']
	[is_parent]Hauptkategorie[/is_parent][is_children]Kindkategorie[/is_children]
	{{name/wst_terms}} - [wst_acf_image id='{{acf_term_id/wst_terms}}' field='field_name' size='full']
	[wst_if field='term_name' compare='=' value='News' id='{{term_id/wst_terms}}']Is a news term![/wst_if]
[/wst_terms]
```

Es können alle [WP_Term](https://codex.wordpress.org/Function_Reference/wp_get_post_terms#Return_Values) Felder `term_id | name | slug | term_group | term_taxonomy_id | taxonomy | description | parent | count` als Platzhalter im Inhalt der Schleife verwenden werden.

Platzhalter können wie folgt maskiert werden: `[term_id]` or `#term_id#`.

Falls bestimmte **ACF Felder** einer Kategorie zugeordnet wurden, so kann jeder Smart Template Shortcode im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{acf_term_id/loop_name}}` angegeben werden (siehe Beispiel).

###### Zusätzliche Shortcodes

 `[is_parent]My Content[/is_parent]` Gibt den anzuzeigenden Inhalt nur für einen Eltern-Begriff zurück.

 `[is_children]My Content[/is_children]` Gibt den anzuzeigenden Inhalt nur für einen Kind-Begriff zurück.

 `[is_first_term]My Content[/is_first_term]` Gibt den anzuzeigenden Inhalt nur für den ersten Begriff zurück.

 `[is_last_term]My Content[/is_last_term]` Gibt den anzuzeigenden Inhalt nur für den letzten Begriff zurück.

#### [wst_background_image]

- **Shortcode:** `wst_background_image`

Erzeugt ein Stylesheet mit einer Hintergrundbild Eigenschaft eines Elements.

**Grundsyntax**

```text
[wst_background_image selector='element']Bild Quelle (ID oder URL)[/wst_background_image]
```

**Parameter und Optionen**

- `selector` — Zeigt auf das HTML-Element, für das Sie ein Hintergrundbild festlegen möchten. *(Pflicht; Standard: `element`)*
- `size` — Name der Standard Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `full`)*
- `media_size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.) für diesen Bildschirm. *(optional)*
- `media_min_width` — Die minimale Breite (in Pixel) des Bildschirms. *(optional)*
- `media_max_width` — Die maximale Breite (in Pixel) des Bildschirms. *(optional)*

**Zusätzliche Informationen**

###### Hintergrundbild mit mehreren Medienabfragen:

Der folgende Shortcode erstellt ein Hintergrundbild mit mehreren Medienabfragen.

```text
[wst_background_image selector='element' size='full' media_size='thumbnail' media_min_width='150' media_max_width='150' media_size_2='large' media_min_width_2='600' media_max_width_2='600']
	Bild Quelle (ID oder URL)
[/wst_background_image]
```

#### [wst_image]

- **Shortcode:** `wst_image`

Gibt ein standard **Bild** Element zurück.

**Grundsyntax**

```text
[wst_image src='1234']
```

**Parameter und Optionen**

- `src` — Die Bild-ID, URL oder Dateiname. *(Pflicht; Standard: `1234`)*
- `size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `thumbnail`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `attr` — HTML Attribute des Bildes als Query String. *(optional)*
- `inline` — Das Bild inline als Data-URL (Base64-kodiert für Nicht-SVG-Formate) oder direkt als rohen SVG-Code einbetten. *(optional)*
- `srcset` — Berechnet die Bilder, welche in ein srcset-Attribut aufgenommen werden sollen. *(optional; Standard: `1`)*

#### [wst_gallery]

- **Shortcode:** `wst_gallery`

Gibt eine Standard WordPress Galerie zurück.

**Grundsyntax**

```text
[wst_gallery]Bild-IDs Liste (Komma getrennt).[/wst_gallery]
```

**Zusätzliche Informationen**

Es können alle Parameter des [WordPress Galerie Shortcodes](https://codex.wordpress.org/Gallery_Shortcode) verwendet werden.

#### [wst_swiper_gallery]

- **Shortcode:** `wst_swiper_gallery`

Erstellt eine Swiper Galerie.

**Grundsyntax**

```text
[wst_swiper_gallery]Bild-IDs Liste (Komma getrennt).[/wst_swiper_gallery]
```

**Parameter und Optionen**

- `ids` — Durch Kommata getrennte Anhang-IDs, um nur die Bilder aus diesen Anhängen anzuzeigen. *(optional)*
- `size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `thumbnail`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional)*
- `attr` — HTML Attribute des Bildes als Query String. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `link_file` — Swiper-Elemente klickbar machen. *(optional; Standard: `1`)*
- `slides_to_show` — Slides in Ansicht *(optional; Standard: `auto`)*
  - `auto` — Automatische Slides in Ansicht.
  - `1` — 1 Slide pro Ansicht
  - `2` — 2 Slides pro Ansicht
  - `3` — 3 Slides pro Ansicht
  - `4` — 4 Slides pro Ansicht
  - `5` — 5 Slides pro Ansicht
  - `6` — 6 Slides pro Ansicht
  - `7` — 7 Slides pro Ansicht
  - `8` — 8 Slides pro Ansicht
  - `9` — 9 Slides pro Ansicht
  - `10` — 10 Slides pro Ansicht
- `slides_to_scroll` — Anzahl der Slides zum scrollen *(optional; Standard: `3`)*
  - `1` — 1 Slide zum Scrollen
  - `2` — 2 Slides zum Scrollen
  - `3` — 3 Slides zum Scrollen
  - `4` — 4 Slides zum Scrollen
  - `5` — 5 Slides zum Scrollen
  - `6` — 6 Slides zum Scrollen
  - `7` — 7 Slides zum Scrollen
  - `8` — 8 Slides zum Scrollen
  - `9` — 9 Slides zum Scrollen
  - `10` — 10 Slides zum Scrollen
- `space_between` — Abstand zwischen den Slides in px. *(optional; Standard: `30`)*
- `navigation` — Navigation *(optional; Standard: `both`)*
  - `none` — Keine
  - `both` — Pfeile und Navigationspunkte
  - `arrows` — Pfeile
  - `dots` — Navigationspunkte
- `caption_type` — Die Beschriftung für den Anhang, wie er in der Datenbank vorliegt. *(optional; Standard: `none`)*
  - `none` — Keine
  - `title` — Titel
  - `caption` — Bildbeschriftung
  - `description` — Beschreibung
- `lazyload` — Lazyload *(optional; Standard: `0`)*
- `autoplay` — Automatische Wiedergabe *(optional; Standard: `1`)*
- `centered_slides` — Der aktive Slide wird zentriert. *(optional; Standard: `0`)*
- `pause_on_hover` — Pause bei Hover *(optional; Standard: `1`)*
- `pause_on_interaction` — Pause bei Interaktion *(optional; Standard: `1`)*
- `autoplay_speed` — Abspielgeschwindigkeit *(optional; Standard: `5000`)*
- `autoplay_progress` — Zeigt einen Fortschrittsbalken an, wenn Autoplay aktiviert ist. *(optional; Standard: `none`)*
  - `none` — Keine
  - `circle` — Kreis
  - `bar` — Bar
- `initial_slide` — Indexnummer des ersten Slide - Objekts. *(optional; Standard: `0`)*
- `infinite` — Endlosschleife *(optional; Standard: `1`)*
- `effect` — Übergangseffekt. Kann "slide", "fade", "cube", "coverflow", "flip", "creative" oder "cards" sein. *(optional; Standard: `slide`)*
- `speed` — Dauer des Übergangs zwischen den Slides (in ms). *(optional; Standard: `500`)*
- `allow_touch_move` — Möglichkeit, den Slider zu bewegen, indem man ihn mit der Maus anfasst oder mit dem Finger berührt. *(optional; Standard: `1`)*
- `direction` — Slider Richtung *(optional; Standard: `horizontal`)*
  - `horizontal` — Horizontal
  - `vertical` — Vertikal
- `text_direction` — Textausrichtung *(optional; Standard: `ltr`)*
  - `ltr` — Links nach rechts
  - `rtl` — Rechts nach links
- `lightbox` — Lightbox *(optional; Standard: `1`)*
- `lightbox_caption_type` — Beschriftung der Lightbox, wie sie in der Datenbank vorhanden ist. *(optional; Standard: `none`)*
  - `none` — Keine
  - `title` — Titel
  - `caption` — Bildbeschriftung
  - `description` — Beschreibung
- `lightbox_toolbar` — Zeigt eine Symbolleiste für jede Art von Inhalt an. *(optional; Standard: `1`)*
- `lightbox_toolbar_left` — Symbolleisten Elemente, welche an der linken Position angezeigt werden sollen. *(optional; Standard: `infobar`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen
- `lightbox_toolbar_middle` — Symbolleisten Elemente, welche an der mittleren Position angezeigt werden sollen. *(optional; Standard: `inline`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen
- `lightbox_toolbar_right` — Symbolleisten Elemente, welche an der rechten Position angezeigt werden sollen. *(optional; Standard: `iterateZoom,slideshow,fullscreen,thumbs,close`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen

#### [wst_swiper]

- **Shortcode:** `wst_swiper`

Erstellt einen Swiper Slider.

**Grundsyntax**

```text
[wst_swiper]Swiper Slides Markup[/wst_swiper]
```

**Parameter und Optionen**

- `class` — Das class Attribut des Elements. *(optional)*
- `slides_to_show` — Slides in Ansicht *(optional; Standard: `auto`)*
  - `auto` — Automatische Slides in Ansicht.
  - `1` — 1 Slide pro Ansicht
  - `2` — 2 Slides pro Ansicht
  - `3` — 3 Slides pro Ansicht
  - `4` — 4 Slides pro Ansicht
  - `5` — 5 Slides pro Ansicht
  - `6` — 6 Slides pro Ansicht
  - `7` — 7 Slides pro Ansicht
  - `8` — 8 Slides pro Ansicht
  - `9` — 9 Slides pro Ansicht
  - `10` — 10 Slides pro Ansicht
- `slides_to_scroll` — Anzahl der Slides zum scrollen *(optional; Standard: `3`)*
  - `1` — 1 Slide zum Scrollen
  - `2` — 2 Slides zum Scrollen
  - `3` — 3 Slides zum Scrollen
  - `4` — 4 Slides zum Scrollen
  - `5` — 5 Slides zum Scrollen
  - `6` — 6 Slides zum Scrollen
  - `7` — 7 Slides zum Scrollen
  - `8` — 8 Slides zum Scrollen
  - `9` — 9 Slides zum Scrollen
  - `10` — 10 Slides zum Scrollen
- `space_between` — Abstand zwischen den Slides in px. *(optional; Standard: `30`)*
- `navigation` — Navigation *(optional; Standard: `both`)*
  - `none` — Keine
  - `both` — Pfeile und Navigationspunkte
  - `arrows` — Pfeile
  - `dots` — Navigationspunkte
- `caption_type` — Die Beschriftung für den Anhang, wie er in der Datenbank vorliegt. *(optional; Standard: `none`)*
  - `none` — Keine
  - `title` — Titel
  - `caption` — Bildbeschriftung
  - `description` — Beschreibung
- `lazyload` — Lazyload *(optional; Standard: `0`)*
- `autoplay` — Automatische Wiedergabe *(optional; Standard: `1`)*
- `centered_slides` — Der aktive Slide wird zentriert. *(optional; Standard: `0`)*
- `pause_on_hover` — Pause bei Hover *(optional; Standard: `1`)*
- `pause_on_interaction` — Pause bei Interaktion *(optional; Standard: `1`)*
- `autoplay_speed` — Abspielgeschwindigkeit *(optional; Standard: `5000`)*
- `autoplay_progress` — Zeigt einen Fortschrittsbalken an, wenn Autoplay aktiviert ist. *(optional; Standard: `none`)*
  - `none` — Keine
  - `circle` — Kreis
  - `bar` — Bar
- `initial_slide` — Indexnummer des ersten Slide - Objekts. *(optional; Standard: `0`)*
- `infinite` — Endlosschleife *(optional; Standard: `1`)*
- `effect` — Übergangseffekt. Kann "slide", "fade", "cube", "coverflow", "flip", "creative" oder "cards" sein. *(optional; Standard: `slide`)*
- `speed` — Dauer des Übergangs zwischen den Slides (in ms). *(optional; Standard: `500`)*
- `allow_touch_move` — Möglichkeit, den Slider zu bewegen, indem man ihn mit der Maus anfasst oder mit dem Finger berührt. *(optional; Standard: `1`)*
- `direction` — Slider Richtung *(optional; Standard: `horizontal`)*
  - `horizontal` — Horizontal
  - `vertical` — Vertikal
- `text_direction` — Textausrichtung *(optional; Standard: `ltr`)*
  - `ltr` — Links nach rechts
  - `rtl` — Rechts nach links
- `lightbox` — Lightbox *(optional; Standard: `1`)*
- `lightbox_caption_type` — Beschriftung der Lightbox, wie sie in der Datenbank vorhanden ist. *(optional; Standard: `none`)*
  - `none` — Keine
  - `title` — Titel
  - `caption` — Bildbeschriftung
  - `description` — Beschreibung
- `lightbox_toolbar` — Zeigt eine Symbolleiste für jede Art von Inhalt an. *(optional; Standard: `1`)*
- `lightbox_toolbar_left` — Symbolleisten Elemente, welche an der linken Position angezeigt werden sollen. *(optional; Standard: `infobar`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen
- `lightbox_toolbar_middle` — Symbolleisten Elemente, welche an der mittleren Position angezeigt werden sollen. *(optional; Standard: `inline`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen
- `lightbox_toolbar_right` — Symbolleisten Elemente, welche an der rechten Position angezeigt werden sollen. *(optional; Standard: `iterateZoom,slideshow,fullscreen,thumbs,close`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen

**Zusätzliche Informationen**

###### Shortcode Benutzung

```text
[wst_swiper]
	<div class="swiper-slide">Slide 1</div>
	<div class="swiper-slide">Slide 2</div>
	<div class="swiper-slide">Slide 3</div>
	<div class="swiper-slide">Slide 4</div>
	<div class="swiper-slide">Slide 5</div>
[/wst_swiper]
```

#### [wst_nav_menu]

- **Shortcode:** `wst_nav_menu`

Gibt ein Wordpress-Navigationsmenü zurück.

**Grundsyntax**

```text
[wst_nav_menu]
```

**Parameter und Optionen**

- `menu` — Gewünschtes Menü. Akzeptiert eine Menü-ID, einen Slug oder einen Namen. *(Pflicht)*
- `container` — Gibt an, ob das ul-Element umspannt und womit man es umspannen soll. *(optional; Standard: `div`)*
- `container_class` — Klasse, die auf den Container angewendet wird. *(optional; Standard: `menu-{menu slug}-container`)*
- `container_id` — Die ID, die auf den Container angewendet wird. *(optional)*
- `menu_class` — CSS-Klasse, die für das ul-Element verwendet wird, welche das Menü bildet. *(optional; Standard: `menu`)*
- `menu_id` — Die ID, die auf das ul-Element angewendet wird, das das Menü bildet. Standardmäßig ist dies der Menü slug, inkrementiert. *(optional)*
- `before` — Text vor dem Link-Markup. *(optional)*
- `after` — Text nach dem Link-Markup. *(optional)*
- `link_before` — Text vor dem Linktext. *(optional)*
- `link_after` — Text nach dem Linktext. *(optional)*
- `item_spacing` — Gibt an, ob Leerzeichen innerhalb der Menüs HTML beibehalten werden sollen. Akzeptiert "preserve" oder "discard". *(optional; Standard: `preserve`)*
- `depth` — Wie viele Ebenen der Hierarchie einbezogen werden sollen. 0 bedeutet alle. *(optional; Standard: `0`)*

#### [wst_typing_animation]

- **Shortcode:** `wst_typing_animation`

Erstellt einen Text mit animiertem Tippeffekt.

**Grundsyntax**

```text
[wst_typing_animation]Welcome to WESEO Smart Website Builder Bootstrap[/wst_typing_animation]
```

**Parameter und Optionen**

- `type_speed` — Tippgeschwindigkeit in Millisekunden. *(optional; Standard: `0`)*
- `start_delay` — Zeit bis zum Beginn des Tippens in Millisekunden. *(optional; Standard: `0`)*
- `back_speed` — Geschwindigkeit des Rückwärtslaufs in Millisekunden. *(optional; Standard: `0`)*
- `smart_backspace` — Nur das zurücksetzen, was nicht mit der vorherigen Zeichenfolge übereinstimmt. *(optional; Standard: `yes`)*
- `shuffle` — Zeichenketten mischen. *(optional; Standard: `no`)*
- `back_delay` — Zeit vor dem Zurücksetzen in Millisekunden. *(optional; Standard: `700`)*
- `fade_out` — Ausblenden statt Zurücksetzen *(optional; Standard: `no`)*
- `fade_out_class` — CSS-Klasse für die Einblendungsanimation. *(optional; Standard: `typed-fade-out`)*
- `fade_out_delay` — Ausblendverzögerung in Millisekunden. *(optional; Standard: `500`)*
- `loop` — Zeichenketten in Schleife ausgeben. *(optional; Standard: `0`)*
- `loop_count` — Anzahl der Schleifen. *(optional)*
- `show_cursor` — Zeige den Cursor. *(optional; Standard: `yes`)*
- `cursor_char` — Zeichen für den Cursor. *(optional; Standard: `|`)*
- `auto_insert_css` — CSS für Cursor und FadeOut in HTML einfügen. *(optional; Standard: `yes`)*
- `attr` — Attribut für die Eingabe: Eingabeplatzhalter, Wert oder einfach HTML-Text. *(optional)*
- `bind_input_focus_events` — An focus und blur binden, falls das Element eine Textfeld ist. *(optional; Standard: `no`)*
- `content_type` — HTML oder plain für Klartext. *(optional; Standard: `html`)*

**Zusätzliche Informationen**

###### Smart Backspacing

Im folgenden Beispiel würde dies nur die Wörter nach "This is a" zurücksetzen

```text
[wst_typing_animation]
	<p>This is a Typo3 Website</p>
	<p>This is a WordPress Website</p>
[/wst_typing_animation]
```

#### [wst_counter]

- **Shortcode:** `wst_counter`

Erzeugt einen Text mit animiertem Zähleffekt.

**Grundsyntax**

```text
[wst_counter]604.800 seconds in 10.080 minutes in 168 hours in 7 days[/wst_counter]
```

**Parameter und Optionen**

- `duration` — Dauer in Millisekunden. *(optional; Standard: `1000`)*
- `delay` — Verzögerung zwischen den einzelnen Schritten der Zählanimation in Millisekunden. *(optional; Standard: `16`)*
- `trigger_point` — Der Ort des Triggerpunktes. *(optional; Standard: `bottom-in-view`)*
  - `number` — Eine Anzahl (z.B.: '200') von Pixeln.
  - `percentage` — Ein Prozentsatz (z. B.: '200%') der Höhe des Viewports.
  - `bottom-in-view` — Dies ist eine Abkürzung, ein Alias für eine Offset-Funktion, die den Handler auslöst, wenn der untere Rand des Elements den unteren Rand des Ansichtsfensters erreicht.

#### [wst_qr_code]

- **Shortcode:** `wst_qr_code`

Generiert einen QR-Code und gibt diesen als Bild zurück.

**Grundsyntax**

```text
[wst_qr_code]content[/wst_qr_code]
```

**Parameter und Optionen**

- `title` — Das title Attribut des Elements. *(optional; Standard: `QR-Code`)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `output_type` — Der Ausgabetyp des QR-Codes. *(optional; Standard: `svg`)*
  - `bmp` — Dateityp: BMP
  - `gif` — Dateityp: GIF
  - `jpg` — Dateityp: JPG
  - `png` — Dateityp: PNG
  - `webp` — Dateityp: WEBP

#### [wst_superglobal]

- **Shortcode:** `wst_superglobal`

Gibt den Wert einer superglobalen PHP-Variablen zurück

**Grundsyntax**

```text
[wst_superglobal field='field_name']
```

**Parameter und Optionen**

- `id` — Typ der superglobalen PHP-Variable. *(optional; Standard: `GLOBALS`)*
  - `GLOBALS` — Verweist auf alle im globalen Bereich verfügbaren Variablen.
  - `_SERVER` — Informationen zum Server und zur Ausführungsumgebung.
  - `_GET` — HTTP GET Variablen.
  - `_POST` — HTTP POST Variablen.
  - `_FILES` — HTTP Datei Upload Variablen.
  - `_COOKIE` — HTTP Cookies.
  - `_SESSION` — Session Variablen.
  - `_REQUEST` — HTTP REQUEST Variablen.
  - `_ENV` — Umgebungsvariablen.
- `field` — Der Name des superglobalen Feldes. *(Pflicht; Standard: `field_name`)*
- `default` — Der Standardwert des superglobalen Feldes. *(Pflicht)*
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

#### [wst_iframe]

- **Shortcode:** `wst_iframe`

Erzeugt ein iFrame mit automatischer Größenänderung.

**Grundsyntax**

```text
[wst_iframe src='1234']
```

**Parameter und Optionen**

- `src` — Gibt die Adresse des eingebetteten Dokuments an. *(Pflicht; Standard: `1234`)*
- `title` — Das title Attribut des Elements. *(optional)*
- `name` — Das name Attribut des Elements. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `width` — Die Breite (in Pixel) des Elements. *(optional; Standard: `100%`)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional; Standard: `600`)*
- `scrolling` — Das scrolling Attribut des Elements. *(optional; Standard: `no`)*
  - `auto` — The scrollbar appears when needed.
  - `yes` — Zeige die Scrollbar.
  - `no` — Zeigt die Scrollbar nicht an.
- `auto_resize` — Automatische Größenanpassung der Höhe und Breite sowohl gleicher als auch domänenübergreifender iFrames an den enthaltenen Inhalt. *(optional; Standard: `0`)*

#### [wst_ics]

- **Shortcode:** `wst_ics`

Erstellt einen ICS-Kalender Datei Button.

**Grundsyntax**

```text
[wst_ics date_start='1784198923']Beschreibung des Kalender Events.[/wst_ics]
```

**Parameter und Optionen**

- `title` — Der Titel der Formularschaltfläche. *(optional; Standard: `Zum Kalender hinzufügen`)*
- `message` — Eine benutzerdefinierte Formularnachricht. *(optional)*
- `gmt` — Ob die GMT-Zeitzone verwendet werden soll. *(optional; Standard: `0`)*
- `date_start` — Startdatum des Kalender Events. *(Pflicht; Standard: `1784198923`)*
- `date_end` — Enddatum des Kalender Events. *(optional)*
- `location` — Ort des Kalender Events. *(optional)*
- `summary` — Titel / Auszug des Kalender Events. *(optional)*
- `permalink` — Permalink des Kalender Events. *(optional)*
- `filename` — Name der ICS Datei. *(optional)*
- `populate_post_data` — Befüllt das Feld Zusammenfassung, Beschreibung und Permalink mit Beitragsdaten. *(optional; Standard: `0`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*

#### [wst_google_calendar]

- **Shortcode:** `wst_google_calendar`

Erzeugt einen Google Kalender Button.

**Grundsyntax**

```text
[wst_google_calendar date_start='1784198923']Beschreibung des Kalender Events.[/wst_google_calendar]
```

**Parameter und Optionen**

- `title` — Der Titel des Buttons. *(optional; Standard: `Zum Kalender hinzufügen`)*
- `date_start` — Startdatum des Kalender Events. *(Pflicht; Standard: `1784198923`)*
- `date_end` — Enddatum des Kalender Events. *(optional)*
- `location` — Ort des Kalender Events. *(optional)*
- `summary` — Titel / Auszug des Kalender Events. *(optional)*
- `populate_post_data` — Befüllt das Feld Zusammenfassung und Beschreibung mit Beitragsdaten. *(optional; Standard: `0`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*

#### [wst_pdf]

- **Shortcode:** `wst_pdf`

Erstellt einen PDF-Datei Button aus einem Smart Template.

**Grundsyntax**

```text
[wst_pdf template_id='1234']
```

**Parameter und Optionen**

- `title` — Der Titel der Formularschaltfläche. *(optional; Standard: `PDF`)*
- `message` — Eine benutzerdefinierte Formularnachricht. *(optional)*
- `template_id` — ID des zu rendernden Templates. *(Pflicht; Standard: `1234`)*
- `size` — Legt die Papiergröße fest. *(optional; Standard: `a4`)*
  - `4a0` — 1682 x 2378 mm
  - `2a0` — 1189 x 1682 mm
  - `a0` — 841 x 1189 mm
  - `a1` — 594 x 841 mm
  - `a2` — 420 x 594 mm
  - `a3` — 297 x 420 mm
  - `a4` — 210 x 297 mm
  - `a5` — 148 x 210 mm
  - `a6` — 105 x 148 mm
  - `a7` — 74 x 105 mm
  - `a8` — 52 x 74 mm
  - `a9` — 37 x 52 mm
  - `a10` — 26 x 37 mm
  - `b0` — 1000 x 1414 mm
  - `b1` — 707 x 1000 mm
  - `b2` — 500 x 707 mm
  - `b3` — 353 x 500 mm
  - `b4` — 250 x 353 mm
  - `b5` — 176 x 250 mm
  - `b6` — 125 x 176 mm
  - `b7` — 88 x 125 mm
  - `b8` — 62 x 88 mm
  - `b9` — 44 x 62 mm
  - `b10` — 31 x 44 mm
  - `c0` — 917 x 1297 mm
  - `c1` — 648 x 917 mm
  - `c2` — 458 x 648 mm
  - `c3` — 324 x 458 mm
  - `c4` — 229 x 324 mm
  - `c5` — 162 x 229 mm
  - `c6` — 114 x 162 mm
  - `c7` — 81 x 114 mm
  - `c8` — 57 x 81 mm
  - `c9` — 40 x 57 mm
  - `c10` — 28 x 40 mm
  - `ra0` — 860 x 1220 mm
  - `ra1` — 610 x 860 mm
  - `ra2` — 430 x 610 mm
  - `ra3` — 305 x 430 mm
  - `ra4` — 215 x 305 mm
  - `sra0` — 900 x 1280 mm
  - `sra1` — 640 x 900 mm
  - `sra2` — 450 x 640 mm
  - `sra3` — 320 x 450 mm
  - `sra4` — 225 x 320 mm
  - `letter` — 216 x 279 mm
  - `half-letter` — 140 x 216 mm
  - `legal` — 216 x 356 mm
  - `ledger` — 432 x 279 mm
  - `tabloid` — 279 x 432 mm
  - `executive` — 184 x 267 mm
  - `folio` — 216 x 330 mm
  - `commercial #10 envelope` — 241 x 105 mm
  - `catalog #10 1/2 envelope` — 229 x 305 mm
  - `8.5x11` — 216 x 279 mm
  - `8.5x14` — 216 x 356 mm
  - `11x17` — 279 x 432 mm
- `orientation` — Legt die Papierausrichtung fest. *(optional; Standard: `portrait`)*
  - `portrait` — Papierausrichtung im Hochformat.
  - `landscape` — Papierausrichtung im Querformat.
- `stream` — Streamt die PDF-Datei an den Client. *(optional; Standard: `inline`)*
  - `inline` — Zeigt die PDF-Datei direkt in einen neuen Browser Tab an.
  - `attachment` — Den Browser zwingen, einen Download-Dialog zu öffnen.
- `filename` — Name der PDF Datei. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*

#### [wst_zip]

- **Shortcode:** `wst_zip`

Erzeugt einen ZIP Download Button aus einer Liste von Attachments.

**Grundsyntax**

```text
[wst_zip]Attachment-IDs Liste (Komma getrennt).[/wst_zip]
```

**Parameter und Optionen**

- `title` — Der Titel der Formularschaltfläche. *(optional; Standard: `PDF`)*
- `message` — Eine benutzerdefinierte Formularnachricht. *(optional)*
- `files` — Attachment-IDs Liste (Komma getrennt). *(optional)*
- `filename` — Name der ZIP-Datei. *(optional; Standard: `downloads.zip`)*
- `selectable` — Wenn einzelne Dateien auswählbar sein sollen. *(optional; Standard: `0`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*

#### [wst_vcard]

- **Shortcode:** `wst_vcard`

Erzeugt einen vCard Button aus einem Template.

**Grundsyntax**

```text
[wst_vcard]
```

**Parameter und Optionen**

- `title` — Der Titel der Formularschaltfläche. *(optional; Standard: `vCard`)*
- `message` — Eine benutzerdefinierte Formularnachricht. *(optional)*
- `firstname` — Der Vorname des vCard-Objekts. *(optional)*
- `lastname` — Der Nachname des vCard-Objekts. *(optional)*
- `additional` — Der zusätzliche Name des vCard-Objekts. *(optional)*
- `prefix` — Das Präfix des vCard-Objekts. *(optional)*
- `suffix` — Das Suffix des vCard-Objekts. *(optional)*
- `company` — Der Name der Organisation, der das vCard-Objekt zugeordnet ist. *(optional)*
- `job_title` — Angabe der Stellenbezeichnung, funktionellen Stellung oder Funktion der mit dem vCard-Objekt verbundenen Person. *(optional)*
- `role` — Rolle, Beruf oder Wirtschaftskategorie des vCard-Objekts. *(optional)*
- `email` — E-Mail-Adresse zur Kommunikation mit dem vCard-Objekt. *(optional)*
- `phone` — Die Telefonnummer des vCard-Objekts. *(optional)*
- `mobile` — Die Mobiltelefonnummer des vCard-Objekts. *(optional)*
- `street` — Die Straße des vCard-Objekts. *(optional)*
- `city` — Die Stadt des vCard-Objekts. *(optional)*
- `postcode` — Die Postleitzahl des vCard-Objekts. *(optional)*
- `country` — Das Land des vCard-Objekts. *(optional)*
- `permalink` — URL zu einer Website, welche die Person repräsentiert. *(optional)*
- `logo` — Logo der Organisation, mit der die Person in Beziehung steht, der die vCard gehört. *(optional)*
- `image` — Bild oder Fotografie der mit der vCard verbundenen Person. *(optional)*
- `filename` — Der Name der vCard-Datei. *(optional)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `form`)*
  - `form` — Gibt die vCard als Formular zurück.
  - `url` — Gibt die vCard als URL zurück.
  - `raw` — Gibt die vCard als Rohdaten zurück.

#### [wst_ajax_container]

- **Shortcode:** `wst_ajax_container`

Erzeugt einen Container, der Inhalte per Ajax lädt.

**Grundsyntax**

```text
[wst_ajax_container template_id='1234']My Ajax Content[/wst_ajax_container]
```

**Parameter und Optionen**

- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Details anzeigen`)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional)*
- `template_id` — ID des zu rendernden Templates. *(Pflicht; Standard: `1234`)*
- `autoload` — Zeigt den Ajax-Inhalt beim Laden der Seite an. *(optional; Standard: `1`)*
- `refresh` — Zeigt einen Refresh-Button. *(optional; Standard: `0`)*
- `refresh_text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Aktualisieren`)*
- `refresh_class` — Das class Attribut des Elements. *(optional)*
- `refresh_style` — Das style Attribut des Elements. *(optional)*
- `refresh_title` — Das title Attribut des Elements. *(optional)*
- `ajax_loader` — Der Template Name für den Grid-Ajax-Loader. *(optional; Standard: `content`)*

### 5.2 Mehrsprachigkeit

- **Interne ID:** `multilingual`
- **Kategoriefarbe:** `#A03F3F`
- **Einträge:** 3

#### [wst_language_switcher]

- **Shortcode:** `wst_language_switcher`

Erstellt einen Sprachenwechsler.

**Grundsyntax**

```text
[wst_language_switcher]
```

**Parameter und Optionen**

- `dropdown` — Die Liste als Dropdown ausgeben. *(optional; Standard: `0`)*
- `hide_if_empty` — Sprachen ausblenden, die keine Beiträge (oder Seiten) enthalten. *(optional; Standard: `1`)*
- `show_flags` — Flaggen anzeigen. *(optional; Standard: `0`)*
- `show_names` — Sprachennamen anzeigen. *(optional; Standard: `1`)*
- `display_names_as` — Gibt an ob der Name der Sprache oder ihr Code angezeigt werden soll. *(optional; Standard: `name`)*
  - `slug` — Sprachcode
  - `name` — Name der Sprache
- `force_home` — Immer auf die Homepage in der übersetzten Sprache verlinken. *(optional; Standard: `0`)*
- `hide_if_no_translation` — Den Link ausblenden, wenn es keine Übersetzung gibt. *(optional; Standard: `0`)*
- `hide_current` — Die aktuelle Sprache ausblenden. *(optional; Standard: `0`)*
- `item_spacing` — Ob Leerzeichen zwischen Listenelementen erhalten bleiben oder verworfen werden sollen. *(optional; Standard: `preserve`)*
  - `preserve` — Leerzeichen beibehalten.
  - `discard` — Leerzeichen verwerfen.
- `classes` — Eine Liste von CSS-Klassen, die für jedes ausgegebene Element festgelegt wird. *(optional)*
- `link_classes` — Eine Liste von CSS-Klassen, die für jeden ausgegebenen Link festgelegt werden. *(optional)*

#### [wst_languages]

- **Shortcode:** `wst_languages`

Gibt die Sprachen als Schleife zurück.

**Grundsyntax**

```text
[wst_languages]
```

**Parameter und Optionen**

- `skip_missing` — Fehlende Übersetzung überspringen. *(optional; Standard: `0`)*
- `orderby` — Name des Feldes `id, slug, name, country` nach dem sortiert werden soll. *(optional; Standard: `id`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `ASC`)*

**Zusätzliche Informationen**

###### Die Sprachen als Schleife verwenden, um einen benutzerdefinierten Sprachumschalter zu erstellen.

Der folgender Shortcode verwandelt das Feld languages in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_languages orderby='code']
	{{name/wst_languages}} ({{country/wst_languages}}) [is_current_lang](Current Language)[/is_current_lang]
[/wst_languages]
```

Jedes Sprachfeld `name | slug | locale | url | flag | classes | link_classes | country | home_url` kann als Platzhalter im Inhalt der Schleife verwendet werden.

Platzhalter können wie folgt maskiert werden: `[name]` or `#name#`.

###### Zusätzliche Shortcodes

Zeigt den Inhalt nur in der aktuellen Sprache an.

```text
[is_current_lang]
	My Content
[/is_current_lang]
```

Zeigt den Inhalt nur an, wenn die Sprache eine Übersetzung hat.

```text
[has_translation]
	My Content
[/has_translation]
```

#### [wst_i18n_string]

- **Shortcode:** `wst_i18n_string`

Registriert und übersetzt eine Zeichenkette mit Polylang oder WPML.

**Grundsyntax**

```text
[wst_i18n_string]My translated string[/wst_i18n_string]
```

**Parameter und Optionen**

- `group` — Die Gruppe, in der die Zeichenfolge registriert ist. *(optional; Standard: `wst-i18n-shortcode`)*
- `name` — Der eindeutige Name der registrierten Zeichenfolge. *(optional; Standard: `wst-i18n-shortcode-7089825e38cd1ea95d95266a6b00dfc1`)*

### 5.3 Formatierung

- **Interne ID:** `formatting`
- **Einträge:** 8

#### [wst_string_replace]

- **Shortcode:** `wst_string_replace`

Ersetzt alle Vorkommen des Suchstrings durch einen anderen String.

**Grundsyntax**

```text
[wst_string_replace search='cool' replace='smart']My cool Content[/wst_string_replace]
```

**Parameter und Optionen**

- `search` — Der gesuchte Wert, auch Nadel (needle) genannt. Ein Array kann genutzt werden, um mehrere Nadeln zu bestimmen. *(Pflicht; Standard: `cool`)*
- `replace` — Der Ersetzungswert, der gefundene search Werte ersetzt. Ein Array kann genutzt werden, um mehrere Werte zu bestimmen. *(Pflicht; Standard: `smart`)*

#### [wst_calculate]

- **Shortcode:** `wst_calculate`

Berechnet einen mathematischen Ausdruck.

**Grundsyntax**

```text
[wst_calculate]2*pi*10[/wst_calculate]
```

**Parameter und Optionen**

- `decimal_point` — Das Trennzeichen für die Nachkommastellen. *(optional; Standard: `,`)*
- `thousands_sep` — Das Tausendertrennzeichen. *(optional; Standard: `.`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `0`)*

#### [wst_date]

- **Shortcode:** `wst_date`

Ruft das Datum im lokalisierten Format ab.

**Grundsyntax**

```text
[wst_date]+1 week 2 days 4 hours 2 seconds[/wst_date]
```

**Parameter und Optionen**

- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `j. F Y`)*

#### [wst_number_format]

- **Shortcode:** `wst_number_format`

Konvertiert eine Zahl in ein Format, das auf dem aktuellen Gebietsschema basiert.

**Grundsyntax**

```text
[wst_number_format]1000[/wst_number_format]
```

**Parameter und Optionen**

- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `0`)*

#### [wst_strip_tags]

- **Shortcode:** `wst_strip_tags`

Alle HTML-Tags, einschließlich Script und Style, werden entfernt.

**Grundsyntax**

```text
[wst_strip_tags]String containing &lt;b&gt;HTML&lt;/b&gt; tags.[/wst_strip_tags]
```

**Parameter und Optionen**

- `remove_breaks` — Ob übrig gebliebene Zeilenumbrüche und Leerzeichen entfernt werden sollen. *(optional; Standard: `0`)*

#### [wst_make_clickable]

- **Shortcode:** `wst_make_clickable`

Konvertiert Klartext-URI in HTML-Links.

**Grundsyntax**

```text
[wst_make_clickable]String containing the plaintext www.google.com URI.[/wst_make_clickable]
```

#### [wst_trim_string]

- **Shortcode:** `wst_trim_string`

Schneidet einen Text auf eine bestimmte Anzahl von Wörtern oder Zeichen zu.

**Grundsyntax**

```text
[wst_trim_string]
```

**Parameter und Optionen**

- `words` — Beschneidet den Text auf eine bestimmte Anzahl von Wörtern. *(optional)*
- `chars` — Beschneidet den Text auf eine bestimmte Anzahl von Zeichen. *(optional)*
- `strip_tags` — Alle HTML-Tags, einschließlich Script und Style, werden entfernt. *(optional)*
- `more` — Was angehängt werden soll, wenn der String gekürzt wird. *(optional; Standard: `…`)*

#### [wst_sanitize_title]

- **Shortcode:** `wst_sanitize_title`

Bereinigt eine Zeichenkette in einen Permalink, der in URLs oder HTML-Attributen verwendet werden kann.

**Grundsyntax**

```text
[wst_sanitize_title]
```

**Parameter und Optionen**

- `fallback_title` — Ein Titel, der verwendet wird, wenn $title leer ist. *(optional)*
- `context` — Die Operation, für die die Zeichenkette bereinigt wird. *(optional; Standard: `save`)*

### 5.4 Lightbox

- **Interne ID:** `lightbox`
- **Einträge:** 2

#### [wst_thickbox]

- **Shortcode:** `wst_thickbox`

Erstellt einen Button, welcher eine WordPress ThickBox Lightbox anzeigt.

**Grundsyntax**

```text
[wst_thickbox]My thickbox content[/wst_thickbox]
```

**Parameter und Optionen**

- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Details anzeigen`)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional)*
- `width` — Die Breite (in Pixel) des Elements. *(optional; Standard: `600`)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional; Standard: `550`)*
- `iframe` — Gibt die URL an, welche die Lightbox zum Laden einer Webseite als Iframe verwenden soll. *(optional)*
- `show_onload` — Zeigt die Lightbox beim Laden der Seite an. *(optional)*

#### [wst_fancybox]

- **Shortcode:** `wst_fancybox`

Erstellt einen Button, welcher eine fancybox Lightbox anzeigt.

**Grundsyntax**

```text
[wst_fancybox]My fancybox content[/wst_fancybox]
```

**Parameter und Optionen**

- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Details anzeigen`)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional)*
- `template_ids` — Template-IDs durch Komma getrennt. *(optional)*
- `start_template_id` — Legt das Template fest, welches als erstes angezeigt werden soll. *(optional)*
- `show_onload` — Zeigt die Lightbox beim Laden der Seite an. *(optional)*
  - `always` — Zeigt die Lightbox immer beim Laden der Seite an.
  - `new_visitors` — Zeigt die Lightbox nur bei neuen Besuchern beim Laden der Seite an.
  - `returning_visitors` — Zeigt die Lightbox nur bei wiederkehrenden Besuchern beim Laden der Seite an.
- `show_onload_delay` — Die Wartezeit in Millisekunden, nach der die Lightbox angezeigt werden soll. *(optional; Standard: `0.5`)*
- `src` — Liste von src Elementen. *(optional; Beispiel: `https://www.google.com|type=iframe,https://www.youtube.com/watch?v=Wimkqo8gDZ0|type=youtube`)*
- `type` — Gibt den Inhaltstyp an. *(optional; Standard: `inline`)*
  - `image` — Bild
  - `iframe` — iFrame
  - `youtube` — YouTube
  - `vimeo` — Vimeo
  - `inline` — Inline
  - `html` — HTML
- `toolbar` — Zeigt eine Symbolleiste für jede Art von Inhalt an. *(optional; Standard: `1`)*
- `toolbar_left` — Symbolleisten Elemente, welche an der linken Position angezeigt werden sollen. *(optional; Standard: `infobar`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen
- `toolbar_middle` — Symbolleisten Elemente, welche an der mittleren Position angezeigt werden sollen. *(optional; Standard: `inline`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen
- `toolbar_right` — Symbolleisten Elemente, welche an der rechten Position angezeigt werden sollen. *(optional; Standard: `iterateZoom,slideshow,fullscreen,thumbs,close`)*
  - `zoomIn` — Vergrößern
  - `zoomOut` — Verkleinern
  - `toggle1to1` — Originalgröße umschalten
  - `rotateCCW` — Gegen den Uhrzeigersinn drehen
  - `rotateCW` — Im Uhrzeigersinn drehen
  - `flipX` — Horizontal spiegeln
  - `flipY` — Vertikal spiegeln
  - `infobar` — Infobar
  - `next` — Weiter
  - `prev` — Zurück
  - `download` — Herunterladen
  - `iterateZoom` — Zoomstufe umschalten
  - `slideshow` — Diashow wechseln
  - `fullscreen` — Vollbildmodus umschalten
  - `thumbs` — Miniaturansichten umschalten
  - `close` — Schließen
- `drag_to_close` — "Ziehen zum Schließen" Geste aktivieren. Den Inhalt nach oben/unten ziehen, um die Instanz zu schließen. *(optional; Standard: `1`)*

### 5.5 Benutzer

- **Interne ID:** `user`
- **Einträge:** 3

#### [wst_is_user_logged_in]

- **Shortcode:** `wst_is_user_logged_in`

Überprüft ob ein Benutzer eingeloggt ist.

**Grundsyntax**

```text
[wst_is_user_logged_in]My Content[/wst_is_user_logged_in]
```

**Parameter und Optionen**

- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

#### [wst_current_user_can]

- **Shortcode:** `wst_current_user_can`

Überprüft ob der eingeloggte Benutzer eine bestimmte Berechtigung besitzt.

**Grundsyntax**

```text
[wst_current_user_can capability='editor']My Content[/wst_current_user_can]
```

**Parameter und Optionen**

- `capability` — Name oder Liste (PIPE Zeichen getrennt) der einschränkenden [Rolle / Berechtigung](https://codex.wordpress.org/Roles_and_Capabilities). *(Pflicht; Standard: `editor`)*
- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

#### [wst_userdata]

- **Shortcode:** `wst_userdata`

Gibt Benutzerinformationen über die Benutzer ID zurück.

**Grundsyntax**

```text
[wst_userdata key='user_firstname']
```

**Parameter und Optionen**

- `id` — Die Benutzer ID. *(Pflicht; Standard: `0`)*
- `key` — Der Name des Benutzerfeldes. *(Pflicht; Standard: `user_firstname`)*
  - `nickname` — Gibt den Spitznamen des Benutzers zurück.
  - `user_description` — Gibt die Beschreibung des Benutzers zurück.
  - `user_firstname` — Gibt den Vornamen des Benutzers zurück.
  - `user_lastname` — Gibt den Nachnamen des Benutzers zurück.
  - `user_login` — Gibt den Login des Benutzers zurück.
  - `user_nicename` — Gibt den Slug des Benutzers zurück.
  - `user_email` — Gibt die E-Mail-Adresse des Benutzers zurück.
  - `user_url` — Gibt den Permalink des Benutzers zurück.
  - `user_registered` — Gibt das Registrierungsdatum des Benutzers zurück.
  - `user_activation_key` — Gibt den Aktivierungsschlüssel des Benutzers zurück.
  - `user_status` — Gibt den Status des Benutzers zurück.
  - `user_level` — Gibt die Berechtigungs Ebene des Benutzers zurück.
  - `display_name` — Gibt den öffentlichen Name des Benutzers zurück.
  - `locale` — Gibt das Sprache des Benutzers zurück.
  - `rich_editing` — Gibt den Visueller Editor Status des Benutzers zurück.
  - `syntax_highlighting` — Gibt das Syntaxhervorhebung-Status des Benutzers zurück.

### 5.6 Account

- **Interne ID:** `account`
- **Einträge:** 5

#### [wst_logout_url]

- **Shortcode:** `wst_logout_url`

Gibt die URL zurück, mit der sich der Benutzer von der Seite abmelden kann.

**Grundsyntax**

```text
[wst_logout_url]
```

**Parameter und Optionen**

- `redirect` — URL, auf die umgeleitet werden soll. *(optional)*

#### [wst_login_form]

- **Shortcode:** `wst_login_form`

Stellt ein Login Formular bereit, das überall in WordPress verwendet werden kann.

**Grundsyntax**

```text
[wst_login_form]
```

**Parameter und Optionen**

- `message` — Benutzerdefinierte Nachricht am Anfang des Formulars. *(optional)*
- `redirect` — URL, auf die umgeleitet werden soll. *(optional)*
- `hidden` — Ausblenden des Formulars beim Laden der Seite. *(optional)*

#### [wst_lost_password_form]

- **Shortcode:** `wst_lost_password_form`

Stellt ein Passwort vergessen Formular bereit, das überall in WordPress verwendet werden kann.

**Grundsyntax**

```text
[wst_lost_password_form]
```

**Parameter und Optionen**

- `redirect_on_reset` — URL, auf die umgeleitet werden soll. *(optional)*

#### [wst_edit_account]

- **Shortcode:** `wst_edit_account`

Stellt ein Formular zum Bearbeiten von Konten bereit, das überall in WordPress verwendet werden kann.

**Grundsyntax**

```text
[wst_edit_account]
```

**Parameter und Optionen**

- `user` — Die Benutzer ID. *(Pflicht; Standard: `0`)*
- `redirect` — URL, auf die umgeleitet werden soll. *(optional)*

#### [wst_register_form]

- **Shortcode:** `wst_register_form`

Stellt ein Benutzer Registrierungs Formular bereit, das überall in WordPress verwendet werden kann.

**Grundsyntax**

```text
[wst_register_form]
```

**Parameter und Optionen**

- `show_fullname` — Zeigt die Felder Vorname und Nachname auf dem Registrierungsformular an. *(optional; Standard: `1`)*
- `generate_display_name` — Beim Erstellen eines Kontos automatisch einen Anzeigename für den Benutzer generieren, basierend auf dessen Namen. *(optional; Standard: `0`)*
- `generate_username` — Beim Erstellen eines Kontos automatisch einen Benutzername für den Benutzer generieren, basierend auf dessen Namen, Nachnamen oder E-Mail-Adresse. *(optional; Standard: `0`)*
- `generate_password` — Senden Sie dem neuen Benutzer beim Erstellen seines Kontos einen Link, über den er sein Passwort festlegen kann. *(optional; Standard: `0`)*
- `role` — Benutzerrolle *(optional; Standard: `subscriber`)*
- `redirect` — URL, auf die umgeleitet werden soll. *(optional)*

### 5.7 Fernsteuern

- **Interne ID:** `remote`
- **Einträge:** 5

#### [wst_website_snapshot]

- **Shortcode:** `wst_website_snapshot`

Generiert einen Screenshot von einer entfernten Website und gibt diesen als Bild zurück.

**Grundsyntax**

```text
[wst_website_snapshot]URL[/wst_website_snapshot]
```

**Parameter und Optionen**

- `width` — Die Breite (in Pixel) des Elements. *(optional; Standard: `400`)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional; Standard: `300`)*
- `title` — Das title Attribut des Elements. *(optional; Standard: `Snapshot`)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*

#### [wst_website_title]

- **Shortcode:** `wst_website_title`

Gibt den Titel einer externen Website zurück.

**Grundsyntax**

```text
[wst_website_title]URL[/wst_website_title]
```

#### [wst_website_description]

- **Shortcode:** `wst_website_description`

Gibt die Beschreibung einer externen Website zurück.

**Grundsyntax**

```text
[wst_website_description]URL[/wst_website_description]
```

#### [wst_geolocate]

- **Shortcode:** `wst_geolocate`

Gibt ein bestimmtes Feld der geolokalisierten IP-Adresse zurück.

**Grundsyntax**

```text
[wst_geolocate field='country']
```

**Parameter und Optionen**

- `field` — Der Name des geolokalisierten Feldes. *(Pflicht; Standard: `country`)*
  - `continent` — Name des Kontinents
  - `continentCode` — Zweibuchstabiger Kontinentalcode
  - `country` — Name des Landes
  - `countryCode` — Zweibuchstabiger Kontinentalcode [ISO 3166-1 alpha-2](https://de.wikipedia.org/wiki/ISO-3166-1-Kodierliste)
  - `region` — Region/Staat Kurzzeichen (FIPS oder ISO)
  - `regionName` — Region/Staat
  - `city` — Stadt
  - `district` — Bezirk
  - `zip` — Postleitzahl
  - `lat` — Breitengrad
  - `lon` — Längengrad
  - `timezone` — Zeitzone
  - `offset` — Zeitzone UTC mit Sommerzeitversatz in Sekunden
  - `currency` — Nationale Währung
  - `isp` — ISP Name
  - `org` — Name des Unternehmens
  - `as` — AS-Nummer und Organisation, getrennt durch Leerzeichen (RIR). Leer für IP-Blöcke, die nicht in BGP-Tabellen angekündigt werden.
  - `asname` — AS-Name (RIR). Leer für IP-Blöcke, die nicht in BGP-Tabellen angekündigt werden.
  - `reverse` — Reverse DNS der IP (kann die Antwort verzögern)
  - `mobile` — Mobile (zellulare) Verbindung
  - `proxy` — Proxy-, VPN- oder Tor-Ausgangsadresse
  - `hosting` — Hosting, Colocation oder Rechenzentrum
  - `query` — IP Adresse für die verwendete Abfrage
- `lang` — Die Sprache der Antwort. *(optional; Standard: `false`)*

#### [wst_openai_create_image]

- **Shortcode:** `wst_openai_create_image`

Erzeugt ein Bild anhand einer Eingabeaufforderung.

**Grundsyntax**

```text
[wst_openai_create_image]An astronaut riding a horse in photorealistic style.[/wst_openai_create_image]
```

**Parameter und Optionen**

- `prompt` — Eine Textbeschreibung des gewünschten Bildes. Die maximale Länge beträgt 1000 Zeichen. *(optional)*
- `size` — Die Größe der generierten Bilder. Muss eine der Größen 256x256, 512x512 oder 1024x1024 sein. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional)*
- `alt` — Das alt Attribut des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional)*

### 5.8 Beitrags Felder

- **Interne ID:** `post`
- **Kategoriefarbe:** `#00A7DA`
- **Einträge:** 18

#### [wst_post_id]

- **Shortcode:** `wst_post_id`

Gibt die ID des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_id]
```

#### [wst_post_title]

- **Shortcode:** `wst_post_title`

Gibt den Titel des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_title]
```

**Parameter und Optionen**

- `words` — Beschneidet den Text auf eine bestimmte Anzahl von Wörtern. *(optional)*
- `chars` — Beschneidet den Text auf eine bestimmte Anzahl von Zeichen. *(optional)*
- `more` — Was angehängt werden soll, wenn der Titel gekürzt wird. *(optional; Standard: `…`)*

#### [wst_post_name]

- **Shortcode:** `wst_post_name`

Gibt die Titelform des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_name]
```

#### [wst_post_date]

- **Shortcode:** `wst_post_date`

Gibt das Datum des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_date]
```

**Parameter und Optionen**

- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `j. F Y`)*

#### [wst_post_time]

- **Shortcode:** `wst_post_time`

Gibt die Uhrzeit des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_time]
```

**Parameter und Optionen**

- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `G:i`)*

#### [wst_post_modified]

- **Shortcode:** `wst_post_modified`

Gibt das Änderungsdatum des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_modified]
```

**Parameter und Optionen**

- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `j. F Y`)*

#### [wst_post_permalink]

- **Shortcode:** `wst_post_permalink`

Gibt den Permalink des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_permalink]link_text[/wst_post_permalink]
```

**Parameter und Optionen**

- `title` — Das title Attribut des Elements. *(optional; Standard: `post_title`)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `target` — Die Zielfensterbasis des Elements: `_self | _blank | _parent | _top`. *(optional; Standard: `_self`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `link_text` — Der Text des Links als Zeichenkette oder HTML Code. *(optional)*

#### [wst_post_content]

- **Shortcode:** `wst_post_content`

Gibt den Inhalt des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_content]
```

**Parameter und Optionen**

- `words` — Beschneidet den Text auf eine bestimmte Anzahl von Wörtern. *(optional)*
- `chars` — Beschneidet den Text auf eine bestimmte Anzahl von Zeichen. *(optional)*
- `more` — Was angehängt werden soll, wenn der Inhalt gekürzt wird. *(optional; Standard: `…`)*
- `strip_tags` — Alle HTML-Tags, einschließlich Script und Style, werden entfernt. *(optional)*
- `read_more` — Der Typ des Read More-Tags. *(optional; Standard: `wp`)*
  - `wp` — Gibt den Standard WordPress Read More-Tag zurück.
  - `expandable` — Gibt ein erweiterbares Read More-Tag zurück.
- `read_more_icon` — Das mehr lesen Link-Icon. *(optional; Standard: `+`)*
- `read_more_text` — Der mehr lesen Linktext. *(optional; Standard: `Weiterlesen`)*
- `read_less_icon` — Das weniger lesen Link-Icon. *(optional; Standard: `−`)*
- `read_less_text` — Der weniger lesen Linktext. *(optional; Standard: `Weiterlesen`)*
- `read_more_link_position` — Die Position des "Weiterlesen"-Links. *(optional; Standard: `bottom`)*

#### [wst_post_excerpt]

- **Shortcode:** `wst_post_excerpt`

Gibt den Auszug des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_excerpt]
```

**Parameter und Optionen**

- `words` — Beschneidet den Text auf eine bestimmte Anzahl von Wörtern. *(optional; Standard: `55`)*
- `chars` — Beschneidet den Text auf eine bestimmte Anzahl von Zeichen. *(optional)*
- `more` — Was angehängt werden soll, wenn der Auszug gekürzt wird. *(optional; Standard: `…`)*
- `fallback` — Erstellt den Auszug aus dem Inhalt, falls dieser nicht vorhanden ist. *(optional; Standard: `1`)*
- `html` — HTML-Elemente im Inhalt erlauben. *(optional; Standard: `1`)*
- `read_more` — Der Typ des Read More-Tags. *(optional; Standard: `wp`)*
  - `wp` — Gibt den Standard WordPress Read More-Tag zurück.
  - `expandable` — Gibt ein erweiterbares Read More-Tag zurück.
- `read_more_icon` — Das mehr lesen Link-Icon. *(optional; Standard: `+`)*
- `read_more_text` — Der mehr lesen Linktext. *(optional; Standard: `Weiterlesen`)*
- `read_less_icon` — Das weniger lesen Link-Icon. *(optional; Standard: `−`)*
- `read_less_text` — Der weniger lesen Linktext. *(optional; Standard: `Weiterlesen`)*
- `read_more_link_position` — Die Position des "Weiterlesen"-Links. *(optional; Standard: `bottom`)*

#### [wst_post_thumbnail]

- **Shortcode:** `wst_post_thumbnail`

Gibt das Bild des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_thumbnail]
```

**Parameter und Optionen**

- `size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `thumbnail`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `attr` — HTML Attribute des Bildes als Query String. *(optional)*
- `inline` — Das Bild inline als Data-URL (Base64-kodiert für Nicht-SVG-Formate) oder direkt als rohen SVG-Code einbetten. *(optional)*
- `srcset` — Berechnet die Bilder, welche in ein srcset-Attribut aufgenommen werden sollen. *(optional; Standard: `1`)*

#### [wst_post_meta]

- **Shortcode:** `wst_post_meta`

Gibt den Wert eines übergebenen Beitrag-Metafeldes zurück.

**Grundsyntax**

```text
[wst_post_meta field='field_name']
```

**Parameter und Optionen**

- `field` — Der Feldname oder Schlüssel. *(Pflicht; Standard: `field_name`)*
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

#### [wst_post_author]

- **Shortcode:** `wst_post_author`

Gibt ein bestimmtes Benutzerfeld des Beitragsautors zurück.

**Grundsyntax**

```text
[wst_post_author field='user_display_name']
```

**Parameter und Optionen**

- `field` — Der Name des Benutzerfeldes. *(Pflicht; Standard: `user_display_name`)*
  - `user_nickname` — Gibt den Spitznamen des Benutzers zurück.
  - `user_description` — Gibt die Beschreibung des Benutzers zurück.
  - `user_firstname` — Gibt den Vornamen des Benutzers zurück.
  - `user_lastname` — Gibt den Nachnamen des Benutzers zurück.
  - `user_login` — Gibt den Login des Benutzers zurück.
  - `user_nicename` — Gibt den Slug des Benutzers zurück.
  - `user_email` — Gibt die E-Mail-Adresse des Benutzers zurück.
  - `user_url` — Gibt den Permalink des Benutzers zurück.
  - `user_registered` — Gibt das Registrierungsdatum des Benutzers zurück.
  - `user_activation_key` — Gibt den Aktivierungsschlüssel des Benutzers zurück.
  - `user_status` — Gibt den Status des Benutzers zurück.
  - `user_level` — Gibt die Berechtigungs Ebene des Benutzers zurück.
  - `user_display_name` — Gibt den öffentlichen Name des Benutzers zurück.
  - `user_locale` — Gibt das Sprache des Benutzers zurück.
  - `user_rich_editing` — Gibt den Visueller Editor Status des Benutzers zurück.
  - `user_roles` — Gibt die Rollen des Benutzers zurück.
  - `user_syntax_highlighting` — Gibt das Syntaxhervorhebung-Status des Benutzers zurück.
  - `user_bookmark_posts` — Gibt alle Lesezeichen-Beiträge des Benutzers zurück.
  - `user_like_posts` — Gibt alle Like-Beiträge des Benutzers zurück.
  - `user_wc_recently_viewed_products` — Gibt alle zuletzt angesehenen WooCommerce-Produkte des Benutzers zurück.
  - `ID` — Gibt die Benutzer-ID zurück.

#### [wst_post_terms]

- **Shortcode:** `wst_post_terms`

Gibt alle **Beitrags Begriffe**als Schleife zurück.

**Grundsyntax**

```text
[wst_post_terms taxonomy='category']row_content[/wst_post_terms]
```

**Parameter und Optionen**

- `taxonomy` — Name der Taxonomie. Wenn kein Name anagegeben ist, werden alle Taxonomie des selben Inhaltstypes einbezogen. *(Pflicht; Standard: `category`)*
- `orderby` — Name des Feldes `name | count | slug | term_group | term_order | term_id | none` nach dem sortiert werden soll. *(optional)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `ASC`)*
- `include` — Komma / Leerzeichen getrennte Zeichenfolge der einzuschließenden Term-IDs. *(optional)*
- `exclude` — Komma / Leerzeichen getrennte Zeichenfolge der auszuschließenden Term-IDs. *(optional)*
- `exclude_tree` — Komma / Leerzeichen getrennte Zeichenfolge der auszuschließenden Term-IDs, zusammen mit allen Nachkommen. *(optional)*
- `number` — Maximale Anzahl der zurückgegebenen Begriffe. *(optional)*
- `offset` — Die Nummer, um die die Abfrage der Begriffe versetzt werden soll. *(optional)*
- `name` — Name oder durch Komma / Leerzeichen getrennte Zeichenfolge von Namen, für die Begriffe zurückgegeben werden sollen. *(optional)*
- `slug` — Titelform oder durch Komma / Leerzeichen getrennte Zeichenfolge von Titelformen, für die der Begriff zurückgegeben werden soll. *(optional)*
- `term_taxonomy_id` — Begriff-Taxonomie-ID oder Array von Begriff-Taxonomie-IDs, die bei der Abfrage von Begriffen abgeglichen werden sollen. *(optional)*
- `hierarchical` — Gibt an, ob Begriffe mit nicht leeren Nachkommen eingeschlossen werden sollen. *(optional)*
- `search` — Suchkriterien zur Übereinstimmung von Begriffen Wird vorher und nachher mit Platzhaltern formatiert. *(optional)*
- `name__like` — Begriffe mit Kriterien abrufen, bei denen ein Begriff LIKE "name__like" lautet. *(optional)*
- `description__like` — Begriffe abrufen, bei denen die Beschreibung LIKE "description__like" lautet. *(optional)*
- `pad_counts` — Gibt an, ob die Anzahl der Kind-Begriffe in der Menge der Objektvariablen "count" aufgefüllt werden soll. *(optional; Standard: `0`)*
- `get` — Gibt an, ob Begriffe unabhängig von ihrer Herkunft zurückgegeben werden sollen oder ob die Begriffe leer sind. Akzeptiert "all" oder leer. *(optional)*
- `child_of` — Term-ID, um Kind-Begriffe abzurufen. Wenn mehrere Taxonomien übergeben werden, wird "child_of" ignoriert. *(optional; Standard: `0`)*
- `parent` — Übergeordnete Term-ID, um direkt untergeordnete Begriffe abzurufen. *(optional)*
- `childless` — Auf Begriffe einschränken, die keine Kinder haben. Dieser Parameter hat keine Auswirkung auf nicht hierarchische Taxonomien. *(optional; Standard: `0`)*
- `meta_key` — Begrenzen der Begriffe auf einem bestimmten Metadaten-Schlüssel. Kann zusammen mit meta_value verwendet werden. *(optional)*
- `meta_value` — Begrenzen der Begriffe auf einem bestimmten Metadaten-Wert. Kann zusammen mit meta_key verwendet werden. *(optional)*
- `meta_type` — MySQL-Datentyp, in den der meta_value für Vergleiche umgewandelt wird. *(optional)*
- `meta_compare` — Vergleichsoperator zum Testen von "meta_value". *(optional)*

**Zusätzliche Informationen**

###### Die Beitrags Begriffe als Schleife verwenden

Der folgender Shortcode verwandelt das Feld post terms in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_post_terms taxonomy='category']
	[is_parent]Hauptkategorie[/is_parent][is_children]Kindkategorie[/is_children]
	{{name/wst_post_terms}} - [wst_acf_image id='{{acf_term_id/wst_post_terms}}' field='field_name' size='full']
	[wst_if field='term_name' compare='=' value='News' id='{{term_id/wst_post_terms}}']Is a news term![/wst_if]
[/wst_post_terms]
```

Es können alle [WP_Term](https://codex.wordpress.org/Function_Reference/wp_get_post_terms#Return_Values) Felder `term_id | name | slug | term_group | term_taxonomy_id | taxonomy | description | parent | count` als Platzhalter im Inhalt der Schleife verwenden werden.

Platzhalter können wie folgt maskiert werden: `[term_id]` or `#term_id#`.

Falls bestimmte **ACF Felder** einer Kategorie zugeordnet wurden, so kann jeder Smart Template Shortcode im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{acf_term_id/loop_name}}` angegeben werden (siehe Beispiel).

###### Zusätzliche Shortcodes

 `[is_parent]My Content[/is_parent]` Gibt den anzuzeigenden Inhalt nur für einen Eltern-Begriff zurück.

 `[is_children]My Content[/is_children]` Gibt den anzuzeigenden Inhalt nur für einen Kind-Begriff zurück.

 `[is_first_term]My Content[/is_first_term]` Gibt den anzuzeigenden Inhalt nur für den ersten Begriff zurück.

 `[is_last_term]My Content[/is_last_term]` Gibt den anzuzeigenden Inhalt nur für den letzten Begriff zurück.

#### [wst_post_comments]

- **Shortcode:** `wst_post_comments`

Gibt die Kommentar Funktion des Beitrages zurück.

**Grundsyntax**

```text
[wst_post_comments]
```

**Parameter und Optionen**

- `file` — Das zu anzuzeigende Kommentar Template. *(optional; Standard: `/comments.php`)*
- `separate` — Gibt an, ob die Kommentare nach Kommentartyp getrennt werden sollen. *(optional; Standard: `0`)*

#### [wst_post_password_form]

- **Shortcode:** `wst_post_password_form`

Gibt das Formular für den Passwort geschützten Inhalt eines Beitrages zurück.

**Grundsyntax**

```text
[wst_post_password_form]
```

#### [wst_post_translations]

- **Shortcode:** `wst_post_translations`

Gibt alle Übersetzungen eines Beitrags als Schleife zurück.

**Grundsyntax**

```text
[wst_post_translations]row_content[/wst_post_translations]
```

**Parameter und Optionen**

- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*
- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional; Standard: `post__in`)*

**Zusätzliche Informationen**

###### Beitragsübersetzungen als Schleife verwenden.

Folgender Shortcode generiert eine Schleife, welche alle Beiträge der Reihe nach abarbeitet.

```text
[wst_post_translations]
	{{language/wst_posts}} [wst_post_title id='{{post_id/wst_posts}}']
[/wst_post_translations]
```

Jeder Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{post_id/name_of_the_loop}}` angegeben werden (siehe Beispiel).

#### [wst_post_ics]

- **Shortcode:** `wst_post_ics`

Erstellt einen ICS-Kalender Datei Button.

**Grundsyntax**

```text
[wst_post_ics date_start='1784198923']Der Inhalt des Beitrags.[/wst_post_ics]
```

**Parameter und Optionen**

- `title` — Das title Attribut des Elements. *(optional; Standard: `Zum Kalender hinzufügen`)*
- `date_start` — Startdatum des Kalender Events. *(Pflicht; Standard: `1784198923`)*
- `date_end` — Enddatum des Kalender Events. *(optional)*
- `location` — Ort des Kalender Events. *(optional)*
- `summary` — Titel / Auszug des Kalender Events. *(optional; Standard: `Der Titel des Beitrags.`)*
- `permalink` — Permalink des Kalender Events. *(optional; Standard: `Der Permalink des Beitrags.`)*
- `filename` — Name der ICS Datei. *(optional; Standard: `calendar.ics`)*
- `populate_post_data` — Befüllt das Feld Zusammenfassung, Beschreibung und Permalink mit Beitragsdaten. *(optional; Standard: `1`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*

#### [wst_post_google_calendar]

- **Shortcode:** `wst_post_google_calendar`

Erzeugt einen Google Kalender Button.

**Grundsyntax**

```text
[wst_post_google_calendar date_start='1784198923']Beschreibung des Kalender Events.[/wst_post_google_calendar]
```

**Parameter und Optionen**

- `title` — Der Titel des Buttons. *(optional; Standard: `Zum Kalender hinzufügen`)*
- `date_start` — Startdatum des Kalender Events. *(Pflicht; Standard: `1784198923`)*
- `date_end` — Enddatum des Kalender Events. *(optional)*
- `location` — Ort des Kalender Events. *(optional)*
- `summary` — Titel / Auszug des Kalender Events. *(optional)*
- `populate_post_data` — Befüllt das Feld Zusammenfassung und Beschreibung mit Beitragsdaten. *(optional; Standard: `1`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*

### 5.9 Wetter

- **Interne ID:** `weather`
- **Kategoriefarbe:** `#FDD247`
- **Einträge:** 19

#### [wst_weather_date]

- **Shortcode:** `wst_weather_date`

Gibt das Datum einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_date]
```

**Parameter und Optionen**

- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `d.m.Y`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_time]

- **Shortcode:** `wst_weather_time`

Gibt die Uhrzeit einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_time]
```

**Parameter und Optionen**

- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `H:i`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_sunrise]

- **Shortcode:** `wst_weather_sunrise`

Gibt die Uhrzeit des Sonnenaufgangs einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_sunrise]
```

**Parameter und Optionen**

- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `H:i`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_sunset]

- **Shortcode:** `wst_weather_sunset`

Gibt die Uhrzeit des Sonnenuntergangs einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_sunset]
```

**Parameter und Optionen**

- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `H:i`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_icon]

- **Shortcode:** `wst_weather_icon`

Gibt das Symbol einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_icon]
```

**Parameter und Optionen**

- `width` — Die Breite (in Pixel) des Elements. *(optional; Standard: `50`)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional; Standard: `50`)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `icon_path` — Das Bildpfad der Wettersymbole. *(optional; Standard: `https://openweathermap.org/img/w`)*
- `icon_extension` — Das Bilddateiformat des Wettersymbols. *(optional; Standard: `png`)*
- `icon_type` — Der Typ des Wettersymbols. *(optional; Standard: `auto`)*
  - `auto` — Automatische Erkennung des Symboltyps.
  - `day` — Gibt das Tages Wettersymbol zurück.
  - `night` — Gibt das Nacht Wettersymbol zurück.
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_title]

- **Shortcode:** `wst_weather_title`

Gibt den Titel einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_title]
```

**Parameter und Optionen**

- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_description]

- **Shortcode:** `wst_weather_description`

Gibt die Beschreibung einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_description]
```

**Parameter und Optionen**

- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_temp]

- **Shortcode:** `wst_weather_temp`

Gibt die Temperatur einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_temp]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `1`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_temp_min]

- **Shortcode:** `wst_weather_temp_min`

Gibt die minimale Temperatur einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_temp_min]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `1`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_temp_max]

- **Shortcode:** `wst_weather_temp_max`

Gibt die maximale Temperatur einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_temp_max]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `1`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_pressure]

- **Shortcode:** `wst_weather_pressure`

Gibt den Luftdruck einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_pressure]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `1`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_humidity]

- **Shortcode:** `wst_weather_humidity`

Gibt die Luftfeuchtigkeit einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_humidity]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `0`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_clouds]

- **Shortcode:** `wst_weather_clouds`

Gibt die Bewölkung einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_clouds]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `0`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_wind_speed]

- **Shortcode:** `wst_weather_wind_speed`

Gibt die Windgeschwindigkeit einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_wind_speed]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `1`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_wind_degree]

- **Shortcode:** `wst_weather_wind_degree`

Gibt den Windgrad einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_wind_degree]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `0`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_wind_direction]

- **Shortcode:** `wst_weather_wind_direction`

Gibt die Windrichtung einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_wind_direction]
```

**Parameter und Optionen**

- `format` — Die Formatierung der Windrichtung: `name | desc`. *(optional; Standard: `desc`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_rain_volume]

- **Shortcode:** `wst_weather_rain_volume`

Gibt das Regenvolumen einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_rain_volume]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `1`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_snow_volume]

- **Shortcode:** `wst_weather_snow_volume`

Gibt das Schneevolumen einer Wetterabfrage zurück.

**Grundsyntax**

```text
[wst_weather_snow_volume]
```

**Parameter und Optionen**

- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `decimals` — Die Anzahl an Nachkommastellen. *(optional; Standard: `1`)*
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `address` — Die Straße für Ihren Wetter Standort *(optional)*
- `address_2` — Eine weitere, optionale Adresszeile für Ihren Wetter Standort *(optional)*
- `city` — Die Stadt, in der sich Ihr Wetter befindet. *(optional)*
- `postcode` — Die Postleitzahl, falls vorhanden, in der sich Ihr Wetter befindet. *(optional)*
- `country` — Das Land oder die Provinz, wenn es eine gibt, in welcher sich Ihr Wetter befindet *(optional; Standard: `AT`)*
- `latitude` — Die Breitenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*
- `longitude` — Die Längenkoordinaten, in denen sich Ihr Wetter befindet. *(optional)*

#### [wst_weather_forecast]

- **Shortcode:** `wst_weather_forecast`

Gibt die Wettervorhersage als Schleife zurück.

**Grundsyntax**

```text
[wst_weather_forecast days='5' hours='3']row_content[/wst_weather_forecast]
```

**Parameter und Optionen**

- `days` — Die Anzahl der Tage für eine aktuelle Wettervorhersage. *(Pflicht; Standard: `5`)*
- `hours` — Die Anzahl der Stunden für eine aktuelle Wettervorhersage. *(Pflicht; Standard: `3`)*

**Zusätzliche Informationen**

###### Die Wettervorhersage als Schleife verwenden.

Der folgender Shortcode verwandelt das Feld Wetter in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_weather_forecast days='5' hours='24']
	[wst_weather_date] - [wst_weather_icon] [wst_weather_description] [wst_weather_temp]
[/wst_weather_forecast]
```

### 5.10 Page Builder

- **Interne ID:** `page_builder`
- **Einträge:** 1

#### [wst_mfn_builder_content]

> **Verfügbarkeit:** Dieser Eintrag ist in der aktuellen Admin-Hilfe ausgeblendet, bleibt hier aber vollständig dokumentiert.

- **Shortcode:** `wst_mfn_builder_content`

Gibt den gesamten Inhalt des Muffin Builders zurück.

**Grundsyntax**

```text
[wst_mfn_builder_content]
```

### 5.11 Grid Plugins

- **Interne ID:** `grid`
- **Kategoriefarbe:** `#0069ff`
- **Einträge:** 3

#### [wst_wpgb_grid]

- **Shortcode:** `wst_wpgb_grid`

Rendert ein Gitter mithilfe dem **WP Grid Builder** Plugin.

**Grundsyntax**

```text
[wst_wpgb_grid grid='1']
```

**Parameter und Optionen**

- `grid` — Die ID des Grids. *(Pflicht; Standard: `1`)*
- `grid_mobile` — Die ID des Mobile-Grids. *(optional)*
- `grid_tablet` — Die ID des Tablet-Grids. *(optional)*
- `ajax` — Lädt das Grid und seinen Inhalt mit Ajax. *(optional; Standard: `0`)*
- `ajax_loader` — Der Template Name für den Grid-Ajax-Loader. *(optional; Standard: `grid`)*
- `ajax_delay` — Die Verzögerungszeit in Sekunden, welche angibt, wie lange gewartet werden soll, bis das Grid geladen wird. *(optional; Standard: `0`)*
- `ajax_button` — Zeigt einen Ajax-Button. *(optional; Standard: `0`)*
- `ajax_button_text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Details anzeigen`)*
- `ajax_button_title` — Das title Attribut des Elements. *(optional)*
- `ajax_button_class` — Das class Attribut des Elements. *(optional)*
- `ajax_button_style` — Das style Attribut des Elements. *(optional)*
- `post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes. *(optional; Standard: `post`)*
- `post_status` — Name oder Liste (durch Komma getrennt) des Beitragsstatus. *(optional)*
- `posts_per_page` — Die Anzahl der anzuzeigenden Beiträge pro Seite. *(optional)*
- `posts_per_page_tablet` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem Tablet. *(optional)*
- `posts_per_page_mobile` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem mobilen Gerät (Smartphone). *(optional)*
- `offset` — Die Anzahl der zu versetzenden oder zu übergehenden Beiträgen. *(optional)*
- `related` — Den eigenen Beitrag in der Query nicht ausgegeben. *(optional)*
- `post_ids` — Komma getrennte Beitrag-IDs, um nur diese Beiträge in der Query anzuzeigen. *(optional)*
- `post_names` — Durch Komma getrennte Beitrags-Titelformen, sodass nur diese Beiträge in der Abfrage angezeigt werden. *(optional)*
- `post_parent` — Gibt nur untergeordnete Beiträge von einem übergeordneten Beitrag aus. *(optional)*
  - `current` — Gibt die ID des Beitrages zurück.
  - `parent` — Gibt die ID des übergeordneten Beitrags zurück.
  - `parent|level=1` — Gibt die ID eines spezifischen (Level) übergeordneten Beitrags zurück.
  - `post_id` — Benutzerdefinierte ID eines Beitrages

**Taxonomie Parameter**

- `taxonomy_relation` — Beziehung der verschachtelten Taxonomy Blöcke. *(optional; Standard: `OR`)*
- `reset_taxonomy_query` — Setzt die aktuelle Taxonomie Abfrage zurück. *(optional; Standard: `0`)*
- `reset_posts_query` — Setzt die aktuelle Beitrags (post__in) Abfrage zurück. *(optional; Standard: `0`)*

**Spezifische Taxonomie-Begriffe**

- `my_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Kategorien einer bestimmten Taxonomie. *(optional; Beispiel: `product_cat="clothes"`)*
- `my_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `my_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `my_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Spezifische Begriffe**

- `term_list` — Name oder Liste (durch Komma getrennt) der eingeschränkten Kategorien. *(optional; Beispiel: `term_list="1234,4321"`)*
- `term_list_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `term_list_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `term_list_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Selbe Beitrags Taxonomy**

- `same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional)*
- `same_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `same_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `same_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Primäre Beitrags Taxonomie**

- `primary_post_taxonomy` — Name oder Liste (durch ein Komma getrennt) der eingeschränkten Taxonomie, welche die Primäre Kategorie enthält. *(optional)*
- `primary_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `primary_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `primary_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Advanced Custom Fields**

- `acf_id` — Die ACF ID, unter welcher der Wert gespeichert wird. *(optional)*
- `acf_post_field` — Name des zu einschränkenden ACF Beitrag Objekt Feldes. *(optional)*
- `acf_taxonomy_field` — Name des zu einschränkenden ACF Taxonomy Feldes. *(optional)*
- `acf_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `acf_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `acf_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Lesezeichen**

- `user_bookmark_posts` — Name der Merkliste, um alle gespeicherten Beiträge des Benutzers in der Query auszugeben. *(optional)*

**Gefällt mir**

- `user_like_posts` — Alle gespeicherten Like-Beiträge des Benutzers in der Abfrage ausgeben. *(optional; Standard: `0`)*

**WooCommerce**

- `wc_product_upsell_products` — Alle Zusatzverkäufe (UpSells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_product_cross_sell_products` — Alle Querverkäufe (Cross-Sells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_cart_cross_sell_products` — Ausgabe von Cross-Sell-Produkten basierend auf den Artikeln im Warenkorb. *(optional; Standard: `0`)*
- `wc_product_gallery_images` — Alle Galerie Bilder eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `user_wc_recently_viewed_products` — Gibt alle kürzlich angesehenen WooCommerce-Produkte des Benutzers in der Abfrage aus. *(optional; Standard: `0`)*

**Wetter**

- `weather` — Name oder Liste (Beistrich getrennt) der einschränkenden Wetter Kategorien. *(optional)*
  - `live` — Kategorie der aktuellen Wetterabfrage (Keine Daten bereitgestellt.).
  - `thunderstorm` — **Gewitter** (Gewitter mit leichtem Regen, Gewitter mit Regen, Gewitter mit starkem Regen, Leichtes Gewitter, Gewitter, Starkes Gewitter, Wiederkehrende Gewitter, Gewitter mit leichtem Nieselregen, Gewitter mit Nieselregen, Gewitter mit starkem Nieselregen)
  - `drizzle` — **Nieselregen** (Leichter Nieselregen, Nieselregen, Starker Nieselregen, Leichter nieselartiger Niederschlag, Nieselartiger Niederschlag, Starker nieselartiger Niederschlag, Kurzer Regenschauer und Nieselregen, Starker Regenschauer und Nieselregen, Kurzer, nieselartiger Schauer)
  - `rain` — **Regen** (Leichter Regen, Mäßiger Regen, Starker Regen, Sehr starker Regen, Starkregen, Eisregen, Kurzer, leichter Regenschauer, Kurzer Regenschauer, Kurzer, starker Regenschauer, Wiederkehrende kurze Regenschauer)
  - `snow` — **Schneefall** (Leichter Schneefall, Schneefall, Starker Schneefall, Graupel, Kurzer Graupelschauer, Leichter Schneeregen, Schneeregen, Kurzer, leichter Schneeschauer, Kurzer Schneeschauer, Kurzer, starker Schneeschauer)
  - `atmosphere` — **Atmosphäre** (Dunst, Nebel, Rauch, Dunst, trübe Sicht, Staub und Sandwirbel, Nebel, Sand, Staub, Vulkanasche, Windböen, Tornado)
  - `clear` — **Heiter** (Klarer Himmel)
  - `clouds` — **Wolken** (Leicht bewölkt, Wolkig, Stark bewölkt, Bedeckt)
- `weather_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `weather_forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `weather_forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*

**Datumsfelder**

- `date_year` — 4-stelliges Jahr (z. B. 2011). *(optional)*
- `date_month` — Monatsnummer (von 1 bis 12). *(optional)*
- `date_week` — Woche des Jahres (von 0 bis 53). *(optional)*
- `date_day` — Tag des Monats (von 1 bis 31). *(optional)*
- `date_hour` — Stunde (von 0 bis 23). *(optional)*
- `date_minute` — Minute (von 0 bis 59). *(optional)*
- `date_second` — Sekunde (0 bis 59). *(optional)*
- `date_after` — Datum, nach dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatiblen String. *(optional)*
- `date_before` — Datum, vor dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatible Strings. *(optional)*
- `date_inclusive` — Für after/before, ob der genaue Wert abgeglichen werden soll oder nicht. *(optional; Standard: `0`)*
- `date_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `date_column` — Beitrags-Spalte, nach welcher abgefragt werden soll. *(optional; Standard: `post_date`)*
- `date_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Individuelle Felder**

- `meta_key` — Der Name des Post Metafeldes. *(optional)*
- `meta_value` — Der zu überprüfende Wert. *(optional)*
  - `post_id` — Gibt die ID des Beitrages zurück.
  - `my_meta_key` — Gibt den Wert eines übergebenen Beitrag-Metafeldes zurück.
  - `today` — Gibt das aktuelle Datum (2026-07-16) zurück.
  - `this_weekday` — Gibt den numerischer Tag (4), der aktuellen Woche zurück.
  - `this_week` — Gibt die Wochennummer (29), des aktuellen Jahres zurück (Woche beginnt am Montag).
  - `this_day` — Gibt die aktuelle Tageszahl (16), ohne führende Nullen zurück.
  - `this_month` — Gibt die aktuelle Monatszahl (7), ohne führende Nullen zurück.
  - `this_year` — Gibt das aktuelle Jahr (2026), als vierstellige Zahl zurück.
  - `page_on_front` — Gibt die **ID (10)** der Seite, welche als **Startseite (Startseite)** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `page_for_posts` — Gibt die **ID (0)** der Seite, welche als **Beitragsseite ()** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
- `meta_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `meta_type` — Der Feldtyp: `NUMERIC, BINARY, CHAR, DATE, DATETIME, DECIMAL, SIGNED, TIME, UNSIGNED`. *(optional; Standard: `CHAR`)*
- `meta_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Reihenfolge**

- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional)*
- `orderby_meta_key` — Name des benutzerdefinierten Feldes nach dem sortiert werden soll. *(optional)*
- `orderby_post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes, nach dem sortiert werden soll. *(optional)*
- `orderby_taxonomy_term` — Name oder Liste (durch Pipe getrennt) des Taxonomie Kategorie, nach dem sortiert werden soll. *(optional; Beispiel: `category(category_1,category_2)=date/DESC|post_tag(tag_1,tag_2)=title/ASC`)*
- `orderby_same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der sortierten Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `orderby_primary_post_taxonomy` — Name oder Liste (durch Pipe getrennt) der sortierten Taxonomie mit der primären Beitragskategorie. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*

**Zusätzliche Informationen**

###### Beiträge auf bestimmte Taxonomien einschränken.

Um Beiträge bestimmter Taxonomien im Grid anzuzeigen, muss der eindeutige Name der **Taxonomie**, mit den einschränkenden Kategorien (Beistrich getrennt) als Titelform im Shortcode angegeben werden.

Mit dem vorangestellten Negationszeichen `!`, können Kategorien ausgeschlossen werden.
`[wst_the_grid name='my grid' product_cat='jeans,!shoes' product_tag='stretch' tax_relation='AND']`

Der folgende Shortcode, gibt nur Produkte (z.B.: bei WooCommerce) mit der Kategorie "Jeans" und dem Schlagwort "stretch" in einem Grid aus.

###### Eigene Felder verbinden

Mit folgenden Shortcode können Eigene Felder für zusätzliche Einschränkungen eingebunden werden.

Es wird die Standard [Meta_Query](https://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters) Funktion von WordPress verwendet.

`[wst_the_grid name='my grid' meta_key='field_name' meta_value='today' meta_compare='>=' meta_type='date' meta_relation='OR' meta_key_2='field_name_2' meta_value_2='Y']`

###### Shortcodes im Skin Editor verwenden.

Beim Skin Editor (Essential Grid / The Grid), muss zwingend die **Beitrags-ID** im Shortcode (mittels Platzhalter) übergeben werden.

WP Grid Builder: `[wst_post_title id='{{post.id}}']`

#### [wst_the_grid]

> **Verfügbarkeit:** Dieser Eintrag ist in der aktuellen Admin-Hilfe ausgeblendet, bleibt hier aber vollständig dokumentiert.

- **Shortcode:** `wst_the_grid`

Rendert ein Gitter mithilfe dem **The Grid** Plugin.

**Grundsyntax**

```text
[wst_the_grid name='my_grid']
```

**Parameter und Optionen**

- `name` — Der Name des Grids. *(Pflicht; Standard: `my_grid`)*
- `hash_filters` — Die Grid Filterung über die URL ermöglichen. *(optional; Standard: `0`)*
- `post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes. *(optional; Standard: `post`)*
- `post_status` — Name oder Liste (durch Komma getrennt) des Beitragsstatus. *(optional)*
- `posts_per_page` — Die Anzahl der anzuzeigenden Beiträge pro Seite. *(optional)*
- `posts_per_page_tablet` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem Tablet. *(optional)*
- `posts_per_page_mobile` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem mobilen Gerät (Smartphone). *(optional)*
- `offset` — Die Anzahl der zu versetzenden oder zu übergehenden Beiträgen. *(optional)*
- `related` — Den eigenen Beitrag in der Query nicht ausgegeben. *(optional)*
- `post_ids` — Komma getrennte Beitrag-IDs, um nur diese Beiträge in der Query anzuzeigen. *(optional)*
- `post_names` — Durch Komma getrennte Beitrags-Titelformen, sodass nur diese Beiträge in der Abfrage angezeigt werden. *(optional)*
- `post_parent` — Gibt nur untergeordnete Beiträge von einem übergeordneten Beitrag aus. *(optional)*
  - `current` — Gibt die ID des Beitrages zurück.
  - `parent` — Gibt die ID des übergeordneten Beitrags zurück.
  - `parent|level=1` — Gibt die ID eines spezifischen (Level) übergeordneten Beitrags zurück.
  - `post_id` — Benutzerdefinierte ID eines Beitrages

**Taxonomie Parameter**

- `taxonomy_relation` — Beziehung der verschachtelten Taxonomy Blöcke. *(optional; Standard: `OR`)*
- `reset_taxonomy_query` — Setzt die aktuelle Taxonomie Abfrage zurück. *(optional; Standard: `0`)*
- `reset_posts_query` — Setzt die aktuelle Beitrags (post__in) Abfrage zurück. *(optional; Standard: `0`)*

**Spezifische Taxonomie-Begriffe**

- `my_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Kategorien einer bestimmten Taxonomie. *(optional; Beispiel: `product_cat="clothes"`)*
- `my_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `my_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `my_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Spezifische Begriffe**

- `term_list` — Name oder Liste (durch Komma getrennt) der eingeschränkten Kategorien. *(optional; Beispiel: `term_list="1234,4321"`)*
- `term_list_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `term_list_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `term_list_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Selbe Beitrags Taxonomy**

- `same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional)*
- `same_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `same_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `same_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Primäre Beitrags Taxonomie**

- `primary_post_taxonomy` — Name oder Liste (durch ein Komma getrennt) der eingeschränkten Taxonomie, welche die Primäre Kategorie enthält. *(optional)*
- `primary_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `primary_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `primary_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Advanced Custom Fields**

- `acf_id` — Die ACF ID, unter welcher der Wert gespeichert wird. *(optional)*
- `acf_post_field` — Name des zu einschränkenden ACF Beitrag Objekt Feldes. *(optional)*
- `acf_taxonomy_field` — Name des zu einschränkenden ACF Taxonomy Feldes. *(optional)*
- `acf_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `acf_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `acf_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Lesezeichen**

- `user_bookmark_posts` — Name der Merkliste, um alle gespeicherten Beiträge des Benutzers in der Query auszugeben. *(optional)*

**Gefällt mir**

- `user_like_posts` — Alle gespeicherten Like-Beiträge des Benutzers in der Abfrage ausgeben. *(optional; Standard: `0`)*

**WooCommerce**

- `wc_product_upsell_products` — Alle Zusatzverkäufe (UpSells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_product_cross_sell_products` — Alle Querverkäufe (Cross-Sells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_cart_cross_sell_products` — Ausgabe von Cross-Sell-Produkten basierend auf den Artikeln im Warenkorb. *(optional; Standard: `0`)*
- `wc_product_gallery_images` — Alle Galerie Bilder eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `user_wc_recently_viewed_products` — Gibt alle kürzlich angesehenen WooCommerce-Produkte des Benutzers in der Abfrage aus. *(optional; Standard: `0`)*

**Wetter**

- `weather` — Name oder Liste (Beistrich getrennt) der einschränkenden Wetter Kategorien. *(optional)*
  - `live` — Kategorie der aktuellen Wetterabfrage (Keine Daten bereitgestellt.).
  - `thunderstorm` — **Gewitter** (Gewitter mit leichtem Regen, Gewitter mit Regen, Gewitter mit starkem Regen, Leichtes Gewitter, Gewitter, Starkes Gewitter, Wiederkehrende Gewitter, Gewitter mit leichtem Nieselregen, Gewitter mit Nieselregen, Gewitter mit starkem Nieselregen)
  - `drizzle` — **Nieselregen** (Leichter Nieselregen, Nieselregen, Starker Nieselregen, Leichter nieselartiger Niederschlag, Nieselartiger Niederschlag, Starker nieselartiger Niederschlag, Kurzer Regenschauer und Nieselregen, Starker Regenschauer und Nieselregen, Kurzer, nieselartiger Schauer)
  - `rain` — **Regen** (Leichter Regen, Mäßiger Regen, Starker Regen, Sehr starker Regen, Starkregen, Eisregen, Kurzer, leichter Regenschauer, Kurzer Regenschauer, Kurzer, starker Regenschauer, Wiederkehrende kurze Regenschauer)
  - `snow` — **Schneefall** (Leichter Schneefall, Schneefall, Starker Schneefall, Graupel, Kurzer Graupelschauer, Leichter Schneeregen, Schneeregen, Kurzer, leichter Schneeschauer, Kurzer Schneeschauer, Kurzer, starker Schneeschauer)
  - `atmosphere` — **Atmosphäre** (Dunst, Nebel, Rauch, Dunst, trübe Sicht, Staub und Sandwirbel, Nebel, Sand, Staub, Vulkanasche, Windböen, Tornado)
  - `clear` — **Heiter** (Klarer Himmel)
  - `clouds` — **Wolken** (Leicht bewölkt, Wolkig, Stark bewölkt, Bedeckt)
- `weather_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `weather_forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `weather_forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*

**Datumsfelder**

- `date_year` — 4-stelliges Jahr (z. B. 2011). *(optional)*
- `date_month` — Monatsnummer (von 1 bis 12). *(optional)*
- `date_week` — Woche des Jahres (von 0 bis 53). *(optional)*
- `date_day` — Tag des Monats (von 1 bis 31). *(optional)*
- `date_hour` — Stunde (von 0 bis 23). *(optional)*
- `date_minute` — Minute (von 0 bis 59). *(optional)*
- `date_second` — Sekunde (0 bis 59). *(optional)*
- `date_after` — Datum, nach dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatiblen String. *(optional)*
- `date_before` — Datum, vor dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatible Strings. *(optional)*
- `date_inclusive` — Für after/before, ob der genaue Wert abgeglichen werden soll oder nicht. *(optional; Standard: `0`)*
- `date_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `date_column` — Beitrags-Spalte, nach welcher abgefragt werden soll. *(optional; Standard: `post_date`)*
- `date_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Individuelle Felder**

- `meta_key` — Der Name des Post Metafeldes. *(optional)*
- `meta_value` — Der zu überprüfende Wert. *(optional)*
  - `post_id` — Gibt die ID des Beitrages zurück.
  - `my_meta_key` — Gibt den Wert eines übergebenen Beitrag-Metafeldes zurück.
  - `today` — Gibt das aktuelle Datum (2026-07-16) zurück.
  - `this_weekday` — Gibt den numerischer Tag (4), der aktuellen Woche zurück.
  - `this_week` — Gibt die Wochennummer (29), des aktuellen Jahres zurück (Woche beginnt am Montag).
  - `this_day` — Gibt die aktuelle Tageszahl (16), ohne führende Nullen zurück.
  - `this_month` — Gibt die aktuelle Monatszahl (7), ohne führende Nullen zurück.
  - `this_year` — Gibt das aktuelle Jahr (2026), als vierstellige Zahl zurück.
  - `page_on_front` — Gibt die **ID (10)** der Seite, welche als **Startseite (Startseite)** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `page_for_posts` — Gibt die **ID (0)** der Seite, welche als **Beitragsseite ()** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
- `meta_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `meta_type` — Der Feldtyp: `NUMERIC, BINARY, CHAR, DATE, DATETIME, DECIMAL, SIGNED, TIME, UNSIGNED`. *(optional; Standard: `CHAR`)*
- `meta_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Reihenfolge**

- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional)*
- `orderby_meta_key` — Name des benutzerdefinierten Feldes nach dem sortiert werden soll. *(optional)*
- `orderby_post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes, nach dem sortiert werden soll. *(optional)*
- `orderby_taxonomy_term` — Name oder Liste (durch Pipe getrennt) des Taxonomie Kategorie, nach dem sortiert werden soll. *(optional; Beispiel: `category(category_1,category_2)=date/DESC|post_tag(tag_1,tag_2)=title/ASC`)*
- `orderby_same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der sortierten Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `orderby_primary_post_taxonomy` — Name oder Liste (durch Pipe getrennt) der sortierten Taxonomie mit der primären Beitragskategorie. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*

**Zusätzliche Informationen**

###### Beiträge auf bestimmte Taxonomien einschränken.

Um Beiträge bestimmter Taxonomien im Grid anzuzeigen, muss der eindeutige Name der **Taxonomie**, mit den einschränkenden Kategorien (Beistrich getrennt) als Titelform im Shortcode angegeben werden.

Mit dem vorangestellten Negationszeichen `!`, können Kategorien ausgeschlossen werden.
`[wst_the_grid name='my grid' product_cat='jeans,!shoes' product_tag='stretch' tax_relation='AND']`

Der folgende Shortcode, gibt nur Produkte (z.B.: bei WooCommerce) mit der Kategorie "Jeans" und dem Schlagwort "stretch" in einem Grid aus.

###### Eigene Felder verbinden

Mit folgenden Shortcode können Eigene Felder für zusätzliche Einschränkungen eingebunden werden.

Es wird die Standard [Meta_Query](https://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters) Funktion von WordPress verwendet.

`[wst_the_grid name='my grid' meta_key='field_name' meta_value='today' meta_compare='>=' meta_type='date' meta_relation='OR' meta_key_2='field_name_2' meta_value_2='Y']`

###### Shortcodes im Skin Editor verwenden.

Beim Skin Editor (Essential Grid / The Grid), muss zwingend die **Beitrags-ID** im Shortcode (mittels Platzhalter) übergeben werden.

WP Grid Builder: `[wst_post_title id='{{post.id}}']`

#### [wst_ess_grid]

> **Verfügbarkeit:** Dieser Eintrag ist in der aktuellen Admin-Hilfe ausgeblendet, bleibt hier aber vollständig dokumentiert.

- **Shortcode:** `wst_ess_grid`

Rendert ein Gitter mithilfe dem **Essential Grid** Plugin.

**Grundsyntax**

```text
[wst_ess_grid alias='my_grid']
```

**Parameter und Optionen**

- `alias` — Der Name des Grids. *(Pflicht; Standard: `my_grid`)*
- `post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes. *(optional; Standard: `post`)*
- `post_status` — Name oder Liste (durch Komma getrennt) des Beitragsstatus. *(optional)*
- `posts_per_page` — Die Anzahl der anzuzeigenden Beiträge pro Seite. *(optional)*
- `posts_per_page_tablet` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem Tablet. *(optional)*
- `posts_per_page_mobile` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem mobilen Gerät (Smartphone). *(optional)*
- `offset` — Die Anzahl der zu versetzenden oder zu übergehenden Beiträgen. *(optional)*
- `related` — Den eigenen Beitrag in der Query nicht ausgegeben. *(optional)*
- `post_ids` — Komma getrennte Beitrag-IDs, um nur diese Beiträge in der Query anzuzeigen. *(optional)*
- `post_names` — Durch Komma getrennte Beitrags-Titelformen, sodass nur diese Beiträge in der Abfrage angezeigt werden. *(optional)*
- `post_parent` — Gibt nur untergeordnete Beiträge von einem übergeordneten Beitrag aus. *(optional)*
  - `current` — Gibt die ID des Beitrages zurück.
  - `parent` — Gibt die ID des übergeordneten Beitrags zurück.
  - `parent|level=1` — Gibt die ID eines spezifischen (Level) übergeordneten Beitrags zurück.
  - `post_id` — Benutzerdefinierte ID eines Beitrages

**Taxonomie Parameter**

- `taxonomy_relation` — Beziehung der verschachtelten Taxonomy Blöcke. *(optional; Standard: `OR`)*
- `reset_taxonomy_query` — Setzt die aktuelle Taxonomie Abfrage zurück. *(optional; Standard: `0`)*
- `reset_posts_query` — Setzt die aktuelle Beitrags (post__in) Abfrage zurück. *(optional; Standard: `0`)*

**Spezifische Taxonomie-Begriffe**

- `my_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Kategorien einer bestimmten Taxonomie. *(optional; Beispiel: `product_cat="clothes"`)*
- `my_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `my_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `my_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Spezifische Begriffe**

- `term_list` — Name oder Liste (durch Komma getrennt) der eingeschränkten Kategorien. *(optional; Beispiel: `term_list="1234,4321"`)*
- `term_list_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `term_list_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `term_list_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Selbe Beitrags Taxonomy**

- `same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional)*
- `same_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `same_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `same_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Primäre Beitrags Taxonomie**

- `primary_post_taxonomy` — Name oder Liste (durch ein Komma getrennt) der eingeschränkten Taxonomie, welche die Primäre Kategorie enthält. *(optional)*
- `primary_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `primary_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `primary_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Advanced Custom Fields**

- `acf_id` — Die ACF ID, unter welcher der Wert gespeichert wird. *(optional)*
- `acf_post_field` — Name des zu einschränkenden ACF Beitrag Objekt Feldes. *(optional)*
- `acf_taxonomy_field` — Name des zu einschränkenden ACF Taxonomy Feldes. *(optional)*
- `acf_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `acf_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `acf_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Lesezeichen**

- `user_bookmark_posts` — Name der Merkliste, um alle gespeicherten Beiträge des Benutzers in der Query auszugeben. *(optional)*

**Gefällt mir**

- `user_like_posts` — Alle gespeicherten Like-Beiträge des Benutzers in der Abfrage ausgeben. *(optional; Standard: `0`)*

**WooCommerce**

- `wc_product_upsell_products` — Alle Zusatzverkäufe (UpSells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_product_cross_sell_products` — Alle Querverkäufe (Cross-Sells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_cart_cross_sell_products` — Ausgabe von Cross-Sell-Produkten basierend auf den Artikeln im Warenkorb. *(optional; Standard: `0`)*
- `wc_product_gallery_images` — Alle Galerie Bilder eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `user_wc_recently_viewed_products` — Gibt alle kürzlich angesehenen WooCommerce-Produkte des Benutzers in der Abfrage aus. *(optional; Standard: `0`)*

**Wetter**

- `weather` — Name oder Liste (Beistrich getrennt) der einschränkenden Wetter Kategorien. *(optional)*
  - `live` — Kategorie der aktuellen Wetterabfrage (Keine Daten bereitgestellt.).
  - `thunderstorm` — **Gewitter** (Gewitter mit leichtem Regen, Gewitter mit Regen, Gewitter mit starkem Regen, Leichtes Gewitter, Gewitter, Starkes Gewitter, Wiederkehrende Gewitter, Gewitter mit leichtem Nieselregen, Gewitter mit Nieselregen, Gewitter mit starkem Nieselregen)
  - `drizzle` — **Nieselregen** (Leichter Nieselregen, Nieselregen, Starker Nieselregen, Leichter nieselartiger Niederschlag, Nieselartiger Niederschlag, Starker nieselartiger Niederschlag, Kurzer Regenschauer und Nieselregen, Starker Regenschauer und Nieselregen, Kurzer, nieselartiger Schauer)
  - `rain` — **Regen** (Leichter Regen, Mäßiger Regen, Starker Regen, Sehr starker Regen, Starkregen, Eisregen, Kurzer, leichter Regenschauer, Kurzer Regenschauer, Kurzer, starker Regenschauer, Wiederkehrende kurze Regenschauer)
  - `snow` — **Schneefall** (Leichter Schneefall, Schneefall, Starker Schneefall, Graupel, Kurzer Graupelschauer, Leichter Schneeregen, Schneeregen, Kurzer, leichter Schneeschauer, Kurzer Schneeschauer, Kurzer, starker Schneeschauer)
  - `atmosphere` — **Atmosphäre** (Dunst, Nebel, Rauch, Dunst, trübe Sicht, Staub und Sandwirbel, Nebel, Sand, Staub, Vulkanasche, Windböen, Tornado)
  - `clear` — **Heiter** (Klarer Himmel)
  - `clouds` — **Wolken** (Leicht bewölkt, Wolkig, Stark bewölkt, Bedeckt)
- `weather_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `weather_forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `weather_forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*

**Datumsfelder**

- `date_year` — 4-stelliges Jahr (z. B. 2011). *(optional)*
- `date_month` — Monatsnummer (von 1 bis 12). *(optional)*
- `date_week` — Woche des Jahres (von 0 bis 53). *(optional)*
- `date_day` — Tag des Monats (von 1 bis 31). *(optional)*
- `date_hour` — Stunde (von 0 bis 23). *(optional)*
- `date_minute` — Minute (von 0 bis 59). *(optional)*
- `date_second` — Sekunde (0 bis 59). *(optional)*
- `date_after` — Datum, nach dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatiblen String. *(optional)*
- `date_before` — Datum, vor dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatible Strings. *(optional)*
- `date_inclusive` — Für after/before, ob der genaue Wert abgeglichen werden soll oder nicht. *(optional; Standard: `0`)*
- `date_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `date_column` — Beitrags-Spalte, nach welcher abgefragt werden soll. *(optional; Standard: `post_date`)*
- `date_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Individuelle Felder**

- `meta_key` — Der Name des Post Metafeldes. *(optional)*
- `meta_value` — Der zu überprüfende Wert. *(optional)*
  - `post_id` — Gibt die ID des Beitrages zurück.
  - `my_meta_key` — Gibt den Wert eines übergebenen Beitrag-Metafeldes zurück.
  - `today` — Gibt das aktuelle Datum (2026-07-16) zurück.
  - `this_weekday` — Gibt den numerischer Tag (4), der aktuellen Woche zurück.
  - `this_week` — Gibt die Wochennummer (29), des aktuellen Jahres zurück (Woche beginnt am Montag).
  - `this_day` — Gibt die aktuelle Tageszahl (16), ohne führende Nullen zurück.
  - `this_month` — Gibt die aktuelle Monatszahl (7), ohne führende Nullen zurück.
  - `this_year` — Gibt das aktuelle Jahr (2026), als vierstellige Zahl zurück.
  - `page_on_front` — Gibt die **ID (10)** der Seite, welche als **Startseite (Startseite)** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `page_for_posts` — Gibt die **ID (0)** der Seite, welche als **Beitragsseite ()** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
- `meta_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `meta_type` — Der Feldtyp: `NUMERIC, BINARY, CHAR, DATE, DATETIME, DECIMAL, SIGNED, TIME, UNSIGNED`. *(optional; Standard: `CHAR`)*
- `meta_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Reihenfolge**

- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional)*
- `orderby_meta_key` — Name des benutzerdefinierten Feldes nach dem sortiert werden soll. *(optional)*
- `orderby_post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes, nach dem sortiert werden soll. *(optional)*
- `orderby_taxonomy_term` — Name oder Liste (durch Pipe getrennt) des Taxonomie Kategorie, nach dem sortiert werden soll. *(optional; Beispiel: `category(category_1,category_2)=date/DESC|post_tag(tag_1,tag_2)=title/ASC`)*
- `orderby_same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der sortierten Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `orderby_primary_post_taxonomy` — Name oder Liste (durch Pipe getrennt) der sortierten Taxonomie mit der primären Beitragskategorie. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*

**Zusätzliche Informationen**

###### Beiträge auf bestimmte Taxonomien einschränken.

Um Beiträge bestimmter Taxonomien im Grid anzuzeigen, muss der eindeutige Name der **Taxonomie**, mit den einschränkenden Kategorien (Beistrich getrennt) als Titelform im Shortcode angegeben werden.

Mit dem vorangestellten Negationszeichen `!`, können Kategorien ausgeschlossen werden.
`[wst_the_grid name='my grid' product_cat='jeans,!shoes' product_tag='stretch' tax_relation='AND']`

Der folgende Shortcode, gibt nur Produkte (z.B.: bei WooCommerce) mit der Kategorie "Jeans" und dem Schlagwort "stretch" in einem Grid aus.

###### Eigene Felder verbinden

Mit folgenden Shortcode können Eigene Felder für zusätzliche Einschränkungen eingebunden werden.

Es wird die Standard [Meta_Query](https://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters) Funktion von WordPress verwendet.

`[wst_the_grid name='my grid' meta_key='field_name' meta_value='today' meta_compare='>=' meta_type='date' meta_relation='OR' meta_key_2='field_name_2' meta_value_2='Y']`

###### Shortcodes im Skin Editor verwenden.

Beim Skin Editor (Essential Grid / The Grid), muss zwingend die **Beitrags-ID** im Shortcode (mittels Platzhalter) übergeben werden.

WP Grid Builder: `[wst_post_title id='{{post.id}}']`

### 5.12 ACF Grundlage

- **Interne ID:** `acf_general`
- **Kategoriefarbe:** `#00dab4`
- **Einträge:** 5

#### [wst_acf]

- **Shortcode:** `wst_acf`

Der Standard Shortcode von ACF selber. Funktioniert nur für einfache textbasierte Werte.

**Grundsyntax**

```text
[wst_acf field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

#### [wst_acf_text]

- **Shortcode:** `wst_acf_text`

Gibt das ACF Text Feld zurück.

**Grundsyntax**

```text
[wst_acf_text field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `words` — Beschneidet den Text auf eine bestimmte Anzahl von Wörtern. *(optional)*
- `chars` — Beschneidet den Text auf eine bestimmte Anzahl von Zeichen. *(optional)*

#### [wst_acf_number]

- **Shortcode:** `wst_acf_number`

Gibt das ACF Numerisch Feld zurück.

**Grundsyntax**

```text
[wst_acf_number field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `decimals` — Genauigkeit der Anzahl der Dezimalstellen. *(optional; Standard: `0`)*

#### [wst_acf_range]

- **Shortcode:** `wst_acf_range`

Gibt das ACF Bereich Feld zurück.

**Grundsyntax**

```text
[wst_acf_range field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `decimals` — Genauigkeit der Anzahl der Dezimalstellen. *(optional; Standard: `0`)*

#### [wst_acf_url]

- **Shortcode:** `wst_acf_url`

Gibt das ACF URL Feld zurück.

**Grundsyntax**

```text
[wst_acf_url field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `protocol` — Fügt der URL automatisch das Protokoll hinzu, falls es fehlt. *(optional; Standard: `1`)*

### 5.13 ACF Inhalt

- **Interne ID:** `acf_content`
- **Kategoriefarbe:** `#00dab4`
- **Einträge:** 8

#### [wst_acf_image]

- **Shortcode:** `wst_acf_image`

Gibt das ACF Bild Feld zurück.

**Grundsyntax**

```text
[wst_acf_image field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `thumbnail`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `attr` — HTML Attribute des Bildes als Query String. *(optional)*
- `inline` — Das Bild inline als Data-URL (Base64-kodiert für Nicht-SVG-Formate) oder direkt als rohen SVG-Code einbetten. *(optional; Standard: `0`)*
- `srcset` — Berechnet die Bilder, welche in ein srcset-Attribut aufgenommen werden sollen. *(optional; Standard: `1`)*

#### [wst_acf_gallery_image]

- **Shortcode:** `wst_acf_gallery_image`

Gibt ein bestimmtes Bild aus dem Feld ACF Galerie zurück.

**Grundsyntax**

```text
[wst_acf_gallery_image field='field_name' image='1']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `image` — Die Nummer des Bildes (z.B.: 1, 2, first, last, usw.), das zurückgegeben werden soll. *(Pflicht; Standard: `1`)*
- `size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `thumbnail`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `attr` — HTML Attribute des Bildes als Query String. *(optional)*
- `inline` — Das Bild inline als Data-URL (Base64-kodiert für Nicht-SVG-Formate) oder direkt als rohen SVG-Code einbetten. *(optional)*
- `srcset` — Berechnet die Bilder, welche in ein srcset-Attribut aufgenommen werden sollen. *(optional; Standard: `1`)*

#### [wst_acf_file]

- **Shortcode:** `wst_acf_file`

Gibt das ACF Datei Feld zurück.

**Grundsyntax**

```text
[wst_acf_file field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `title` — Das title Attribut des Elements. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `target` — Die Zielfensterbasis des Elements: `_self | _blank | _parent | _top`. *(optional; Standard: `_self`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `url` — Gibt die URL der Datei zurück.
  - `title` — Gibt den Titel der Datei zurück.
  - `size` — Gibt die Größe der Datei zurück.
  - `type` — Gibt den Typ der Datei zurück.

#### [wst_acf_file_title]

- **Shortcode:** `wst_acf_file_title`

Gibt die ACF Datei Titel-Informationen zurück.

**Grundsyntax**

```text
[wst_acf_file_title field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

#### [wst_acf_file_size]

- **Shortcode:** `wst_acf_file_size`

Gibt die ACF Datei Größeninformationen zurück.

**Grundsyntax**

```text
[wst_acf_file_size field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

#### [wst_acf_file_type]

- **Shortcode:** `wst_acf_file_type`

Gibt die ACF Datei Typinformationen zurück.

**Grundsyntax**

```text
[wst_acf_file_type field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

#### [wst_acf_wysiwyg]

- **Shortcode:** `wst_acf_wysiwyg`

Gibt das ACF Feld zurück.

**Grundsyntax**

```text
[wst_acf_wysiwyg field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `words` — Beschneidet den Text auf eine bestimmte Anzahl von Wörtern. *(optional)*
- `chars` — Beschneidet den Text auf eine bestimmte Anzahl von Zeichen. *(optional)*
- `more` — Was angehängt werden soll, wenn der String gekürzt wird. *(optional; Standard: `…`)*
- `strip_tags` — Alle HTML-Tags, einschließlich Script und Style, werden entfernt. *(optional)*
- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `read_more` — Der Typ des Read More-Tags. *(optional; Standard: `wp`)*
  - `wp` — Gibt den Standard WordPress Read More-Tag zurück.
  - `expandable` — Gibt ein erweiterbares Read More-Tag zurück.
- `read_more_icon` — Das mehr lesen Link-Icon. *(optional; Standard: `+`)*
- `read_more_text` — Der mehr lesen Linktext. *(optional; Standard: `Weiterlesen`)*
- `read_less_icon` — Das weniger lesen Link-Icon. *(optional; Standard: `−`)*
- `read_less_text` — Der weniger lesen Linktext. *(optional; Standard: `Weiterlesen`)*
- `read_more_link_position` — Die Position des "Weiterlesen"-Links. *(optional; Standard: `bottom`)*

#### [wst_acf_gallery]

- **Shortcode:** `wst_acf_gallery`

Gibt das ACF Feld als Standard WordPress Galerie zurück.

**Grundsyntax**

```text
[wst_acf_gallery field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

**Zusätzliche Informationen**

Es können alle Parameter des [WordPress Galerie Shortcodes](https://codex.wordpress.org/Gallery_Shortcode) verwendet werden.

### 5.14 ACF Auswahl

- **Interne ID:** `acf_selection`
- **Kategoriefarbe:** `#00dab4`
- **Einträge:** 4

#### [wst_acf_select]

- **Shortcode:** `wst_acf_select`

Gibt das ACF Auswahl Feld zurück.

**Grundsyntax**

```text
[wst_acf_select field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `values` — Gibt die Werte des Elements zurück.
  - `labels` — Gibt die Beschriftungen des Elements zurück.
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

**Zusätzliche Informationen**

###### Benutzerdefiniertes HTML

Das folgende Beispiel generiert ein benutzerdefiniertes HTML aus dem ausgewählten Feld.

```text
[wst_foreach list='{{acf_select_field|fields=raw}}']
	Key: {{key/wst_foreach}} - Value:{{value/wst_foreach}}
[/wst_foreach]
```

#### [wst_acf_radio]

- **Shortcode:** `wst_acf_radio`

Gibt das ACF Optionsfeld Feld zurück.

**Grundsyntax**

```text
[wst_acf_radio field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `values` — Gibt die Werte des Elements zurück.
  - `labels` — Gibt die Beschriftungen des Elements zurück.
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

**Zusätzliche Informationen**

###### Benutzerdefiniertes HTML

Das folgende Beispiel generiert ein benutzerdefiniertes HTML aus dem ausgewählten Feld.

```text
[wst_foreach list='{{acf_radio_field|fields=raw}}']
	Key: {{key/wst_foreach}} - Value:{{value/wst_foreach}}
[/wst_foreach]
```

#### [wst_acf_checkbox]

- **Shortcode:** `wst_acf_checkbox`

Gibt das ACF Auswahlkästchen Feld zurück.

**Grundsyntax**

```text
[wst_acf_checkbox field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `values` — Gibt die Werte des Elements zurück.
  - `labels` — Gibt die Beschriftungen des Elements zurück.
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

**Zusätzliche Informationen**

###### Benutzerdefiniertes HTML

Das folgende Beispiel generiert ein benutzerdefiniertes HTML aus dem ausgewählten Feld.

```text
[wst_foreach list='{{acf_checkbox_field|fields=raw}}']
	Key: {{key/wst_foreach}} - Value:{{value/wst_foreach}}
[/wst_foreach]
```

#### [wst_acf_button_group]

- **Shortcode:** `wst_acf_button_group`

Gibt das ACF Button-Gruppe Feld zurück.

**Grundsyntax**

```text
[wst_acf_button_group field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `values` — Gibt die Werte des Elements zurück.
  - `labels` — Gibt die Beschriftungen des Elements zurück.
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

**Zusätzliche Informationen**

###### Benutzerdefiniertes HTML

Das folgende Beispiel generiert ein benutzerdefiniertes HTML aus dem ausgewählten Feld.

```text
[wst_foreach list='{{acf_button_group_field|fields=raw}}']
	Key: {{key/wst_foreach}} - Value:{{value/wst_foreach}}
[/wst_foreach]
```

### 5.15 ACF Relation

- **Interne ID:** `acf_relation`
- **Kategoriefarbe:** `#00dab4`
- **Einträge:** 5

#### [wst_acf_link]

- **Shortcode:** `wst_acf_link`

Gibt das ACF Link Feld zurück.

**Grundsyntax**

```text
[wst_acf_link field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `title` — Das title Attribut des Elements. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `aria_label` — Das aria-label Attribut des Elements. *(optional)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `title` — Gibt den Titel des Elements zurück.
  - `url` — Gibt die URL des Elements zurück.
  - `target` — Gibt das Ziel des Elements zurück.

#### [wst_acf_page_link]

- **Shortcode:** `wst_acf_page_link`

Gibt das ACF Seitenlink Feld zurück.

**Grundsyntax**

```text
[wst_acf_page_link field='field_name' output='html']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `title` — Das title Attribut des Elements. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `target` — Die Zielfensterbasis des Elements: `_self | _blank | _parent | _top`. *(optional; Standard: `_self`)*
- `output` — Der Ausgabetyp des Elements. *(Pflicht; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `title` — Gibt den Titel des Elements zurück.
  - `url` — Gibt die URL des Elements zurück.

#### [wst_acf_post_object]

- **Shortcode:** `wst_acf_post_object`

Gibt das ACF Beitrags-Objekt Feld als Schleife zurück.

**Grundsyntax**

```text
[wst_acf_post_object field='field_name']row_content[/wst_acf_post_object]
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*
- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional; Standard: `post__in`)*

**Zusätzliche Informationen**

###### ACF Beitrags Objekt Feld als Schleife verwenden

Der folgender Shortcode verwandelt das Feld ACF post object in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_acf_post_object field='field_name']
	[wst_post_title id='{{post_id/wst_acf_post_object}}']
[/wst_acf_post_object]
```

###### ACF Beitrags Objekt Felder als verschachtelten Schleifen verwenden.

Folgendes Beispiel zeigt mehrere ACF Beitrags Objekt Felder als verschachtelte Schleifen.

```text
[wst_acf_post_object field='post_products']
	[wst_post_title id='{{post_id/wst_acf_post_object}}']
	[wst_acf_post_object_b field='product_posts' id='{{post_id/wst_acf_post_object}}']
		[wst_post_title id='{{post_id/wst_acf_post_object_b}}']
	[/wst_acf_post_object_b]
[/wst_acf_post_object]
```

Jeder Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{post_id/loop_name}}` angegeben werden (siehe Beispiel).

#### [wst_acf_relationship]

- **Shortcode:** `wst_acf_relationship`

Gibt das ACF Beziehung Feld als Schleife zurück.

**Grundsyntax**

```text
[wst_acf_relationship field='field_name']row_content[/wst_acf_relationship]
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*
- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional; Standard: `post__in`)*

**Zusätzliche Informationen**

###### ACF Beziehungs Feld als Schleife verwenden

Der folgender Shortcode verwandelt das Feld ACF relationship in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_acf_relationship field='field_name']
	[wst_post_title id='{{post_id/wst_acf_relationship}}']
[/wst_acf_relationship]
```

###### ACF Beziehungs Felder als verschachtelten Schleifen verwenden.

Folgendes Beispiel zeigt mehrere ACF Beziehungs Felder als verschachtelte Schleifen.

```text
[wst_acf_relationship field='post_products']
	[wst_post_title id='{{post_id/wst_acf_relationship}}']
	[wst_acf_relationship_b field='product_posts' id='{{post_id/wst_acf_relationship}}']
		[wst_post_title id='{{post_id/wst_acf_relationship_b}}']
	[/wst_acf_relationship_b]
[/wst_acf_relationship]
```

Jeder Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{post_id/loop_name}}` angegeben werden (siehe Beispiel).

#### [wst_acf_taxonomy]

- **Shortcode:** `wst_acf_taxonomy`

Gibt das ACF Taxonomie Feld als Schleife zurück.

**Grundsyntax**

```text
[wst_acf_taxonomy field='field_name']row_content[/wst_acf_taxonomy]
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `orderby` — Name des Feldes `name | count | slug | term_group | term_order | term_id | none` nach dem sortiert werden soll. *(optional)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `ASC`)*
- `hide_empty` — Gibt an, ob Begriffe ausgeblendet werden sollen, die keinem Beitrag zugeordnet sind. *(optional; Standard: `0`)*
- `include` — Komma / Leerzeichen getrennte Zeichenfolge der einzuschließenden Term-IDs. *(optional)*
- `exclude` — Komma / Leerzeichen getrennte Zeichenfolge der auszuschließenden Term-IDs. *(optional)*
- `exclude_tree` — Komma / Leerzeichen getrennte Zeichenfolge der auszuschließenden Term-IDs, zusammen mit allen Nachkommen. *(optional)*
- `number` — Maximale Anzahl der zurückgegebenen Begriffe. *(optional)*
- `offset` — Die Nummer, um die die Abfrage der Begriffe versetzt werden soll. *(optional)*
- `name` — Name oder durch Komma / Leerzeichen getrennte Zeichenfolge von Namen, für die Begriffe zurückgegeben werden sollen. *(optional)*
- `slug` — Titelform oder durch Komma / Leerzeichen getrennte Zeichenfolge von Titelformen, für die der Begriff zurückgegeben werden soll. *(optional)*
- `hierarchical` — Gibt an, ob Begriffe mit nicht leeren Nachkommen eingeschlossen werden sollen. *(optional)*
- `search` — Suchkriterien zur Übereinstimmung von Begriffen Wird vorher und nachher mit Platzhaltern formatiert. *(optional)*
- `name__like` — Begriffe mit Kriterien abrufen, bei denen ein Begriff LIKE "name__like" lautet. *(optional)*
- `description__like` — Begriffe abrufen, bei denen die Beschreibung LIKE "description__like" lautet. *(optional)*
- `pad_counts` — Gibt an, ob die Anzahl der Kind-Begriffe in der Menge der Objektvariablen "count" aufgefüllt werden soll. *(optional; Standard: `0`)*
- `get` — Gibt an, ob Begriffe unabhängig von ihrer Herkunft zurückgegeben werden sollen oder ob die Begriffe leer sind. Akzeptiert "all" oder leer. *(optional)*
- `child_of` — Term-ID, um Kind-Begriffe abzurufen. Wenn mehrere Taxonomien übergeben werden, wird "child_of" ignoriert. *(optional; Standard: `0`)*
- `parent` — Übergeordnete Term-ID, um direkt untergeordnete Begriffe abzurufen. *(optional)*
- `childless` — Auf Begriffe einschränken, die keine Kinder haben. Dieser Parameter hat keine Auswirkung auf nicht hierarchische Taxonomien. *(optional; Standard: `0`)*
- `meta_key` — Begrenzen der Begriffe auf einem bestimmten Metadaten-Schlüssel. Kann zusammen mit meta_value verwendet werden. *(optional)*
- `meta_value` — Begrenzen der Begriffe auf einem bestimmten Metadaten-Wert. Kann zusammen mit meta_key verwendet werden. *(optional)*
- `meta_type` — MySQL-Datentyp, in den der meta_value für Vergleiche umgewandelt wird. *(optional)*
- `meta_compare` — Vergleichsoperator zum Testen von "meta_value". *(optional)*

**Zusätzliche Informationen**

###### Das ACF Taxonomie Feld als Schleife verwenden

Der folgender Shortcode verwandelt das Feld ACF taxonomy in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_acf_taxonomy field='field_name']
	[is_parent]Hauptkategorie[/is_parent][is_children]Kindkategorie[/is_children]
	[name] - [wst_acf_image id='{{acf_term_id/wst_acf_taxonomy}}' field='field_name' size='full']
	[wst_if field='term_name' compare='=' value='News' id='{{term_id/wst_post_terms}}']Is a news term![/wst_if]
[/wst_acf_taxonomy]
```

Es können alle [WP_Term](https://codex.wordpress.org/Function_Reference/wp_get_post_terms#Return_Values) Felder `term_id | name | slug | term_group | term_taxonomy_id | taxonomy | description | parent | count` als Platzhalter im Inhalt der Schleife verwenden werden.

Falls bestimmte **ACF Felder** einer Kategorie zugeordnet wurden, so kann jeder Smart Template Shortcode im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{acf_term_id/loop_name}}` angegeben werden (siehe Beispiel).

Mit dem Shortcode `[is_parent]` und `[is_children]` kann definiert werden, ob bestimmte Inhalte in der Haupt oder Kind Kategorie ausgegeben werden sollen.

Platzhalter können wie folgt maskiert werden: `[term_id]` or `#term_id#`.

### 5.16 ACF Erweitert

- **Interne ID:** `acf_advanced`
- **Kategoriefarbe:** `#00dab4`
- **Einträge:** 8

#### [wst_acf_date]

- **Shortcode:** `wst_acf_date`

Gibt das ACF Datumspicker Feld zurück.

**Grundsyntax**

```text
[wst_acf_date field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `j. F Y`)*
- `modify` — Die [Modifikation](http://php.net/manual/de/datetime.formats.relative.php) des auszugebenden Datums. *(optional)*

#### [wst_acf_date_time]

- **Shortcode:** `wst_acf_date_time`

Gibt das ACF Datums- und Zeitauswahl Feld zurück.

**Grundsyntax**

```text
[wst_acf_date_time field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `j. F Y G:i`)*
- `modify` — Die [Modifikation](http://php.net/manual/de/datetime.formats.relative.php) des auszugebenden Datums. *(optional)*

#### [wst_acf_time]

- **Shortcode:** `wst_acf_time`

Gibt das ACF Zeitpicker Feld zurück.

**Grundsyntax**

```text
[wst_acf_time field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `G:i`)*
- `modify` — Die [Modifikation](http://php.net/manual/de/datetime.formats.relative.php) des auszugebenden Datums. *(optional)*

#### [wst_acf_color]

- **Shortcode:** `wst_acf_color`

Gibt das ACF Farbpicker Feld zurück.

**Grundsyntax**

```text
[wst_acf_color field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabe Typ des Farbwertes. *(optional; Standard: `rgb`)*
  - `rgb` — Gibt die RGB-Farbwerte zurück.
  - `rgba` — Gibt die RGB-Farbwerte mit einem Alphakanal zurück.
  - `hex` — Gibt die hexadezimalen Farbwerte zurück.
  - `hsl` — Gibt die RGB-Farbwerte zurück.
  - `hsla` — Gibt die RGB-Farbwerte mit einem Alphakanal zurück.
  - `r` — Gibt die Intensität der Farbe Rot zurück.
  - `g` — Gibt die Intensität der Farbe Grün zurück.
  - `b` — Gibt die Intensität der Farbe Blau zurück.
  - `h` — Gibt den Farbton einer Farbe zurück, der den Grad auf dem Farbkreis von 0 bis 360 angibt.
  - `s` — Gibt die Sättigung einer Farbe zurück, die als Intensität einer Farbe beschrieben wird.
  - `l` — Gibt die Helligkeit einer Farbe zurück, die beschreibt, wie viel Licht die Farbe haben sollte. Dabei bedeutet 0% kein Licht (dunkel), 50% bedeutet 50% Licht (weder dunkel noch hell) und 100% bedeutet volles Licht.
- `alpha` — Der Alphakanalwert (0 - 1) der Farbe. *(optional)*

#### [wst_acf_phone_number]

- **Shortcode:** `wst_acf_phone_number`

Gibt das ACF Extended Phone Number Feld zurück.

**Grundsyntax**

```text
[wst_acf_phone_number field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `title` — Das title Attribut des Elements. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `aria_label` — Das aria-label Attribut des Elements. *(optional)*
- `format` — Das Format der Telefonnummer. *(optional; Standard: `international`)*
  - `e164` — E.164 (z. B. +41446681800).
  - `national` — National (z. B. 044 668 18 00).
  - `international` — International (z. B. +41 44 668 18 00).
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `title` — Gibt den Titel des Elements zurück.
  - `url` — Gibt die URL des Elements zurück.

#### [wst_acf_map]

- **Shortcode:** `wst_acf_map`

Gibt das ACF Google Maps Feld zurück.

**Grundsyntax**

```text
[wst_acf_map field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `style` — Der Name des Map Styles im JSON Format, welche in den ACF Optionen erstellt wurde. *(optional; Standard: `none`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional; Standard: `100%`)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional; Standard: `400`)*
- `marker_icon` — Die URL des eigenen Marker Icons. *(optional)*
- `lat` — Der initial Map Breitengrad. *(optional)*
- `lng` — Der initial Map Längengrad. *(optional)*
- `zoom` — Die Vergrößerungsstufe der Google Map. Wenn dieser Parameter nicht gesetzt ist, erfolgt die Bestimmung der Zoomstufe automatisch. *(optional)*
- `min_zoom` — Die maximale Zoomstufe, die auf der Karte angezeigt wird. Wenn dieser Parameter nicht gesetzt ist, wird stattdessen die maximale Zoomstufe des aktuellen Kartentyps verwendet. *(optional)*
- `max_zoom` — Die minimale Zoomstufe, die auf der Karte angezeigt wird. Wenn dieser Parameter nicht gesetzt ist, wird stattdessen die minimale Zoomstufe des aktuellen Kartentyps verwendet. *(optional)*

**Darstellung der Steuerelemente**

- `zoom_control` — Aktiviert/deaktiviert das Steuerelement **Zoom**. *(optional; Standard: `1`)*
- `map_type_control` — Aktiviert/deaktiviert das Steuerelement **Map Type**, mit dem der Benutzer zwischen Kartentypen (z. B. "Map" und "Satellite") wechseln kann. *(optional; Standard: `1`)*
- `street_view_control` — Aktiviert/deaktiviert das **Pegman-Steuerelement**, mit dem der Benutzer ein **Street View-Panorama** aktivieren kann. *(optional; Standard: `1`)*
- `rotate_control` — Aktiviert/deaktiviert die Anzeige eines Steuerelements **Rotate** zur Steuerung der Ausrichtung von 45 °-Bildern. *(optional; Standard: `1`)*
- `scale_control` — Aktiviert/deaktiviert das Steuerelement **Scale**, mit dem eine einfache Kartenskalierung bereitgestellt wird. *(optional; Standard: `0`)*
- `fullscreen_control` — Aktiviert/deaktiviert, das Steuerelement **Vollbildmodus**. Auf Smartphones und Mobilgeräten ist dieses Steuerelement standardmäßig sichtbar, auf Desktopgeräten hingegen nicht. *(optional; Standard: `1`)*

**Darstellung des Steuerpanels.**

- `control_title` — Der Titel des Steuerpanels. *(optional)*
- `control_heading` — Der Heading Tag des Steuerpanels. *(optional; Standard: `h4`)*
- `control_position` — Die [Positionierung des Steuerpanels](https://developers.google.com/maps/documentation/javascript/controls#ControlPositioning) auf der Map. *(optional)*

**Ortssuche**

- `geo_search` — Aktivieren des Steuerelements **Ortssuche** (Places-Dienst). *(optional; Standard: `0`)*
- `geo_search_placeholder` — Der Platzhalter des Suchfeldes. *(optional; Standard: `Suchen...`)*
- `geo_search_value` — Die Standardadresse des Suchfeldes. *(optional)*
- `geo_search_country` — Die Suche auf gewisse Länder einschränken. *(optional; Standard: `at|de`)*
- `geo_search_marker_icon` — Die URL des eigenen Marker Icons. *(optional)*
- `geo_search_zoom` — Die Vergrößerungsstufe der Ortssuche der Google Map. Wenn dieser Parameter nicht gesetzt ist, erfolgt die Bestimmung der Zoomstufe automatisch. *(optional)*

**Geolocation Suche**

- `geo_location` — Aktivieren des Steuerelements **Geolocation Suche** (Eigener Standort). Dieser Dienst ist nur mit SSL verfügbar. *(optional; Standard: `0`)*
- `geo_location_label` — Das Label des Geolocation Suche Buttons. *(optional; Standard: ``)*
- `geo_location_marker_icon` — Die URL des eigenen Marker Icons. *(optional)*
- `geo_location_zoom` — Die Vergrößerungsstufe der Geolocation Suche der Google Map. Wenn dieser Parameter nicht gesetzt ist, erfolgt die Bestimmung der Zoomstufe automatisch. *(optional)*

**Umkreissuche**

- `area_search` — Aktivieren des Steuerelements **Umkreissuche** (in Meter) mit einem Bereichsschieberegler. *(optional; Standard: `0`)*
- `area_search_label` — Das Label der Umkreissuche. *(optional; Standard: ``)*
- `area_search_range_min` — Der Minimal-Wert des Bereichsschiebereglers. *(optional; Standard: `0`)*
- `area_search_range_max` — Der Maximal-Wert des Bereichsschiebereglers. *(optional; Standard: `10`)*
- `area_search_range_value` — Der Standard-Wert des Bereichsschiebereglers. *(optional; Standard: `0`)*
- `area_search_range_step` — Der Schritt-Wert des Bereichsschiebereglers. *(optional; Standard: `1`)*
- `area_search_range_multiplier` — Der Multiplikator der Umkreissuche. *(optional; Standard: `1000`)*
- `area_search_range_unit` — Die Maßeinheit des Bereichsschiebereglers. *(optional; Standard: `km`)*
- `area_search_circle_stroke_color` — Die Strich-Farbe des Kreises auf der Map. *(optional; Standard: `#0066CC`)*
- `area_search_circle_stroke_opacity` — Die Strich-Deckkraft des Kreises auf der Map. *(optional; Standard: `0.8`)*
- `area_search_circle_stroke_weight` — Die Strich-Stärke des Kreises auf der Map. *(optional; Standard: `2`)*
- `area_search_circle_fill_color` — Die Füll-Farbe des Kreises auf der Map. *(optional; Standard: `#0066CC`)*
- `area_search_circle_fill_opacity` — Die Füll-Deckkraft des Kreises auf der Map. *(optional; Standard: `0.35`)*

**Routenplanung**

- `directions` — Aktivieren des Steuerelements **Routenplanung**. Dieser Dienst ist nur mit SSL verfügbar. *(optional; Standard: `0`)*
- `directions_label` — Das Label des Berechnen Buttons. *(optional; Standard: ``)*
- `directions_search_placeholder` — Der Platzhalter des Suchfeldes. *(optional; Standard: `Suchen...`)*
- `directions_search_country` — Die Suche auf gewisse Länder einschränken. *(optional; Standard: `at|de`)*
- `directions_panel` — Anzeigen eines Panels mit Routenergebnisse. *(optional; Standard: `0`)*
- `directions_panel_position` — Die [Positionierung des Panels](https://developers.google.com/maps/documentation/javascript/controls#ControlPositioning) auf der Map. *(optional; Standard: `0`)*
- `directions_travel_mode` — Der Reisemodus `DRIVING | WALKING | BICYCLING | TRANSIT` für die Berechnung der Route. *(optional; Standard: `DRIVING`)*

**Karte Zurücksetzen**

- `reset` — Aktivieren des Steuerelements **Karte Zurücksetzen**. *(optional; Standard: `0`)*
- `reset_label` — Das Label des Zurücksetzen Buttons. *(optional; Standard: ``)*

**Zusätzliche Informationen**

###### Wichtige Informationen

- Die Anordnung der einzelnen Steuerelemente erfolgt über die Reihenfolge der Aktivierungs-Parameter im Shortcode.

###### Ein Marker Info-Fenster mit benutzerdefinierten Inhalten erstellen.

Folgender Shortcode erstellt eine ACF Google Map Übersichtskarte mit benutzerdefinierten Marker Info-Fenstern.

```text
[wst_acf_map map_field='field_name']
	[wst_post_title id='{{post_id/wst_acf_map}}']
	[wst_acf_map_address field='field_name' id='{{post_id/wst_acf_map}}']
[/wst_acf_map]
```

#### [wst_acf_map_address]

- **Shortcode:** `wst_acf_map_address`

Gibt die Adressdaten (durch Komma getrennt) des ACF Google Maps Feldes zurück.

**Grundsyntax**

```text
[wst_acf_map_address field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `hide_country` — Verstecken der Länderinformationen. *(optional; Standard: `0`)*
- `format_value` — Gibt an, ob die Adressdaten **Zeilenumbruch getrennt** ausgegeben werden sollen. *(optional; Standard: `1`)*

#### [wst_acf_map_overview]

- **Shortcode:** `wst_acf_map_overview`

Gibt das ACF Google Maps Feld mit jeder Position eines Inhaltstyps zurück.

**Grundsyntax**

```text
[wst_acf_map_overview map_field='field_name']
```

**Parameter und Optionen**

- `map_field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `marker_icon_field` — Der ACF Feld-Name des Marker Icon Feldes. *(optional)*
- `post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes. *(optional; Standard: `post`)*
- `post_status` — Name oder Liste (durch Komma getrennt) des Beitragsstatus. *(optional)*
- `posts_per_page` — Die Anzahl der anzuzeigenden Beiträge pro Seite. *(optional)*
- `posts_per_page_tablet` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem Tablet. *(optional)*
- `posts_per_page_mobile` — Die Anzahl der anzuzeigenden Beiträge pro Seite auf einem mobilen Gerät (Smartphone). *(optional)*
- `offset` — Die Anzahl der zu versetzenden oder zu übergehenden Beiträgen. *(optional)*
- `related` — Den eigenen Beitrag in der Query nicht ausgegeben. *(optional)*
- `post_ids` — Komma getrennte Beitrag-IDs, um nur diese Beiträge in der Query anzuzeigen. *(optional)*
- `post_names` — Durch Komma getrennte Beitrags-Titelformen, sodass nur diese Beiträge in der Abfrage angezeigt werden. *(optional)*
- `post_parent` — Gibt nur untergeordnete Beiträge von einem übergeordneten Beitrag aus. *(optional)*
  - `current` — Gibt die ID des Beitrages zurück.
  - `parent` — Gibt die ID des übergeordneten Beitrags zurück.
  - `parent|level=1` — Gibt die ID eines spezifischen (Level) übergeordneten Beitrags zurück.
  - `post_id` — Benutzerdefinierte ID eines Beitrages

**Taxonomie Parameter**

- `taxonomy_relation` — Beziehung der verschachtelten Taxonomy Blöcke. *(optional; Standard: `OR`)*
- `reset_taxonomy_query` — Setzt die aktuelle Taxonomie Abfrage zurück. *(optional; Standard: `0`)*
- `reset_posts_query` — Setzt die aktuelle Beitrags (post__in) Abfrage zurück. *(optional; Standard: `0`)*

**Spezifische Taxonomie-Begriffe**

- `my_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Kategorien einer bestimmten Taxonomie. *(optional; Beispiel: `product_cat="clothes"`)*
- `my_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `my_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `my_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Spezifische Begriffe**

- `term_list` — Name oder Liste (durch Komma getrennt) der eingeschränkten Kategorien. *(optional; Beispiel: `term_list="1234,4321"`)*
- `term_list_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `term_list_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `term_list_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Selbe Beitrags Taxonomy**

- `same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der einschränkenden Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional)*
- `same_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `same_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `same_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Primäre Beitrags Taxonomie**

- `primary_post_taxonomy` — Name oder Liste (durch ein Komma getrennt) der eingeschränkten Taxonomie, welche die Primäre Kategorie enthält. *(optional)*
- `primary_post_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `primary_post_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `primary_post_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Advanced Custom Fields**

- `acf_id` — Die ACF ID, unter welcher der Wert gespeichert wird. *(optional)*
- `acf_post_field` — Name des zu einschränkenden ACF Beitrag Objekt Feldes. *(optional)*
- `acf_taxonomy_field` — Name des zu einschränkenden ACF Taxonomy Feldes. *(optional)*
- `acf_taxonomy_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `acf_taxonomy_include_children` — Kind-Kategorien für hierarchische Taxonomien einbeziehen oder nicht. *(optional; Standard: `1`)*
- `acf_taxonomy_filter` — Filtereinstellung der Kategorien. *(optional; Standard: `none`)*
  - `none` — Keine Filtereinstellung bei den Kategorien definieren.
  - `hierarchical` — Falls keine Beiträge (mit den einschränkenden Kategorien) gefunden werden, so wird sequentiell in der nächsthöheren Hiercharchie (Kategorien) nach Datensätzen gesucht.

**Lesezeichen**

- `user_bookmark_posts` — Name der Merkliste, um alle gespeicherten Beiträge des Benutzers in der Query auszugeben. *(optional)*

**Gefällt mir**

- `user_like_posts` — Alle gespeicherten Like-Beiträge des Benutzers in der Abfrage ausgeben. *(optional; Standard: `0`)*

**WooCommerce**

- `wc_product_upsell_products` — Alle Zusatzverkäufe (UpSells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_product_cross_sell_products` — Alle Querverkäufe (Cross-Sells) eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `wc_cart_cross_sell_products` — Ausgabe von Cross-Sell-Produkten basierend auf den Artikeln im Warenkorb. *(optional; Standard: `0`)*
- `wc_product_gallery_images` — Alle Galerie Bilder eines WooCommerce Produktes in der Query ausgeben. *(optional; Standard: `0`)*
- `user_wc_recently_viewed_products` — Gibt alle kürzlich angesehenen WooCommerce-Produkte des Benutzers in der Abfrage aus. *(optional; Standard: `0`)*

**Wetter**

- `weather` — Name oder Liste (Beistrich getrennt) der einschränkenden Wetter Kategorien. *(optional)*
  - `live` — Kategorie der aktuellen Wetterabfrage (Keine Daten bereitgestellt.).
  - `thunderstorm` — **Gewitter** (Gewitter mit leichtem Regen, Gewitter mit Regen, Gewitter mit starkem Regen, Leichtes Gewitter, Gewitter, Starkes Gewitter, Wiederkehrende Gewitter, Gewitter mit leichtem Nieselregen, Gewitter mit Nieselregen, Gewitter mit starkem Nieselregen)
  - `drizzle` — **Nieselregen** (Leichter Nieselregen, Nieselregen, Starker Nieselregen, Leichter nieselartiger Niederschlag, Nieselartiger Niederschlag, Starker nieselartiger Niederschlag, Kurzer Regenschauer und Nieselregen, Starker Regenschauer und Nieselregen, Kurzer, nieselartiger Schauer)
  - `rain` — **Regen** (Leichter Regen, Mäßiger Regen, Starker Regen, Sehr starker Regen, Starkregen, Eisregen, Kurzer, leichter Regenschauer, Kurzer Regenschauer, Kurzer, starker Regenschauer, Wiederkehrende kurze Regenschauer)
  - `snow` — **Schneefall** (Leichter Schneefall, Schneefall, Starker Schneefall, Graupel, Kurzer Graupelschauer, Leichter Schneeregen, Schneeregen, Kurzer, leichter Schneeschauer, Kurzer Schneeschauer, Kurzer, starker Schneeschauer)
  - `atmosphere` — **Atmosphäre** (Dunst, Nebel, Rauch, Dunst, trübe Sicht, Staub und Sandwirbel, Nebel, Sand, Staub, Vulkanasche, Windböen, Tornado)
  - `clear` — **Heiter** (Klarer Himmel)
  - `clouds` — **Wolken** (Leicht bewölkt, Wolkig, Stark bewölkt, Bedeckt)
- `weather_relation` — Beziehung der eingeschränkten Kategorien. *(optional; Standard: `OR`)*
- `weather_forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `weather_forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*

**Datumsfelder**

- `date_year` — 4-stelliges Jahr (z. B. 2011). *(optional)*
- `date_month` — Monatsnummer (von 1 bis 12). *(optional)*
- `date_week` — Woche des Jahres (von 0 bis 53). *(optional)*
- `date_day` — Tag des Monats (von 1 bis 31). *(optional)*
- `date_hour` — Stunde (von 0 bis 23). *(optional)*
- `date_minute` — Minute (von 0 bis 59). *(optional)*
- `date_second` — Sekunde (0 bis 59). *(optional)*
- `date_after` — Datum, nach dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatiblen String. *(optional)*
- `date_before` — Datum, vor dem Beiträge abgerufen werden sollen. Akzeptiert strtotime()-kompatible Strings. *(optional)*
- `date_inclusive` — Für after/before, ob der genaue Wert abgeglichen werden soll oder nicht. *(optional; Standard: `0`)*
- `date_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `date_column` — Beitrags-Spalte, nach welcher abgefragt werden soll. *(optional; Standard: `post_date`)*
- `date_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Individuelle Felder**

- `meta_key` — Der Name des Post Metafeldes. *(optional)*
- `meta_value` — Der zu überprüfende Wert. *(optional)*
  - `post_id` — Gibt die ID des Beitrages zurück.
  - `my_meta_key` — Gibt den Wert eines übergebenen Beitrag-Metafeldes zurück.
  - `today` — Gibt das aktuelle Datum (2026-07-16) zurück.
  - `this_weekday` — Gibt den numerischer Tag (4), der aktuellen Woche zurück.
  - `this_week` — Gibt die Wochennummer (29), des aktuellen Jahres zurück (Woche beginnt am Montag).
  - `this_day` — Gibt die aktuelle Tageszahl (16), ohne führende Nullen zurück.
  - `this_month` — Gibt die aktuelle Monatszahl (7), ohne führende Nullen zurück.
  - `this_year` — Gibt das aktuelle Jahr (2026), als vierstellige Zahl zurück.
  - `page_on_front` — Gibt die **ID (10)** der Seite, welche als **Startseite (Startseite)** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `page_for_posts` — Gibt die **ID (0)** der Seite, welche als **Beitragsseite ()** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
- `meta_compare` — Der Vergleichsoperator: `=, !=, >, >=, <, <=, LIKE, NOT LIKE, IN, NOT IN, BETWEEN, NOT BETWEEN, NOT EXISTS, REGEXP, NOT REGEXP oder RLIKE.`. *(optional; Standard: `=`)*
- `meta_type` — Der Feldtyp: `NUMERIC, BINARY, CHAR, DATE, DATETIME, DECIMAL, SIGNED, TIME, UNSIGNED`. *(optional; Standard: `CHAR`)*
- `meta_relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*

**Reihenfolge**

- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional)*
- `orderby_meta_key` — Name des benutzerdefinierten Feldes nach dem sortiert werden soll. *(optional)*
- `orderby_post_type` — Name oder Liste (durch Komma getrennt) des Inhaltstypes, nach dem sortiert werden soll. *(optional)*
- `orderby_taxonomy_term` — Name oder Liste (durch Pipe getrennt) des Taxonomie Kategorie, nach dem sortiert werden soll. *(optional; Beispiel: `category(category_1,category_2)=date/DESC|post_tag(tag_1,tag_2)=title/ASC`)*
- `orderby_same_post_taxonomy` — Name oder Liste (Beistrich getrennt) der sortierten Taxonomie, welche die selben Kategorien des Beitrags haben. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `orderby_primary_post_taxonomy` — Name oder Liste (durch Pipe getrennt) der sortierten Taxonomie mit der primären Beitragskategorie. *(optional; Beispiel: `category=date/DESC|post_tag=title/ASC`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*
- `style` — Der Name des Map Styles im JSON Format, welche in den ACF Optionen erstellt wurde. *(optional; Standard: `none`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional; Standard: `100%`)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional; Standard: `400`)*
- `marker_icon` — Die URL des eigenen Marker Icons. *(optional)*
- `lat` — Der initial Map Breitengrad. *(optional)*
- `lng` — Der initial Map Längengrad. *(optional)*
- `zoom` — Die Vergrößerungsstufe der Google Map. Wenn dieser Parameter nicht gesetzt ist, erfolgt die Bestimmung der Zoomstufe automatisch. *(optional)*
- `min_zoom` — Die maximale Zoomstufe, die auf der Karte angezeigt wird. Wenn dieser Parameter nicht gesetzt ist, wird stattdessen die maximale Zoomstufe des aktuellen Kartentyps verwendet. *(optional)*
- `max_zoom` — Die minimale Zoomstufe, die auf der Karte angezeigt wird. Wenn dieser Parameter nicht gesetzt ist, wird stattdessen die minimale Zoomstufe des aktuellen Kartentyps verwendet. *(optional)*

**Darstellung der Steuerelemente**

- `zoom_control` — Aktiviert/deaktiviert das Steuerelement **Zoom**. *(optional; Standard: `1`)*
- `map_type_control` — Aktiviert/deaktiviert das Steuerelement **Map Type**, mit dem der Benutzer zwischen Kartentypen (z. B. "Map" und "Satellite") wechseln kann. *(optional; Standard: `1`)*
- `street_view_control` — Aktiviert/deaktiviert das **Pegman-Steuerelement**, mit dem der Benutzer ein **Street View-Panorama** aktivieren kann. *(optional; Standard: `1`)*
- `rotate_control` — Aktiviert/deaktiviert die Anzeige eines Steuerelements **Rotate** zur Steuerung der Ausrichtung von 45 °-Bildern. *(optional; Standard: `1`)*
- `scale_control` — Aktiviert/deaktiviert das Steuerelement **Scale**, mit dem eine einfache Kartenskalierung bereitgestellt wird. *(optional; Standard: `0`)*
- `fullscreen_control` — Aktiviert/deaktiviert, das Steuerelement **Vollbildmodus**. Auf Smartphones und Mobilgeräten ist dieses Steuerelement standardmäßig sichtbar, auf Desktopgeräten hingegen nicht. *(optional; Standard: `1`)*

**Darstellung des Steuerpanels.**

- `control_title` — Der Titel des Steuerpanels. *(optional)*
- `control_heading` — Der Heading Tag des Steuerpanels. *(optional; Standard: `h4`)*
- `control_position` — Die [Positionierung des Steuerpanels](https://developers.google.com/maps/documentation/javascript/controls#ControlPositioning) auf der Map. *(optional)*

**Ortssuche**

- `geo_search` — Aktivieren des Steuerelements **Ortssuche** (Places-Dienst). *(optional; Standard: `0`)*
- `geo_search_placeholder` — Der Platzhalter des Suchfeldes. *(optional; Standard: `Suchen...`)*
- `geo_search_value` — Die Standardadresse des Suchfeldes. *(optional)*
- `geo_search_country` — Die Suche auf gewisse Länder einschränken. *(optional; Standard: `at|de`)*
- `geo_search_marker_icon` — Die URL des eigenen Marker Icons. *(optional)*
- `geo_search_zoom` — Die Vergrößerungsstufe der Ortssuche der Google Map. Wenn dieser Parameter nicht gesetzt ist, erfolgt die Bestimmung der Zoomstufe automatisch. *(optional)*

**Geolocation Suche**

- `geo_location` — Aktivieren des Steuerelements **Geolocation Suche** (Eigener Standort). Dieser Dienst ist nur mit SSL verfügbar. *(optional; Standard: `0`)*
- `geo_location_label` — Das Label des Geolocation Suche Buttons. *(optional; Standard: ``)*
- `geo_location_marker_icon` — Die URL des eigenen Marker Icons. *(optional)*
- `geo_location_zoom` — Die Vergrößerungsstufe der Geolocation Suche der Google Map. Wenn dieser Parameter nicht gesetzt ist, erfolgt die Bestimmung der Zoomstufe automatisch. *(optional)*

**Umkreissuche**

- `area_search` — Aktivieren des Steuerelements **Umkreissuche** (in Meter) mit einem Bereichsschieberegler. *(optional; Standard: `0`)*
- `area_search_label` — Das Label der Umkreissuche. *(optional; Standard: ``)*
- `area_search_range_min` — Der Minimal-Wert des Bereichsschiebereglers. *(optional; Standard: `0`)*
- `area_search_range_max` — Der Maximal-Wert des Bereichsschiebereglers. *(optional; Standard: `10`)*
- `area_search_range_value` — Der Standard-Wert des Bereichsschiebereglers. *(optional; Standard: `0`)*
- `area_search_range_step` — Der Schritt-Wert des Bereichsschiebereglers. *(optional; Standard: `1`)*
- `area_search_range_multiplier` — Der Multiplikator der Umkreissuche. *(optional; Standard: `1000`)*
- `area_search_range_unit` — Die Maßeinheit des Bereichsschiebereglers. *(optional; Standard: `km`)*
- `area_search_circle_stroke_color` — Die Strich-Farbe des Kreises auf der Map. *(optional; Standard: `#0066CC`)*
- `area_search_circle_stroke_opacity` — Die Strich-Deckkraft des Kreises auf der Map. *(optional; Standard: `0.8`)*
- `area_search_circle_stroke_weight` — Die Strich-Stärke des Kreises auf der Map. *(optional; Standard: `2`)*
- `area_search_circle_fill_color` — Die Füll-Farbe des Kreises auf der Map. *(optional; Standard: `#0066CC`)*
- `area_search_circle_fill_opacity` — Die Füll-Deckkraft des Kreises auf der Map. *(optional; Standard: `0.35`)*

**Routenplanung**

- `directions` — Aktivieren des Steuerelements **Routenplanung**. Dieser Dienst ist nur mit SSL verfügbar. *(optional; Standard: `0`)*
- `directions_label` — Das Label des Berechnen Buttons. *(optional; Standard: ``)*
- `directions_search_placeholder` — Der Platzhalter des Suchfeldes. *(optional; Standard: `Suchen...`)*
- `directions_search_country` — Die Suche auf gewisse Länder einschränken. *(optional; Standard: `at|de`)*
- `directions_panel` — Anzeigen eines Panels mit Routenergebnisse. *(optional; Standard: `0`)*
- `directions_panel_position` — Die [Positionierung des Panels](https://developers.google.com/maps/documentation/javascript/controls#ControlPositioning) auf der Map. *(optional; Standard: `0`)*
- `directions_travel_mode` — Der Reisemodus `DRIVING | WALKING | BICYCLING | TRANSIT` für die Berechnung der Route. *(optional; Standard: `DRIVING`)*

**Karte Zurücksetzen**

- `reset` — Aktivieren des Steuerelements **Karte Zurücksetzen**. *(optional; Standard: `0`)*
- `reset_label` — Das Label des Zurücksetzen Buttons. *(optional; Standard: ``)*

**Marker-Cluster**

- `marker_cluster` — Aktivieren der **Marker-Cluster**, um eine große Anzahl von Markern auf einer Karte anzuzeigen. *(optional; Standard: `0`)*
- `marker_cluster_grid_size` — Die Rastergröße eines Clusters in Pixel. *(optional)*
- `marker_cluster_max_zoom` — Die maximale Zoomstufe, zu der ein Marker Teil eines Clusters sein kann. *(optional)*
- `marker_cluster_image_path` — Der Bildpfad zu den Marker-Cluster. *(optional; Standard: `https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m`)*
- `marker_cluster_image_extension` — Das Bild-Dateiformat der Marker-Cluster. *(optional; Standard: `png`)*
- `marker_cluster_zoom_on_click` — Automatischer Zoom beim Klick auf einen Cluster. *(optional; Standard: `1`)*
- `marker_cluster_average_center` — Ob das Zentrum jedes Clusters der Durchschnitt aller Marker im Cluster sein soll. *(optional; Standard: `0`)*
- `marker_cluster_minimum_cluster_size` — Die Mindestanzahl an Markern in einem Cluster, bevor die Marker verborgen werden und die Anzahl an Markern angezeigt wird. *(optional)*

**Marker-Cluster Stileigenschaften**

- `marker_cluster_style_url` — Die Bild-URL des Marker-Cluster. *(optional)*
- `marker_cluster_style_width` — Die Bild-Breite in Pixel des Marker-Cluster (Optional). *(optional)*
- `marker_cluster_style_height` — Die Bild-Höhe in Pixel des Marker-Cluster (Optional). *(optional)*
- `marker_cluster_style_text_color` — Die Textfarbe des Marker-Cluster-Beschriftungstexts. *(optional)*
- `marker_cluster_style_anchor` — Die Ankerposition `x|y` in Pixel des Marker-Cluster-Beschriftungstexts. *(optional)*
- `marker_cluster_style_text_size` — Die Textgröße des Marker-Cluster-Beschriftungstexts. *(optional)*
- `marker_cluster_style_background_position` — Die Position des Hintergrunds des Marker-Cluster. *(optional)*
- `marker_cluster_style_icon_anchor` — Die Ankerposition `x|y` in Pixel des Marker-Cluster. *(optional)*

**Zusätzliche Informationen**

###### Wichtige Informationen

- Die Anordnung der einzelnen Steuerelemente erfolgt über die Reihenfolge der Aktivierungs-Parameter im Shortcode.
- Weitere Marker-Cluster Stileigenschaften (für unterschiedliche Zoomstufen), können mit einer zusätzlichen Nummernangabe hinzugefügt werden.
z.B.: `marker_cluster_style_url_2, marker_cluster_style_text_color_2, usw.`

###### Ein Marker Info-Fenster mit benutzerdefinierten Inhalten erstellen.

Folgender Shortcode erstellt eine ACF Google Map Übersichtskarte mit benutzerdefinierten Marker Info-Fenstern.

```text
[wst_acf_map_overview map_field='field_name']
	[wst_post_title id='{{post_id/wst_acf_map_overview}}']
	[wst_acf_map_address field='field_name' id='{{post_id/wst_acf_map_overview}}']
[/wst_acf_map_overview]
```

### 5.17 ACF Layout

- **Interne ID:** `acf_layout`
- **Kategoriefarbe:** `#00dab4`
- **Einträge:** 2

#### [wst_acf_repeater]

- **Shortcode:** `wst_acf_repeater`

Gibt das ACF Wiederholung Feld als Schleife zurück.

**Grundsyntax**

```text
[wst_acf_repeater field='field_name']row_content[/wst_acf_repeater]
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

**Zusätzliche Informationen**

###### Das ACF Wiederholungs Feld als Schleife verwenden

Der folgender Shortcode verwandelt das Feld ACF repeater in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_acf_repeater field='field_name']
	[wst_acf_number field='price' id='{{row_id/wst_acf_repeater}}']
[/wst_acf_repeater]
```

###### ACF Wiederholungs Felder als **verschachtelten Schleifen** verwenden.

Folgendes Beispiel zeigt mehrere ACF Wiederholungs Felder als verschachtelte Schleifen.

```text
[wst_acf_repeater field='post_downloads']
	[wst_acf field='category_title' id='{{row_id/wst_acf_repeater}}']
	[wst_acf_repeater_b field='downloads' id='{{row_id/wst_acf_repeater}}']
		[wst_acf field='title' id='{{row_id/wst_acf_repeater_b}}']
		[wst_acf_repeater_c field='files' id='{{row_id/wst_acf_repeater_b}}']
			[wst_acf field='file' id='{{row_id/wst_acf_repeater_c}}']
		[/wst_acf_repeater_c]
	[/wst_acf_repeater_b]
[/wst_acf_repeater]
```

Jeder ACF Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{row_id/loop_name}}` angegeben werden (siehe Beispiel).

#### [wst_acf_table]

> **Verfügbarkeit:** Dieser Eintrag ist in der aktuellen Admin-Hilfe ausgeblendet, bleibt hier aber vollständig dokumentiert.

- **Shortcode:** `wst_acf_table`

Rendert eine ACF-Tabelle mithilfe dem **Advanced Custom Fields: Table Field** Plugin.

**Grundsyntax**

```text
[wst_acf_table field='field_name']
```

**Parameter und Optionen**

- `field` — Der ACF Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `table_class` — Das class Attribut des Elements. *(optional)*

### 5.18 ACF Formular

- **Interne ID:** `acf_form`
- **Kategoriefarbe:** `#00dab4`
- **Einträge:** 1

#### [wst_acf_form]

- **Shortcode:** `wst_acf_form`

Gibt ein Formular zum Hinzufügen oder Bearbeiten eines Beitrags zurück.

**Grundsyntax**

```text
[wst_acf_form]
```

**Parameter und Optionen**

- `id` — Ein eindeutiger Bezeichner für das Formular. *(optional; Standard: `acf-form`)*
- `post_id` — Die Beitrags-ID, mit der bestimmt wird, welche Felder angezeigt werden, wo die Daten geladen und wo sie gespeichert werden. Standardmäßig wird die aktuelle Beitrags-ID verwendet. Kann auch an "new_post" gesendet werden, um beim Absenden einen neuen Beitrag zu erstellen. *(optional)*
- `new_post` — Wenn der Parameter "post_id" auf "new_post" gesetzt ist, wird diese Einstellung zur Erstellung des Beitrags verwendet. Siehe [wp_insert_post](https://developer.wordpress.org/reference/functions/wp_insert_post/) für verfügbare Parameter. *(optional; Beispiel: `post_type|post,post_status|publish`)*
- `field_groups` — Ein Array von Feldgruppen-IDs/Schlüsseln, um die in diesem Formular angezeigten Felder zu überschreiben. *(optional; Standard: `0`)*
- `fields` — Ein Array von Feld-IDs/Schlüsseln, um die in diesem Formular angezeigten Felder zu überschreiben. *(optional; Standard: `0`)*
- `post_title` — Gibt an, ob das Textfeld für den Beitragstitel angezeigt werden soll oder nicht. *(optional; Standard: `0`)*
- `post_title_label` — Die Beschriftung des Textfeldes für den Beitragstitel. *(optional; Standard: `Titel`)*
- `post_content` — Gibt an, ob das Feld für den Beitragsinhalt-Editor angezeigt werden soll oder nicht. *(optional; Standard: `0`)*
- `post_content_label` — Die Beschriftung des Feldes im Beitragseditor. *(optional; Standard: `Inhalt`)*
- `form` — Gibt an, ob ein Formularelement erstellt werden soll oder nicht. Nützlich beim Hinzufügen zu einem bestehenden Formular. *(optional; Standard: `1`)*
- `form_attributes` — Ein Array oder HTML-Attribute für das Formularelement. *(optional; Beispiel: `class|wst-acf-form`)*
- `return` — Die URL, an die nach dem Absenden des Formulars weitergeleitet werden soll. Standardmäßig wird die aktuelle URL mit einem GET-Parameter verwendet. *(optional; Standard: `?updated=true`)*
- `html_before_fields` — Zusätzliches HTML, das vor den Feldern eingefügt wird. *(optional)*
- `html_after_fields` — Zusätzliches HTML, das nach den Feldern eingefügt wird. *(optional)*
- `submit_value` — Der Text, welcher auf dem Senden-Button angezeigt wird. *(optional; Standard: `Aktualisieren`)*
- `updated_message` — Die Nachricht, die nach der Weiterleitung über dem Formular angezeigt wird. Kann auch auf false gesetzt werden, wenn keine Nachricht angezeigt werden soll. *(optional; Standard: `Post updated`)*
- `label_placement` — Legt fest, wo die Feldbezeichnungen in Bezug auf die Felder platziert werden. *(optional; Standard: `top`)*
  - `top` — Über den Feldern
  - `left` — Neben den Feldern
- `instruction_placement` — Legt fest, wo Feldanweisungen im Verhältnis zu Feldern platziert werden. *(optional; Standard: `label`)*
  - `label` — Untere Labels
  - `field` — Untere Felder
- `field_el` — Bestimmt das Element, das zum Umhüllen eines Feldes verwendet wird. *(optional; Standard: `div`)*
  - `div` — Divisions Element
  - `tr` — Tabelle Zeilenelement
  - `td` — Tabelle Datenelement
  - `ul` — Ungeordnetes Listenelement
  - `ol` — Geordnetes Listenelement
  - `dl` — Beschreibungs Listenelement
- `uploader` — Gibt an, ob der WP-Uploader oder eine einfache Eingabe für Bild- und Dateifelder verwendet werden soll. *(optional; Standard: `wp`)*
  - `wp` — WordPress-Uploader
  - `basic` — HTML Datei-Eingabeelement
- `honeypot` — Gibt an, ob ein verborgenes Eingabefeld zur Erfassung nicht-menschlicher Formularübertragungen eingefügt werden soll. *(optional; Standard: `1`)*
- `kses` — Gibt an, ob alle $_POST-Daten mit der Funktion wp_kses_post() bereinigt werden sollen oder nicht. *(optional; Standard: `1`)*

### 5.19 Bedingte Logik

- **Interne ID:** `condition`
- **Kategoriefarbe:** `#B3B6B7`
- **Einträge:** 8

#### [wst_if]

- **Shortcode:** `wst_if`

Gibt den anzuzeigenden Inhalt über eine bedingte Logik zurück.

**Grundsyntax**

```text
[wst_if field='field_name' compare='=' value='1']My Content[/wst_if]
```

**Parameter und Optionen**

- `field` — Der Name des Beitragsfeldes / Metafeldes. *(Pflicht; Standard: `field_name`)*
  - `my_meta_key` — Gibt den Wert des übergebenen Beitrag-Metafeldes zurück.
  - `post_id` — Gibt die ID des Beitrages zurück.
  - `post_title` — Gibt den Titel des Beitrages zurück.
  - `post_name` — Gibt die Titelform des Beitrages zurück.
  - `post_content` — Gibt den Inhalt des Beitrages zurück.
  - `post_excerpt` — Gibt den Auszug des Beitrages zurück.
  - `post_type` — Gibt den Inhaltstyp des Beitrages zurück.
  - `post_status` — Gibt den Status des Beitrages zurück.
  - `post_format` — Gibt das Format des Beitrages zurück.
  - `post_thumbnail` — Gibt das Bild des Beitrages zurück.
  - `post_date` — Gibt das Datum zurück, an dem der Beitrag verfasst wurde.
  - `post_time` — Gibt die Uhrzeit zurück, an welcher der Beitrag verfasst wurde.
  - `post_modified` — Gibt das Datum zurück, an dem der Beitrag geändert wurde.
  - `post_permalink` — Gibt den Permalink des Beitrages zurück.
  - `post_parent` — Gibt die ID des übergeordneten Beitrags zurück.
  - `post_menu_order` — Gibt die Reihenfolge des Beitrages zurück.
  - `post_children` — Gibt die Kind IDs des Beitrages zurück.
  - `post_terms` — Gibt alle Beitrags-Begriffe der übergebenen Taxonomy zurück.
  - `post_parent_level_count` — Gibt die Levelanzahl der Eltern-Beiträge zurück.
  - `post_children_level_count` — Gibt die Levelanzahl der Kind-Beiträge zurück.
  - `post_password_required` — Gibt zurück, ob ein Beitrag ein Passwort erfordert und das korrekte Passwort angegeben wurde.
  - `post_language` — Gibt die Sprachinformationen eines Beitrags zurück.
  - `post_translations` — Gibt alle Übersetzungen eines Beitrags zurück.
  - `post_primary_term_id` — Gibt die ID der primären Kategorie des Beitrags zurück.
  - `attachment_image_url` — Gibt die URL für einen Bild-Anhang zurück.
  - `attachment_url` — Gibt die URL für einen Anhang zurück.
  - `attachment_metadata` — Gibt die Metadaten für ein Attachment zurück.
  - `wc_product_type` — Gibt den internen Typ zurück.
  - `wc_product_name` — Gibt den Produktnamen zurück.
  - `wc_product_slug` — Gibt die Titelform des Produktes zurück.
  - `wc_product_date_created` — Gibt das Erstellungsdatum des Produktes zurück.
  - `wc_product_date_modified` — Gibt das Änderungsdatum des Produktes zurück.
  - `wc_product_status` — Gibt den Status des Produktes zurück.
  - `wc_product_featured` — Gibt zurück, ob das Produkt hervorgehoben ist.
  - `wc_product_catalog_visibility` — Gibt die Sichtbarkeit des Katalogs zurück.
  - `wc_product_description` — Gibt die Beschreibung des Produktes zurück.
  - `wc_product_short_description` — Gibt die Kurzbeschreibung des Produktes zurück.
  - `wc_product_sku` — Gibt die Artikelnummer zurück.
  - `wc_product_global_unique_id` — Gibt die Eindeutige ID zurück.
  - `wc_product_price` — Gibt den aktiven Preis des Produktes zurück.
  - `wc_product_price_excluding_tax` — Gibt den aktiven Preis des Produkts mit Steuern zurück.
  - `wc_product_price_including_tax` — Gibt den aktiven Preis des Produkts mit Steuern zurück.
  - `wc_product_regular_price` — Gibt den regulären Preis des Produktes zurück.
  - `wc_product_sale_price` — Gibt den Angebotspreis des Produktes zurück.
  - `wc_product_date_on_sale_from` — Gibt das Startdatum des Angebots zurück.
  - `wc_product_date_on_sale_to` — Gibt das Verkaufsdatum zurück.
  - `wc_product_total_sales` — Gibt die Gesamtzahl der Verkäufe zurück.
  - `wc_product_tax_status` — Gibt den Steuerstatus zurück.
  - `wc_product_tax_class` — Gibt die Steuerklasse zurück.
  - `wc_product_manage_stock` — Gibt zurück, ob das Produkt eine Lagerverwaltung verwendet.
  - `wc_product_stock_quantity` — Gibt die Anzahl der zum Verkauf stehenden Artikel zurück."
  - `wc_product_stock_status` — Gibt den Lagerbestand zurück.
  - `wc_product_backorders` — Gibt die Nachbestellungen zurück.
  - `wc_product_low_stock_amount` — Gibt den Schwellwert für „geringer Lagerbestand“ zurück.
  - `wc_product_sold_individually` — Gibt zurück, ob einzeln verkauft werden soll.
  - `wc_product_weight` — Gibt das Gewicht des Produkts zurück.
  - `wc_product_length` — Gibt die Produktlänge zurück.
  - `wc_product_width` — Gibt die Produktbreite zurück.
  - `wc_product_height` — Gibt die Produkthöhe zurück.
  - `wc_product_dimensions` — Gibt die Abmessungen formatiert zurück.
  - `wc_product_upsell_ids` — Gibt die Upsell-IDs. zurück.
  - `wc_product_cross_sell_ids` — Gibt die Cross-Sell-IDs zurück.
  - `wc_product_parent_id` — Gibt die ID des Eltern-Produktes zurück.
  - `wc_product_reviews_allowed` — Gibt zurück, ob Bewertungen erlaubt sind.
  - `wc_product_purchase_note` — Gibt den Hinweis zum Kauf zurück.
  - `wc_product_attributes` — Gibt die Produktattribute zurück.
  - `wc_product_default_attributes` — Gibt die Standardattribute zurück.
  - `wc_product_menu_order` — Gibt die Menüreihenfolge zurück.
  - `wc_product_post_password` — Gibt das Passwort des Beitrags zurück.
  - `wc_product_category_ids` — Gibt die Kategorie-IDs zurück.
  - `wc_product_tag_ids` — Gibt die Schlagwort-IDs zurück.
  - `wc_product_virtual` — Gibt zurück, ob das Produkt virtuell ist.
  - `wc_product_gallery_image_ids` — Gibt die Anhang-IDs der Galerie zurück.
  - `wc_product_shipping_class_id` — Gibt die ID der Versandklasse zurück.
  - `wc_product_downloads` — Gibt die Downloads zurück.
  - `wc_product_download_expiry` — Gibt das Ablaufdatum des Downloads zurück.
  - `wc_product_downloadable` — Prüft, ob ein Produkt herunterladbar ist.
  - `wc_product_download_limit` — Gibt das Download-Limit zurück.
  - `wc_product_image_id` — Gibt die ID des Hauptbildes zurück.
  - `wc_product_rating_counts` — Gibt die Bewertungsanzahl zurück.
  - `wc_product_average_rating` — Gibt die durchschnittliche Bewertung zurück.
  - `wc_product_review_count` — Gibt die Anzahl der Beurteilungen zurück.
  - `wc_product_supports` — Prüft, ob ein Produkt ein bestimmtes Feature unterstützt.
  - `wc_product_exists` — Gibt zurück, ob der Produkt-Beitrag existiert oder nicht.
  - `wc_product_is_type` — Prüft den Produkttyp.
  - `wc_product_is_downloadable` — Prüft, ob ein Produkt herunterladbar ist.
  - `wc_product_is_virtual` — Prüft, ob ein Produkt virtuell ist (keinen Versand hat).
  - `wc_product_is_featured` — Gibt zurück, ob das Produkt hervorgehoben ist oder nicht.
  - `wc_product_is_sold_individually` — Prüft, ob ein Produkt einzeln verkauft wird (keine Mengen).
  - `wc_product_is_visible` — Gibt zurück, ob das Produkt im Katalog sichtbar ist oder nicht.
  - `wc_product_is_purchasable` — Gibt false zurück, wenn das Produkt nicht gekauft werden kann.
  - `wc_product_is_on_sale` — Gibt zurück, ob das Produkt im Angebot ist oder nicht.
  - `wc_product_has_dimensions` — Gibt zurück, ob für das Produkt Abmessungen festgelegt wurden oder nicht.
  - `wc_product_has_weight` — Gibt zurück, ob für das Produkt ein Gewicht festgelegt wurde oder nicht.
  - `wc_product_is_in_stock` — Gibt zurück, ob das Produkt gekauft werden kann oder nicht.
  - `wc_product_needs_shipping` — Prüft, ob ein Produkt verschickt werden muss.
  - `wc_product_is_taxable` — Gibt an, ob das Produkt steuerpflichtig ist oder nicht.
  - `wc_product_is_shipping_taxable` — Gibt zurück, ob der Produktversand steuerpflichtig ist oder nicht.
  - `wc_product_managing_stock` — Gibt zurück, ob das Produkt eine Lagerverwaltung verwendet oder nicht.
  - `wc_product_backorders_allowed` — Gibt zurück, ob das Produkt nachbestellt werden kann oder nicht.
  - `wc_product_backorders_require_notification` — Gibt zurück, ob das Produkt den Kunden über den Rückstand informieren muss oder nicht.
  - `wc_product_is_on_backorder` — Prüft, ob ein Produkt im Rückstand ist.
  - `wc_product_has_enough_stock` — Gibt zurück, ob das Produkt genug Bestand für die Bestellung hat oder nicht.
  - `wc_product_has_attributes` — Gibt zurück, ob das Produkt sichtbare Eigenschaften hat oder nicht.
  - `wc_product_has_child` — Gibt zurück, ob das Produkt ein untergeordnetes Produkt hat oder nicht.
  - `wc_product_child_has_dimensions` — Hat ein Kind Maße?
  - `wc_product_child_has_weight` — Hat ein Kind ein Gewicht?
  - `wc_product_has_file` — Prüft, ob an das herunterladbare Produkt eine Datei angehängt ist.
  - `wc_product_has_options` — Gibt zurück, ob das Produkt zusätzliche Optionen hat, die vor dem Hinzufügen zum Warenkorb ausgewählt werden müssen.
  - `wc_product_title` — Gibt den Titel des Produktes zurück. Bei Produkten ist dies der Produktname.
  - `wc_product_permalink` — Produkt Permalink.
  - `wc_product_children` — Gibt die IDs der Kinder zurück, falls zutreffend.
  - `wc_product_stock_managed_by_id` — Wenn der Lagerbestand von einer anderen Produkt-ID stammt, sollte diese geändert werden.
  - `wc_product_price_html` — Gibt den Preis im HTML-Format zurück.
  - `wc_product_formatted_name` — Gibt den Produktnamen mit SKU oder ID zurück. Wird in der Administration verwendet.
  - `wc_product_min_purchase_quantity` — Gibt die Mindestmenge, die auf einmal gekauft werden kann zurück.
  - `wc_product_max_purchase_quantity` — Gibt die maximale Menge, die auf einmal gekauft werden kann zurück.
  - `wc_product_add_to_cart_url` — Gibt die "In den Warenkorb" URL zurück, welche hauptsächlich in Schleifen verwendet wird.
  - `wc_product_single_add_to_cart_text` — Gibt den "In den Warenkorb" Button Text für die einzelne Seite zurück.
  - `wc_product_add_to_cart_aria_describedby` — Gibt die aria-describedby Beschreibung für den "In den Warenkorb" Button zurück.
  - `wc_product_add_to_cart_text` — Gibt den "In den Warenkorb" Button Text zurück.
  - `wc_product_add_to_cart_description` — Gibt die Textbeschreibung für den "In den Warenkorb" Button zurück - wird in Aria-Tags verwendet.
  - `wc_product_image` — Gibt das Hauptproduktbild zurück.
  - `wc_product_shipping_class` — Gibt die Produktversandklasse SLUG zurück.
  - `wc_product_attribute` — Gibt ein einzelnes Produktattribut als String zurück.
  - `wc_product_rating_count` — Gibt die Gesamtzahl (COUNT) der Bewertungen oder nur die Anzahl für eine Bewertung, z.B. die Anzahl der 5-Sterne-Bewertungen zurück.
  - `wc_product_file` — Gibt eine Datei nach $download_id zurück.
  - `wc_product_file_download_path` — Gibt den Pfad zum Dateidownload, der durch $download_id identifiziert wird zurück.
  - `wc_product_price_suffix` — Gibt das Suffix, das nach Preisen > 0 angezeigt wird zurück.
  - `wc_product_availability` — Gibt die Verfügbarkeit des Produkts zurück.
  - `wc_cart_cross_sell_products` — Gibt die Cross-Sell-Produkte basierend auf den Artikeln im Warenkorb zurück.
  - `term_id` — Gibt die ID des Begriffs zurück.
  - `term_name` — Gibt den Namen des Begriffs zurück.
  - `term_slug` — Gibt die Titelform des Begriffs zurück.
  - `term_link` — Gibt den Permalink für ein Taxonomie-Begriffsarchiv zurück.
  - `term_group` — Gibt die Gruppennummer des Begriffs zurück.
  - `term_taxonomy_id` — Gibt die Taxonomy-ID des Begriffs zurück.
  - `term_taxonomy` — Gibt die Taxonomy des Begriffs zurück.
  - `term_description` — Gibt die Beschreibung des Begriffs zurück.
  - `term_parent` — Gibt die ID des übergeordneten Begriffs zurück.
  - `term_count` — Gibt die Anzahl der zugeordneten Beiträge des Begriffs zurück.
  - `term_is_parent` — Prüft ob der übergebene Begriff ein Eltern-Begriff ist.
  - `term_is_children` — Prüft ob der übergebene Begriff ein Kind-Begriff ist.
  - `current_user_id` — Gibt die ID des aktuellen Benutzers zurück.
  - `user_nickname` — Gibt den Spitznamen des Benutzers zurück.
  - `user_description` — Gibt die Beschreibung des Benutzers zurück.
  - `user_firstname` — Gibt den Vornamen des Benutzers zurück.
  - `user_lastname` — Gibt den Nachnamen des Benutzers zurück.
  - `user_login` — Gibt den Login des Benutzers zurück.
  - `user_nicename` — Gibt den Slug des Benutzers zurück.
  - `user_email` — Gibt die E-Mail-Adresse des Benutzers zurück.
  - `user_url` — Gibt den Permalink des Benutzers zurück.
  - `user_registered` — Gibt das Registrierungsdatum des Benutzers zurück.
  - `user_activation_key` — Gibt den Aktivierungsschlüssel des Benutzers zurück.
  - `user_status` — Gibt den Status des Benutzers zurück.
  - `user_level` — Gibt die Berechtigungs Ebene des Benutzers zurück.
  - `user_display_name` — Gibt den öffentlichen Name des Benutzers zurück.
  - `user_locale` — Gibt das Sprache des Benutzers zurück.
  - `user_rich_editing` — Gibt den Visueller Editor Status des Benutzers zurück.
  - `user_roles` — Gibt die Rollen des Benutzers zurück.
  - `user_syntax_highlighting` — Gibt das Syntaxhervorhebung-Status des Benutzers zurück.
  - `user_bookmark_posts` — Gibt alle Lesezeichen-Beiträge des Benutzers zurück.
  - `user_like_posts` — Gibt alle Like-Beiträge des Benutzers zurück.
  - `user_wc_recently_viewed_products` — Gibt alle zuletzt angesehenen WooCommerce-Produkte des Benutzers zurück.
  - `GLOBALS` — Verweist auf alle im globalen Bereich verfügbaren Variablen.
  - `_SERVER` — Informationen zum Server und zur Ausführungsumgebung.
  - `_GET` — HTTP GET Variablen.
  - `_POST` — HTTP POST Variablen.
  - `_FILES` — HTTP Datei Upload Variablen.
  - `_COOKIE` — HTTP Cookies.
  - `_SESSION` — Session Variablen.
  - `_REQUEST` — HTTP REQUEST Variablen.
  - `_ENV` — Umgebungsvariablen.
  - `$my_variable` — Gibt den Wert einer Variable zurück.
  - `$my_variable=blue` — Setzt den Wert einer Variable.
  - `$my_variable|int=1234` — Setzt den Wert und den Typ einer Variable.
  - `today` — Gibt das aktuelle Datum (2026-07-16) zurück.
  - `this_weekday` — Gibt den numerischer Tag (4), der aktuellen Woche zurück.
  - `this_week` — Gibt die Wochennummer (29), des aktuellen Jahres zurück (Woche beginnt am Montag).
  - `this_day` — Gibt die aktuelle Tageszahl (16), ohne führende Nullen zurück.
  - `this_month` — Gibt die aktuelle Monatszahl (7), ohne führende Nullen zurück.
  - `this_year` — Gibt das aktuelle Jahr (2026), als vierstellige Zahl zurück.
  - `page_on_front` — Gibt die **ID (10)** der Seite, welche als **Startseite (Startseite)** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `page_for_posts` — Gibt die **ID (0)** der Seite, welche als **Beitragsseite ()** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `count_posts` — Zählt die Anzahl der Beiträge eines Beitragstyps.
  - `current_language` — Gibt die aktuelle Sprache am Frontend zurück.
  - `default_language` — Gibt die Standardsprache der Website zurück.
  - `is_mobile` — Überprüft, ob der aktuelle Browser auf einem mobilen Gerät (Smartphone) ausgeführt wird.
  - `is_tablet` — Überprüft, ob der aktuelle Browser auf einem Tablet läuft.
  - `is_ios` — Überprüft, ob der aktuelle Browser auf einer IOS Plattform läuft.
  - `is_android` — Überprüft, ob der aktuelle Browser auf einer Android OS Plattform läuft.
  - `is_mobile_network` — Überprüft, ob die aktuelle Verbindung über ein Mobilfunknetz läuft.
  - `is_user_logged_in` — Überprüft ob ein Benutzer eingeloggt ist.
  - `is_404` — Ermittelt, ob die Abfrage zu einer 404 geführt hat.
  - `is_single` — Legt fest, ob sich die Abfrage auf einen vorhandenen einzelnen Beitrag bezieht.
  - `is_page` — Bestimmt, ob sich die Abfrage auf eine bestehende Einzelseite bezieht.
  - `is_singular` — Bestimmt, ob sich die Abfrage auf einen vorhandenen einzelnen Beitrag eines beliebigen Inhaltstyps (post, attachment, page, custom post types) bezieht.
  - `is_archive` — Bestimmt, ob sich die Abfrage auf eine bestehende Archivseite bezieht.
  - `is_post_type_archive` — Bestimmt, ob sich die Abfrage auf eine bestehende Archivseite des Inhaltstyps bezieht.
  - `queried_object_id` — Gibt die ID des aktuell abgefragten Objekts zurück.
  - `weather` — Gibt den Wetter-Gruppenschlüssel (thunderstorm, drizzle, rain, snow, atmosphere, clear, clouds) zurück.
  - `geolocate` — Gibt ein bestimmtes Feld (continent, continentCode, country, countryCode, region, regionName, city, district, zip, lat, lon, timezone, offset, currency, isp, org, as, asname, reverse, mobile, proxy, hosting, query) der geolokalisierten IP-Adresse zurück.
  - `loop_row_index` — Gibt den aktuellen Schleifen-Index innerhalb einer Smart Template-Schleife zurück.
  - `loop_row_count` — Gibt die Anzahl aller Elemente innerhalb einer Smart Template Schleife zurück.
  - `loop_row_first` — Gibt zurück, ob sich das aktuelle Element am Ende der Smart Template-Schleife befindet.
  - `loop_row_last` — Gibt zurück, ob sich das aktuelle Element am Anfang der Smart Template-Schleife befindet.
  - `loop_row_even` — Gibt zurück, ob das aktuelle Element eine gerade Zahl der Smart Template-Schleife ist.
  - `loop_row_odd` — Gibt zurück, ob das aktuelle Element eine ungerade Zahl der Smart Template-Schleife ist.
  - `acf_fc_layout` — Gibt den Namen des aktuellen ACF-Layouts für den flexiblen Inhalt zurück.
- `compare` — Der Vergleichsoperator für die Überprüfung. Alle Operatoren können auch mit `not` oder `!` negiert werden. *(Pflicht; Standard: `=`)*
  - `=` — Prüft ob der übergebene Wert(e) **gleich** des Beitragsfeldes ist.
  - `>=` — Prüft ob der übergebene Wert **größer gleich** des Beitragsfeldes ist.
  - `>` — Prüft ob der übergebene Wert **größer** des Beitragsfeldes ist.
  - `` — Prüft ob der übergebene Wert **kleiner gleich** des Beitragsfeldes ist.
  - `` — Prüft ob der übergebene Wert **kleiner** des Beitragsfeldes ist.
  - `contains` — Prüft ob die übergebene **Zeichenkette** im Beitragsfeld **enthalten** ist.
  - `in` — Prüft ob der **Wert** des Beitragsfeldes in der übergebenen **Zeichenkette** enthalten ist.
  - `start_with` — Prüft ob die übergebene **Zeichenkette** mit dem Wert des Beitragsfeldes **beginnt**.
  - `end_with` — Prüft ob die übergebene **Zeichenkette** mit dem Wert des Beitragsfeldes **endet**.
  - `start_with_csi` — Prüft ob die übergebene **Zeichenkette** mit dem Wert des Beitragsfeldes **beginnt**.
  - `end_with_csi` — Prüft ob die übergebene **Zeichenkette** mit dem Wert des Beitragsfeldes **endet**.
  - `match` — Durchsucht mit dem übergebenen **regulären Ausdruck** das Beitragsfeld nach Übereinstimmungen.
  - `between` — Prüft ob die übergebenen Werte **zwischen** denen des Beitragsfeldes liegen.
- `value` — Der zu überprüfende Wert. *(Pflicht; Standard: `1`)*
  - `my_meta_key` — Gibt den Wert des übergebenen Beitrag-Metafeldes zurück.
  - `post_id` — Gibt die ID des Beitrages zurück.
  - `now` — Gibt die aktuelle Zeit basierend auf dem MySQL-Typ zurück.
  - `today` — Gibt das aktuelle Datum (2026-07-16) zurück.
  - `this_weekday` — Gibt den numerischer Tag (4), der aktuellen Woche zurück.
  - `this_week` — Gibt die Wochennummer (29), des aktuellen Jahres zurück (Woche beginnt am Montag).
  - `this_day` — Gibt die aktuelle Tageszahl (16), ohne führende Nullen zurück.
  - `this_month` — Gibt die aktuelle Monatszahl (7), ohne führende Nullen zurück.
  - `this_year` — Gibt das aktuelle Jahr (2026), als vierstellige Zahl zurück.
  - `page_on_front` — Gibt die **ID (10)** der Seite, welche als **Startseite (Startseite)** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `page_for_posts` — Gibt die **ID (0)** der Seite, welche als **Beitragsseite ()** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.
  - `queried_object_id` — Gibt die ID des aktuell abgefragten Objekts zurück.
  - `current_user_id` — Gibt die ID des aktuellen Benutzers zurück.
  - `current_language` — Gibt die aktuelle Sprache am Frontend zurück.
  - `default_language` — Gibt die Standardsprache der Website zurück.
- `relation` — Die Beziehung zu weiteren Felder: `and, or`. *(optional; Standard: `and`)*
- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

**Zusätzliche Informationen**

###### Spezielle Konvertierungs Befehle

Mit einem Konvertierungs-Befehl kann der Wert des Feldes gezielt umgewandelt werden.

```text
[wst_if field='wst_news_posts|count' compare='>' value='1']
	More than one post available
[/wst_if]
```

```text
[wst_if field='text_field|count_words' compare='>' value='10']
	More than 10 words available in the text
[/wst_if]
```

```text
[wst_if field='text_field|strlen' compare='>' value='10']
	More than 10 characters available in the text
[/wst_if]
```

```text
[wst_if field='post_terms|taxonomy=category' compare='contains' value='news']
	The post contains the category news!
[/wst_if]
```

```text
[wst_if field='post_terms|taxonomy=product_type' compare='contains' value='simple']
	This is a simple WooCommerce product!
[/wst_if]
```

```text
[wst_if field='post_thumbnail|image_size=full' compare='end_with' value='.jpg']
	Its a JPEG Thumbnail!
[/wst_if]
```

```text
[wst_if field='wst_news_gallery|fields=ids' compare='contains' value='1234' ]
	Image 1234 in gallery!
[/wst_if]
```

```text
[wst_if field='weather|forecast=24' value='rain']
	It will rain tomorrow!
[/wst_if]
```

```text
[wst_if field='product_type|field_type=post_meta' value='event']
	This product is a event!
[/wst_if]
```

```text
[wst_if field='product_cat_thumbnail_id|field_type=term_meta' compare='!=' value='' id='The term id']
	The product category image is available!
[/wst_if]
```

```text
[wst_if field='img|clone=wso_rooms_breaker' compare='!=' value='']
	No image selected!
[/wst_if]
```

```text
[wst_if field='img' compare='!=' value='' clone='wso_rooms_breaker']
	No image selected!
[/wst_if]
```

```text
[wst_if field='img|layout=my_acf_fc_layout' compare='!=' value='']
	No image selected!
[/wst_if]
```

```text
[wst_if field='img' compare='!=' value='' layout='my_acf_fc_layout']
	No image selected!
[/wst_if]
```

```text
[wst_if field='count_posts|post_type=post&post_status=publish' compare='>' value='0']
	Posts available!
[/wst_if]
```

```text
[wst_if field='count_posts|post_type=attachment&post_status=inherit&post_parent=1234&post_mime_type=image' compare='>' value='0']
	Images available!
[/wst_if]
```

```text
[wst_if field='is_singular|post_type=page']
	A single page is displayed!
[/wst_if]
```

###### Globale Array Elemente

Verwendung der bedingter Logik mit Globale Array Elemente.

```text
[wst_if field='preview' value='true' id='_GET']
	My preview only content
[/wst_if]
```

###### Variablen als Wert

Verwendung der bedingter Logik mit Variablen als Wert.

```text
[wst_if field='post_id' value='$my_variable']
	My Content
[/wst_if]
```

###### Zusammengesetze Logiken

Bei der bedingten Logik können auch mehrere Felder zusammen eine Beziehung aufbauen und eine gemeinsame Logik aufbauen.

```text
[wst_if field='birthdate' compare='between' value='16.06.2026,16.08.2026' field_2='background_image' compare_2='ends with' value_2='.jpg' id_2='1234' relation='or']
	My Content
[/wst_if]
```

Weitere Logik-Felder können mit einer zusätzlichen Nummernvergabe hinzugefügt werden.`field_2, value_2, compare_2, field_3, value_3, compare_3, field_4, ...`
Mit dem `relation` Parameter kann hierfür die Beziehung der Felder definiert werden.

Das angegebene Beispiel würde den Inhalt ausgeben, wenn das eingetragene Geburtsdatum zwischen dem 16.06.2026 und 16.08.2026 liegt, oder die URL des Hintergrundbildes mit ".jpg" enden würde.

###### elseif / else Anweisung

Elseif und Else erweitert eine if-Anweisung um eine weitere Anweisung, die dann ausgeführt werden soll, wenn der Ausdruck in der if-Anweisung zu FALSE ausgewertet wird.

```text
[wst_if field='user_roles' compare='contains' value='administrator' id='{{current_user_id}}']
	User is logged in as a administrator!
[else_if field='user_roles' compare='contains' value='customer' id='{{current_user_id}}']
	User is logged in as a customer!
[!else_if field='is_user_logged_in']
	User is NOT logged in!
[else]
	User is logged in!
[/wst_if]
```

###### Verschachtelte Logiken

Es können bedingte Logiken folgendermaßen beliebig verschachtelt werden.

```text
[wst_if field='show_birthdate' value='yes']
	[wst_if_b field='birthdate' compare='between' value='now,31.07.2026']
		Your birthday is [wst_acf_time field='birthdate'] this month!
	[else_if_b field='birthdate' compare='between' value='01.08.2026,31.08.2026']
		Your birthday is [wst_acf_time field='birthdate'] next month!
	[else_if_b field='birthdate' compare='!=' value='']
		It is not your birthday this month!
	[else_b]
		No birthday available!
	[/wst_if_b]
[else]
	Birthday field hidden!
[/wst_if]
```

Wichtig ist hier, den Shortcode mit `a-z` zu markieren, damit WordPress keine Probleme beim Auflösen der verschachtelten Shortcodes hat.

###### Weiterführende Beispiele

```text
[wst_if field='post_id' compare='in' value='1,2,3']
	The post ID is contained in 1, 2 or 3!
[/wst_if]
```

```text
[!wst_if field='post_id' compare='=' value='100']
	The post ID is NOT 100!
[/!wst_if]
```

#### [wst_is_language]

- **Shortcode:** `wst_is_language`

Gibt den anzuzeigenden Inhalt über eine Sprachprüfung zurück.

**Grundsyntax**

```text
[wst_is_language code='de']My Content[/wst_is_language]
```

**Parameter und Optionen**

- `code` — Der zu überprüfende Sprachcode. *(Pflicht; Standard: `de`)*
- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

#### [wst_is_mobile]

- **Shortcode:** `wst_is_mobile`

Überprüft, ob der aktuelle Browser auf einem mobilen Gerät (Smartphone) ausgeführt wird.

**Grundsyntax**

```text
[wst_is_mobile]My Content[/wst_is_mobile]
```

**Parameter und Optionen**

- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

#### [wst_is_tablet]

- **Shortcode:** `wst_is_tablet`

Überprüft, ob der aktuelle Browser auf einem Tablet läuft.

**Grundsyntax**

```text
[wst_is_tablet]My Content[/wst_is_tablet]
```

**Parameter und Optionen**

- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

#### [wst_is_ios]

- **Shortcode:** `wst_is_ios`

Überprüft, ob der aktuelle Browser auf einer IOS Plattform läuft.

**Grundsyntax**

```text
[wst_is_ios]My Content[/wst_is_ios]
```

**Parameter und Optionen**

- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

#### [wst_is_android]

- **Shortcode:** `wst_is_android`

Überprüft, ob der aktuelle Browser auf einer Android OS Plattform läuft.

**Grundsyntax**

```text
[wst_is_android]My Content[/wst_is_android]
```

**Parameter und Optionen**

- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

#### [wst_is_mobile_network]

- **Shortcode:** `wst_is_mobile_network`

Überprüft, ob die aktuelle Verbindung über ein Mobilfunknetz läuft.

**Grundsyntax**

```text
[wst_is_mobile_network]My Content[/wst_is_mobile_network]
```

**Parameter und Optionen**

- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

#### [wst_is_weather]

- **Shortcode:** `wst_is_weather`

Liefert den anzuzeigenden Inhalt per Wetter Überprüfung zurück.

**Grundsyntax**

```text
[wst_is_weather key='clear']My Content[/wst_is_weather]
```

**Parameter und Optionen**

- `key` — Der Wetter-Gruppenschlüssel (thunderstorm, drizzle, rain, snow, atmosphere, clear, clouds). *(Pflicht; Standard: `clear`)*
  - `thunderstorm` — **Gewitter** (Gewitter mit leichtem Regen, Gewitter mit Regen, Gewitter mit starkem Regen, Leichtes Gewitter, Gewitter, Starkes Gewitter, Wiederkehrende Gewitter, Gewitter mit leichtem Nieselregen, Gewitter mit Nieselregen, Gewitter mit starkem Nieselregen)
  - `drizzle` — **Nieselregen** (Leichter Nieselregen, Nieselregen, Starker Nieselregen, Leichter nieselartiger Niederschlag, Nieselartiger Niederschlag, Starker nieselartiger Niederschlag, Kurzer Regenschauer und Nieselregen, Starker Regenschauer und Nieselregen, Kurzer, nieselartiger Schauer)
  - `rain` — **Regen** (Leichter Regen, Mäßiger Regen, Starker Regen, Sehr starker Regen, Starkregen, Eisregen, Kurzer, leichter Regenschauer, Kurzer Regenschauer, Kurzer, starker Regenschauer, Wiederkehrende kurze Regenschauer)
  - `snow` — **Schneefall** (Leichter Schneefall, Schneefall, Starker Schneefall, Graupel, Kurzer Graupelschauer, Leichter Schneeregen, Schneeregen, Kurzer, leichter Schneeschauer, Kurzer Schneeschauer, Kurzer, starker Schneeschauer)
  - `atmosphere` — **Atmosphäre** (Dunst, Nebel, Rauch, Dunst, trübe Sicht, Staub und Sandwirbel, Nebel, Sand, Staub, Vulkanasche, Windböen, Tornado)
  - `clear` — **Heiter** (Klarer Himmel)
  - `clouds` — **Wolken** (Leicht bewölkt, Wolkig, Stark bewölkt, Bedeckt)
- `forecast` — Anzahl der Stunden (1 - 120) für eine aktuelle Wettervorhersage (max. 5 Tage). *(optional)*
- `forecast_time` — Die genaue [Uhrzeit](http://php.net/manual/de/datetime.formats.relative.php) der Wettervorhersage. *(optional)*
- `visibility` — Die Sichtbarkeit des Inhaltes: `visible` oder `hidden`. *(optional; Standard: `visible`)*

### 5.20 Lesezeichen

- **Interne ID:** `bookmark`
- **Kategoriefarbe:** `#C45500`
- **Einträge:** 5

#### [wst_bookmark_button]

- **Shortcode:** `wst_bookmark_button`

Erstellt einen Button, welcher einen aktuellen Beitrag zur Merkliste hinzufügt.

**Grundsyntax**

```text
[wst_bookmark_button name='posts']
```

**Parameter und Optionen**

- `name` — Der Name der Merkliste. *(Pflicht; Standard: `posts`)*
- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Zu Lesezeichen hinzufügen`)*
- `text_active` — Der Text des aktiven Buttons als Zeichenkette. *(optional; Standard: `Von Lesezeichen entfernen`)*
- `save_text` — Der Text des Buttons als Zeichenkette, welcher beim Speichervorgang angezeigt werden soll. *(optional; Standard: `Speichern...`)*
- `icon` — [Dashicon](https://developer.wordpress.org/resource/dashicons) Klasse für den nicht aktiven Button. *(optional; Standard: ``)*
- `icon_active` — [Dashicon](https://developer.wordpress.org/resource/dashicons) Klasse für den aktiven Button. *(optional; Standard: ``)*
- `icon_color` — Die Dashicon Farbe für den Button. *(optional)*
- `icon_color_active` — Die Dashicon-Farbe für den aktiven Button. *(optional)*
- `redirect` — Nach dem Speichern zu einer bestimmten Seite verlinken: Seiten ID, Seiten Titel oder individueller Link. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional)*

#### [wst_bookmark_share]

- **Shortcode:** `wst_bookmark_share`

Erstellt einen Button, welcher eine Merkliste zum Teilen mittels URL bereitstellt.

**Grundsyntax**

```text
[wst_bookmark_share name='posts']
```

**Parameter und Optionen**

- `name` — Der eindeutige Name der Merkliste. *(Pflicht; Standard: `posts`)*
- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Zu Lesezeichen hinzufügen`)*
- `icon` — [Dashicon](https://developer.wordpress.org/resource/dashicons) Klasse für den nicht aktiven Button. *(optional; Standard: ``)*
- `icon_color` — Die Dashicon Farbe für den Button. *(optional)*
- `channel` — Die Plattform auf welcher die Teilen-URL bereitgestellt werden soll. *(optional; Standard: `email`)*
  - `email` — Teilen der Merkliste per E-Mail.
  - `whatsapp` — Teilen der Merkliste mit WhatsApp.
- `mailto` — Die Lesezeichen teilen E-Mail-Adresse. *(optional)*
- `subject` — Der Lesezeichen teilen E-Mail Betreff. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional)*
- `page` — Die Seite, welche geteilt werden soll: Seiten ID, Seiten Titel oder individueller Link. *(optional)*

#### [wst_bookmark_reset]

- **Shortcode:** `wst_bookmark_reset`

Erstellt einen Button, welcher eine Merkliste wieder zurücksetzt.

**Grundsyntax**

```text
[wst_bookmark_reset name='posts']
```

**Parameter und Optionen**

- `name` — Der eindeutige Name der Merkliste. *(Pflicht; Standard: `posts`)*
- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Zu Lesezeichen hinzufügen`)*
- `icon` — [Dashicon](https://developer.wordpress.org/resource/dashicons) Klasse für den nicht aktiven Button. *(optional; Standard: ``)*
- `icon_color` — Die Dashicon Farbe für den Button. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional)*
- `redirect` — Nach dem Speichern zu einer bestimmten Seite verlinken: Seiten ID, Seiten Titel oder individueller Link. *(optional)*

#### [wst_bookmark_counter]

- **Shortcode:** `wst_bookmark_counter`

Erstellt einen Counter, welcher alle Beiträge der Merkliste zählt.

**Grundsyntax**

```text
[wst_bookmark_counter name='posts']
```

**Parameter und Optionen**

- `name` — Der eindeutige Name der Merkliste. *(Pflicht; Standard: `posts`)*
- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Zu Lesezeichen hinzufügen`)*
- `icon` — [Dashicon](https://developer.wordpress.org/resource/dashicons) Klasse für den nicht aktiven Button. *(optional; Standard: ``)*
- `icon_color` — Die Dashicon Farbe für den Button. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `page` — Den Counter zu einer bestimmten Seite verlinken: Seiten ID, Seiten Titel oder individueller Link. *(optional)*
- `target` — Die Zielfensterbasis des Elements: `_self | _blank | _parent | _top`. *(optional; Standard: `_self`)*
- `lang` — Die Sprache. *(optional; Standard: `false`)*

#### [wst_bookmark_posts]

- **Shortcode:** `wst_bookmark_posts`

Gibt jeden **Beitrag** der Merkliste als Schleife zurück.

**Grundsyntax**

```text
[wst_bookmark_posts name='posts']row_content[/wst_bookmark_posts]
```

**Parameter und Optionen**

- `name` — Der Name der Merkliste. *(Pflicht; Standard: `posts`)*
- `post_type` — Der Name des Inhaltstyps. *(optional; Standard: `post`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*
- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional; Standard: `post__in`)*

**Zusätzliche Informationen**

###### Die Merkliste als Schleife verwenden

Der folgender Shortcode verwandelt das Feld user bookmark posts in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_bookmark_posts name='posts']
	[wst_post_title id='{{post_id/wst_bookmark_posts}}']
[/wst_bookmark_posts]
```

Jeder Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{post_id/loop_name}}` angegeben werden (siehe Beispiel).

### 5.21 Gefällt mir

- **Interne ID:** `like`
- **Kategoriefarbe:** `#0866FF`
- **Einträge:** 3

#### [wst_like_button]

- **Shortcode:** `wst_like_button`

Erzeugt einen Button, welcher den aktuellen Beitrag zur Like-Liste hinzufügt.

**Grundsyntax**

```text
[wst_like_button]
```

**Parameter und Optionen**

- `text` — Der Text des Buttons als Zeichenkette. *(optional; Standard: `Gefällt mir`)*
- `text_active` — Der Text des aktiven Buttons als Zeichenkette. *(optional; Standard: `Gefällt mir nicht mehr`)*
- `icon` — [Dashicon](https://developer.wordpress.org/resource/dashicons) Klasse für den nicht aktiven Button. *(optional; Standard: ``)*
- `icon_active` — [Dashicon](https://developer.wordpress.org/resource/dashicons) Klasse für den aktiven Button. *(optional; Standard: ``)*
- `icon_color` — Die Dashicon Farbe für den Button. *(optional)*
- `icon_color_active` — Die Dashicon-Farbe für den aktiven Button. *(optional; Standard: `blue`)*
- `redirect` — Nach dem Speichern zu einer bestimmten Seite verlinken: Seiten ID, Seiten Titel oder individueller Link. *(optional)*
- `login_url` — Link zur Anmeldeseite, wenn die Like-Funktion für abgemeldete Benutzer deaktiviert ist: Seiten ID, Seiten Titel oder individueller Link. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `title` — Das title Attribut des Elements. *(optional)*

#### [wst_like_post_count]

- **Shortcode:** `wst_like_post_count`

Erzeugt einen Zähler, der jedes Like eines Beitrags zählt.

**Grundsyntax**

```text
[wst_like_post_count]
```

#### [wst_like_posts]

- **Shortcode:** `wst_like_posts`

Gibt alle **Beiträge** aus einer Benutzer Like Liste als Schleife zurück.

**Grundsyntax**

```text
[wst_like_posts]row_content[/wst_like_posts]
```

**Parameter und Optionen**

- `post_type` — Der Name des Inhaltstyps. *(optional; Standard: `post`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*
- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional; Standard: `post__in`)*

**Zusätzliche Informationen**

###### Die Benutzer Likes als Schleife verwenden

Der folgender Shortcode verwandelt das Feld like in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_like_posts name='posts']
	[wst_post_title id='{{post_id/wst_like_posts}}']
[/wst_like_posts]
```

Jeder Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{post_id/loop_name}}` angegeben werden (siehe Beispiel).

### 5.22 WooCommerce

- **Interne ID:** `woocommerce`
- **Kategoriefarbe:** `#7B51AD`
- **Einträge:** 14

#### [wst_woocommerce_product_sale_flash]

- **Shortcode:** `wst_woocommerce_product_sale_flash`

Gibt das **Angebot Sticker** (Sale Flash) des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_sale_flash]
```

#### [wst_woocommerce_product_images]

- **Shortcode:** `wst_woocommerce_product_images`

Gibt das **Beitragsbild** und die **Gallery** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_images]
```

#### [wst_woocommerce_product_thumbnail]

- **Shortcode:** `wst_woocommerce_product_thumbnail`

Gibt das **Beitragsbild** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_thumbnail]
```

**Parameter und Optionen**

- `size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `woocommerce_thumbnail`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `attr` — HTML Attribute des Bildes als Query String. *(optional)*
- `placeholder` — Ob das WooCommerce Platzhalter Bild, bei fehlen eines Produktbildes ausgegeben werden soll. *(optional; Standard: `0`)*
- `template` — Rendering Template `woocommerce` oder `wordpress` für den Bild-Inhalt. *(optional; Standard: `weseo-smart-template`)*

#### [wst_woocommerce_product_gallery]

- **Shortcode:** `wst_woocommerce_product_gallery`

Gibt die **Galerie** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_gallery]
```

**Parameter und Optionen**

- `template` — Rendering Template `woocommerce` oder `wordpress` für den Bild-Inhalt. *(optional; Standard: `weseo-smart-template`)*

**Zusätzliche Informationen**

Es können alle Parameter des Standard [WordPress Galerie Shortcodes](https://codex.wordpress.org/Gallery_Shortcode) verwendet werden.

#### [wst_woocommerce_product_title]

- **Shortcode:** `wst_woocommerce_product_title`

Gibt den **Titel** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_title]
```

#### [wst_woocommerce_product_rating]

- **Shortcode:** `wst_woocommerce_product_rating`

Gibt die **Bewertung** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_rating]
```

#### [wst_woocommerce_product_price]

- **Shortcode:** `wst_woocommerce_product_price`

Gibt den **Preis** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_price]
```

#### [wst_woocommerce_product_excerpt]

- **Shortcode:** `wst_woocommerce_product_excerpt`

Gibt den **Auszug** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_excerpt]
```

#### [wst_woocommerce_product_meta]

- **Shortcode:** `wst_woocommerce_product_meta`

Gibt die **Metadaten** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_meta]
```

#### [wst_woocommerce_product_sharing]

- **Shortcode:** `wst_woocommerce_product_sharing`

Gibt die **Teilen** Funktion (externe Plugins) des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_sharing]
```

#### [wst_woocommerce_product_add_to_cart]

- **Shortcode:** `wst_woocommerce_product_add_to_cart`

Gibt den **In den Warenkorb** Funktion des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_add_to_cart]
```

#### [wst_woocommerce_product_data_tabs]

- **Shortcode:** `wst_woocommerce_product_data_tabs`

Gibt die **Detail-Tabs** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_data_tabs]
```

#### [wst_woocommerce_product_upsell_display]

- **Shortcode:** `wst_woocommerce_product_upsell_display`

Gibt die **Zusatzverkäufe** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_upsell_display]
```

#### [wst_woocommerce_product_related_products]

- **Shortcode:** `wst_woocommerce_product_related_products`

Gibt die **Verwandten Produkte** des Produktes zurück.

**Grundsyntax**

```text
[wst_woocommerce_product_related_products]
```

### 5.23 Befehle

- **Interne ID:** `command`
- **Kategoriefarbe:** `#212F3D`
- **Einträge:** 8

#### [wst_variable]

- **Shortcode:** `wst_variable`

Befehls Shortcode zum Setzen oder Abrufen einer Variable.

**Grundsyntax**

```text
[wst_variable name='my_variable']value[/wst_variable]
```

**Parameter und Optionen**

- `name` — Der Name der Variable. *(Pflicht; Standard: `my_variable`)*
- `type` — Der Typ der Variable. *(optional; Standard: `string`)*
  - `string` — String
  - `int` — Integer
  - `float` — Float
  - `array` — Array
  - `boolean` — Boolean

**Zusätzliche Informationen**

###### Abrufen eines Variablenwerts

```text
[wst_variable name='my_variable']
```

###### Einen Variablenwert setzen

```text
[wst_variable name='my_variable']value[/wst_variable]
```

#### [wst_foreach]

- **Shortcode:** `wst_foreach`

Befehls Shortcode zum Erstellen einer benutzerdefinierten Schleife.

**Grundsyntax**

```text
[wst_foreach list='blue,gree,red']row_content[/wst_foreach]
```

**Parameter und Optionen**

- `list` — Liste von Array-Elementen *(Pflicht; Standard: `blue,gree,red`)*
  - `countries` — Gibt eine Liste mit Ländern und Codes zurück.
- `range_start` — Der erste Wert der Folge. *(optional)*
- `range_end` — Der letzte mögliche Wert der Folge. *(optional)*
- `range_step` — Gibt an, wie weit die einzelnen Werte der erzeugten Folge auseinander liegen. *(optional; Standard: `1`)*
- `key_tag` — Der Name des Schlüssel-Tags in der Schleife. *(optional; Standard: `key`)*
- `value_tag` — Der Name des Werte-Tags in der Schleife. *(optional; Standard: `value`)*
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*
- `key_value_separator` — Das Schlüssel/Wert-Trennzeichen zum Verbinden von Array-Elementen. *(optional; Standard: `|`)*

**Zusätzliche Informationen**

###### Shortcode Benutzung

```text
[wst_foreach list='blue,gree,red']
	{{value/wst_foreach}}
[/wst_foreach]
```

###### Assoziatives Array

Das folgende Beispiel zeigt ein assoziatives Array mit verschiedenen Schlüssel/Wert-Tag-Namen.

```text
[wst_foreach list='8276|Home,1933|Privacy Page,4521|Imprint Page' key_tag='post_id' value_tag='post_title']
	{{post_title/wst_foreach}} - [wst_post_permalink id='{{post_id/wst_foreach}}' url='1']
[/wst_foreach]
```

###### Bereich der Elemente

Das folgende Beispiel erstellt eine Liste, welche einen Bereich von Elementen enthält.

```text
[wst_foreach range_start='1' range_end='10' range_step='0.1']
	{{value/wst_foreach}}
[/wst_foreach]
```

```text
[wst_foreach range_start='a' range_end='z']
	{{value/wst_foreach}}
[/wst_foreach]
```

###### Vordefinierte Listen

Im folgenden Beispiel werden Arrays aus vordefinierten Listen erstellt.

```text
[wst_foreach list='countries' key_tag='country_code' value_tag='country_name']
	{{country_code/wst_foreach}} - {{country_name/wst_foreach}}
[/wst_foreach]
```

```text
[wst_foreach list='languages' key_tag='language_code' value_tag='language_name']
	{{language_code/wst_foreach}} - {{language_name/wst_foreach}}
[/wst_foreach]
```

#### [wst_continue_loop]

- **Shortcode:** `wst_continue_loop`

Befehls Shortcode, um eine aktuelle Schleife zu überspringen.

**Grundsyntax**

```text
[wst_continue_loop id='loop_name']
```

**Parameter und Optionen**

- `id` — Der Name der Schleife. *(Pflicht; Standard: `loop_name`)*

**Zusätzliche Informationen**

###### Shortcode Benutzung

```text
[wst_posts field='field_name']
	[wst_if field='post_id' value='1234' id='{{post_id/wst_posts}}']
		[wst_continue_loop id='wst_posts']
	[/wst_if]
	[wst_post_title id='{{post_id/wst_posts}}']
[/wst_posts]
```

Dieser Shortcode kann in allen Smart Template Schleifen verwendet werden.

#### [wst_break_loop]

- **Shortcode:** `wst_break_loop`

Befehls Shortcode, um eine aktuelle Schleife zu unterbrechen.

**Grundsyntax**

```text
[wst_break_loop id='loop_name']
```

**Parameter und Optionen**

- `id` — Der Name der Schleife. *(Pflicht; Standard: `loop_name`)*

**Zusätzliche Informationen**

###### Shortcode Benutzung

```text
[wst_posts field='field_name']
	[wst_post_title id='{{post_id/wst_posts}}']
	[wst_if field='post_id' value='1234' id='{{post_id/wst_posts}}']
		[wst_break_loop id='wst_posts']
	[/wst_if]
[/wst_posts]
```

Dieser Shortcode kann in allen Smart Template Schleifen verwendet werden.

#### [wst_add_log]

- **Shortcode:** `wst_add_log`

Befehls Shortcode, um einen Log-Eintrag zu schreiben.

**Grundsyntax**

```text
[wst_add_log level='debug']log_message[/wst_add_log]
```

**Parameter und Optionen**

- `level` — Das Logging Level. *(Pflicht; Standard: `debug`)*
  - `emergency` — Das System ist unbrauchbar.
  - `alert` — Es muss sofort gehandelt werden.
  - `critical` — Kritische Bedingungen.
  - `error` — Fehlerbedingungen.
  - `warning` — Warnende Bedingungen.
  - `notice` — Normale, aber signifikante Bedingung.
  - `info` — Informationelle Nachrichten.
  - `debug` — Fehlerbehebungs-Nachrichten.
- `source` — Zusätzliche Quellenangaben. *(optional; Standard: `wst_add_log`)*

#### [wst_add_kint_log]

- **Shortcode:** `wst_add_kint_log`

Befehls Shortcode, um einen detaillierten Log-Eintrag zu schreiben.

**Grundsyntax**

```text
[wst_add_kint_log level='debug' field='field_name']log_message[/wst_add_kint_log]
```

**Parameter und Optionen**

- `level` — Das Logging Level. *(Pflicht; Standard: `debug`)*
  - `emergency` — Das System ist unbrauchbar.
  - `alert` — Es muss sofort gehandelt werden.
  - `critical` — Kritische Bedingungen.
  - `error` — Fehlerbedingungen.
  - `warning` — Warnende Bedingungen.
  - `notice` — Normale, aber signifikante Bedingung.
  - `info` — Informationelle Nachrichten.
  - `debug` — Fehlerbehebungs-Nachrichten.
- `field` — Der Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `0`)*
- `source` — Zusätzliche Quellenangaben. *(optional; Standard: `wst_add_kint_log`)*

#### [wst_footer]

- **Shortcode:** `wst_footer`

Befehls-Shortcode zum Hinzufügen von Inhalten in der Fußzeile.

**Grundsyntax**

```text
[wst_footer]content[/wst_footer]
```

#### [wst_implode]

- **Shortcode:** `wst_implode`

Verbindet Array-Elemente zu einem String.

**Grundsyntax**

```text
[wst_implode]Liste von Array-Elementen[/wst_implode]
```

### 5.24 SmartTags

- **Interne ID:** `smarttag`
- **Einträge:** 1

#### {{SmartTag/*}}


SmartTags sind Platzhalter, die bei der Ausgabe einer Seite durch bestimmte Inhalte ersetzt werden.

**Grundsyntax**

```text
{{SmartTag/*}}
```

**Zusätzliche Informationen**

###### SmartTags

SmartTags sind Platzhalter, die bei der Ausgabe einer Seite durch bestimmte Inhalte ersetzt werden.
So kann beispielsweise der Titel einer Seite angezeigt oder das Beitragsdatum angesprochen werden.
SmartTags können fast überall in WordPress verwendet werden.

Die Platzhalter können nur textlichen Inhalt zurückgeben, mehrdimensionale Felder (z.B. ACF Beitrags Objekte) geben den Inhalt Beistrich getrennt zurück.
**Liste aller Beitrags Elemente**

 `{{my_meta_key}}`
 Gibt den Wert des übergebenen Beitrag-Metafeldes zurück.

 `{{post_id}}`
 Gibt die ID des Beitrages zurück.

 `{{post_title}}`
 Gibt den Titel des Beitrages zurück.

 `{{post_name}}`
 Gibt die Titelform des Beitrages zurück.

 `{{post_content}}`
 Gibt den Inhalt des Beitrages zurück.

 `{{post_excerpt}}`
 Gibt den Auszug des Beitrages zurück.

 `{{post_type}}`
 Gibt den Inhaltstyp des Beitrages zurück.

 `{{post_status}}`
 Gibt den Status des Beitrages zurück.

 `{{post_format}}`
 Gibt das Format des Beitrages zurück.

 `{{post_thumbnail}}`
 Gibt das Bild des Beitrages zurück.

 `{{post_date}}`
 Gibt das Datum zurück, an dem der Beitrag verfasst wurde.

 `{{post_time}}`
 Gibt die Uhrzeit zurück, an welcher der Beitrag verfasst wurde.

 `{{post_modified}}`
 Gibt das Datum zurück, an dem der Beitrag geändert wurde.

 `{{post_permalink}}`
 Gibt den Permalink des Beitrages zurück.

 `{{post_parent}}`
 Gibt die ID des übergeordneten Beitrags zurück.

 `{{post_menu_order}}`
 Gibt die Reihenfolge des Beitrages zurück.

 `{{post_children}}`
 Gibt die Kind IDs des Beitrages zurück.

 `{{post_terms}}`
 Gibt alle Beitrags-Begriffe der übergebenen Taxonomy zurück.

 `{{post_parent_level_count}}`
 Gibt die Levelanzahl der Eltern-Beiträge zurück.

 `{{post_children_level_count}}`
 Gibt die Levelanzahl der Kind-Beiträge zurück.

 `{{post_password_required}}`
 Gibt zurück, ob ein Beitrag ein Passwort erfordert und das korrekte Passwort angegeben wurde.

 `{{post_language}}`
 Gibt die Sprachinformationen eines Beitrags zurück.

 `{{post_translations}}`
 Gibt alle Übersetzungen eines Beitrags zurück.

 `{{post_primary_term_id}}`
 Gibt die ID der primären Kategorie des Beitrags zurück.

Mit dem folgenden Smart-Tag-Format, können Seiten und Beiträge über ihre ID verknüpft werden.
`{{post_title/*}}`
* kann mit der Beitrags-ID oder dem aktuellen Schleifennamen ersetzt werden.

**Liste aller Anhang Elemente**

 `{{attachment_image_url}}`
 Gibt die URL für einen Bild-Anhang zurück.

 `{{attachment_url}}`
 Gibt die URL für einen Anhang zurück.

 `{{attachment_metadata}}`
 Gibt die Metadaten für ein Attachment zurück.

Mit dem folgenden Smart-Tag-Format, können Anhänge über ihre ID verknüpft werden.
`{{attachment_url/*}}`
* kann mit der Anhang-ID oder dem aktuellen Schleifennamen ersetzt werden.

**Liste aller WooCommerce Produktelemente**

 `{{wc_product_type}}`
 Gibt den internen Typ zurück.

 `{{wc_product_name}}`
 Gibt den Produktnamen zurück.

 `{{wc_product_slug}}`
 Gibt die Titelform des Produktes zurück.

 `{{wc_product_date_created}}`
 Gibt das Erstellungsdatum des Produktes zurück.

 `{{wc_product_date_modified}}`
 Gibt das Änderungsdatum des Produktes zurück.

 `{{wc_product_status}}`
 Gibt den Status des Produktes zurück.

 `{{wc_product_featured}}`
 Gibt zurück, ob das Produkt hervorgehoben ist.

 `{{wc_product_catalog_visibility}}`
 Gibt die Sichtbarkeit des Katalogs zurück.

 `{{wc_product_description}}`
 Gibt die Beschreibung des Produktes zurück.

 `{{wc_product_short_description}}`
 Gibt die Kurzbeschreibung des Produktes zurück.

 `{{wc_product_sku}}`
 Gibt die Artikelnummer zurück.

 `{{wc_product_global_unique_id}}`
 Gibt die Eindeutige ID zurück.

 `{{wc_product_price}}`
 Gibt den aktiven Preis des Produktes zurück.

 `{{wc_product_price_excluding_tax}}`
 Gibt den aktiven Preis des Produkts mit Steuern zurück.

 `{{wc_product_price_including_tax}}`
 Gibt den aktiven Preis des Produkts mit Steuern zurück.

 `{{wc_product_regular_price}}`
 Gibt den regulären Preis des Produktes zurück.

 `{{wc_product_sale_price}}`
 Gibt den Angebotspreis des Produktes zurück.

 `{{wc_product_date_on_sale_from}}`
 Gibt das Startdatum des Angebots zurück.

 `{{wc_product_date_on_sale_to}}`
 Gibt das Verkaufsdatum zurück.

 `{{wc_product_total_sales}}`
 Gibt die Gesamtzahl der Verkäufe zurück.

 `{{wc_product_tax_status}}`
 Gibt den Steuerstatus zurück.

 `{{wc_product_tax_class}}`
 Gibt die Steuerklasse zurück.

 `{{wc_product_manage_stock}}`
 Gibt zurück, ob das Produkt eine Lagerverwaltung verwendet.

 `{{wc_product_stock_quantity}}`
 Gibt die Anzahl der zum Verkauf stehenden Artikel zurück."

 `{{wc_product_stock_status}}`
 Gibt den Lagerbestand zurück.

 `{{wc_product_backorders}}`
 Gibt die Nachbestellungen zurück.

 `{{wc_product_low_stock_amount}}`
 Gibt den Schwellwert für „geringer Lagerbestand“ zurück.

 `{{wc_product_sold_individually}}`
 Gibt zurück, ob einzeln verkauft werden soll.

 `{{wc_product_weight}}`
 Gibt das Gewicht des Produkts zurück.

 `{{wc_product_length}}`
 Gibt die Produktlänge zurück.

 `{{wc_product_width}}`
 Gibt die Produktbreite zurück.

 `{{wc_product_height}}`
 Gibt die Produkthöhe zurück.

 `{{wc_product_dimensions}}`
 Gibt die Abmessungen formatiert zurück.

 `{{wc_product_upsell_ids}}`
 Gibt die Upsell-IDs. zurück.

 `{{wc_product_cross_sell_ids}}`
 Gibt die Cross-Sell-IDs zurück.

 `{{wc_product_parent_id}}`
 Gibt die ID des Eltern-Produktes zurück.

 `{{wc_product_reviews_allowed}}`
 Gibt zurück, ob Bewertungen erlaubt sind.

 `{{wc_product_purchase_note}}`
 Gibt den Hinweis zum Kauf zurück.

 `{{wc_product_attributes}}`
 Gibt die Produktattribute zurück.

 `{{wc_product_default_attributes}}`
 Gibt die Standardattribute zurück.

 `{{wc_product_menu_order}}`
 Gibt die Menüreihenfolge zurück.

 `{{wc_product_post_password}}`
 Gibt das Passwort des Beitrags zurück.

 `{{wc_product_category_ids}}`
 Gibt die Kategorie-IDs zurück.

 `{{wc_product_tag_ids}}`
 Gibt die Schlagwort-IDs zurück.

 `{{wc_product_virtual}}`
 Gibt zurück, ob das Produkt virtuell ist.

 `{{wc_product_gallery_image_ids}}`
 Gibt die Anhang-IDs der Galerie zurück.

 `{{wc_product_shipping_class_id}}`
 Gibt die ID der Versandklasse zurück.

 `{{wc_product_downloads}}`
 Gibt die Downloads zurück.

 `{{wc_product_download_expiry}}`
 Gibt das Ablaufdatum des Downloads zurück.

 `{{wc_product_downloadable}}`
 Prüft, ob ein Produkt herunterladbar ist.

 `{{wc_product_download_limit}}`
 Gibt das Download-Limit zurück.

 `{{wc_product_image_id}}`
 Gibt die ID des Hauptbildes zurück.

 `{{wc_product_rating_counts}}`
 Gibt die Bewertungsanzahl zurück.

 `{{wc_product_average_rating}}`
 Gibt die durchschnittliche Bewertung zurück.

 `{{wc_product_review_count}}`
 Gibt die Anzahl der Beurteilungen zurück.

 `{{wc_product_supports}}`
 Prüft, ob ein Produkt ein bestimmtes Feature unterstützt.

 `{{wc_product_exists}}`
 Gibt zurück, ob der Produkt-Beitrag existiert oder nicht.

 `{{wc_product_is_type}}`
 Prüft den Produkttyp.

 `{{wc_product_is_downloadable}}`
 Prüft, ob ein Produkt herunterladbar ist.

 `{{wc_product_is_virtual}}`
 Prüft, ob ein Produkt virtuell ist (keinen Versand hat).

 `{{wc_product_is_featured}}`
 Gibt zurück, ob das Produkt hervorgehoben ist oder nicht.

 `{{wc_product_is_sold_individually}}`
 Prüft, ob ein Produkt einzeln verkauft wird (keine Mengen).

 `{{wc_product_is_visible}}`
 Gibt zurück, ob das Produkt im Katalog sichtbar ist oder nicht.

 `{{wc_product_is_purchasable}}`
 Gibt false zurück, wenn das Produkt nicht gekauft werden kann.

 `{{wc_product_is_on_sale}}`
 Gibt zurück, ob das Produkt im Angebot ist oder nicht.

 `{{wc_product_has_dimensions}}`
 Gibt zurück, ob für das Produkt Abmessungen festgelegt wurden oder nicht.

 `{{wc_product_has_weight}}`
 Gibt zurück, ob für das Produkt ein Gewicht festgelegt wurde oder nicht.

 `{{wc_product_is_in_stock}}`
 Gibt zurück, ob das Produkt gekauft werden kann oder nicht.

 `{{wc_product_needs_shipping}}`
 Prüft, ob ein Produkt verschickt werden muss.

 `{{wc_product_is_taxable}}`
 Gibt an, ob das Produkt steuerpflichtig ist oder nicht.

 `{{wc_product_is_shipping_taxable}}`
 Gibt zurück, ob der Produktversand steuerpflichtig ist oder nicht.

 `{{wc_product_managing_stock}}`
 Gibt zurück, ob das Produkt eine Lagerverwaltung verwendet oder nicht.

 `{{wc_product_backorders_allowed}}`
 Gibt zurück, ob das Produkt nachbestellt werden kann oder nicht.

 `{{wc_product_backorders_require_notification}}`
 Gibt zurück, ob das Produkt den Kunden über den Rückstand informieren muss oder nicht.

 `{{wc_product_is_on_backorder}}`
 Prüft, ob ein Produkt im Rückstand ist.

 `{{wc_product_has_enough_stock}}`
 Gibt zurück, ob das Produkt genug Bestand für die Bestellung hat oder nicht.

 `{{wc_product_has_attributes}}`
 Gibt zurück, ob das Produkt sichtbare Eigenschaften hat oder nicht.

 `{{wc_product_has_child}}`
 Gibt zurück, ob das Produkt ein untergeordnetes Produkt hat oder nicht.

 `{{wc_product_child_has_dimensions}}`
 Hat ein Kind Maße?

 `{{wc_product_child_has_weight}}`
 Hat ein Kind ein Gewicht?

 `{{wc_product_has_file}}`
 Prüft, ob an das herunterladbare Produkt eine Datei angehängt ist.

 `{{wc_product_has_options}}`
 Gibt zurück, ob das Produkt zusätzliche Optionen hat, die vor dem Hinzufügen zum Warenkorb ausgewählt werden müssen.

 `{{wc_product_title}}`
 Gibt den Titel des Produktes zurück. Bei Produkten ist dies der Produktname.

 `{{wc_product_permalink}}`
 Produkt Permalink.

 `{{wc_product_children}}`
 Gibt die IDs der Kinder zurück, falls zutreffend.

 `{{wc_product_stock_managed_by_id}}`
 Wenn der Lagerbestand von einer anderen Produkt-ID stammt, sollte diese geändert werden.

 `{{wc_product_price_html}}`
 Gibt den Preis im HTML-Format zurück.

 `{{wc_product_formatted_name}}`
 Gibt den Produktnamen mit SKU oder ID zurück. Wird in der Administration verwendet.

 `{{wc_product_min_purchase_quantity}}`
 Gibt die Mindestmenge, die auf einmal gekauft werden kann zurück.

 `{{wc_product_max_purchase_quantity}}`
 Gibt die maximale Menge, die auf einmal gekauft werden kann zurück.

 `{{wc_product_add_to_cart_url}}`
 Gibt die "In den Warenkorb" URL zurück, welche hauptsächlich in Schleifen verwendet wird.

 `{{wc_product_single_add_to_cart_text}}`
 Gibt den "In den Warenkorb" Button Text für die einzelne Seite zurück.

 `{{wc_product_add_to_cart_aria_describedby}}`
 Gibt die aria-describedby Beschreibung für den "In den Warenkorb" Button zurück.

 `{{wc_product_add_to_cart_text}}`
 Gibt den "In den Warenkorb" Button Text zurück.

 `{{wc_product_add_to_cart_description}}`
 Gibt die Textbeschreibung für den "In den Warenkorb" Button zurück - wird in Aria-Tags verwendet.

 `{{wc_product_image}}`
 Gibt das Hauptproduktbild zurück.

 `{{wc_product_shipping_class}}`
 Gibt die Produktversandklasse SLUG zurück.

 `{{wc_product_attribute}}`
 Gibt ein einzelnes Produktattribut als String zurück.

 `{{wc_product_rating_count}}`
 Gibt die Gesamtzahl (COUNT) der Bewertungen oder nur die Anzahl für eine Bewertung, z.B. die Anzahl der 5-Sterne-Bewertungen zurück.

 `{{wc_product_file}}`
 Gibt eine Datei nach $download_id zurück.

 `{{wc_product_file_download_path}}`
 Gibt den Pfad zum Dateidownload, der durch $download_id identifiziert wird zurück.

 `{{wc_product_price_suffix}}`
 Gibt das Suffix, das nach Preisen > 0 angezeigt wird zurück.

 `{{wc_product_availability}}`
 Gibt die Verfügbarkeit des Produkts zurück.

 `{{wc_cart_cross_sell_products}}`
 Gibt die Cross-Sell-Produkte basierend auf den Artikeln im Warenkorb zurück.

Mit dem folgenden Smart-Tag-Format, können WooCommerce-Produkte über ihre ID verknüpft werden.
`{{wc_product_upsell_products/*}}`
* kann durch die Produkt-ID oder den aktuellen Schleifennamen ersetzt werden.

**Liste aller Begriff Elemente**

 `{{term_id}}`
 Gibt die ID des Begriffs zurück.

 `{{term_name}}`
 Gibt den Namen des Begriffs zurück.

 `{{term_slug}}`
 Gibt die Titelform des Begriffs zurück.

 `{{term_link}}`
 Gibt den Permalink für ein Taxonomie-Begriffsarchiv zurück.

 `{{term_group}}`
 Gibt die Gruppennummer des Begriffs zurück.

 `{{term_taxonomy_id}}`
 Gibt die Taxonomy-ID des Begriffs zurück.

 `{{term_taxonomy}}`
 Gibt die Taxonomy des Begriffs zurück.

 `{{term_description}}`
 Gibt die Beschreibung des Begriffs zurück.

 `{{term_parent}}`
 Gibt die ID des übergeordneten Begriffs zurück.

 `{{term_count}}`
 Gibt die Anzahl der zugeordneten Beiträge des Begriffs zurück.

 `{{term_is_parent}}`
 Prüft ob der übergebene Begriff ein Eltern-Begriff ist.

 `{{term_is_children}}`
 Prüft ob der übergebene Begriff ein Kind-Begriff ist.

Mit dem folgenden Smart-Tag-Format, können Begriffe über ihre ID verknüpft werden.
`{{term_name/*}}`
* kann mit der Term-ID oder dem aktuellen Schleifennamen ersetzt werden.

**Liste aller Benutzer Elemente**

 `{{current_user_id}}`
 Gibt die ID des aktuellen Benutzers zurück.

 `{{user_nickname}}`
 Gibt den Spitznamen des Benutzers zurück.

 `{{user_description}}`
 Gibt die Beschreibung des Benutzers zurück.

 `{{user_firstname}}`
 Gibt den Vornamen des Benutzers zurück.

 `{{user_lastname}}`
 Gibt den Nachnamen des Benutzers zurück.

 `{{user_login}}`
 Gibt den Login des Benutzers zurück.

 `{{user_nicename}}`
 Gibt den Slug des Benutzers zurück.

 `{{user_email}}`
 Gibt die E-Mail-Adresse des Benutzers zurück.

 `{{user_url}}`
 Gibt den Permalink des Benutzers zurück.

 `{{user_registered}}`
 Gibt das Registrierungsdatum des Benutzers zurück.

 `{{user_activation_key}}`
 Gibt den Aktivierungsschlüssel des Benutzers zurück.

 `{{user_status}}`
 Gibt den Status des Benutzers zurück.

 `{{user_level}}`
 Gibt die Berechtigungs Ebene des Benutzers zurück.

 `{{user_display_name}}`
 Gibt den öffentlichen Name des Benutzers zurück.

 `{{user_locale}}`
 Gibt das Sprache des Benutzers zurück.

 `{{user_rich_editing}}`
 Gibt den Visueller Editor Status des Benutzers zurück.

 `{{user_roles}}`
 Gibt die Rollen des Benutzers zurück.

 `{{user_syntax_highlighting}}`
 Gibt das Syntaxhervorhebung-Status des Benutzers zurück.

 `{{user_bookmark_posts}}`
 Gibt alle Lesezeichen-Beiträge des Benutzers zurück.

 `{{user_like_posts}}`
 Gibt alle Like-Beiträge des Benutzers zurück.

 `{{user_wc_recently_viewed_products}}`
 Gibt alle zuletzt angesehenen WooCommerce-Produkte des Benutzers zurück.

Mit dem folgenden Smart-Tag-Format, können Benutzer über ihre ID verknüpft werden.
`{{user_firstname/*}}`
* kann mit der Benutzer-ID oder dem aktuellen Schleifennamen ersetzt werden.

**Liste aller Globale Array Elemente**

 `{{GLOBALS}}`
 Verweist auf alle im globalen Bereich verfügbaren Variablen.

 `{{_SERVER}}`
 Informationen zum Server und zur Ausführungsumgebung.

 `{{_GET}}`
 HTTP GET Variablen.

 `{{_POST}}`
 HTTP POST Variablen.

 `{{_FILES}}`
 HTTP Datei Upload Variablen.

 `{{_COOKIE}}`
 HTTP Cookies.

 `{{_SESSION}}`
 Session Variablen.

 `{{_REQUEST}}`
 HTTP REQUEST Variablen.

 `{{_ENV}}`
 Umgebungsvariablen.

**Liste aller Standard Elemente**

 `{{today}}`
 Gibt das aktuelle Datum (2026-07-16) zurück.

 `{{this_weekday}}`
 Gibt den numerischer Tag (4), der aktuellen Woche zurück.

 `{{this_week}}`
 Gibt die Wochennummer (29), des aktuellen Jahres zurück (Woche beginnt am Montag).

 `{{this_day}}`
 Gibt die aktuelle Tageszahl (16), ohne führende Nullen zurück.

 `{{this_month}}`
 Gibt die aktuelle Monatszahl (7), ohne führende Nullen zurück.

 `{{this_year}}`
 Gibt das aktuelle Jahr (2026), als vierstellige Zahl zurück.

 `{{page_on_front}}`
 Gibt die **ID (10)** der Seite, welche als **Startseite (Startseite)** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.

 `{{page_for_posts}}`
 Gibt die **ID (0)** der Seite, welche als **Beitragsseite ()** [definiert](https://naturelhotels.com.weseo.dev/wp-admin/options-reading.php) wurde zurück.

 `{{count_posts}}`
 Zählt die Anzahl der Beiträge eines Beitragstyps.

 `{{current_language}}`
 Gibt die aktuelle Sprache am Frontend zurück.

 `{{default_language}}`
 Gibt die Standardsprache der Website zurück.

 `{{is_mobile}}`
 Überprüft, ob der aktuelle Browser auf einem mobilen Gerät (Smartphone) ausgeführt wird.

 `{{is_tablet}}`
 Überprüft, ob der aktuelle Browser auf einem Tablet läuft.

 `{{is_ios}}`
 Überprüft, ob der aktuelle Browser auf einer IOS Plattform läuft.

 `{{is_android}}`
 Überprüft, ob der aktuelle Browser auf einer Android OS Plattform läuft.

 `{{is_mobile_network}}`
 Überprüft, ob die aktuelle Verbindung über ein Mobilfunknetz läuft.

 `{{is_user_logged_in}}`
 Überprüft ob ein Benutzer eingeloggt ist.

 `{{is_404}}`
 Ermittelt, ob die Abfrage zu einer 404 geführt hat.

 `{{is_single}}`
 Legt fest, ob sich die Abfrage auf einen vorhandenen einzelnen Beitrag bezieht.

 `{{is_page}}`
 Bestimmt, ob sich die Abfrage auf eine bestehende Einzelseite bezieht.

 `{{is_singular}}`
 Bestimmt, ob sich die Abfrage auf einen vorhandenen einzelnen Beitrag eines beliebigen Inhaltstyps (post, attachment, page, custom post types) bezieht.

 `{{is_archive}}`
 Bestimmt, ob sich die Abfrage auf eine bestehende Archivseite bezieht.

 `{{is_post_type_archive}}`
 Bestimmt, ob sich die Abfrage auf eine bestehende Archivseite des Inhaltstyps bezieht.

 `{{queried_object_id}}`
 Gibt die ID des aktuell abgefragten Objekts zurück.

 `{{weather}}`
 Gibt den Wetter-Gruppenschlüssel (thunderstorm, drizzle, rain, snow, atmosphere, clear, clouds) zurück.

 `{{geolocate}}`
 Gibt ein bestimmtes Feld (continent, continentCode, country, countryCode, region, regionName, city, district, zip, lat, lon, timezone, offset, currency, isp, org, as, asname, reverse, mobile, proxy, hosting, query) der geolokalisierten IP-Adresse zurück.

 `{{loop_row_index}}`
 Gibt den aktuellen Schleifen-Index innerhalb einer Smart Template-Schleife zurück.

 `{{loop_row_count}}`
 Gibt die Anzahl aller Elemente innerhalb einer Smart Template Schleife zurück.

 `{{loop_row_first}}`
 Gibt zurück, ob sich das aktuelle Element am Ende der Smart Template-Schleife befindet.

 `{{loop_row_last}}`
 Gibt zurück, ob sich das aktuelle Element am Anfang der Smart Template-Schleife befindet.

 `{{loop_row_even}}`
 Gibt zurück, ob das aktuelle Element eine gerade Zahl der Smart Template-Schleife ist.

 `{{loop_row_odd}}`
 Gibt zurück, ob das aktuelle Element eine ungerade Zahl der Smart Template-Schleife ist.

 `{{acf_fc_layout}}`
 Gibt den Namen des aktuellen ACF-Layouts für den flexiblen Inhalt zurück.

**SmartTags als Variable**

 `{{$my_variable}}`
 Gibt den Wert einer Variable zurück.

 `{{$my_variable=blue}}`
 Setzt den Wert einer Variable.

 `{{$my_variable|int=1234}}`
 Setzt den Wert und den Typ einer Variable.

###### SmartTags in Schleifen verwenden

Folgender Shortcode generiert eine Schleife, welche alle Beiträge der Reihe nach abarbeitet.

```text
[wst_posts]
	{{post_title/wst_posts}}
[/wst_posts]
```

Hierfür muss zwingend der "Name des aktuellen Schleifen-Shortcodes" mit dem Platzhalter verwendet werden (siehe Beispiel).

###### ACF Beitrags Objekt Felder als verschachtelten Schleifen verwenden.

Folgendes Beispiel zeigt mehrere ACF Beitrags Objekt Felder als verschachtelte Schleifen.

```text
[wst_acf_post_object field='post_products']
	{{post_title/wst_acf_post_object}}
	[wst_acf_post_object_b field='product_posts' id='{{post_id/wst_acf_post_object}}']
		{{post_title/wst_acf_post_object_b}}
		{{post_title/{{post_parent/wst_acf_post_object_b}}}}
	[/wst_acf_post_object_b]
[/wst_acf_post_object]
```

###### ACF Wiederholungs Felder als **verschachtelten Schleifen** verwenden.

Folgendes Beispiel zeigt mehrere ACF Wiederholungs Felder als verschachtelte Schleifen.

```text
[wst_acf_repeater field='post_downloads']
	{{group_title/wst_acf_repeater}}
	[wst_acf_repeater_b field='downloads' id='{{row_id/wst_acf_repeater}}']
		{{title/wst_acf_repeater_b}}
		[wst_acf_repeater_c field='files' id='{{row_id/wst_acf_repeater_b}}']
			{{file/wst_acf_repeater_c}}
		[/wst_acf_repeater_c]
	[/wst_acf_repeater_b]
[/wst_acf_repeater]
```

###### Verschachtelte SmartTags

Das folgende Beispiel zeigt einen verschachtelten SmartTag.

```text
{{post_title/{{post_parent}}}}
```

###### Anhang-Metadaten

Das folgende Beispiel zeigt SmartTags für Metadaten von Anhängen.

```text
Alternativtext: {{_wp_attachment_image_alt/*}}
Titel: {{post_title/*}}
Beschriftung: {{post_excerpt/*}}
Beschreibung: {{post_content/*}}
Datei-URL: {{attachment_url/*}}
```

* kann mit der Anhang-ID oder dem aktuellen Schleifennamen ersetzt werden.

###### Spezielle Konvertierungs Befehle

Mit einem Konvertierungs-Befehl kann der Wert des Feldes gezielt umgewandelt werden.

```text
{{post_title|chars=10}}
{{post_excerpt|words=10}}
{{post_date|format=m}}
{{post_thumbnail|image_size=full}}
{{attachment_image_url|image_size=full}}
{{post_thumbnail|fields=ids}}
{{post_terms|taxonomy=category&count=1}}
{{user_bookmark_posts|bookmark=post&count=1}}
{{count_posts|post_type=post&post_status=publish&lang=en}}
{{post_language|field=display_name}}
{{acf_repeater_field|count}}
{{acf_post_date_field|format=j. F Y&modify=+1 day}}
{{geolocate|field=country}}
{{post_type|field=singular_label}}
{{post_title|count_words}}
{{post_title|strlen}}
{{post_title|htmlentities}}
{{post_content|strip_tags}}
{{post_menu_order|modulo=3}}
{{price|number_format&decimals=2}}
{{wki_hotel_property_code/{{wso_tax_company_hotel_id|field_type=term_meta/{{post_primary_term_id|taxonomy=wso_tax_company}}}}}}
```

###### SmartTags als Variable

```text
{{$my_variable|int={{post_id}}}}
{{$my_wysiwyg_content=[wst_acf field='wysiwyg_field' id='{{$my_variable}}']}}
```

**Optionale CMB2-Integration (Kategorien 5.25–5.30)**

Die folgenden sechs Kategorien sind vollständig im Plugin enthalten, werden jedoch nur bei aktivem CMB2 in der Admin-Hilfe registriert.

### 5.25 CMB2 Grundlage

> **Optionale Integration:** Diese Kategorie ist im Plugin vorhanden, wird von der Admin-Hilfe aber nur bei aktivem CMB2 registriert.

- **Interne ID:** `cmb2_general`
- **Kategoriefarbe:** `#5D67FF`
- **Einträge:** 4

#### [wst_cmb2]

- **Shortcode:** `wst_cmb2`

Der Standard Shortcode von CMB2 selber. Funktioniert nur für einfache textbasierte Werte.

**Grundsyntax**

```text
[wst_cmb2 field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format_value` — Ausgabe Format (formatiert oder Rohdaten) des Feldes. *(optional; Standard: `1`)*
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

#### [wst_cmb2_text]

- **Shortcode:** `wst_cmb2_text`

Gibt das CMB2 Text Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_text field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `words` — Beschneidet den Text auf eine bestimmte Anzahl von Wörtern. *(optional)*
- `chars` — Beschneidet den Text auf eine bestimmte Anzahl von Zeichen. *(optional)*

#### [wst_cmb2_number]

- **Shortcode:** `wst_cmb2_number`

Gibt das CMB2 Numerisch Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_number field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `decimals` — Genauigkeit der Anzahl der Dezimalstellen. *(optional; Standard: `0`)*

#### [wst_cmb2_url]

- **Shortcode:** `wst_cmb2_url`

Gibt das CMB2 URL Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_url field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `protocol` — Fügt der URL automatisch das Protokoll hinzu, falls es fehlt. *(optional; Standard: `1`)*

### 5.26 CMB2 Inhalt

> **Optionale Integration:** Diese Kategorie ist im Plugin vorhanden, wird von der Admin-Hilfe aber nur bei aktivem CMB2 registriert.

- **Interne ID:** `cmb2_content`
- **Kategoriefarbe:** `#5D67FF`
- **Einträge:** 8

#### [wst_cmb2_image]

- **Shortcode:** `wst_cmb2_image`

Gibt das CMB2 Bild Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_image field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `thumbnail`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `attr` — HTML Attribute des Bildes als Query String. *(optional)*
- `inline` — Das Bild inline als Data-URL (Base64-kodiert für Nicht-SVG-Formate) oder direkt als rohen SVG-Code einbetten. *(optional; Standard: `0`)*
- `srcset` — Berechnet die Bilder, welche in ein srcset-Attribut aufgenommen werden sollen. *(optional; Standard: `1`)*

#### [wst_cmb2_gallery_image]

- **Shortcode:** `wst_cmb2_gallery_image`

Gibt ein bestimmtes Bild aus dem Feld CMB2 Galerie zurück.

**Grundsyntax**

```text
[wst_cmb2_gallery_image field='field_name' image='1']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `image` — Die Nummer des Bildes (z.B.: 1, 2, first, last, usw.), das zurückgegeben werden soll. *(Pflicht; Standard: `1`)*
- `size` — Name der Bildgröße (z.B.: thumbnail, medium, full, usw.). *(optional; Standard: `thumbnail`)*
- `width` — Die Breite (in Pixel) des Elements. *(optional)*
- `height` — Die Höhe (in Pixel) des Elements. *(optional)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `attr` — HTML Attribute des Bildes als Query String. *(optional)*
- `inline` — Das Bild inline als Data-URL (Base64-kodiert für Nicht-SVG-Formate) oder direkt als rohen SVG-Code einbetten. *(optional)*
- `srcset` — Berechnet die Bilder, welche in ein srcset-Attribut aufgenommen werden sollen. *(optional; Standard: `1`)*

#### [wst_cmb2_file]

- **Shortcode:** `wst_cmb2_file`

Gibt das CMB2 Datei Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_file field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `title` — Das title Attribut des Elements. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `target` — Die Zielfensterbasis des Elements: `_self | _blank | _parent | _top`. *(optional; Standard: `_self`)*
- `url` — Gibt nur die URL des Elements zurück. *(optional; Standard: `0`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `url` — Gibt die URL der Datei zurück.
  - `title` — Gibt den Titel der Datei zurück.
  - `size` — Gibt die Größe der Datei zurück.
  - `type` — Gibt den Typ der Datei zurück.

#### [wst_cmb2_file_title]

- **Shortcode:** `wst_cmb2_file_title`

Gibt die CMB2 Datei Titel-Informationen zurück.

**Grundsyntax**

```text
[wst_cmb2_file_title field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

#### [wst_cmb2_file_size]

- **Shortcode:** `wst_cmb2_file_size`

Gibt die CMB2 Datei Größeninformationen zurück.

**Grundsyntax**

```text
[wst_cmb2_file_size field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

#### [wst_cmb2_file_type]

- **Shortcode:** `wst_cmb2_file_type`

Gibt die CMB2 Datei Typinformationen zurück.

**Grundsyntax**

```text
[wst_cmb2_file_type field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

#### [wst_cmb2_wysiwyg]

- **Shortcode:** `wst_cmb2_wysiwyg`

Gibt das CMB2 Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_wysiwyg field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `words` — Beschneidet den Text auf eine bestimmte Anzahl von Wörtern. *(optional)*
- `chars` — Beschneidet den Text auf eine bestimmte Anzahl von Zeichen. *(optional)*
- `more` — Was angehängt werden soll, wenn der String gekürzt wird. *(optional; Standard: `…`)*
- `strip_tags` — Alle HTML-Tags, einschließlich Script und Style, werden entfernt. *(optional)*
- `read_more` — Der Typ des Read More-Tags. *(optional; Standard: `wp`)*
  - `wp` — Gibt den Standard WordPress Read More-Tag zurück.
  - `expandable` — Gibt ein erweiterbares Read More-Tag zurück.
- `read_more_icon` — Das mehr lesen Link-Icon. *(optional; Standard: `+`)*
- `read_more_text` — Der mehr lesen Linktext. *(optional; Standard: `Weiterlesen`)*
- `read_less_icon` — Das weniger lesen Link-Icon. *(optional; Standard: `−`)*
- `read_less_text` — Der weniger lesen Linktext. *(optional; Standard: `Weiterlesen`)*
- `read_more_link_position` — Die Position des "Weiterlesen"-Links. *(optional; Standard: `bottom`)*

#### [wst_cmb2_gallery]

- **Shortcode:** `wst_cmb2_gallery`

Gibt das CMB2 Feld als Standard WordPress Galerie zurück.

**Grundsyntax**

```text
[wst_cmb2_gallery field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

**Zusätzliche Informationen**

Es können alle Parameter des [WordPress Galerie Shortcodes](https://codex.wordpress.org/Gallery_Shortcode) verwendet werden.

### 5.27 CMB2 Auswahl

> **Optionale Integration:** Diese Kategorie ist im Plugin vorhanden, wird von der Admin-Hilfe aber nur bei aktivem CMB2 registriert.

- **Interne ID:** `cmb2_selection`
- **Kategoriefarbe:** `#5D67FF`
- **Einträge:** 4

#### [wst_cmb2_select]

- **Shortcode:** `wst_cmb2_select`

Gibt das CMB2 Auswahl Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_select field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `values` — Gibt die Werte des Elements zurück.
  - `labels` — Gibt die Beschriftungen des Elements zurück.
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

**Zusätzliche Informationen**

###### Benutzerdefiniertes HTML

Das folgende Beispiel generiert ein benutzerdefiniertes HTML aus dem ausgewählten Feld.

```text
[wst_foreach list='{{cmb2_select_field|fields=raw}}']
	Key: {{key/wst_foreach}} - Value:{{value/wst_foreach}}
[/wst_foreach]
```

#### [wst_cmb2_radio]

- **Shortcode:** `wst_cmb2_radio`

Gibt das CMB2 Radiobutton Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_radio field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `values` — Gibt die Werte des Elements zurück.
  - `labels` — Gibt die Beschriftungen des Elements zurück.
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

**Zusätzliche Informationen**

###### Benutzerdefiniertes HTML

Das folgende Beispiel generiert ein benutzerdefiniertes HTML aus dem ausgewählten Feld.

```text
[wst_foreach list='{{cmb2_radio_field|fields=raw}}']
	Key: {{key/wst_foreach}} - Value:{{value/wst_foreach}}
[/wst_foreach]
```

#### [wst_cmb2_checkbox]

- **Shortcode:** `wst_cmb2_checkbox`

Gibt das CMB2 Checkbox Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_checkbox field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `values` — Gibt die Werte des Elements zurück.
  - `labels` — Gibt die Beschriftungen des Elements zurück.
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

**Zusätzliche Informationen**

###### Benutzerdefiniertes HTML

Das folgende Beispiel generiert ein benutzerdefiniertes HTML aus dem ausgewählten Feld.

```text
[wst_foreach list='{{cmb2_checkbox_field|fields=raw}}']
	Key: {{key/wst_foreach}} - Value:{{value/wst_foreach}}
[/wst_foreach]
```

#### [wst_cmb2_checkbox_group]

- **Shortcode:** `wst_cmb2_checkbox_group`

Gibt das CMB2 Checkbox Gruppe Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_checkbox_group field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `values` — Gibt die Werte des Elements zurück.
  - `labels` — Gibt die Beschriftungen des Elements zurück.
- `list_separator` — Das Trennzeichen zum Verbinden von Array - Elementen. *(optional; Standard: `,`)*

**Zusätzliche Informationen**

###### Benutzerdefiniertes HTML

Das folgende Beispiel generiert ein benutzerdefiniertes HTML aus dem ausgewählten Feld.

```text
[wst_foreach list='{{cmb2_checkbox_group_field|fields=raw}}']
	Key: {{key/wst_foreach}} - Value:{{value/wst_foreach}}
[/wst_foreach]
```

### 5.28 CMB2 Relation

> **Optionale Integration:** Diese Kategorie ist im Plugin vorhanden, wird von der Admin-Hilfe aber nur bei aktivem CMB2 registriert.

- **Interne ID:** `cmb2_relation`
- **Kategoriefarbe:** `#5D67FF`
- **Einträge:** 2

#### [wst_cmb2_page_link]

- **Shortcode:** `wst_cmb2_page_link`

Gibt das CMB2 Page Link Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_page_link field='field_name' output='html']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `title` — Das title Attribut des Elements. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `target` — Die Zielfensterbasis des Elements: `_self | _blank | _parent | _top`. *(optional; Standard: `_self`)*
- `output` — Der Ausgabetyp des Elements. *(Pflicht; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `title` — Gibt den Titel des Elements zurück.
  - `url` — Gibt die URL des Elements zurück.

#### [wst_cmb2_post_object]

- **Shortcode:** `wst_cmb2_post_object`

Gibt das CMB2 Post Object Feld als Schleife zurück.

**Grundsyntax**

```text
[wst_cmb2_post_object field='field_name']row_content[/wst_cmb2_post_object]
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `order` — Bestimmt die aufsteigende `ASC` oder absteigende `DESC` Reihenfolge der Elemente. *(optional; Standard: `DESC`)*
- `orderby` — Name des [Parameters](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters) nach dem sortiert werden soll. *(optional; Standard: `post__in`)*

**Zusätzliche Informationen**

###### CMB2 Beitrags Objekt Feld als Schleife verwenden

Der folgender Shortcode verwandelt das Feld CMB2 post object in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_cmb2_post_object field='field_name']
	[wst_post_title id='{{post_id/wst_cmb2_post_object}}']
[/wst_cmb2_post_object]
```

###### CMB2 Beitrags Objekt Felder als verschachtelten Schleifen verwenden.

Folgendes Beispiel zeigt mehrere CMB2 Beitrags-Objekt-Felder als verschachtelte Schleifen.

```text
[wst_cmb2_post_object field='post_products']
	[wst_post_title id='{{post_id/wst_cmb2_post_object}}']
	[wst_cmb2_post_object_b field='product_posts' id='{{post_id/wst_cmb2_post_object}}']
		[wst_post_title id='{{post_id/wst_cmb2_post_object_b}}']
	[/wst_cmb2_post_object_b]
[/wst_cmb2_post_object]
```

Jeder Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{post_id/loop_name}}` angegeben werden (siehe Beispiel).

### 5.29 CMB2 Erweitert

> **Optionale Integration:** Diese Kategorie ist im Plugin vorhanden, wird von der Admin-Hilfe aber nur bei aktivem CMB2 registriert.

- **Interne ID:** `cmb2_advanced`
- **Kategoriefarbe:** `#5D67FF`
- **Einträge:** 5

#### [wst_cmb2_date]

- **Shortcode:** `wst_cmb2_date`

Gibt das CMB2 Datumspicker Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_date field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `j. F Y`)*
- `modify` — Die [Modifikation](http://php.net/manual/de/datetime.formats.relative.php) des auszugebenden Datums. *(optional)*

#### [wst_cmb2_date_time]

- **Shortcode:** `wst_cmb2_date_time`

Gibt das CMB2 Datums- und Zeitauswahl Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_date_time field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `j. F Y G:i`)*
- `modify` — Die [Modifikation](http://php.net/manual/de/datetime.formats.relative.php) des auszugebenden Datums. *(optional)*

#### [wst_cmb2_time]

- **Shortcode:** `wst_cmb2_time`

Gibt das CMB2 Zeitpicker Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_time field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `format` — Die [Formatierung](http://php.net/manual/de/function.date.php) des auszugebenden Datums. *(optional; Standard: `G:i`)*
- `modify` — Die [Modifikation](http://php.net/manual/de/datetime.formats.relative.php) des auszugebenden Datums. *(optional)*

#### [wst_cmb2_color]

- **Shortcode:** `wst_cmb2_color`

Gibt das CMB2 Farbpicker Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_color field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `output` — Der Ausgabe Typ des Farbwertes. *(optional; Standard: `rgb`)*
  - `rgb` — Gibt die RGB-Farbwerte zurück.
  - `rgba` — Gibt die RGB-Farbwerte mit einem Alphakanal zurück.
  - `hex` — Gibt die hexadezimalen Farbwerte zurück.
  - `hsl` — Gibt die RGB-Farbwerte zurück.
  - `hsla` — Gibt die RGB-Farbwerte mit einem Alphakanal zurück.
  - `r` — Gibt die Intensität der Farbe Rot zurück.
  - `g` — Gibt die Intensität der Farbe Grün zurück.
  - `b` — Gibt die Intensität der Farbe Blau zurück.
  - `h` — Gibt den Farbton einer Farbe zurück, der den Grad auf dem Farbkreis von 0 bis 360 angibt.
  - `s` — Gibt die Sättigung einer Farbe zurück, die als Intensität einer Farbe beschrieben wird.
  - `l` — Gibt die Helligkeit einer Farbe zurück, die beschreibt, wie viel Licht die Farbe haben sollte. Dabei bedeutet 0% kein Licht (dunkel), 50% bedeutet 50% Licht (weder dunkel noch hell) und 100% bedeutet volles Licht.
- `alpha` — Der Alphakanalwert (0 - 1) der Farbe. *(optional)*

#### [wst_cmb2_phone_number]

- **Shortcode:** `wst_cmb2_phone_number`

Gibt das CMB2 Extended Phone Number Feld zurück.

**Grundsyntax**

```text
[wst_cmb2_phone_number field='field_name']
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*
- `title` — Das title Attribut des Elements. *(optional)*
- `class` — Das class Attribut des Elements. *(optional)*
- `style` — Das style Attribut des Elements. *(optional)*
- `aria_label` — Das aria-label Attribut des Elements. *(optional)*
- `format` — Das Format der Telefonnummer. *(optional; Standard: `international`)*
  - `e164` — E.164 (z. B. +41446681800).
  - `national` — National (z. B. 044 668 18 00).
  - `international` — International (z. B. +41 44 668 18 00).
- `output` — Der Ausgabetyp des Elements. *(optional; Standard: `html`)*
  - `html` — Gibt den HTML-Tag des Elements zurück.
  - `title` — Gibt den Titel des Elements zurück.
  - `url` — Gibt die URL des Elements zurück.

### 5.30 CMB2 Layout

> **Optionale Integration:** Diese Kategorie ist im Plugin vorhanden, wird von der Admin-Hilfe aber nur bei aktivem CMB2 registriert.

- **Interne ID:** `cmb2_layout`
- **Kategoriefarbe:** `#5D67FF`
- **Einträge:** 1

#### [wst_cmb2_repeater]

- **Shortcode:** `wst_cmb2_repeater`

Gibt das CMB2 Wiederholung Feld als Schleife zurück.

**Grundsyntax**

```text
[wst_cmb2_repeater field='field_name']row_content[/wst_cmb2_repeater]
```

**Parameter und Optionen**

- `field` — Der CMB2 Feld-Name des Metafeldes. *(Pflicht; Standard: `field_name`)*

**Zusätzliche Informationen**

###### CMB2 Wiederholungs Feld als Schleife verwenden

Der folgender Shortcode verwandelt das Feld CMB2 repeater in eine Schleife, welche alle ausgewählen Elemente der Reihe nach abarbeitet.

```text
[wst_cmb2_repeater field='field_name']
	[wst_cmb2_number field='price' id='{{row_id/wst_cmb2_repeater}}']
[/wst_cmb2_repeater]
```

###### CMB2 Wiederholungs Felder als **verschachtelten Schleifen** verwenden.

Folgendes Beispiel zeigt mehrere CMB2 Wiederholungs-Felder als verschachtelte Schleifen.

```text
[wst_cmb2_repeater field='post_downloads']
	[wst_cmb2 field='category_title' id='{{row_id/wst_cmb2_repeater}}']
	[wst_cmb2_repeater_b field='downloads' id='{{row_id/wst_cmb2_repeater}}']
		[wst_cmb2 field='title' id='{{row_id/wst_cmb2_repeater_b}}']
		[wst_cmb2_repeater_c field='files' id='{{row_id/wst_cmb2_repeater_b}}']
			[wst_cmb2 field='file' id='{{row_id/wst_cmb2_repeater_c}}']
		[/wst_cmb2_repeater_c]
	[/wst_cmb2_repeater_b]
[/wst_cmb2_repeater]
```

Jeder CMB2 Smart Template Shortcode kann im Inhalt der Schleife verwendet werden.

Hierfür muss zwingend der Parameter `id` mit dem Smart-Tag `{{row_id/loop_name}}` angegeben werden (siehe Beispiel).

---

## 6. PHP-Helper-Funktionen

### Kernfunktionen

```php
// Smart-Template-Instanz
$wst = WST();

// SmartTags auflösen
$output = wst_do_smarttag( $content );

// Verschachtelte Shortcodes auflösen
$output = wst_do_shortcode( $content );

// Logging
wst_add_log( 'Nachricht', 'info', 'source' );

// Detailliertes Logging
wst_add_kint_log( $variable );
```

### Formatierung

```php
wst_string_to_bool( 'yes' ); // true
wst_bool_to_string( true ); // 'yes'
wst_string_to_array( 'a,b,c' ); // ['a', 'b', 'c']
wst_trim_string( $text, 200, '...' );
wst_make_phone_clickable( '+43 123 456' );
```

## 7. Plugin-Integrationen

- **ACF Pro:** Feldtypen, Repeater, Flexible Content und Google Maps.
- **ACF Extended:** Performance-Modus und erweiterte Feldtypen.
- **CMB2:** Feldtypen und Repeater.
- **WP Grid Builder:** Eigene Blöcke, Grids und Facettenfilterung.
- **WooCommerce:** Produkt-, Warenkorb- und Shop-Ausgaben.
- **Polylang:** Sprachumschalter und Übersetzungen.
- **WPML:** Sprachumschalter und Übersetzungen.
- **BeTheme:** Muffin-Builder-Integration.
- **Elementor:** Template-Unterstützung.
- **WP Rocket:** Cache-, Lazyload- und Delay-JavaScript-Kompatibilität.
- **Yoast SEO:** SEO-Metadaten und Indexierungsintegration.

---

## 8. Smart Template Builder

Das Add-on `weseo-smart-template-builder` erweitert Smart Template und ACF um einen Flexible-Content-Section-Builder. Es registriert ACF-Feldgruppen, rendert die Flexible-Content-Zeilen über `wst_acf_flexible_content` und `wst_include`, ergänzt Seiteninhalte und stellt eine direkte Frontend-Bearbeitung bereit.

### 8.1 Voraussetzungen und Initialisierung

- Benötigt das Basis-Plugin **WESEO Smart Template**.
- Benötigt **Advanced Custom Fields**, praktisch ACF Pro für Flexible Content.
- Initialisierung erfolgt über den Hook `smart_template_loaded`.
- Die Builder-Instanz steht unter `WST()->builder` zur Verfügung.
- Plugin-Konstanten: `WST_BUILDER_ABSPATH`, `WST_BUILDER_PLUGIN_BASENAME`, `WST_BUILDER_PLUGIN_URL`, `WST_BUILDER_VERSION`.

### 8.2 Rendering-Ablauf

1. Für Seiten mit einem nichtleeren ACF-Feld `flexible_content` hängt der Builder das Template `flexible-content.php` an den Seiteninhalt an.
2. Das Standardtemplate durchläuft das Flexible-Content-Feld mit `[wst_acf_flexible_content field="flexible_content"]`.
3. Jede Layout-ID wird über `[wst_include ... layout="..."]` einem Section-Template zugeordnet.
4. Während ein Smart Template gerendert wird, setzt der Builder einen internen Zustand. Dadurch werden Rekursionen verhindert und Feld-Shortcodes nicht vorzeitig aufgelöst.
5. Nach dem Rendern wird der Zustand wieder zurückgesetzt.

### 8.3 Template-Auflösung und Overrides

- Standard-Templatepfad: `smart-template-builder/`.
- Zuerst wird im aktiven Child-/Parent-Theme nach `smart-template-builder/<template>` gesucht.
- Falls dort nichts gefunden wird, fällt der Loader auf `weseo-smart-template-builder/templates/<template>` zurück.
- Der Pfad ist über den Filter `smart_template_builder_template_path` anpassbar.
- Theme-spezifische Builder-Funktionen können in `smart-template-builder/functions.php` liegen und werden für aktive Themes geladen.

### 8.4 Standard-Layouts im Flexible-Content-Template

- `layout_text_one_column` → `sections/text-columns-one.php`
- `layout_text_two_column` → `sections/text-columns-two.php`
- `layout_text_three_column` → `sections/text-columns-three.php`
- `layout_text_four_column` → `sections/text-columns-four.php`
- `layout_text_left_img_right` → `sections/text-images-one.php`
- `layout_text_left_img_right_fullwidth` → `sections/text-image-full-with.php`
- `layout_text_left_2_img_right` → `sections/text-images-two.php`
- `layout_text_left_3_img_right` → `sections/text-images-three.php`
- `layout_img_left_text_right` → `sections/text-images-one.php`
- `layout_img_left_text_right_fullwidth` → `sections/text-image-full-with.php`
- `layout_2_img_left_text_right` → `sections/text-images-two.php`
- `layout_3_img_left_text_right` → `sections/text-images-three.php`
- `layout_intro` → `sections/intro.php`
- `layout_breaker` → `sections/breaker.php`
- `layout_slider` → `sections/slider.php`
- `layout_grid` → `sections/grid.php`
- `layout_image_gallery` → `sections/gallery.php`
- `layout_code_editor` → `sections/code-editor.php`
- `layout_google_map` → `sections/google-map.php`
- `layout_counter` → `sections/counter.php`
- `layout_image_boxes` → `sections/image-boxes.php`
- `layout_tabs` → `sections/tabs.php`
- `layout_accordion` → `sections/accordion.php`
- `layout_multi_column` → `sections/multi-columns.php`
- `layout_form` → `sections/form.php`
- `layout_animated_column` → `sections/animated-columns.php`
- `layout_template` → dynamisches Smart Template über die Feldreferenz `template`

### 8.5 Builder-Einstellungen

Die Einstellungen erscheinen als zusätzlicher Reiter **Page Builder** in den Smart-Template-Einstellungen.

- `wst_builder_polylang_acf_translations_field` — globaler Standard für das Polylang-ACF-Übersetzungsfeld. Optionen: `none`, `ignore`, `copy_once` (Standard), `translate`, `translate_once`, `sync`.
- `wst_builder_enable_section_edit` — aktiviert den Button **Edit Section** im Frontend.
- `wst_builder_section_edit_button_position` — Position `center` (Standard), `left` oder `right`.
- `wst_builder_section_edit_color` — Farbe für Button, Highlight und Admin-Markierung; Standard `#2271b1`.

### 8.6 Frontend-Bearbeitung von Sections

- Wird nur für Benutzer mit `edit_posts` und aktivierter Option geladen.
- Unterstützte Frontend-Container: `div.section.mcb-section` und `section.wso-section`.
- Beim Hover wird der Container markiert und ein Edit-Link eingeblendet.
- Der Link öffnet den Beitragseditor in einem neuen Tab und ergänzt `wst-builder-edit-section=<Index>`.
- Das Admin-Script sucht die entsprechende sichtbare ACF-Flexible-Content-Zeile, scrollt zu ihr, klappt sie auf und markiert sie in der konfigurierten Farbe.

### 8.7 ACF-Verhalten

- ACF Extended Performance Mode wird beim Hook `acfe/init` auf `hybrid` gesetzt.
- Leere Unterfelder von Flexible Content werden nicht gespeichert.
- Nicht gesetzte `true_false`- und `acfe_hidden`-Werte mit `0` werden entfernt.
- Werte, die exakt dem ACF-Standardwert entsprechen, werden nicht gespeichert.
- Das Flexible-Content-Hauptfeld selbst wird von dieser Bereinigung ausgenommen.
- Ein eigener relationaler ACF-Feldtyp **WP Grid Builder** kann Grids nach Quelle (`post_type`, `term`, `user`) auswählen, unterstützt AJAX-Suche, Nullwerte und Mehrfachauswahl und speichert Grid-IDs.

### 8.8 Registrierte ACF-Bausteine

**Elemente**

- Button
- Highlight Button
- Bildausrichtung
- Bildbox
- 1 Bild
- 2 Bilder
- 3 Bilder
- Bild mobil
- Layout
- Layout mehrspaltig
- Klasse
- Inhalt
- Darstellung
- Grid-Optionen
- Link
- Medien
- Video
- Formular

**Sections**

- Accordion
- Breaker
- Code Editor
- Counter
- Gallery
- Google Map
- Grid
- Image Boxes
- Intro
- Multi Column Layout
- Slider
- Smart Template
- Tabs
- Text einspaltig
- Text zweispaltig
- Text dreispaltig
- Text vierspaltig
- Text/Bild mit 1 Bild
- Text/Bild mit 2 Bildern
- Text/Bild mit 3 Bildern
- Text/Bild volle Breite
- Formular
- Animated Columns

**Flexible-Content-Gruppen**

- Flexible Content
- Flexible Content Flexblocks
- Flexible Content Multi Columns

### 8.9 Polylang-Verhalten

- Der konfigurierte Standardwert wird auf das ACF-Feld `translations` beziehungsweise die ACF-Eigenschaft `translations` angewendet.
- Beim Anlegen einer Übersetzung werden IDs aus ACF-Feldern der Typen `image`, `file`, `post_object`, `gallery` und `relationship` nach Möglichkeit auf die Zielsprachobjekte umgeschrieben.
- Im Frontend werden diese IDs ebenfalls auf die aktuelle Sprachversion aufgelöst.

### 8.10 Öffentliche Builder-Funktionen

- `wst_builder_set_state( $post = null, $value = true )` — Setzt den Builder-Zustand für einen Beitrag.
- `wst_builder_get_state( $post = null )` — Liest den Builder-Zustand eines Beitrags.
- `wst_builder_get_active_state()` — Liest den aktuell aktiven Builder-Zustand.
- `wst_builder_reset_builder( $post = null )` — Entfernt den Builder-Zustand.
- `wst_builder_has_shortcode( $content, $tag = "" )` — Prüft Inhalt auf einen konkreten oder beliebigen registrierten Shortcode.
- `wst_builder_smart_template_get_content( $tpl_id = null )` — Rendert den Flexible-Content-Inhalt eines Smart Templates.
- `wst_builder_normalize_whitespace_in_html_attributes( $html )` — Normalisiert Zeilenumbrüche und doppelte Leerzeichen in HTML-Attributen.
- `wst_builder_the_content()` — Gibt den gerenderten Builder-Inhalt direkt aus.
- `wst_builder_get_the_content( $post = null )` — Liefert den gerenderten Builder-Inhalt als String.

### 8.11 Hinweis zum installierten Builder-Paket

> Beim WP-CLI-Bootstrap dieser Installation meldet Version 1.6.2, dass `weseo-smart-template-builder/smart-template-builder/functions.php` fehlt. Der Include ist im Builder fest vorgesehen und erzeugt aktuell eine nicht-fatale PHP-Warnung. Theme-eigene Builder-Funktionen unter `wp-content/themes/<theme>/smart-template-builder/functions.php` werden davon unabhängig geladen.

---

## 9. Support

- **WESEO**
- E-Mail: [office@weseo.at](mailto:office@weseo.at)
- Website: [www.weseo.at](https://www.weseo.at)

## 10. Quellen dieses Exports

- `wp-content/plugins/weseo-smart-template/includes/admin/views/html-admin-help.php`
- `wp-content/plugins/weseo-smart-template/includes/admin/wst-help-functions.php`
- `wp-content/plugins/weseo-smart-template/includes/admin/help/class-wst-help-*.php`
- `wp-content/plugins/weseo-smart-template/README.md`
- `wp-content/plugins/weseo-smart-template/CHANGELOG.md`
- `wp-content/plugins/weseo-smart-template-builder/` (Version 1.6.2)

---

## 11. Vollständiger Smart-Template-Changelog

All notable changes to this project will be documented in this file.
## 6.19.4
* Add - Schutz für Shortcodes in HTML-Attributen beim Speichern auf den nativen WP Grid Builder "Custom HTML" Block (Feld "raw_content") ausgeweitet (Filter "wst_wpgb_protected_code_fields")
* Add - PHP Function "wpgb_wst_normalize_shortcode" und "wpgb_wst_has_attribute_shortcode"
* Fix - Shortcodes in HTML-Attributen wurden seit WordPress 7 falsch gerendert, da wp_kses_post() (neues wp_kses_hair über die HTML-API) die Anführungszeichen vor do_shortcode() zu &apos; kodierte; die Auflösung erfolgt nun vor wp_kses_post()
* Fix - Bereits beschädigt gespeicherte Shortcode-Attribute (&apos; usw.) werden beim Rendern automatisch wiederhergestellt
* Dev - Code Optimierungen und Formatierungen

## 6.19.3
* Add - Parameter "read_more" beim Shortcode "wst_post_content" und "wst_post_excerpt"
* Dev - Formatting PHP Function "wst_do_more_tag" entfernt; Unterscheidung wp/expandable erfolgt nun direkt in den Shortcodes
* Dev - Formatting PHP Function "wst_do_expandable_more_tag" benötigt keinen Post-Bezug mehr
* Dev - Formatting PHP Function "wst_do_wp_more_tag" gibt bei fehlendem Post den unveränderten Content statt eines leeren Strings zurück
* Dev - Code Optimierungen und Formatierungen
* Fix - Bookmark Shortcode "redirect" Parameter wird nun auf Existenz des Beitrags geprüft
* Fix - Bookmark PHP Function "wst_update_cookie_bookmarks" gibt nun zuverlässig "true" zurück

## 6.19.2
* Fix - Shortcodes in HTML-Attributen wurden beim WP Grid Builder "Code Editor" Block (Feld "wst_code_editor_content") beim Speichern durch wp_kses_post beschädigt (einfache Anführungszeichen wurden zu &apos; kodiert)
* Dev - Code Optimierungen und Formatierungen

## 6.19.1
* Add - readme.txt implementiert
* Dev - Einstellungen -> Aktualisierungen -> Bitbucket Zugangsdaten auf API-Token umgestellt
* Dev - Bitbucket Auto-Update Authentifizierung von OAuth (Consumer Key/Secret) auf API-Token (Scope "read:repository:bitbucket") umgestellt
* Dev - Vendor Library "plugin-update-checker" auf v5.7 aktualisiert
* Dev - Übersetzungen aktualisiert
* Dev - Code Optimierungen und Formatierungen

## 6.19.0
* Add - Komplett überarbeitete Hilfeseite
* Add - Hreflang "x-default" Attribut bei allen übersetzten Seiten
* Add - Loop PHP Function "wst_reset_postdata"
* Add - ACF PHP Function "wst_acf_is_field_group_edit_screen", ""
* Add - Einstellungen -> ACF -> Option "Feldname kopieren"
* Add - Einstellungen -> Wartungsmodus -> Begriffe einbeziehen/ausschließen
* Add - Parameter "effect" bei allen wst_swiper Shortcodes
* Add - Parameter "grid_tablet", "grid_mobile", "ajax_button" beim Shortcode "wst_wpgb_grid"
* Add - Parameter "format_value" beim Shortcode "wst_acf_wysiwyg"
* Add - Einstellungen -> Integration -> E-Mail Zustellung
* Add - Einstellungen -> Integration -> E-Mail-Beschränkung
* Add - Skeleton Loader Templates
* Add - wst-util.js Objekt "wst.VIEWPORT_IS_MOBILE", "wst.VIEWPORT_IS_TABLET" und Funktion "wst.isValidURL"
* Add - Core PHP Function "wst_str_ends_with", "wst_add_details_log", "wst_add_email_log"
* Add - ACF PHP Function "wst_acf_is_field_group_edit_screen"
* Add - Condition PHP Function "wst_is_admin_bar_email_info_enabled", "wst_is_postman_smtp_active", "wst_is_mailjet_active", "wst_is_email_logging_enabled", "wst_current_user_can_install_and_activate_plugins"
* Add - Formatting PHP Function "wst_nl2br"
* Add - Post PHP Function "wst_has_post_term"
* Add - Template PHP Function "wst_hide", "wst_wpgb_get_device_grid_id"
* Add - Theme PHP Function "wst_get_mobile_breakpoint", "wst_get_tablet_breakpoint"
* Add - Translation PHP Function "wst_i18n_get_term_id", "wst_pll_get_taxonomies"
* Add - WooCommerce SmartTag "wc_product_price_excluding_tax", "wc_product_price_including_tax"
* Add - Post Passwort Template Integration
* Dev - Erforderliche PHP-Version auf 8.0 erhöht
* Dev - Warnungen wegen veralteter Funktionen in PHP 8.1 bei Null-Parametern in internen Funktionen wurden behoben
* Dev - Added null-safe access for $wp_scripts/$wp_styles registered objects
* Dev - Composer-Abhängigkeiten aktualisiert
* Dev - PHPDoc Dokumentation für 32 undokumentierte PHP-Funktionen hinzugefügt
* Dev - Frontend JavaScript Dateien: $suffix Variable für .min.js Unterstützung hinzugefügt
* Dev - Minifizierte JavaScript Dateien (.min.js) für alle Frontend-Scripts erstellt
* Dev - Code Optimierungen und Formatierungen
* Dev - Übersetzungen aktualisiert
* Dev - Hilfe aktualisiert
* A11y - HTML Optimierungen
* A11y - Map Buttons: aria-label und aria-hidden für Icons hinzugefügt
* A11y - Map Inputs: Labels für Screenreader und aria-label hinzugefügt
* A11y - Map Select: Label und aria-label für Zielauswahl hinzugefügt
* A11y - Swiper Progress: aria-hidden und focusable Attribute für SVG und Bars hinzugefügt
* A11y - Links mit target="_blank": rel="noopener noreferrer" hinzugefügt
* A11y - Admin-Bar Search: role="search" und verbessertes Label hinzugefügt
* A11y - Bookmark Buttons: aria-hidden für Icons, role="button" und aria-pressed hinzugefügt (PHP + JS)
* A11y - Help-Tip: tabindex, role="tooltip" und aria-label hinzugefügt
* A11y - Back-Link: Screen-Reader-Text für das Symbol hinzugefügt
* A11y - Login-Formular: aria-label, required und aria-required Attribute hinzugefügt
* A11y - Registrierungs-Formular: aria-label, required und aria-required Attribute hinzugefügt
* Fix - ACF Extended Performance Mode Handling
* Fix - Bookmark Redirect Page

## 6.18.1
* Dev - Code Optimierungen und Formatierungen
* Dev - Neue Remote PHP Function "wst_search_attachment_id"
* Dev - Überarbeitung der Condition PHP Function "wst_is_valid_url"
* Fix - Rückgabewert bei der PHP Function "wst_get_page_id"

## 6.18.0
* Add - Allgemein Shortcode "wst_ajax_container"
* Add - Command Shortcode "wst_implode"
* Add - Translation PHP Function "wst_i18n_get_page_id"
* Add - Page PHP Function "wst_search_page_id"
* Add - Formatting PHP Function "wst_clean_text"
* Add - Post PHP Function "wst_post_do_shortcode"
* Add - Skeleton loader Template "content"
* Add - Parameter "scheme" bei der Funktion "wst_is_valid_url"
* Dev - Code Optimierungen und Formatierungen
* Dev - Hilfe aktualisiert
* Dev - Übersetzungen aktualisiert
* Fix - Falscher Rückgabewert bei der PHP-Funktion "wst_get_assigned_page_tpl_id"

## 6.17.1
* Fix - Fancybox Swiper Autoplay Funktion

## 6.17.0
* Add - Smart Template Admin Suche inkludiert das Code Editor Feld
* Add - Core PHP Function "wst_get_free_email_domains", "wst_get_disposable_email_domains"
* Add - Helper PHP Function "wst_count", "wst_get_current_url"
* Add - Media PHP Function "wst_rename_attachment"
* Add - Condition PHP Function "wst_is_email", "wst_is_free_email", "wst_is_disposable_email", "wst_wpseo_is_post_indexable", "wst_is_url"
* Add - Post PHP Function "wst_post_type_is", "wst_post_status_is", "wst_parse_post_id_list"
* Add - WordPress Filter "wst_the_content"
* Dev - Optimierung Remote PHP Function "wst_get_attachment_id_from_url"
* Dev - Code Optimierungen und Formatierungen
* Fix - Fehlerhaftes JSON in HTML Attributen beim Shortcode "wst_include"
* Fix - Änderung des reservierten Parameters "context" auf "group" beim Shortcode "wst_i18n_string"
* Fix - Performance bei der Suche nach einem Attachment über die URL
* Fix - JavaScript Objekt Zuweisungen

## 6.16.0
* Add - Admin Menü Settings "Theme-Datei-Editor", "Plugin-Datei-Editor"
* Add - ACF PHP Function "wst_get_acf_date_field_format_list", "wst_acf_get_date_field_format"
* Add - Parameter "context" bei allen Shortcodes
* Dev - Code Optimierungen und Formatierungen
* Dev - CMB2 Code Optimierungen
* Dev - Hilfe aktualisiert
* Dev - Übersetzungen aktualisiert
* Fix - Shortcode "wst_language_switcher" Dropdown ID Konflikt
* Fix - ACF Condition Datums Wert Ermittlung
* Fix - Falscher Rückgabewert bei der PHP Function "wst_i18n_get_languages"
* Fix - Performance bei der Suche nach einem Attachment über die URL

## 6.15.0
* Add - Admin Bar Server Netzwerk Informationen
* Add - Locale Switcher mittels GET Parameter deaktivieren
* Add - CMB2 PHP Function "wst_cmb2_get_date"
* Add - Remote PHP Function "wst_get_server_network_info"
* Add - Condition PHP Function "wst_is_admin_bar_server_info_enabled", "wst_is_cloudflare"
* Add - Date PHP Functions "wst-date-functions.php"
* Add - Helper PHP Functions "wst-helper-functions.php"
* Add - Konstante "wst.IS_ACCESSIBLE" in die JavaScript-Utility-Bibliothek implementiert
* Add - Swiper Navigation Übersetzung
* Dev - Überarbeitung der Datum-Ermittlung bei den Shortcodes und SmartTags
* Dev - Code Optimierungen und Formatierungen
* Dev - CMB2 Code Optimierungen
* Dev - Hilfe aktualisiert
* Dev - Übersetzungen aktualisiert
* Fix - Aufruf der fehlenden Klasse "CMB2_Boxes"

## 6.14.0
* Add - CMB2 General Shortcodes "wst_cmb2", "wst_cmb2_text", "wst_cmb2_number", "wst_cmb2_url"
* Add - CMB2 Content Shortcodes "wst_cmb2_image", "wst_cmb2_gallery_image", "wst_cmb2_file", "wst_cmb2_file_title", "wst_cmb2_file_size", "wst_cmb2_file_type", "wst_cmb2_wysiwyg", "wst_cmb2_gallery"
* Add - CMB2 Selection Shortcodes "wst_cmb2_select", "wst_cmb2_radio", "wst_cmb2_checkbox", "wst_cmb2_checkbox_group"
* Add - CMB2 Relation Shortcodes "wst_cmb2_page_link", "wst_cmb2_post_object"
* Add - CMB2 Advanced Shortcodes "wst_cmb2_date", "wst_cmb2_date_time", "wst_cmb2_time", "wst_cmb2_color", "wst_cmb2_phone_number"
* Add - CMB2 Layout Shortcode "wst_cmb2_repeater"
* Add - Third Party Plugin Integrationen für "CMB2"
* Add - Core PHP Function "wst_cmb2_is_active"
* Add - Loop PHP Function "wst_remove_loop"
* Add - Media PHP Function "wst_get_attachment_image"
* Add - Parameter "post_id" beim Shortcode "wst_include"
* Add - Parameter "option->target" beim Shortcode "wst_acf_link"
* Dev - Optimierung Condition PHP Function "wst_condition_check_compare_statement"
* Dev - Hilfe aktualisiert
* Dev - Übersetzungen aktualisiert
* Dev - Code Optimierungen und Formatierungen
* Fix - Bedingte-Logik "start_with" und "end_with"
* Fix - Fehlender Rückgabewert beim Shortcode "wst_i18n_string"

## 6.13.0
* Add - ACF Shortcode "wst_acf_file_title"
* Add - JavaScript Library "flatpickr.js" implementiert
* Add - Core PHP Function "wst_localize_flatpickr", "wst_search_smarttag_field_id", "wst_search_smarttag_field_id", "wst_prepare_in", "wst_parse_post_id_list"
* Add - Inaktive Polylang Sprachen aus den Yoast SEO XML-Sitemaps ausschließen
* Add - Core PHP Function "wst_multilingual_is_active", "wst_is_block_editor", "wst_kses_allowed_html"
* Add - Term PHP Function "wst_term_get_default_attributes", "wst_terms_has_same_hierarchy_level"
* Add - Page PHP Function "wst_get_maintenance_mode_page_id"
* Add - ACF PHP Function "wst_acf_get_template_acf_id"
* Add - Condition PHP Function "wst_condition_decode_field_key", "wst_condition_convert_field_value", "wst_condition_search_field_value"
* Add - Translation PHP Function "wst_i18n_filter_post_ids_by_language"
* Add - Parameter "name" beim Shortcode "wst_iframe"
* Add - Parameter "acf_id" beim Shortcode "wst_include"
* Add - Parameter "aria_label" beim Shortcode "wst_acf_phone_number", "wst_acf_link"
* Add - Feld Konvertierungs Parameter "strip_tags", "number_format"
* Dev - Mehrsprachigkeit bei den Bookmarks optimiert
* Dev - Optimierung des HTML Markups beim Shortcode "wst_acf_table"
* Dev - Optimierung des Wartungsmodus
* Dev - More Tag Implementation optimiert
* Dev - Hilfe aktualisiert
* Dev - Länder Liste aktualisiert
* Dev - Übersetzungen aktualisiert
* Dev - Code Optimierungen und Formatierungen
* Dev - Vendor Library "dompdf/dompdf" auf Version 3.1.0 aktualisiert
* Dev - Vendor Library "bjeavons/zxcvbn-php" auf Version 1.4.2 aktualisiert
* Dev - Vendor Library "chillerlan/php-qrcode" auf Version 5.0.3 aktualisiert
* Dev - Vendor Library "giggsey/libphonenumber-for-php" auf Version 8.13.55 aktualisiert
* Dev - Vendor Library "yahnis-elsts/plugin-update-checker" auf Version 5.5 aktualisiert
* Dev - Vendor Library "kint-php/kint" auf Version 5.1.1 aktualisiert
* Dev - JavaScript Library "Swiper" auf Version 11.2.6 aktualisiert
* Fix - Kombination aus Parent und Child Kategorie bei der Funktion "wst_build_taxonomy_query"
* Fix - WPGridbuilder Script Handling in Verbindung mit Swiper
* Fix - Parameter "offset" beim Shortcode "wst_post_terms"
* Fix - ACF Flexible Content Feld beim Wartungsmodus
* Fix - Polylang Methode "PLL_Language::get_flag_information"
* Fix - Shortcode Auflösung beim Shortcode "wst_include"
* Fix - Fehlerhaftes JSON in Data-Attributen beim Shortcode "wst_include"
* Fix - SmartTag ID Ermittlung bei Kategorie und Benutzer Feldern

## 6.12.0
* Add - Minify HTML Markup Feature
* Add - Minify HTML Markup Settings
* Add - Optionen (Schlagwörter Seite deaktivieren, Datum Seite deaktivieren) bei den BuildIn-Beitragsseiten-Einstellungen
* Dev - Übersetzungen aktualisiert
* Dev - Code Optimierungen und Formatierungen
* Fix - Fehlerhafte Übergabe des Parameters "id" beim Shortcode "wst_if"
* Fix - Fehlerhafte "apply_filters" Aufrufe in der Datei "wst-woocommerce-functions.php"

## 6.11.0
* Add - elseif Bedingung in den Shortcode "wst_if" integriert
* Add - Befehls Shortcode "wst_footer"
* Add - ACF Extended Shortcode "wst_acf_phone_number"
* Add - Condition Feld "user_roles"
* Add - Neuer Parameter "output_type" beim Shortcode "wst_qr_code"
* Add - Neuer Parameter "strip_tags" beim Shortcode "wst_acf_wysiwyg"
* Add - Neuer Parameter "drag_to_close" beim Shortcode "wst_fancybox"
* Add - Neuer Parameter "inline" beim Shortcode "wst_image", "wst_post_thumbnail", "wst_acf_image", "wst_acf_gallery_image"
* Add - PHP Library "libphonenumber" implementiert
* Add - WPGridBuilder build.min.js
* Add - Core PHP Function "wst_attachment_base64_encode", "wst_base64_encode_file"
* Add - Translation PHP Function "wst_i18n_parse_wp_locale"
* Add - Formatting PHP Function "wst_parse_phone_number", "wst_make_clickable", "_wst_make_phone_clickable_cb", "wst_get_emojis"
* Add - Media PHP Functions "wst-media-functions.php"
* Add - Konvertierungs Parameter "modulo"
* Add - Condition Feld "post_menu_order"
* Dev - Registrierung des Shortcodes "wst_include" optimiert
* Dev - Überarbeitung des Shortcodes "wst_if"
* Dev - Code Optimierungen und Formatierungen
* Dev - Übersetzungen aktualisiert
* Dev - Hilfe aktualisiert
* Dev - Backbone Modal plugin update
* Dev - PHP "Background Process" Klassen umbenannt
* Dev - PHP Klasse "WST_YoastSEO" in "WST_WP_SEO" umbenannt
* Fix - Loop-Mode beim Swiper mit nur einem Slide
* Fix - Mehrfache Fancybox Lokalisierung bei Shortcodes
* Fix - Mehrfache Video.js Lokalisierung bei Shortcodes
* Fix - Transient Abfrage beim Shortcode "wst_the_grid" und "wst_ess_grid"
* Fix - Fehlerhafte Registrierung des Shortcodes "wst_include"
* Fix - Fehlerhafter JavaScript Aufruf beim Swiper

## 6.10.0
* Add - Allgemeiner Shortcode "wst_counter"
* Add - JavaScript Library "counter-up2.js" implementiert
* Add - JavaScript Library "jquery.waypoints.js" implementiert
* Add - PHP Filter bei der PHP Function "wst_google_maps_geocode" implementiert
* Dev - Code Optimierungen und Formatierungen
* Dev - Übersetzungen aktualisiert
* Dev - Hilfe aktualisiert
* Fix - Shortcode "wst_if" mit einem SmartTag im "value" Parameter in html tags

## 6.9.1
* Add - Admin Bar Suchformular für Custom Post Types
* Add - Filterung nach Datum und Uhrzeit bei den Log-Dateien
* Add - Date Query Attribute bei der PHP Function "wst_set_wp_query_args"
* Add - PHP Filter "wst_post_thumbnail", "wst_woocommerce_product_gallery_attachment_ids", "wst_posts_by_taxonomy_term_list_term_post_args"
* Add - JavaScript Library "wst-util.js" implementiert
* Add - Kompatibilität zwischen WPGridbuilder und Swiper
* Add - Kompatibilität zwischen dem globalen JavaScript Objekt "wst" und WPGridbuilder, Swiper und Fancybox
* Add - Neuer Parameter "centered_slides" beim Shortcode "wst_swiper_gallery"
* Add - Neuer Parameter "centered_slides" beim Shortcode "wst_swiper"
* Add - Neuer Parameter "srcset" beim Shortcode "wst_image"
* Add - Neuer Parameter "srcset" beim Shortcode "wst_post_thumbnail"
* Add - Core PHP Function "wst_cache_key"
* Add - Query PHP Function "wst_get_date_query"
* Add - Translation PHP Function "_wst_apply_language_cache_key"
* Add - Formatting PHP Function "wst_remove_prefix"
* Add - Condition PHP Function "wst_is_admin_bar_search_form_enabled"
* Add - WooCommerce PHP Function "wst_wc_get_product", "wst_wc_get_product_condition_value"
* Add - Deprecated PHP Function "wst_filter_depricated_condition_field_value_selector"
* Dev - Optimierung der Funktion "Show Onload" bei der Fancybox
* Dev - Update der JavaScript Library "js-cookie" auf Version 3.0.5
* Dev - Code Optimierungen und Formatierungen
* Dev - Übersetzungen aktualisiert
* Dev - Hilfe aktualisiert
* Dev - Update der "WP Background Processing" Library auf Version 1.3.1
* Fix - Prüfung des Datentyps in der PHP Function "wst_maybe_get_term"
* Fix - Ermittlung des Swiper Slide Indexes
* Tweak - MarkerClusterer Library in den übergeordneten Ordner js verschoben

## 6.8.1
* Fix - WPGridbuilder Ajax Handling
* Fix - Setting bei den Remote Logging Einstellungen

## 6.8.0
* Add - Remote Logging
* Add - Globales JavaScript Ojekt "wst"
* Add - Neuer Parameter "layout", "clone" beim Shortcode "wst_if"
* Add - Einstellungen -> Erweitert -> Seiteneinrichtung -> Option "Like Archive Page"
* Add - Einstellungen -> Erweitert -> Log-Dateien-> Option "Aufbewahrungsdauer Tage"
* Add - Post-Display-Status für spezielle WST-Seiten in der Admin-Seitenlistentabelle
* Add - Core PHP Function "wst_logger_get_retention_period", "wst_logger_remote_request", "wst_get_image_sizes", "wst_get_image_size"
* Add - Formatting PHP Function "wst_xml_encode", "wst_array_to_xml"
* Dev - Code Optimierungen und Formatierungen
* Dev - Übersetzungen aktualisiert
* Dev - Hilfe aktualisiert
* Dev - Update der UAParser Library auf Version 2.0.0-beta.3
* Dev - WPGridbuilder Script Handling in Verbindung mit WPRocket
* Fix - Fancybox Swiper Videos werden beim Schließen der Fancybox beendet

## 6.7.1
* Fix - 404 Seiten Redirect

## 6.7.0
* Add - ACF Shortcode "wst_acf_table"
* Add - Website eigene 404 Seite
* Add - Einstellungen - Seiteneinrichtung "404 Seite"
* Add - Core PHP Function "wst_get_file_upload_error_messages"
* Add - PHP Filter "wst_swiper_gallery_lightbox_caption", "wst_wpgb_grid_args" und "wst_swiper_gallery_caption"
* Add - Condition Felder "is_404", "is_single", "is_page", "is_singular", "is_archive", "is_post_type_archive"
* Dev - Code Optimierungen und Formatierungen
* Dev - Übersetzungen aktualisiert
* Dev - Hilfe aktualisiert
* Dev - PHP Function "wst_set_wp_query_args" eigenen query args Key "wst_query_args" implementiert
* Fix - Fehler beim Absenden eines ACF Formulars
* Fix - PHP Function "wst_trim_string" HTML Elemente im Text
* Fix - Shortcode "wst_acf_post_object" fehlender post_type Parameter

## 6.6.1
* Dev - Code Optimierungen und Formatierungen
* Fix - SmartTag Handling bei ID Übergabe

## 6.6.0
* Add - TinyMCE Shortcode Generator
* Add - Befehl Shortcode "wst_variable"
* Add - Variablen in die SmartTags integriert
* Add - Variablen in den Shortcode "wst_if", "wst_add_log" und "wst_add_kint_log" integriert
* Add - Help PHP Function "wst_get_all_help_tabs" und "wst_get_shortcode_tag"
* Add - Core PHP Function "wst_is_variable", "wst_parse_variable_value", "wst_set_variable", "wst_get_variable"
* Add - Formatting PHP Function "wst_sanitize_variable_name"
* Add - Konstante "WST_PLUGIN_DIR_URL"
* Dev - Code Optimierungen und Formatierungen
* Dev - WP Grid Builder V2 Optimierungen
* Dev - Übersetzungen aktualisiert
* Dev - Hilfe aktualisiert
* Fix - Fehlerhafte htmlentities2 Konvertierung bei dem SmartTags
* Fix - Shortcode "wst_typing_animation" Content Parameter Fehler 
* Fix - WP Rocket "Delay JavaScript execution" beim JavaScript "wp-grid-builder.js"
* Fix - Function "wst_localize_get_weather_terms" array_map Fatal Error
* Fix - Fehlende jQuery dependency beim JavaScript "wp-grid-builder.js"

## 6.5.2
* Add - Support für WP Grid Builder V2
* Dev - Code Optimierungen und Formatierungen
* Dev - Sprachen aktualisiert
* Fix - Fehler beim Shortcode "wst_acf_number"
* Fix - ACF Google Maps Marker Icon URL

## 6.5.1
* Add - Formatting PHP Function "wst_remove_html_comments"
* Add - Loop PHP Functions "wst_get_continue_loop_command", "wst_get_break_loop_command"
* Dev - Überarbeitung des Shortcodes "wst_continue_loop" 
* Dev - Überarbeitung des Shortcodes "wst_break_loop"

## 6.5.0
* Add - Befehl Shortcode "wst_continue_loop"
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen und Formatierungen
* Dev - Hilfe Erweiterungen und Aktualisierungen
* Fix - Fehler beim "post__not_in" Parameter in der Funktion "wst_set_wp_query_args"

## 6.4.0
* Add - Website Wartungsmodus
* Add - Core PHP Function "wst_is_maintenance_mode_enabled"
* Add - Admin PHP Function "wst_get_tpl_ids_by_user", "wst_get_tpl_ids_by_taxonomy_terms"
* Add - Smart Templates auf bestimmte Kategorien für eine Benutzerrolle einschränken
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen und Formatierungen
* Fix - ACF Google Maps Marker Generierung

## 6.3.0
* Add - Suche bei der Admin Hilfe
* Add - Shortcodes Tab "Alle" bei der Admin Hilfe
* Add - Farbe bei den Shortcode Kategorien
* Add - Help PHP Function "wst_get_help_read_more_parameters"
* Add - Formatting PHP Functions "wst_do_more_tag", "wst_do_wp_more_tag", "wst_do_expandable_more_tag"
* Add - Fancybox <-> Swiper Kompatibilität implementiert
* Add - Neuer Parameter "space_between", "initial_slide" beim Shortcode "wst_swiper_gallery"
* Add - Neuer Parameter "space_between", "initial_slide" beim Shortcode "wst_swiper"
* Add - Read More Tag beim Shortcode "wst_post_content", "wst_post_excerpt", "wst_acf_wysiwyg"
* Add - jQuery Plugin "jquery.wstGoogleMap.js"
* Dev - ACF Map Initialisierung angepasst
* Dev - User und Term ID Parameter Auflösung beim Third Party "WP Grid Builder" SmartTemplate Block
* Dev - WP Rocket Integration beim Shortcode "wst_wpgb_grid"
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen und Formatierungen
* Dev - Hilfe Erweiterungen und Aktualisierungen
* Fix - Locale Switcher auf localStorage umgestellt

## 6.2.0
* Add - ACF Shortcode "wst_acf_range"
* Add - Neuer Parameter "output" beim Shortcode "wst_acf_file"
* Dev - Auto Plugin Updater optimiert
* Dev - Hilfe Erweiterungen und Aktualisierungen

## 6.1.1
* Dev - Umstellung des Repositories von Github auf Bitbucket
* Dev - Image Lazyload Filter beim Muffin Builder aktivieren

## 6.1.0
* Add - Allgemeiner Shortcode "wst_swiper"
* Add - Beitrags Shortcode "wst_post_type"
* Add - CSS Library "skeleton-screen-css" implementiert
* Add - JavaScript Library "EnlighterJS" implementiert
* Add - JavaScript Library "Video.js" implementiert
* Add - Template PHP Functions "wst_get_skeleton_loader"
* Add - Neuer Parameter "ajax", "ajax_loader", "ajax_delay" beim Shortcode "wst_wpgb_grid"
* Add - Neuer Konvertierungs Paramter "field" beim SmartTag "post_type"
* Add - Core PHP Functions "wst_localize_js_video"
* Dev - Vendor Library "plugin-update-checker" implementiert und aktualisiert
* Dev - Code Optimierungen und Formatierungen
* Dev - Optimierung der Condition Function "wst_condition_check_compare_statement"
* Dev - Sprachen aktualisiert
* Dev - Hilfe Erweiterungen und Aktualisierungen
* Dev - Code Optimierungen und Formatierungen
* Dev - Datei "wpgb-facet-params.js" umbenannt auf "wp-grid-builder.js"
* Dev - Datei "swiper-gallery.js" umbenannt auf "swiper-slider.js"
* Fix - Shortcode "wst_acf_link" Target-Attribut Auflösung.
* Fix - Shortcode "wst_fancybox" übergebener Content mit Shortcodes
* Fix - Shortcode "wst_foreach" vordefinierte Liste aller Sprachen
* Fix - Shortcode "wst_calculate" Fehler bei der Berechnung mit Shortcodes
* Fix - Shortcode "wst_i18n_string" Fehler beim Registrieren des Strings mit einem Shortcode

## 6.0.0
* Add - Account Shortcode "wst_zip", "wst_login_form", "wst_register_form", "wst_lost_password_form", "wst_edit_account"
* Add - Formatting Shortcode "wst_sanitize_title"
* Add - Allgemeiner Shortcode "wst_google_calendar", "wst_vcard"
* Add - Beitrags Shortcode "wst_post_google_calendar"
* Add - ACF Shortcode "wst_acf_select", "wst_acf_radio", "wst_acf_button_group"
* Add - Nested Shortcode "wst_post_terms", "wst_foreach", "wst_posts", "wst_posts", "wst_terms"
* Add - Account PHP Functions "wst-account-functions.php"
* Add - Page PHP Functions File "wst-page-functions.php"
* Add - REST PHP Functions File "wst_jwt_token_field.php"
* Add - Remote PHP Functions "wst_openai_create_image"
* Add - ACF PHP Functions "wst_acf_map_get_marker_icon_sizes", "wst_acfe_is_performance_enabled"
* Add - Admin PHP Functions "wst_get_post_type_options"
* Add - Condition PHP Functions "wst_is_json", "wst_is_valid_url", "wst_wp_theme_get_element_class_name", "wst_is_file_valid_csv", "wst_is_null_or_empty", "wst_is_null_or_whitespace", "wst_is_lost_password_page", "wst_post_content_has_shortcode", "wst_site_is_https", "wst_post_exists"
* Add - Core PHP Functions "wst_cleanup_logs", "wst_is_wp_default_theme_active", "wst_cleanup_session_data", "wst_set_time_limit", "wst_nocache_headers", "wst_localize_fancybox", "wst_generate_google_calendar_link", "wst_generate_vcard"
* Add - Formatting PHP Functions "wst_array_to_string", "wst_string_to_number", "wst_rgba_from_string", "wst_rgb_from_hex", "wst_hex_from_rgba", "wst_hsl_from_rgb", "wst_rgb_from_hsl", "wst_hex_from_hsl", "wst_hsl_from_hex", "wst_strlen", "wst_make_phone_clickable", "wst_starts_with", "wst_ends_with", "wst_contains", "wst_get_domain"
* Add - Term PHP Functions "wst_get_primary_post_term", "wst_set_primary_post_term_id", "wst_maybe_get_term"
* Add - Translation PHP Functions "wst_i18n_parse_object_id_list", "wst_i18n_delete_post_translation", "wst_i18n_delete_term_translation", "wst_i18n_translate_with_gettext_context", "wst_maybe_switch_lang", "wst_i18n_str_replace_flag"
* Add - User PHP Functions "wst_disable_admin_bar", "wst_create_new_user", "wst_create_new_user_username", "wst_set_user_auth_cookie"
* Add - Query PHP Functions "wst_set_primary_post_taxonomy_query", "wst_set_term_list_taxonomy_query", "wst_build_taxonomy_query", "wst_parse_primary_post_taxonomy_list_args", "wst_query_posts_orderby_primary_post_taxonomy", "wst_get_tax_query_count"
* Add - Help PHP Functions "wst_get_help_fancybox_toolbar_item_options"
* Add - Query Parameter "primary_post_taxonomy", "primary_post_taxonomy_relation", "primary_post_taxonomy_include_children", "primary_post_taxonomy_filter"
* Add - Neuer Parameter "more" beim Shortcode "wst_trim_string", "wst_post_title", "wst_post_content", "wst_post_excerpt"
* Add - Neuer Parameter "src", "type", "toolbar", "toolbar_left", "toolbar_middle", "toolbar_right" beim Shortcode "wst_fancybox"
* Add - Neuer Parameter "class", "link_file", "allow_touch_move", "lightbox", "lightbox_caption_type", "lightbox_toolbar", "lightbox_toolbar_left", "lightbox_toolbar_middle", "lightbox_toolbar_right" beim Shortcode "wst_swiper_gallery"
* Add - Neuer ACF Map Parameter "max_zoom", "min_zoom"
* Add - Neue Output Parameter beim Shortcode "wst_acf_color" implementiert
* Add - Shortcode "wst_ics" neuer Parameter "gmt" (Greenwich Mean Time)
* Add - Shortcode "wst_foreach" neue Parameter "range_start, range_end und range_step"
* Add - OpenAI API Klasse und PHP Functionen
* Add - Session Handling für die Verarbeitung von aktuellen Kundensitzungen
* Add - JS Widgets Template Integration
* Add - Beitrags Berechtigungen um Inhalte für bestimmte Benutzerrollen einzuschränken
* Add - JShrink Library
* Add - Benachrichtigungen für Formular Handling
* Add - Parameter ID bei der Funktion "wst_background_process"
* Add - Bookmarks werden nun vorwiegend in der usermeta gespeichert.
* Add - WooCommerce Settings
* Add - WooCommerce zuletzt gesehene Produkte tracken
* Add - Cron Job welcher alle SmartTemplate Logs größer 30 Tage löscht 
* Add - Anzeige von Flaggen (Sprache) im SmartTemplate Log
* Dev - Translation PHP Functions an WPML angepasst
* Dev - Shortcode "wst_qr_code" zu den Allgemeinen Shortcodes verschoben
* Dev - Formular Aufbau bei den Shortcodes "wst_ics, wst_pdf und wst_zip" als Template implementiert 
* Dev - Bookmark Shortcodes und Funktionen optimiert
* Dev - Like Shortcodes optimiert
* Dev - Sprachen aktualisiert
* Dev - Hilfe Erweiterungen und Aktualisierungen
* Dev - GMT Eigenschaft in die ICS Library implementiert
* Dev - Admin Hilfe Erweiterungen und Aktualisierungen
* Dev - Shortcode wst_post_ics optimiert
* Dev - Code Optimierungen und Formatierungen
* Dev - Einbindung des Grid Builder Custom Scripts "wp-grid-builder.js" optimiert
* Dev - Fancybox Library aktualisiert
* Dev - Negative Shortcodes werden nun dynamisch erstellt
* Dev - Vendor Library "php-jwt" implementiert und aktualisiert
* Dev - Vendor Library "php-qrcode" implementiert und aktualisiert
* Dev - Vendor Library "dompdf" implementiert und aktualisiert
* Dev - Vendor Library "vcard" implementiert und aktualisiert
* Dev - Vendor Library "zxcvbn-php" implementiert und aktualisiert
* Dev - Vendor Library "jshrink" implementiert und aktualisiert
* Dev - Vendor Library "mobiledetectlib" implementiert und aktualisiert
* Dev - JavaScript Library "Swiper" aktualisiert
* Dev - JavaScript Library "Fancybox" aktualisiert
* Dev - JavaScript Library "iframeResizer" aktualisiert
* Dev - .gitignore implementiert
* Dev - Filter wst_mfn_builder_content für rocket_lazyload_images entfernt
* Fix - ACFe Performance Mode Abfrage
* Fix - Verschieben des Beitrags und Auszugsfeldes in ein ACF Feld
* Fix - PHP Error beim SmartTag {{today}}
* Fix - Muffin Builder Initialisierung bei den verschiedenen Inhaltstypen
* Tweak - DOMPDF Library in den Ordner Vendor verschoben
* Tweak - Shortcode wst_i18n_string zu den Multilingual Shortcodes verschoben

## 5.0.1
* Add - Shortcode "wst_acf_form" zum Hinzufügen oder Bearbeiten eines Beitrags
* Add - Theme Funktion "wst_get_theme_version"
* Add - Loop Funktion "wst_setup_postdata"
* Add - Condition Funktion "wst_has_cyrillic"
* Add - Remote Funktion "wst_get_google_map_api_key"
* Add - Neuer Condition Parameters "wc_cart_cross_sell_products" und "wc_product_has_attributes"
* Tweak - Query / Condition Parameters "wc_upsell_products" auf "wc_product_upsell_products" umbenannt
* Tweak - Query / Condition Parameters "wc_cross_sell_products" auf "wc_product_cross_sell_products" umbenannt
* Tweak - Query / Condition Parameters "wc_gallery_images" auf "wc_product_gallery_images" umbenannt
* Tweak - Google Map API Key Option "wst_acf_google_map_api_key" auf "wst_google_map_api_key" umbenannt
* Dev - Google Map API Key Option in den Reiter "Integrationen" verschoben
* Dev - Template Loader Optimierungen
* Dev - BeTheme Anpassungen
* Dev - Code Optimierungen und Formatierungen
* Dev - Anpassung bei den Page-Builder Settings
* Dev - Muffin Builder Anpassungen
* Dev - Sprachen aktualisiert
* Dev - Hilfe Erweiterungen und Aktualisierungen
* Fix - Falsche single.php Zuordnung im Template Loader
* Fix - Fehlerhafte Ausgabe in der Funktion "wst_parse_html_attributes"
* Fix - Fehler beim Ermitteln des Datums bei der Funktion "wst_get_formated_date_time"
* Fix - Unset des Value Feldes bei "EXISTS" und "NOT EXISTS" in der Funktion "wst_get_meta_query"

## 5.0.0
* Add - Locale Switcher mit Template Aufbau
* Add - Shortcode "wst_swiper_gallery" mit dem Parameter "caption_type" erweitert
* Add - Shortcode "wst_language_switcher" und "wst_languages"
* Add - Shortcode "wst_add_kint_log"
* Add - Länder Erkennung für Polylang integriert
* Add - Parameter "Eltern Seite" bei den ACF Optionen Seiten implementiert
* Add - Query Orderby Parameter "orderby_post_type", "orderby_taxonomy_term" und "orderby_same_post_taxonomy"
* Add - Query Funktionen "wst_set_custom_order_by_query", "wst_parse_taxonomy_term_list_args", "wst_parse_same_post_taxonomy_list_args", "wst_get_posts_by_taxonomy_term_list", "wst_query_get_taxonomy_terms_orderby_clause", "wst_query_posts_orderby_post_type", "wst_query_posts_orderby_taxonomy_term", "wst_query_posts_orderby_same_post_taxonomy"
* Add - Remote Funktion "wst_get_attachment_id_from_url", "wst_rest_upload_media_from_url", "wst_rest_set_uploaded_media_as_attachment"
* Add - Formatierungs Funktion "wst_count_words", "wst_get_formated_date_time" und "wst_get_post_order_field_from_string"
* Add - Condition Felder "post_primary_term_id" und "term_link"
* Add - Condition Funktion "wst_condition_get_field_types", "wst_is_amp" und "wst_has_bbcode"
* Add - Parameter "language" beim Shortcode "wst_geolocate" erweitert
* Add - Parameter "direction" beim Shortcode "wst_swiper_gallery" erweitert
* Tweak - Shortcode wst_back_button speichert nun die letzte valide URL
* Dev - BeTheme Page Template Action Hook "mfn_before_content" und "mfn_after_content" ergänzt
* Dev - Neue Benutzer Funktion "wst_get_admin_email"
* Dev - Sprachen aktualisiert
* Dev - Hilfe Erweiterungen und Aktualisierungen
* Dev - Hilfe Optimierungen und Formatierungen
* Dev - Code Optimierungen und Formatierungen
* Dev - ACF Shortcode Methode "get_formated_date_time" als Funktion "wst_get_formated_date_time" ausgelagert
* Dev - Update der select2 Library auf Version 4.0.13
* Dev - Update der Swiper Library auf Version 8.4.6
* Dev - Optimierung des "wst_wpgb_grid" Shortcodes
* Dev - Neue Klasse "WST_Geolocation", welche nun die Logik der Geolokalisierung beinhaltet
* Dev - Neue Settings Seiten "Integrationen" und "Mehrsprachigkeit"
* Dev - Anpassung der "Erweitert" Settings Seite
* Dev - Bookmark Cookie Ablaufdatum Standard Wert auf 30 erhöht
* Dev - Neue Ajax Methode "refresh_locale_switcher"
* Fix - Mailster Shortcode Auflösung
* Fix - Shortcode wst_typing_animation string Ermittlung
* Fix - Swiper Gallery JavaScipt Initialisierung
* Fix - ACF Rest API Initialisierung
* Fix - Wetter Kategorien Zuordnungs Berechtigungen
* Fix - Attachment Handling bei den WP Grid Builder Blöcken "Code Editor" und "Template"
* Fix - Fehlerhafte Überprüfung in der Funktion "wst_get_page_permalink"
* Fix - Fehlerhafte Anzeige bei den Bookmark Counter
* Fix - Fehler beim Speichern eines Seiten-Templates

## 4.10.2
* Fix - Fehlerhafte Parameterübergabe bei der Funktion "wst_condition_parse_field_value_conversion"

## 4.10.1
* Dev - Überarbeitung der Smart Template Hilfe
* Dev - SmartTags werden nun auch in Menüs aufgelöst
* Dev - Sprachen aktualisiert
* Dev - Optimierungen beim Shortcode "wst_ics" und "wst_post_ics"
* Fix - Background Prozesse werden richtig gesetzt

## 4.10.0
* Add - JSON Web Token Integration
* Add - Neuer Shortcode "wst_acf_page_link", welcher ein ACF Seiten-Link Feld ausgibt
* Add - Neuer Shortcode "wst_swiper_gallery", welcher eine Swiper Gallery erstellt 
* Add - Neuer Shortcode "wst_typing_animation", welcher eine Typing Animation erstellt
* Add - Neuer Shortcode "wst_superglobal", welcher einen Wert von einer Superglobalen Variable zurück gibt
* Add - Neuer Shortcode "wst_iframe", welcher ein IFrame erstellt, welches automatisch resized wird
* Add - Neuer Shortcode "wst_pdf" welcher die Generierung einer ICS Datei ermöglicht
* Add - Neuer Shortcode "wst_ics" welcher die Generierung einer ICS Datei ermöglicht
* Add - Neuer Shortcode "wst_post_ics" welcher die Generierung einer ICS Datei ermöglicht
* Add - Neuer Shortcode "wst_post_translations", welcher alle Übersetzungen eines Beitrages als Schleife zurückgibt
* Add - Neuer Shortcode "wst_add_log", welcher ein Logging ermöglicht
* Add - Neuer Shortcode "wst_geolocate", welcher ein bestimmtes Feld der geolokalisierten IP-Adresse ermittelt.
* Add - Direkter Link zu den SmartTemplate-Logs in der Admin Bar
* Add - Neue Like Option - Ablauftage des Cookies
* Add - Neue Externe Services Option - IP Geolocation API Key
* Add - Neue ACF API Funktion "wst_acfe_layout_column_size", "wst_acf_load_depricated_rest_api"
* Add - Neue Core API Funktion "wst_is_elementor", "wst_is_valid_smarttag"
* Add - Neue Formatting API Funktion "wst_format_uuid", "wst_xml_decode"
* Add - Neue Translation API Funktion "wst_i18n_get_post_translations", "wst_i18n_get_term_translations", "wst_i18n_get_country", "wst_i18n_is_translated_post_type", "wst_i18n_is_translated_taxonomy"
* Add - Neue Remote API Funktion "wst_get_geolocation_fields", "getallheaders"
* Add - Neue Query API Funktion "wst_set_language_query"
* Dev - Funktion "wst_get_global_arrays" in "wst_get_superglobals" umbenannt
* Dev - Settings Menü Struktur angepasst
* Dev - Third Party Plugin Mailster - Filter für Shortcode Handling integriert
* Dev - Loop Funktionen optimiert
* Dev - Automatische Überprüfung ob die alte ACF REST API geladen werden soll - Funktion wst_acf_load_depricated_rest_api
* Dev - Muffin Builder Front Ausgabe - Elementor Integration
* Dev - SmartTemplate-Logs auf 100 Einträge pro Seite gesetzt
* Dev - Sprachen aktualisiert
* Dev - Hilfe aktualisiert
* Dev - Update der JavaScript Library "css-vars-ponyfill" auf Version 2.4.7
* Dev - Überarbeitung der internen Loop API
* Dev - Überarbeitung der internen Condition API
* Dev - Überarbeitung der internen Geolocate API
* Dev - Code Optimierungen und Formatierungen
* Fix - Bookmark Button JavaScript Data-Attribut angepasst

## 4.9.0
* Add - Third Party Plugin Integrationen für "Duplicate Post", "Mailster", "Polylang" und "WP Grid Builder"
* Add - ACF Funktion "wst_acf_calculate_great_circle_distance"
* Add - Core Funktion "wst_get_build_in_meta_keys"
* Add - Log Funktion "wst_add_log" und "wst_add_kint_log"
* Dev - Code Editor wird nun in voller Höhe angezeigt
* Dev - Code Optimierungen und Formatierungen
* Fix - Fehlerhafte Feld Ausgabe im Zusammenhang mit WP Grid Builder und einem übergebenen Clone Shortcode Parameter
* Fix - Fehler in der Hilfe im Tab "Beitrags Felder"

## 4.8.0
* Add - Formatierungs Shortcode "wst_trim_string"
* Add - Bookmark Shortcode "wst_bookmark_reset"
* Add - Beitrags Shortcode "wst_post_meta"
* Add - Benutzer Shortcode "wst_logout_url"
* Add - Befehl Shortcode "wst_foreach"
* Add - WooCommerce Shortcode "wst_woocommerce_template_single_rating"
* Add - Neuer Parameter "list_separator" beim Shortcode "wst_acf"
* Add - ACF Funktion "wst_acf_get_field_group_from_field"
* Add - Core Funktion "wst_get_global_arrays"
* Add - Formatierungs Funktion "wst_string_to_associative_array", "wst_parse_string_id_list_to_array", "wst_maybe_get", "wst_count_code_units", "wst_number_format_thousands_i18n"
* Add - WooCommerce Funktion "wst_wc_set_product_type"
* Add - Untermenüpunkt "Hilfe", welcher alle Shortcodes anzeigt.
* Add - Condition Felder "attachment_image_url", "attachment_url" und "Globale Arrays"
* Add - Query Paramter "user_like_posts", "wc_cart_cross_sell_products", "ignore_sticky_posts"
* Add - Remote Funktion "wst_google_recaptcha_siteverify", "wst_calculate_great_circle_distance".
* Fix - Speicherung des Längen und Breitengrads bei den Wetter Einstellungen
* Fix - Post ID Ermittlung in der Funktion "wst_acf_get_the_ID"
* Fix - UNIQUE Key "map_unique" in der Tabelle "smart_template_mappings"
* Fix - Session wird erst im Frontend gestartet.
* Dev - Error Logging Optimierung
* Dev - Optimierung der Core Funktionen "wst_count_posts" und "wst_do_smarttag".
* Dev - Umbenennung der Funktion "wst_get_product_upsell_ids" zu "wst_wc_get_product_upsell_ids"
* Dev - Umbenennung der Funktion "wst_get_product_cross_sell_ids" zu "wst_wc_get_product_cross_sell_ids"
* Dev - Umbenennung der Funktion "wst_get_product_gallery_image_ids" zu "wst_wc_get_product_gallery_image_ids"
* Dev - WP Gridbuilder Shortcode Optimierung
* Dev - Code Optimierungen und Formatierungen
* Dev - Sprachen aktualisiert
* Dev - Hilfe aktualisiert
* Dev - Bookmark Buttons Redirect Anpassung.
* Dev - Mobile Detect Library Update auf Version 2.8.37

## 4.7.2
* Fix - Fehler bei der Zuordnung eines ACF Repeaters

## 4.7.1
* Add - Formatierungs Shortcode "wst_strip_tags" und "wst_make_clickable"
* Add - Neuer Parameter "mailto" und "subject" beim Shortcode "wst_bookmark_share"
* Add - Neuer Parameter "strip_tags" beim Shortcode "wst_post_content"
* Add - Neue ACF Funktion "wst_acf_get_option_page_ids"
* Fix - Aktuelle Sprache bei einem Polylang Rest Request setzen 
* Dev - Code Optimierungen
* Dev - Sprachen aktualisiert
* Dev - Hilfe aktualisiert

## 4.7.0
* Add - Gefällt mir Shortcodes "wst_like_button", "wst_like_post_counter" und "wst_like_posts"
* Add - Beitrags Shortcode "wst_post_author"
* Add - Formatierungs Shortcode "wst_number_format"
* Add - Shortcode "wst_is_mobile_network"
* Add - Shortcode "wst_i18n_string"
* Add - Parameter "icon_color_active" beim Shortcode "wst_bookmark_button"
* Add - Parameter "srcset" beim Shortcode "wst_acf_image" und "wst_acf_gallery_image"
* Add - Setting "Gefällt mir"
* Add - ACF Funktion "wst_acf_get_id_type"
* Add - Condition Felder "post_author", "wc_upsell_products", "wc_cross_sell_products", "wc_gallery_images", "is_mobile_network", "count_posts", "user_bookmark_posts", "user_like_posts"
* Add - Parameter "user_like_posts" beim allen Shortcodes mit Query Einschränkung
* Add - WPGridBuilder Smart Template Addon Blöcke "Code Editor" und "Template"
* Add - Database Mapping API
* Add - Neue Funktionen "wst_replace_smarttag_loop_id" und "wst_check_break_loop"
* Dev - Optimierte Filterung beim WPGridBuilder
* Dev - Optimierte Shortcode Auflösung bei dem ACF Repeater Shortcode
* Dev - Fehler bei der ACF Map lat und lng Abfrage
* Dev - ACF Rest API Abfrage Anpassungen
* Dev - Benennung des Shortcodes wst_calculate
* Dev - Code Optimierungen
* Dev - Sprachen aktualisiert
* Dev - Hilfe aktualisiert
* Dev - Diverse Bugfixes

## 4.6.7
* Add - Neue ACF Funktion "wst_acf_get_object_field"
* Fix - Falsche globale Post Variable nach dem Grid Rendering
* Fix - ACF Feld Object wird bei den Repeater Shortcodes falsch ermittelt.
* Fix - Muffin Builder Post Passwort Form Ermittlung
* Dev - Polyfill CSS Vars Connect MutationObserver
* Dev - Uninstall Script erweitert

## 4.6.6
* Add - In den Beitragstypen Optionen können nun Beitrags / Metafelder für die REST API bereitgestellt werden.
* Add - Neue Funktionen "wst_search_wordpress_fields", "wst_acf_get_fields_formatted" und "wst_format_acf_fields".
* Dev - Sprachen aktualisiert
* Dev - Hilfe aktualisiert

## 4.6.5
* Add - ACF Optionen Seiten Parameter "ID" implementiert
* Dev - Wetter Anpassung bei der Metabox und Quick Edit
* Dev - Funktion "wst_set_wp_query_args" optimiert
* Dev - Action Scheduler entfernt
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen

## 4.6.4
* Add - Neuer Shortcode "wst_post_password_form"
* Add - Neues Feld "post_password_required" bei der Bedingten Logik / Smart Tags
* Add - Neuer Konvertierungsparameter "suppress_filters" bei den Condition Abfragen
* Add - Neue Funktion "wst_acf_is_active", "wst_acf_repeater_pre_do_shortcode", "wst_has_password_form", "wst_parse_shortcode_list"
* Add - Neuer Parameter "decimals" bei den Wetter Shortcodes
* Add - Skip Filter bei allen Repeatern
* Dev - Anpassungen im Page Template
* Dev - Anpassungen bei der Funktion "wst_set_wp_query_args" (post__in Parameter)
* Dev - Anpassungen bei der Funktion "wst_media_sideload_image"
* Dev - Funktion "wst_get_mfn_builder_content" mit Post-Passwort Logik.
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen
* Fix - Counter Anzeige für eingeschränkte Smart Templates
* Fix - Rendering bei Nested Repeatern

## 4.6.3
* Add - Neuer Shortcode "wst_wpgb_grid"
* Add - Neuer Parameter "show_onload" beim Shortcode "wst_fancybox"
* Add - Polyfill wird nun auch nach Ajax Requests ausgeführt
* Dev - Option Name Anpassung bei "remove_feature_support"
* Dev - Action Scheduler Update auf 3.1.6

## 4.6.2
* Add - Schreiben verschiedener Benutzer Informationen auf den Body Tag (Browse, Betriebssystem, Internet Geschwindigkeit, unsw..)
* Add - Administrieren von Seiten und Beitrags Features (Editor, Title, Auszug, unsw..)
* Add - Neue Funktionen "wst_unique_id", "wst_has_shortcode", ""
* Add - Neue Parameter "touch_momentum" und "show_onload" beim Shortcode "wst_fancybox"
* Add - Implementation von Kategorien bei den Smart Templates
* Add - Neuer Shortcode "wst_is_tablet", "wst_is_ios" und "wst_is_android"
* Add - Der Inhaltstyp "Seite" kann nun global von einem Smart Template überschrieben werden
* Dev - Der Shortcode "wst_string_replace" verwendet nun das | Zeichen als Trenner.
* Dev - Verbesserung der ACF Clone Feld Erkennung
* Dev - Action Scheduler Update auf 3.1.5
* Dev - Anpasungen bei der Smart Template Admin Übersicht
* Dev - Neuer Filter "smart_template_render_template_content"
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen
* Fix - Bookmark Button Event Handler
* Fix - Bookmark Session Initialisierung
* Fix - Metakey Auswahlfeld bei den Inhaltstyp Einstellungen

## 4.6.1
* Add - Neuer Shortcode "wst_calculate" und "wst_acf_relationship".
* Add - Der Shortcode "wst_if" wurde mit der [else] Anweisung erweitert.
* Add - Neue Loop Abfragen "loop_row_even" und "loop_row_odd".
* Add - Neue Loop Funktionen "wst_get_loop_row_index", "wst_get_loop_row_count", "wst_get_loop_row_first" und "wst_get_loop_row_last".
* Add - Neue Core Funktion "wst_get_queried_object_id".
* Add - Neuer Shortcode "wst_thickbox" und "wst_fancybox".
* Fix - Shortcode "wst_mfn_builder_content" im Seiten-Template-Modus.
* Fix - Taxonomy Konvertierung bei der Funktion "wst_condition_get_field_value".
* Fix - Auflösung der Condition Werte nach Wörter.
* Dev - Action Scheduler Update auf 3.1.4
* Dev - Die Bookmark Buttons werden nun in einer sessionStorage gespeichert um Probleme mit Caching Plugins zu vermeiden.
* Dev - Hilfe Aktualisiert.
* Dev - Sprachen aktualisiert.
* Dev - Code Optimierungen.

## 4.6.0
* Add - Code Editor beim Smart Template implementiert
* Add - Neue Shortcodes "wst_post_name" und "wst_acf_flexible_content"
* Add - Neue Condition Shortcodes "wp_is_mobile", "wst_is_tablet", "wst_is_ios" und "wst_is_android"
* Add - Neuer ACF Sortcode "wst_acf_flexible_content"
* Add - Neue ACF API Funktionen "wst_init_admin_mfn_builder_post_type", "wst_acf_get_repeater_types", "wst_acf_get_template_fc_layout", "wst_acf_get_template_clone", "wst_acf_get_active_fc_layout", "wst_acf_is_loop_active", "wst_acf_parse_clone_field" und "wst_acf_check_field_authorization"
* Add - Neue Formatting API Funktionen "wst_get_smart_template_content", "wst_get_code_editor_content"
* Add - Neue Wetter Einstellungen "Breitengrad" und "Längengrad"
* Add - Neue Wetter Shortcodes "wst_weather_clouds", "wst_weather_wind_degree", "wst_weather_wind_direction", "wst_weather_rain_volume" und "wst_weather_snow_volume"
* Add - Neue Wetter API Funktionen "wst_get_weather_wind_directions", "wst_weather_calculate_wind_speed", "wst_get_weather_longitude", "wst_get_weather_latitude"
* Add - Neue Core API Funktionen "wst_i18n_get_language_codes", "wst_get_closest_number" und "wst_post_type_is"
* Add - Neue Query Parameter "posts_per_page", "posts_per_page_tablet", "posts_per_page_mobile"
* Add - Bei Shortcode "wst_acf_gallery_image", kann als Parameter "image" nun auch "first" oder "last" übergeben werden
* Add - Alle Term Shortcodes können nun mit dem SmartTag Markup umgehen.
* Add - Neuer Parameter "text" beim Shortcode "wst_bookmark_share"
* Dev - Optimierung der Remote Funktion "wst_google_maps_geocode"
* Dev - Überarbeitung der ACF Funktionalität aufgrund der Flexiblen Inhalte
* Dev - Muffin Builder Content Optimierungen
* Dev - Muffin Builder Settings wurden in den Post Type Settings verschoben
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen
* Dev - plugin-update-checker Library update
* Fix - Bookmark Button Click Event Handler
* Fix - Condition Abfrage für Taxonomien

## 4.5.1
* Add - Bedingten Logik / Smart Tags unterstützen nun auch Term und User Felder
* Add - Bei den Berechtigungs Einstellungen können nun auch Benutzer Berechtigungen vergeben werden
* Add - Neues Feld "post_parent_level_count" und "post_parent_children_count" bei der Bedingten Logik / Smart Tags
* Dev - Plugin Update Checker Library 4.8 update
* Dev - Facet-WP Integration bei den Bookmark Controls
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen
* Fix - Die WooCommerce Rest-API wird von der Smart Template API nicht mehr gesperrt
* Fix - Negierte Shortcodes werden nun korrekt aufgerufen

## 4.5.0
* Add - Neuer Settings Tab "Erweitert"
* Add - ACF Felder können nun in der Rest API ausgegeben / bearbeitet werden.
* Add - Request API für Endpoint Callbacks
* Add - REST API Keys Verwaltung
* Add - Die Datenschutzseite kann nun auch für andere Benutzerrollen freigegeben werden
* Add - Die Author Seite kann nun seperat deaktiviert werden
* Add - Shortcode "wst_acf_taxonomy" Parameter Änderungen
* Add - Neue API Funktionen "wst_get_user_agent", "wst_rand_hash", "wst_api_hash", "wst_ip_geolocate"
* Add - Neuer Shortcode "wst_terms", "wst_nav_menu", "wst_user_data"
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen
* Dev - Anpassung der Funktionen "wst_acf_get_field" und "wst_acf_get_field_object" an den ACF Klon Felder
* Fix - wst_help_tip Function in der Action Sheduler Library angepasst
* Fix - Code Optimierung bei allen Taxonomy Shortcodes
* Fix - Polyfill Anpassungen beim Safari Browser
* Fix - Transient Zuordnung bei den Grid Shortcodes

## 4.4.8
* Fix - Endlosschleifen Work Around in allen do while Schleifen
* Fix - ACF Repeater werden nun sauber zurückgesetzt
* Fix - Polyfill Script Aufruf beim Internet Explorer und Edge Browser

## 4.4.7
* Add - Implementation der Action Scheduler Library
* Add - Implementation von Action Queue Funktionen
* Add - Implementation von Background Worker
* Add - Neue Funktion "wst_get_password_strength", "wst_is_valid_date" und "wst_is_valid_vat_number"
* Add - Shortcode "wst_acf_post_object" unterstützt nun auch den Post Type Attachemnts
* Add - Neuer Konvertierungsparameter "field_type" bei den Condition Abfragen
* Add - Der Query Parameter "same_post_taxonomy" unterstützt nun auch negative Werte
* Add - Neuer Query Parameter "post_names"
* Dev - Optimierung der Background Prozess Klasse
* Dev - Optimierung der Condition Funktionen
* Dev - In der Smart Template Admin Übersicht wurde die Shortcode Anzeige verbessert
* Dev - Der Filter für die Menü Beschreibungen wurden optimiert
* Dev - Code Optimierungen in der Wetter API
* Fix - Abfrage der Berechtigungsprüfung auf eingeschränkte Smart Templates
* Fix - Der Shortcode "wst_acf_repeater" wird nun sauber zurückgesetzt
* Fix - URL Abfrage in der Library "css-vars-ponyfill"
* Fix - ACF Datumswerte bei den Codition Abfragen

## 4.4.6
* Add - WP Rocket LazyLoad Support für BeTheme Muffin Builder
* Add - Internet Explorer Polyfill für CSS Variables
* Add - Neue Core Funktion "wst_is_port_open"
* Add - Neuer Shortcode "wst_lightbox"
* Add - Wetter Icons können nun als Pfad definiert werden
* Add - Neue Parameter bei dem Wetter Icon Shortcode
* Add - Neue Wetter Funktion "wst_get_weather_condition_group_title" und "wst_get_weather_condition_icon"
* Fix - Shortcodes in Menüs Conditional Fix
* Fix - Ausgabe beim Muffin Builder Shortcode
* Dev - Meta Query Parameter können nun auch mit Arrays umgehen
* Dev - Bei den Condition Values werden nun auch ACF "date_picker", "date_time_picker" und "link" Felder supported
* Dev - "Taxonomy-Relation" Anpassungen bei der Query Funktion
* Dev - LazyLoad polyfill für IE
* Dev - Wetter Beschreibung wird nun direkt aus dem Setting-Array ermittelt
* Dev - Wetter und Google Maps API Key werden nun erst bei Bedarf abgefragt
* Dev - Wetter Einheiten bei den Wettervorhersagen hinzugefügt
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Code Optimierungen

## 4.4.5
* Fix - Fehler beim Ermitteln der richtigen BeTheme Version

## 4.4.4
* Add - Neuer Shortcode "wst_acf_text"
* Add - Neuer Shortcode "wst_post_time"
* Add - Neuer Shortcode "wst_post_modified"
* Add - Neuer Parameter "geo_search_value" für den Shortcode "wst_acf_map_overview"
* Add - Neuer Parameter "words" und "chars" für die Shortcodes "wst_acf_text", "wst_post_title" und "wst_post_excerpt"
* Add - SmartTags an den neuen Shortcodes angepasst
* Add - Neue Konvertierungs Befehle "words" und "chars"
* Add - Neue Formatierungs Funktion "wst_trim_words"
* Fix - Fehler beim Speichern von mehrfach auswählbaren Templates
* Fix - Fehler beim Ermitteln der ACF Color
* Fix - Alle Shortcodes sind nun auch im Adminbereich verfügbar
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Alle Datum und Uhrzeit Shortcodes laden nun den Standardwert aus den Optionen

## 4.4.3
* Dev - Muffin Builder Anpassungen an die Neue Betheme Version 21.1.4

## 4.4.2
* Add - Neue Deprecated und Theme Functions
* Add - Smart Template Loop API
* Fix - Fehler beim Speichern und Anzeigen bei bestimmten Einstellungen
* Dev - Das Wetter wird erst bei gültigen OpenWeatherMap API Key abgefragt
* Dev - Anpassung an den neuen Muffin Builder
* Dev - Diverse Codeoptimierungen

## 4.4.1
* Add - Wetterarten werden als versteckte Taxonomie "smart_weather" mit vordefinierten Kategorien erstellt
* Add - Wetterarten können jedem öffentlichen Post Type als Taxonomie zugeordnet werden
* Add - Abfrage der live Wetterabfrage in jeder Query Abfrage (Grid / Posts)
* Add - Eigene Wetterarten Metabox für Post Types
* Add - Neuer Wetter Shortcode "wst_weather_sunrise" und "wst_weather_sunset"
* Add - Neuer Parameter "fallback" beim Shortcode "wst_post_excerpt"
* Dev - Sprachoptimierungen
* Dev - Entfernung der alten "Taxomomy Metadata" Wetterabfrage
* Dev - Entfernung der Option "wst_weather_taxonomies"
* Dev - Überarbeitung und Optimierung der Wetter API Funktionen
* Dev - Optimierung der Wetter Conditions

## 4.4.0
* Add - Neuer Shortcode "wst_image"
* Add - Neue Shortcodes für Wetterabfragen/vorhersagen implementiert
* Add - Neuer Bedingte-Logik Shortcode "wst_is_weather"
* Add - Alle Bedingte-Logik Shortcodes können nun mit vorangestellten "!" negiert werden
* Add - Polyfill für object-fit und object-position CSS-Eigenschaften
* Add - Erweiterte Term Abfragen beim Shortcode "wst_post_terms"
* Add - Erweiterte Benutzerrollen Einstellungen
* Add - Neue Funktion "wst_get_weather", "wst_get_attachment_id_from_url", "wst_has_smarttag"
* Add - Neue Admin Funktion "wst_get_user_restricted_tpl_ids"
* Add - Neue Funktionen für interne Schleifen Abläufe
* Add - Neuer Settings Typ "multi_select_template"
* Add - Neue Term-Shortcodes "term_is_first_item" und "term_is_last_item"
* Add - Neuer Parameter "words" beim Shortcode "wst_post_excerpt"
* Add - Neuer Condition Parameter "post_permalink"
* Dev - Code und Sprachoptimierungen
* Dev - Optimierungen bei der Shortcode API
* Fix - ACF Map, wenn nur der Parameter Zoom ohne Längen und Breitengrad übergeben wurde
* Fix - Query Fehler bei verschachtelten Beitragsschleifen
* Fix - Fehler bei verschachtelten SmartTags
* Fix - Bessere Einschränkung bei den Log Levels
* Fix - Shortcode "wst_break_loop" gibt keine 1 mehr zurück

## 4.3.3
* Add - Neuer Shortcode "wst_string_replace"
* Add - Neues Condition Feld "queried_object_id"
* Add - Neuer Query Parameter "reset_posts_query"
* Add - Neuer Paramter "marker_icon_field" beim Shortcode "wst_acf_map_overview"
* Add - Neue ACF Funktion "wst_acf_map_get_marker_icon_url"
* Add - Neue Core Funktion "wst_get_smarttag_matches" um SmartTags besser zu erkennen
* Add - SmartTags sind innerhalb von SmartTags möglich
* Add - Neue Core Sprach Funktion "wst_i18n_set_post_language" und "wst_i18n_set_term_language"
* Dev - Bessere Datentyp Erkennung bei der Condition Abfrage
* Dev - Verbesserung der Condition Abfrage "contains" und "in"
* Dev - Diverse Code und Performance Optimierungen
* Dev - Core Funktion "wst_get_matches_between_tags" um den Parameter "return" erweitern
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Bessere Filterung beim Logging System
* Dev - Optmierung der Core Funktion "wst_i18n_get_object_id"
* Dev - Anpassung des Muffin Builder Nonce Feldes an die neue BeTheme Version
* Fix - Beim Speichern einer ACF Optionen Seite wird der Default-Titel nun richtig gesetzt
* Fix - Beim Aufruf des Customizer kommt es zu keinem Fehler mehr
* Fix - Fehler beim Laden von eigenen Seiten Templates
* Fix - Importieren von Muffin Builder Daten in ein Smart Template
* Fix - Der Shortcode "wst_acf_file_type" gibt nun den richtigen Wert zurück

## 4.3.2
* Add - Neuer Shortcode "wst_break_loop"
* Add - Neuer Shortcode "wst_acf_taxonomy"
* Dev - Optimierung der Smart-Tag Integration
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert

## 4.3.1
* Add - Neuer Shortcode "wst_is_mobile"
* Add - Neuer Shortcode "wst_acf_file_size"
* Add - Neuer Shortcode "wst_acf_file_type"
* Add - Neues Condition Feld "is_mobile"
* Add - Neues Condition Feld "is_user_logged_in"
* Add - Neues Condition Feld "loop_row_index"
* Add - Neues Condition Feld "loop_row_first"
* Add - Neues Condition Feld "loop_row_last"
* Add - Neues Condition Feld "loop_row_count"
* Add - Merkliste per WhatsApp teilbar
* Add - Neuer Parameter "visibility" bei den Shortcodes "wst_is_user_logged_in" und "wst_current_user_can"
* Add - Implementation der Funktionen "wst_set_custom_taxonomy_query", "wst_set_same_post_taxonomy_query", "wst_set_acf_taxonomy_query" und "wst_set_weather_taxonomy_query"
* Dev - Überarbeitung der Convertierungsparameter (siehe Hilfe).
* Dev - Diverse Code und Performance Optimierungen
* Dev - Überarbeitung der Funktion "wst_set_wp_query_args" und Implementation neuer Parameter (siehe Hilfe)
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Fix - Änderung der Logging CSS Klasse
* Fix - Woocommerce Content Ausgabe im Template
* Fix - Funktion "wst_get_matches_between_tags" Regex Korrektur

## 4.3.0
* Add - Neuer Shortcode "wst_acf_url"
* Add - Neuer Shortcode "wst_acf_gallery_image"
* Add - Neuer Shortcode "wst_acf_file"
* Add - Neuer Shortcode "wst_language"
* Add - Neuers Condition Feld "parent_id"
* Add - Smart-Tags - Spezielle Platzhalter die bei der Ausgabe einer Seite durch bestimmte Inhalte ersetzt werden.
* Add - Bedingte Abfrage (wst_if) mit Feld "language"
* Add - Neue Core Funktion "wst_get_matches_between_characters" und "wst_get_matches_between_tags"
* Add - Alle Grid Shortcodes können komma getrennte Beitrags Ids als Content verarbeiten
* Add - BeTheme Seiten können nun Shortcodes in HTML Attributen auflösen
* Dev - Hilfe Aktualisiert
* Dev - Sprachen aktualisiert
* Dev - Core Funktion "wst_replace_placeholder_loop_id" erweitert
* Dev - Core Funktion "wst_do_smarttag" optimiert
* Dev - Query Funktion "wst_set_wp_query_args" optimiert
* Dev - Codeoptimierung bei Shortcodes mit Compare Parametern
* Dev - Codeoptimierung bei Shortcodes in HTML Attributen
* Dev - Schleifen in Schleifen mit richtiger WP_Query Abfrage und Rücksetzung
* Dev - Alle Schleifen Shortcodes arbeiten mit der Standard WP_Query Abfrage
* Fix - Shortcodes in HTML Attributen, können nun auch im Content Editor aufgelößt werden
* Fix - PHP Warning bei leeren ACF Standort Feldern
* Fix - PHP Warning bei neuen Beiträgen in Verbindung mit den Muffin Builder
* Fix - Auflösen von verschachtelten Shortcodes in HTML Attributen
* Fix - Fehlerhafter Aufruf der Funktion "wst_format_country_state_string"
* Fix - PHP Warning beim Ermitteln von Wetterarten

## 4.2.8
* Dev - Update auf Markerclusterer Version 1.0.3
* Dev - Hilfe Aktualisiert
* Dev - Sprach Anpassungen
* Dev - Post Type (smart_template) Einstellung "show_in_rest" auf false gesetzt
* Fix - Problem bei der Shortcode Auflösung in HTML Attributen durch Funktion "wst_do_smarttag"

## 4.2.7
* Add - Map Overview mit allen Query Funktionen erweitert
* Dev - Hilfe Angepasst
* Dev - Funktion "wst_set_wp_query_args" optimiert
* Fix - Fehler beim Ermitteln von Meta Felder Einschränkungen bei der Query Funktion

## 4.2.6
* Add - Shortcode-Paramterwerte mit einzelne oder doppelte Anführungszeichen im Help Tab anzeigen
* Add - Neuer Shortcode "wst_gallery"
* Add - Neue Core Funktion "wst_do_smarttag" und "wst_i18n_save_post_translations"
* Add - Neue Formatierungs Funktion "wst_array_is_assoc" und "wst_i18n_save_post_translations"
* Add - Neue i18n Core Funktionen "wst_i18n_save_post_translations", "wst_i18n_save_term_translations" und  "wst_wpml_save_element_translations"
* Fix - Initialisierungs Problem beim Muffin Builder
* Fix - Speicherung von Slashes im Muffin Builder von Beiträgen
* Fix - Fehlerbehebungen bei dem Condition Shortcode
* Fix - Initialisierungs Problem bei der The Grid Hash Filterung
* Dev - Hilfe Aktualisiert
* Dev - Diverse Code Optimierungen
* Dev - Optimierung bei der Funktion "wst_set_wp_query_args"

## 4.2.5
* Add - Eigene ACF Optionen Seiten über die Einstellungen erstellen
* Fix - Fehler beim Verschieben des Editors/Auszugs in ein geklontes Nachrichten Feld
* Fix - Goolgle Map Styles fix
* Dev - Copyright Optimierung bei Mehrsprachigkeit
* Dev - Übersetzungen Aktualisiert
* Dev - Plugin Update Check implementiert
* Dev - Code Optimierungen bei Template Loading
* Dev - Shortcode "wst_background_image" - Optimierung beim Ermitteln des Bildes über die URL

## 4.2.4
* Add - Neuer Parameter "orderby_meta_key" in der Funktion "wst_set_wp_query_args"
* Add - Hilfe Bereich komplett übersetzt
* Dev - Language File Anpassungen
* Dev - Code Optimierungen bei Grid Shortcodes
* Dev - Template Shortcode "wst_include" implementiert
* Dev - Function "wst_google_maps_geocode" mit https Aufruf
* Fix - Template Shortcode mit eckigen Klammern
* Fix - Grid Shortcodes ermitteln nun die richtige interne ID

## 4.2.3
* Add - Google Map Directions in den ACF Maps
* Add - Neuer Shortcode "wst_back_button" welcher einen intelligenten Zurück Button generiert
* Add - Der Conditional Shortcode kann nun auch mit "wst_condition" oder "wst_if" aufgerufen werden
* Add - Beim Conditional Shortcode kann beim Feldparamter ein Konvertierungs-Befehl mitgegeben werden
* Add - Zusätzliche UTF8 Sonderzeichen Bereinigung (per Option) bei Dateinamen
* Add - Komplett neu überarbeiter Hilfe Bereich
* Add - Neuer Settings Tab "Allgemein"
* Add - Shortcode "wst_bookmark_counter" um die Paremeter "class", "style" und "title" erweitert
* Add - Shortcode "wst_background_image" implementiert (Hilfe ausständig)
* Add - Neuer Parameter "offset" ind der Query Function
* Dev - Code Optimierungen in den ACF Map Shortcodes
* Dev - Code Optimierungen bei den Dropdown Templates
* Dev - Code Optimierungen bei den ACF Settings
* Dev - ACF Google Map Hilfs-Funktionen implementiert
* Fix - ACF Map Centering Position Korrektur wenn nur ein Marker vorhanden ist
* Fix - Funktion "wst_dropdown_templates" nicht vorhandene Argumente

## 4.2.2
* Add - Schleifen Shortcodes "wst_acf_repeater" und "wst_acf_post_object" können nun verschachtelt werden
* Add - Neuer Shortcode "wst_mfn_builder_content", welcher den Inhalte des Muffin Builder ausgibt
* Add - Neue Code Function "wst_replace_placeholder_loop_id"
* Add - Function wst_get_bookmarks mit zusätzlichen Parameter "object"
* Dev - Optimierung beim Ermitteln des Posts bei Shortcode "wst_acf_post_object" und "wst_bookmark_posts"
* Dev - Shortcode Optimierung "wst_post_content"
* Dev - Hilfe angepasst
* Dev - Wetter Localisation mit context
* Dev - Function "get_mfn_builder_content" mit Überprüfung
* Dev - Function "wst_replaced_shortcode_field_keys" für Schleifen Shortcodes angepasst
* Localization - Sprachdateien aktualisiert
* Fix - Query Funktion Paramter "related" in Verbindung mit "post__in"
* Fix - ACF Google MAP Api Key Ermittlung
* Fix - Weather Anzeige bei den Taxonomien
* Fix - Static Methode "init_github_updater"

## 4.2.1
* Fix - Conditional Felder geben wieder den richtigen Wert zurück

## 4.2.0
* Add - Wetter Funktionalität
* Add - Wetter API Klasse
* Add - Wetter Parameter für Shortcode wst_the_grid und wst_post
* Add - Wetter admin bar menu
* Add - Wetter Settings
* Add - Wetter Taxonomien Klasse
* Add - Wetter Globale Funktionen
* Add - Countries Klasse hinzugefügt
* Add - Neuer ACF Shortcode wst_acf_link
* Add - Neues Settings Feld "single_select_country" und "multi_select_countries"
* Add - Vordefinierte Condition Values können nun mit Post Metas umgehen
* Add - Neue Core Funktionen "wst_format_country_state_string", "wst_google_maps_geocode", "wst_parse_html_attributes"
* Dev - Hilfe Aktualisiert
* Dev - Code Optimierung bei The Grid Shortcode
* Localization - Languages verschoben und erweitert
* Fix - Query Funktion Fehlerbehebung
* Fix - Fehlende jQuery Ajax Queue Library implementiert

## 4.1.3
* Fix - Fehler beim Aktivieren des Themes wurde bereinigt
* Fix - Tooltip Fehlerbehebung bei Bookmarks
* Dev - Diverse Codeoptimierungen

## 4.1.2
* Fix - Bookmark Code Optimierung (Chrome fix)
* Add - Bookmark Cookie Ablaufdatum kann in den Settings definiert werden
* Add - AjaxQueue bei dem Bookmark Button
* Add - ACF Settings wurden die Feldschlüssel (Move Editor) durch einfache Dropdowns ersetzt
* Add - The Grid Hash Filter wurde das Event "hashchange"
* Add - Google Map Initial Zoom, Latitude und Longitude
* Add - Neue Core Function "wst_setcookie"
* Dev - Optimierung des Zooms / Center setzen bei der Google Map
* Dev - Codeoptimierung Frontend JavaScripte
* Dev - Hilfe aktualisiert
* Fix - Fehlerbehebung beim Ermitteln des Post Meta Wertes in der conditional Abfrage

## 4.1.1
* Add - Function "wst_i18n_get_post_language_information"
* Add - Map Control Optionen
* Add - Map Zoom für Geo Search und Location
* Dev - Script Optimierung bei den MarkerClusterer
* Dev - Code Optimierung bei den Settings
* Dev - Function "wst_acf_get_post_object_ids" und "wst_acf_get_taxonomy_terms" optimiert
* Fix - Fehlerbehebung beim Speichern des Maps Styles
* Fix - ACF Fehlerbehebung beim Ermitteln der internen ID

## 4.1.0
* Add - Logging System
* Add - Query Parameter "wc_gallery_images"

## 4.0.9
* Fix - Fehler bei dem WooCommerce Titel Shortcode behoben

## 4.0.8
* Add - Neues Untermenü "Status"
* Add - Ausführlicher Systemstatus der Webseite
* Add - Werkzeuge "Transients leeren", "Reset Benutzerollen" und "Thumbnail regenerieren"
* Add - Library für Background Prozesse
* Dev - Admin Styles ID umbenannt
* Dev - Klasse WST_Admin_Updates umbenannt in WST_Admin_Plugin_Updates
* Localization - Übersetzungen angepasst

## 4.0.7
* Add - Eigene Sektion für Bookmarks bei den Einstellungen
* Add - Allgemeine Sektion bei den Einstellungen entfernt
* Dev - Berechtigung für die Sektion "Updates" auf update_plugins geändert
* Localization - Translation fixes

## 4.0.6
* Add - Funktion wst_sanitize_text_fields
* Fix - wst_copyright Shortcode

## 4.0.5
* Add - Copyright Shortcode
* Add - Remote Shortcodes
* Add - Einstellungen für Copyright implementiert
* Add - Logiken und Funktionen für Polylang und WPML implementiert
* Dev - i18n Funktionen umbenannt
* Dev - Hilfe Aktualisiert
* Localization - Lanaguage Files hinzugefügt

## 4.0.4
* Add - Eigene Formatierungs Funktionen
* Add - ACF Map Resize Funktion bei Fusion Builder Toggles

## 4.0.3
* Add - GitHub Access Token über die Einstellungen konfigurierbar
* Dev - CSS Anpassungen

## 4.0.2
* Add - Github Updater Implementation

## 4.0.1
* Dev - Initial Release
