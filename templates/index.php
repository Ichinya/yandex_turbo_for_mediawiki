<?php
/** @var string $currentVersion */
/** @var string $rssTemplate */
/** @var array $config */
/** @var int $countPage */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="./templates/favicon.png">
    <title>Яндекс Турбо для MediWiki</title>
</head>
<body>
<h1>Яндекс Турбо для MediWiki</h1>
<div>версия <?= $currentVersion; ?></div>

<h2>Список доступных лент</h2>
<ol>
    <?php
    for ($i = 0; $i < $countPage; $i++) {
        $strTemplate = ($config['defaultTemplate'] == $rssTemplate) ? '' : "template={$rssTemplate}&";
        $str = "http://{$config['here']}?{$strTemplate}page={$i}";
        ?>
        <li><a href="<?= $str; ?>"><?= $str; ?></a></li>
        <?php
    }
    ?>
</ol>

<?php
if (cUpdate::$needUpdateFromGit) {
    ?>
    <div>Есть новая версия на <a href="https://github.com/Ichinya/yandex_turbo_for_mediawiki" target="_blank">GitHub</a>,
        рекомендуется обновить
    </div>
    <?php
}


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