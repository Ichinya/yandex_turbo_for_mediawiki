<?php

class cPageList extends cContent
{
    protected $fileParams = "params_page_list.json";
    var $listPage = [];
    private $author;
    private $repaceAuthor;

    public function __construct($config)
    {
        parent::__construct($config['urlAPI']);
        $this->author = $config['defaultAuthor'];
        $this->repaceAuthor = $config['replaceAuthor'];
        $this->getPages();
    }

    function getPages()
    {
        $list = $this->getContentAll($this->params);

        foreach ($list as $item) {
            $this->listPage[$item['pageid']] = new cPage(
                $item['pageid'],
                $item['title'],
                $item['timestamp']);
            $this->listPage[$item['pageid']]->revid = $item['revid'];
            if ($this->repaceAuthor) {
                $this->listPage[$item['pageid']]->user = $this->author;
            } else {
                $this->listPage[$item['pageid']]->user = (isset($item['user'])) ? $item['user'] : $this->author;
            }

        }
        return $this->listPage;
    }
}