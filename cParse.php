<?php


class cParse extends cContent
{
    protected $fileParams = "params_page_parse.json";

    public function __construct($urlAPI)
    {
        parent::__construct($urlAPI);

    }

    public function parsePageById($id)
    {
        $params = $this->params;
        $params['pageid'] = $id;
        return $this->getContent($params);
    }

    public function updatePageByParse(cPage &$page)
    {
        $parse = $this->parsePageById($page->id)['parse'];
        $page->text = $parse['text']['*'];
        $page->revid = $parse['revid'];
        if (is_array($parse['categories'])){
            foreach ($parse['categories'] as $category) {
                $page->categories[] = $category['*'];
            }
        }
        return $page;
    }


}