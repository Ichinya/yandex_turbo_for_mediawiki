<?php
/** @var array $data */
$config = $data['config'];
echo'<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss xmlns:yandex="http://news.yandex.ru"
     xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:turbo="http://turbo.yandex.ru"
     version="2.0">
    <channel>
        <!-- Информация о сайте-источнике -->
        <title><?= $config['title']; ?>></title>
        <link><?= $config['link']; ?></link>
        <description><?= $config['description']; ?></description>
        <language>ru</language>
        <turbo:analytics></turbo:analytics>
        <turbo:adNetwork></turbo:adNetwork>
        <?php echo implode('', $data['items']); ?>
    </channel>
</rss>