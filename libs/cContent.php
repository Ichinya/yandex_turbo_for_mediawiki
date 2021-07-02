<?php


abstract class cContent
{
    protected string $urlAPI;
    protected string $fileParams;
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

    public function initParamFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        $json = file_get_contents($file);
        return json_decode($json, true);
    }

    /**
     * получаем данные от АПИ
     * @param array $params параметры обращения
     * @return array данные от АПИ
     */
    protected function getContent(array $params): ?array
    {
        $url = $this->urlAPI . "?" . http_build_query($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        $ret = json_decode($output, true);
        if (empty($ret)) {
            $ret = [];
        }
        return $ret;
    }

    /**
     * при запросах, где ответы от АПИ превышают одну страницу - считываем все страницы
     * @param array $params параметры обращения к АПИ
     * @return array|null ответ от АПИ
     */
    protected function getContentAll(array $params)
    {
        $out = $this->getContent($params);
        $result = $out['query'][$params['list']];
        while ($out['query-continue'][$params['list']]['rccontinue'] != "" ||
            $out['query-continue'][$params['list']]['apcontinue'] != "") {
            $params['rccontinue'] = $out['query-continue'][$params['list']]['rccontinue'];
            $params['apcontinue'] = $out['query-continue'][$params['list']]['apcontinue'];
            $out = $this->getContent($params);
            $result = array_merge($result, $out['query'][$params['list']]);
        }
        return $result;
    }
}