<?php

class cPageList extends cContent
{
    protected string $fileParams = "params_page_list.json";
    public array $listPage = [];
    private string $author;
    private bool $replaceAuthor;

    public function __construct($config)
    {
        parent::__construct($config['urlAPI']);
        $this->author = $config['defaultAuthor'];
        $this->replaceAuthor = $config['replaceAuthor'];
        $this->getPages();
    }

    public function init()
    {
        $params = $this->initParamFile("params_init.json");
        $pagesIndex = $this->getContentAll($params);
        $pages = [];
        foreach ($pagesIndex as $pageIndex) {
            $page = new cPage(
                $pageIndex['pageid'],
                $pageIndex['title'],
                date(DATE_RFC822, 0));
            $pages[] = $page;
        }
        return $pages;
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
            if ($this->replaceAuthor) {
                $this->listPage[$item['pageid']]->user = $this->author;
            } else {
                $this->listPage[$item['pageid']]->user = (isset($item['user'])) ? $item['user'] : $this->author;
            }

        }
        return $this->listPage;
    }

    public function getPageList(int $page, int $count): array
    {
        $db = new cDB();
        $pagesDB = $db->getPageList($page, $count);
        $pages = [];
        foreach ($pagesDB as $pageDB) {
            $pages[] = $this->convertArrayToPage($pageDB);
        }
        return $pages;
    }

    private function convertArrayToPage(array $pageDB): cPage
    {
        $page = new cPage($pageDB['id'], $pageDB['title'], $pageDB['updateAt']);
        $page->url = $pageDB['url'];
        $page->updateAt = $pageDB ['updateAt'];
        $page->categories = explode(',', $pageDB['categories']);
        $page->user = $pageDB['user'];
        $page->text = $pageDB['text'];
        $page->revid = $pageDB['revid'];
        return $page;
    }

    public function getPageId($id)
    {
        $db = new cDB();
        $pageDB = $db->getPageById($id);
        return $this->convertArrayToPage($pageDB);
    }

    public function countPageDB()
    {
        $db = new cDB();
        return $db->getCountPage();
    }

    public function countPage()
    {
        return count($this->listPage);
    }

}