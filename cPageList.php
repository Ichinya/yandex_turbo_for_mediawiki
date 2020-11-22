<?php

class cPageList extends cContent
{
    protected $fileParams = "params_page_list.json";
    var $listPage = [];

    public function __construct($urlAPI)
    {
        parent::__construct($urlAPI);
        $this->getPages();
    }

    function getPages()
    {
        $list = $this->getContentAll($this->params);

        foreach ($list as $item) {
            $this->listPage[$item['pageid']] = new cPage($item['pageid'], $item['title'], $item['timestamp']);
        }
        return $this->listPage;
    }
}