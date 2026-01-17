<?php

$template = file_get_contents(__DIR__ . '/index.html');

$folders = file(__DIR__ . '/folder.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$descs   = file(__DIR__ . '/detail.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$extras  = file(__DIR__ . '/tambah.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$images  = file(__DIR__ . '/img.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);


$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];
$path   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$baseUrl = $scheme . '://' . $host . $path;

if (!$folders || !$descs || !$extras || !$images) {
    die('folder.txt / detail.txt / tambah.txt / img.txt tidak boleh kosong');
}

$sitemapUrls = [];

foreach ($folders as $currentFolder) {

    $currentFolder = trim($currentFolder);
    if ($currentFolder === '') continue;

  // ===== BASIC DATA =====
$title       = $currentFolder;
$description = $descs[array_rand($descs)];
$tambahan    = $extras[array_rand($extras)];
$gambar      = $images[array_rand($images)];
$canonical   = $baseUrl . '/' . $currentFolder . '/';


    // ===== INTERNAL LINK =====
    $links = [];
    foreach ($folders as $targetFolder) {

        $targetFolder = trim($targetFolder);
        if ($targetFolder === '' || $targetFolder === $currentFolder) continue;

        $url = $baseUrl . '/' . $targetFolder . '/';
        $anchor = ucwords(str_replace('-', ' ', $targetFolder));

        $links[] = '<a href="' . $url . '" class="link-item">' . htmlspecialchars($anchor) . '</a>';
    }

    shuffle($links);
    $internalLinks = implode("\n", array_slice($links, 0, 4));

    // ===== BUILD HTML =====
    $html = str_replace(
    ['{{TITLE}}', '{{DESCRIPTION}}', '{{TAMBAHAN}}', '{{GAMBAR}}', '{{INTERNAL_LINKS}}', '{{CANONICAL}}'],
    [$title, $description, $tambahan, $gambar, $internalLinks, $canonical],
    $template
);


    // ===== SAVE PAGE =====
    $dir = __DIR__ . '/' . $currentFolder;
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    file_put_contents($dir . '/index.html', $html);

    // ===== ADD TO SITEMAP =====
    $sitemapUrls[] = $canonical;
}

//
// ===== GENERATE SITEMAP.XML =====
//
$xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$xml .= "<urlset xmlns=\"https://www.sitemaps.org/schemas/sitemap/0.9\">\n";

$date = date('Y-m-d');

foreach ($sitemapUrls as $url) {
    $xml .= "  <url>\n";
    $xml .= "    <loc>{$url}</loc>\n";
    $xml .= "    <lastmod>{$date}</lastmod>\n";
    $xml .= "    <changefreq>daily</changefreq>\n";
    $xml .= "    <priority>0.8</priority>\n";
    $xml .= "  </url>\n";
}

$xml .= "</urlset>";

file_put_contents(__DIR__ . '/sitemap.xml', $xml);

echo "✔ Generate halaman + canonical + sitemap.xml selesai";
