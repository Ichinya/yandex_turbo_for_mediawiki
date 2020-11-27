<?php
return [
    'urlAPI' => $_SERVER['HTTP_HOST'] . "/api.php", // урл API wiki
    'defaultAuthor' => 'Администратор', // если не найден автор статьи, то будет подставляться этот
    'replaceAuthor' => false // меняет авторов статей на defaultAuthor
];