<?php


class cContent
{
    protected $urlAPI;
    protected $fileParams;
    protected $params;

    /**
     * считываем настройки обращения к АПИ mediawiki
     * cContent constructor.
     * @param array настройки
     */
    public function __construct($urlAPI)
    {
        $this->urlAPI = $urlAPI;
        if (!file_exists($this->fileParams)) {
            echo 'ERROR READ PARAMS';
            die();
        }
        $json = file_get_contents($this->fileParams);
        return $this->params = json_decode($json, true);
    }

    /**
     * получаем данные от АПИ
     * @param array $params параметры обращения
     * @return array данные от АПИ
     */
    protected function getContent(array $params): array
    {
        $url = $this->urlAPI . "?" . http_build_query($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     * при запросах, где ответы от АПИ превышают одну страницу - считываем все страницы
     * @param array $params параметры обращения к АПИ
     * @return array ответ от АПИ
     */
    protected function getContentAll(array $params): array
    {
        $out = $this->getContent($params);
        $result = $out['query']['recentchanges'];
        while ($out['query-continue']['recentchanges']['rccontinue'] != "") {
            $params['rccontinue'] = $out['query-continue']['recentchanges']['rccontinue'];
            $out = $this->getContent($params);
            $result = array_merge($result, $out['query']['recentchanges']);
        }
        return $result;
    }
}