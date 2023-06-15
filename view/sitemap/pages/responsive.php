<?php
header('Content-type: application/xml');


$output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
echo $output;

$pages = array(
        "terms-conditions-supply-products",
        "terms-website-use",
        "website-acceptable-use-policy",
        "privacy-notice",
        "cookie-policy",
        "your-information",
        "teens-unite",
        "testimonials",
        "achievers",
        "blog",
        "staff-training",
        "support",
        "support/help-articles",
);

foreach($pages as $page) {
    ?>
    <url>
        <loc><?= SITE_URL ?><?= $page ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php
}

?>
</urlset>