<?php
$config = require_once('config.inc.php');

spl_autoload_register(function ($class) {
    $file = $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$list = new cPageList($config['urlAPI']);
$parse = new cParse($config['urlAPI']);
echo "<pre>";
print_r($parse->updatePageByParse($list->listPage[2]));
echo "</pre>";

