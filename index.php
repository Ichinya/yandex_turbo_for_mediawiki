<?php
$config = require_once('config.inc.php');

spl_autoload_register(function ($class) {
    $file = $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

// формируем список всех страниц
$list = new cPageList($config);
// модуль парсинга страниц
$parse = new cParse($config['urlAPI']);
echo "<pre>";
print_r($list->listPage);
$db = new cDB();
//print_r($ids = $db->getEmptyUrl());
//print_r($parse->parseUrlByIds($ids));
//print_r($parse->parsePageByPageId(3));




// проверяем все страницы
foreach ($list->listPage as $page) {
    // обновляем кэш
    $parse->updateCacheByPageId($page);
}

//print_r($_GET);

echo "</pre>";

