<?php
/** @var array $data */
$config = $data['config'];
echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php echo implode('', $data['items']); ?>
</urlset>
