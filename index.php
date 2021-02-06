<?php
if (!file_exists('config.inc.php') && !copy('default_config.php', 'config.inc.php')) {
    echo 'ERROR';
    die();
}
$config = require_once('config.inc.php');
$currentVersion = "1.3.0";

spl_autoload_register(function ($class) {
    $file = 'libs/' . $class . '.php';
    if (is_file($file)) {
        /** @noinspection PhpIncludeInspection */
        require_once $file;
    }
});

// формируем список всех страниц
$list = new cPageList($config);
// модуль парсинга страниц
$parse = new cParse($config['urlAPI']);
// инициализация кэша, если первый запуск, заполняем БД текущими статьями
if (!$list->getConfigDB('init')) {
    if (!$list->getConfigDB('indexPage')) {
        $listInitPages = $list->init();
        foreach ($listInitPages as $page) {
            // заносим список страниц в БД
            $page->user = $config['defaultAuthor'];
            $list->savePageDB($page);
        }
        $list->setConfigDB('indexPage', 1);
    }
    $pagesIds = $list->getEmptyPages();
    foreach ($pagesIds as $id) {
        $page = $list->getPageId($id);
        $parse->updateCacheByPageId($page);
    }
    $list->setConfigDB('init', 1);
}

// проверяем версию скрипта с версией кэша
cUpdate::checkUpdate($currentVersion);
if (!empty($config['email'])) {
    cUpdate::sendNotify($config['email']);
}

$list->getPages();
// проверяем все страницы
$parse->updateCache($list->listPage);
$parse->fillingURL();
// модуль формирования RSS
$rssTemplate = isset($_GET['template']) ? $_GET['template'] : $config['defaultTemplate'];
$rss = new cRSS($rssTemplate);

if (isset($_GET['page']) || isset($_GET['template'])) {
    // формируем страницу rss
    if (empty($_GET['page'])) {
        $_GET['page'] = 0;
    }
    $listPages = $list->getPageList($_GET['page'], $rss->getMaxCount());
    $lenta = $rss->generateRSS($listPages);
    echo($lenta);
} else {
    // формируем список rss

    $fileParams = glob("rss_templates/*.default_params.php");
    $rssListTemplate = [];
    foreach ($fileParams as $fileParam) {
        $nameRss = preg_replace(['#rss_templates/#', '#\.default_params.php#'], '', $fileParam);
        $currentRss = new cRSS($nameRss);
        $rssListTemplate[$nameRss] = [
            'rss' => $currentRss,
            'maxCount' => $currentRss->getMaxCount()
        ];
    }

    ob_start();
    include('templates/index.php');
    $html = ob_get_contents();
    ob_end_clean();
    echo $html;
}


