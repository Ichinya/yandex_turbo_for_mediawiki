<?php


class cRSS
{
    private string $item;
    private string $rss;
    private array $params;

    public function __construct($template)
    {
        // проверяем наличие шаблона
        $this->rss = "rss_templates/{$template}.rss.php";
        $this->item = "rss_templates/{$template}.item.php";
        $paramsFile = "rss_templates/{$template}.params.php";
        $defaultParamsFile = "rss_templates/{$template}.default_params.php";
        if (!file_exists($paramsFile) && !copy($defaultParamsFile, $paramsFile)) {
            exit('шаблон не найден');
        }
        if (!file_exists($this->rss) || !file_exists($this->item) || !file_exists($paramsFile)) {
            exit('шаблон не найден');
        }
        /** @noinspection PhpIncludeInspection */
        $this->params = require($paramsFile);
        return true;
    }

    /**
     * формирует ленту по массиву страниц
     * @param array $pages массив страниц
     * @return false|string
     */
    public function generateRSS(array $pages)
    {
        header('Content-Type: application/xml; charset=utf-8');
        $items = [];
        foreach ($pages as $page) {
            $items[] = $this->getPartTemplate('item', ['config' => $this->params, 'page' => $page]);
        }
        return $this->getPartTemplate('rss', ['config' => $this->params, 'items' => $items]);
    }

    /**
     * Метод возвращает максимальное количество элементов в ленте
     * @return integer максимальное количество элементов в ленте
     */
    public function getMaxCount(): int
    {
        return $this->params['maxCount'];
    }

    /**
     * формирует по шаблону и переданных данных текст
     * @param string $part название шаблона
     * @param array|null $data данные для шаблона
     * @return false|string сформированный текст
     */
    private function getPartTemplate(string $part, array $data = null)
    {
        ob_start();
        if (!empty($this->{$part})) {
            /** @noinspection PhpIncludeInspection */
            include($this->{$part});
        }
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}