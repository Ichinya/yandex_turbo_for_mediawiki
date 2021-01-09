<?php
$server = $_SERVER['HTTP_HOST'];
return [
    'defaultDate' => 1606780800, // 2020.12.01 00:00
    'debug' => false,
    'server' => $server,
    'here' => $server . $_SERVER['REQUEST_URI'],
    // урл API wiki
    'urlAPI' => $server . "/api.php",
    // если не найден автор статьи, то будет подставляться этот
    'defaultAuthor' => 'Admin',
    // меняет авторов статей на defaultAuthor
    'replaceAuthor' => false,
    // шаблон по-умолчанию
    'defaultTemplate' => 'turbo',
    // электронный ящик для сообщений
    'email' => ''
];