<?php
/** @var array $data */
/** @var cPage $page */
$page = $data['page'];
$params = $data['config'];
?>
<url>
    <loc><?= $page->url; ?></loc>
    <lastmod><?= date('Y-m-H', strtotime($page->updateAt)); ?></lastmod>
    <changefreq><?= $params['changefreq']; ?></changefreq>
    <priority><?= $params['priority']; ?></priority>
</url>