<?php


class cUpdate
{
    private const maxPage = 100;
    private const periodCheck = 24 * 60 * 60;

    private static ?cDB $db = null;
    private static string $version = "";
    public static bool $needUpdateFromGit = false;

    public function __construct()
    {
        self::$db = new cDB();
    }

    public static function checkUpdate($version)
    {
        if (self::$db === null) {
            self::$db = new cDB();
        }

        $versionDB = self::$db->getConfig('version');

        if ($versionDB === null) {
            self::$db->setConfig('version', $version);
        }

        if (self::intVersion($versionDB) < self::intVersion($version)) {
            self::update();
           // self::$db->setConfig('version', $version);
        }

        self::getVersion();
        if (self::intVersion(self::$version) > self::intVersion($version)) {
            self::$needUpdateFromGit = true;
        }
    }

    public static function sendNotify(string $mail = ''): bool
    {
        return mail($mail, 'Тема', 'Сообщение');
    }

    /**
     * Выдает версию на Git
     * @return string Версия на Git
     */
    public static function getVersion(): string
    {
        if (empty(self::$version)) {
            self::checkGitVersion();
        }
        return self::$version;
    }

    /**
     * преобразовывает строковый параметр версии в число
     * @param string|null $str
     * @return int
     */
    private static function intVersion(?string $str): int
    {
        if (!is_string($str)) {
            $str = '';
        }
        return (int)preg_replace('/[^\d]/m', '', $str);
    }

    /**
     * Проверка версии на Git, проверяет максимум раз в сутки
     */
    private static function checkGitVersion()
    {
        if (self::$db === null) {
            self::$db = new cDB();
        }

        $configGitDateCheck = self::$db->getConfig('git_date_check');
        $configGitVersion = self::$db->getConfig('git_version');
        $nextDay = $configGitDateCheck + self::periodCheck;

        if (time() < $nextDay && self::intVersion($configGitVersion) > 100) {
            self::$version = $configGitVersion;
            return;
        }

        $url = "https://api.github.com/repos/ichinya/yandex_turbo_for_mediawiki/git/refs/tags";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "yandex_turbo_for_mediawiki");
        $r = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($r, true);
        $ref = $response[count($response) - 1]['ref'];
        $versionGit = str_replace('refs/tags/', '', $ref);

        if (self::intVersion($versionGit) > 100) {
            self::$db->setConfig('git_version', $versionGit);
            self::$db->setConfig('git_date_check', time());
            self::$version = $versionGit;
        }
    }


    private static function update()
    {
        self::update111to120();
    }

    private static function update111to120()
    {
        $replace = [
            '&#91;' => '[',
            '&#93;' => ']',
            '&lt;' => '<',
            '&gt;' => '>'
        ];

        $pageCurrent = 0;
        while (true) {
            $pageList = self::$db->getPageList($pageCurrent, self::maxPage);
            foreach ($pageList as $page) {
                $oldText = $page['text'];
                $page['text'] = str_replace(array_keys($replace), array_values($replace), $page['text']);
                if ($oldText != $page['text']) {
                    echo 'find' . PHP_EOL;
                    self::$db->updateCache(cPage::convertArrayToPage($page));
                }
            }
            if (count($pageList) == 0) {
                break;
            }
            $pageCurrent++;
        }

    }

}