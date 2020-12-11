<?php
$config = require_once('config.inc.php');

spl_autoload_register(function ($class) {
    $file = $class . '.php';
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

$list->getPages();
// проверяем все страницы
foreach ($list->listPage as $page) {
    // обновляем кэш
    $parse->updateCacheByPageId($page);
}
// модуль формирования RSS
$rssTemplate = isset($_GET['template']) ? $_GET['template'] : $config['defaultTemplate'];
$rss = new cRSS($rssTemplate);

if (isset($_GET['page'])) {
    // формируем страницу rss
    $listPages = $list->getPageList($_GET['page'], $rss->getMaxCount());
    $lenta = $rss->generateRSS($listPages);
    echo($lenta);
} else {
    // формируем список rss
    $countPage = ceil($list->countPageDB() / $rss->getMaxCount());
    echo "<pre>";
    for ($i = 0; $i < $countPage; $i++) {
        $strTemplate = ($config['defaultTemplate'] == $rssTemplate) ? '' : "template={$rssTemplate}&";
        echo $str = "http://{$config['here']}?{$strTemplate}page={$i}<br />";
    }
    echo "</pre>";
}
