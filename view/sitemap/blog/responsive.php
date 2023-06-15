<?php
header('Content-type: application/xml');


$output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
echo $output;
?>
<?php
$courses = ORM::for_table("blog")->where_null("courseID")->order_by_desc("id")->find_many();

foreach($courses as $course) {
    ?>
    <url>
        <loc><?= SITE_URL ?>blog/<?= $course->slug ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php
}
?>
</urlset>