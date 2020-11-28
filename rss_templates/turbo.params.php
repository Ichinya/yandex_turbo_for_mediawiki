<?php
return [
    // максимальное количество элементов в ленте
    'maxCount' => 500,
    // Название RSS-канала.
    // Если экспортируется содержимое всего сайта, укажите название сайта. Если экспортируется раздел сайта, укажите только название раздела */
    'title' => 'Название канала',
    // Описание канала одним предложением. Не используйте HTML-разметку.
    'description' => 'Описание канала',
    // Домен сайта, данные которого транслируются.
    'link' => 'http://' . $_SERVER['HTTP_HOST'],
    // Блок с рубриками, который указан на основной странице сайта.
    // ссылка формируется link/url
    'breadcrumblist' => [
        ['url' => '', 'text' => 'Главная'],
        ['url' => 'Категория:Статьи', 'text' => 'Категории']
    ],
];