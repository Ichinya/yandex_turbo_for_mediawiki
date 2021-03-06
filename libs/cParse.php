<?php


class cParse extends cContent
{
    protected array $paramsParseUrl;
    protected cDB $db;

    public function __construct($urlAPI)
    {
        $this->fileParams = "params/params_url_parse.json";
        $this->paramsParseUrl = parent::__construct($urlAPI);
        $this->fileParams = "params/params_page_parse.json";
        $this->params = parent::__construct($urlAPI);
        $this->db = new cDB();
    }

    /**
     * по массиву идентификаторов формируется массив ссылок на страницы
     * @param array $ids массив идентификаторов страниц
     * @return array массив вида ['ИД страницы' => 'ссылка']
     */
    public function parseUrlByIds(array $ids): array
    {
        $params = $this->paramsParseUrl;
        $request = array_chunk($ids, 50);
        $out = [];
        foreach ($request as $value) {
            $params['pageids'] = implode('|', $value);
            $out = array_merge($out, $this->getContent((array)$params)['query']['pages']);
        }
        $result = [];
        foreach ($out as $item) {
            if ($item['missing']) {
                continue;
            }
            $result[$item['pageid']] = $item['fullurl'];
        }
        return $result;
    }

    /**
     * функция прокладка, которая ищет пустые поля ссылок на страницы и заполняет их
     */
    function fillingURL()
    {
        $ids = $this->db->getEmptyUrl();
        $urls = $this->parseUrlByIds($ids);
        $this->db->updateUrlByIds($urls);
    }

    /**
     * получаем данные страницы (текст в формате html и список категорий)
     * @param int $id ИД страницы
     * @return array|null массив с данными
     */
    private function parsePageByPageId(int $id)
    {
        $params = $this->params;
        $params['pageid'] = $id;
        return $this->getContent((array)$params)['parse'];
    }

    /**
     * Обновляем объект данными парсинга (текст в формате html и списком категории)
     * @param cPage $page
     * @return cPage
     */
    private function updatePageByPage(cPage &$page)
    {
        $parse = $this->parsePageByPageId($page->id);
        if (!is_array($parse)) {
            return false;
        }
        $page->text = $parse['text']['*'];
        $page->revid = $parse['revid'];
        if (is_array($parse['categories'])) {
            foreach ($parse['categories'] as $category) {
                if (trim($category['*']) == '') {
                    continue;
                }
                $page->categories[] = $category['*'];
            }
        }
        return $page;
    }

    private function getCachePage(array $pageList)
    {
        $ids = [];
        foreach ($pageList as $page) {
            $ids[] = $page->id;
        }
        return $this->db->getPageByIds($ids);
    }

    public function updateCache(array $pageList)
    {
        $cachePage = $this->getCachePage($pageList);
        foreach ($pageList as $page) {
            if ($page->revid === 0) {
                continue;
            }
            if (!$cachePage[$page->id] ||
                strtotime($page->updateAt) > strtotime($cachePage[$page->id]['updateAt']) ||
                empty($page->revid)) {
                // парсим страницу и записываем в БД
                $this->updatePageByPage($page);
                $this->db->updateCache($page);
            }
        }
    }

    /**
     * Получаем актуальные данные из кэша, либо обновляем их
     * @param cPage $page
     * @return bool
     */
    public function updateCacheByPageId(cPage $page)
    {
        // получаем данные из БД
        $pageCache = $this->db->getPageById($page->id);
        // нет страницы в кэше

        if ($page->revid === 0) {
            return false;
        }

        if (!$pageCache || strtotime($page->updateAt) > strtotime($pageCache['updateAt']) || empty($page->revid)) {
            // парсим страницу и записываем в БД
            $this->updatePageByPage($page);
            $this->db->updateCache($page);
        }
        // заполняем пустые поля ссылок на страницы
        return true;
    }

}