<?php
/** @var string $currentVersion */
/** @var string $rssTemplate */
/** @var array $rssListTemplate */
/** @var array $config */
/** @var int $countPage */
/** @var cPageList $list */
/** @var array $notify */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="./templates/favicon.png">
    <title>Яндекс Турбо для MediaWiki</title>
</head>
<body>
<h1>Яндекс Турбо для MediaWiki</h1>
<div>версия <?= $currentVersion; ?></div>

<?php
if (!empty($notify['warning'])) {
    foreach ($notify['warning'] as $warning): ?>
        <div style="color: red; font-weight: 800;">
            <p><b>!!!</b> <?= $warning; ?></p>
        </div>
    <?php endforeach;
}
?>

<h2>Список доступных лент</h2>
<ol>
    <?php

    foreach ($rssListTemplate as $rssName => $rss) {

        $maxCount = ($rssListTemplate[$rssName]['maxCount']);

        $countPage = ceil($list->countPageDB() / $maxCount);
        for ($i = 0; $i < $countPage; $i++) {
            $url['template'] = ($config['defaultTemplate'] == $rssName) ? null : "{$rssName}";
            $url['page'] = ($i == 0 && $url['template'] != '') ? null : "{$i}";
            $str = "http://{$config['here']}?" . http_build_query($url);
            ?>
            <li><a href="<?= $str; ?>"><?= $str; ?></a></li>
            <?php
        }
    }
    ?>
</ol>

<?php
if (cUpdate::$needUpdateFromGit): ?>
    <div>Есть новая версия на <a href="https://github.com/Ichinya/yandex_turbo_for_mediawiki" target="_blank">GitHub</a>,
        рекомендуется обновить
    </div>
<?php endif;


if (!isset($_GET['page']) && $config['debug']) {
    $db = new cDB();
    echo "Версия SQLite3: " . SQLite3::version()['versionString'];
    echo '<pre>';
    echo 'Количество запросов к кэшу: ' . $db::$count_query . PHP_EOL;
    print_r($db::$query);
    echo '</pre>';
}
?>

</body>
</html>