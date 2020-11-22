<?php


class cContent
{
    protected $urlAPI;
    protected $fileParams;
    protected $params;

    public function __construct($urlAPI)
    {
        $this->urlAPI = $urlAPI;
        if (!file_exists($this->fileParams)) {
            echo 'ERROR READ PARAMS';
            die();
        }
        $json = file_get_contents($this->fileParams);
        $this->params = json_decode($json, true);
    }

    protected function getContent($params)
    {
        $url = $this->urlAPI . "?" . http_build_query($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
//        print_r($output);
        return json_decode($output, true);
    }

    protected function getContentAll($params)
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