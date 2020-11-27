<?php


class cPage
{

    var $id;
    var $title;
    var $updateAt;
    var $text;
    var $categories = [];
    var $revid;
    var $user;
    var $url;

    public function __construct($id, $title, $time)
    {
        $this->id = $id;
        $this->title = $title;
        $this->updateAt = $time;
    }

}