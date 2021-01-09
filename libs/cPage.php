<?php


class cPage
{

    var $id;
    var $title;
    var $updateAt;
    var $text;
    public array $categories = [];
    var $revid;
    var $user;
    var $url;

    public function __construct($id, $title, $time)
    {
        $this->id = $id;
        $this->title = $title;
        $this->updateAt = $time;
    }


    public static function convertArrayToPage(array $pageDB): cPage
    {
        $page = new cPage($pageDB['id'], $pageDB['title'], $pageDB['updateAt']);
        $page->url = $pageDB['url'];
        $page->updateAt = $pageDB ['updateAt'];
        $page->categories = ($pageDB['categories'] == '') ? [] : explode(',', $pageDB['categories']);
        $page->user = $pageDB['user'];
        $page->text = $pageDB['text'];
        $page->revid = $pageDB['revid'];
        return $page;
    }
}