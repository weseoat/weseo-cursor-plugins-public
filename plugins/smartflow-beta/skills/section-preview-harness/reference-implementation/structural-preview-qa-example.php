<?php
/**
 * Preview QA: structurally check all preview URLs of a section.
 * Call: php intro-preview-qa.php   (plain CLI PHP, no WP needed)
 *
 * This is a generic EXAMPLE. Replace the base URL, the variant slugs, and the
 * expected content/image/brand values with the project's real values. The
 * checks are intentionally structural (HTTP status, variant root class,
 * presence/absence of title/image, body classes), not pixel QA.
 */

$base = 'https://example.weseo.dev/section-preview/intro/';

$cases = array(
    'split' => array(
        'variant' => 'split', 'brand' => null,
        'title' => 'Example headline', 'subtitle' => 'Example subline.',
        'img' => 'example-image', 'wrap' => true,
    ),
    'split-brand-a' => array(
        'variant' => 'split', 'brand' => 'brand-a',
        'title' => 'Example headline', 'subtitle' => 'Example subline.',
        'img' => 'example-image', 'wrap' => true,
    ),
    'centered-image' => array(
        'variant' => 'centered-image', 'brand' => null,
        'title' => 'Example headline', 'subtitle' => 'Example subline.',
        'img' => 'example-image', 'wrap' => true,
    ),
    'fullscreen-image' => array(
        'variant' => 'fullscreen-image', 'brand' => null,
        'title' => null, 'subtitle' => null,
        'img' => 'example-image', 'wrap' => false,
    ),
    'text-only' => array(
        'variant' => 'text-only', 'brand' => null,
        'title' => 'Example headline', 'subtitle' => 'Example subline.',
        'img' => null, 'wrap' => true,
    ),
);

$fails = 0;

foreach ($cases as $slug => $c) {
    $url = $base . $slug . '/';
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 30,
    ));
    $html = (string) curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    $errors = array();

    if ($status !== 200) $errors[] = "HTTP {$status}";

    // Only evaluate the preview area (#primary) - a global second instance of
    // the section (reference popup) can live outside it. Use strpos instead of
    // regex, because lazy quantifiers on the large document blow the backtrack
    // limit. End marker = first </section> after the anchor: the HTML minifier
    // strips comments like <!-- #primary --> from the served output.
    $primary = '';
    $anchor  = strpos($html, 'data-preview-variant="' . $slug . '"');
    if ($anchor !== false) {
        $end = strpos($html, '</section>', $anchor);
        if ($end !== false) {
            $primary = substr($html, $anchor, $end - $anchor);
        }
    }
    if ($primary === '') {
        $errors[] = '#primary with data-preview-variant missing';
    }

    if ($primary !== '') {
        if (strpos($primary, 'wso-intro-variant-' . $c['variant']) === false) $errors[] = "class wso-intro-variant-{$c['variant']} missing";

        $has_wrap = strpos($primary, 'wso-wrap-content') !== false;
        if ($has_wrap !== $c['wrap']) $errors[] = $c['wrap'] ? 'content wrap missing' : 'content wrap not suppressed';

        if ($c['title'] !== null && strpos($primary, $c['title']) === false) $errors[] = "title \"{$c['title']}\" missing";
        if ($c['title'] === null && preg_match('/<h1/', $primary)) $errors[] = 'unexpected h1';
        if ($c['subtitle'] !== null && strpos($primary, $c['subtitle']) === false) $errors[] = "subline \"{$c['subtitle']}\" missing";

        if ($c['img'] !== null && strpos($primary, $c['img']) === false) $errors[] = "image {$c['img']} missing";
        if ($c['img'] === null && strpos($primary, '<img') !== false) $errors[] = 'unexpected image';
    }

    // Body classes (outside #primary)
    if (preg_match('/<body[^>]*class="([^"]*)"/', $html, $b)) {
        if (strpos($b[1], 'wso-section-preview') === false) $errors[] = 'body class wso-section-preview missing';
        if ($c['brand'] !== null && strpos($b[1], $c['brand']) === false) $errors[] = "body class {$c['brand']} missing";
    } else {
        $errors[] = 'body tag not found';
    }

    if ($errors) {
        $fails++;
        echo "FAIL {$slug}: " . implode('; ', $errors) . "\n";
    } else {
        echo "PASS {$slug}\n";
    }
}

$total = count($cases);
echo $fails === 0 ? "\nAll {$total} previews PASS\n" : "\n{$fails} variant(s) FAIL\n";
exit($fails === 0 ? 0 : 1);
