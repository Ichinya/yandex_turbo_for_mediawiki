<?php


class cDB
{
    public static SQLite3 $db;
    public static int $count_query = 0;
    public static array $query = [];

    public static array $config = [];
    private static ?string $getSQL;

    public function __construct()
    {
        if (!isset(self::$db) || self::$db === NULL) {
            self::$db = new SQLite3("cache.db");
            self::createTablePage();
            self::createTableConfig();
        }

        if (empty(self::$config)) {

            $this->readConfigFromBD();
            self::$getSQL = self::$getSQL ?? $this->getConfig('getSQL');

            if (self::$getSQL === null) {
                self::$getSQL = true;
                try {
                    self::$db->enableExceptions(true);
                    $sql = "SELECT * FROM config WHERE name = :name;";
                    $query = self::$db->prepare($sql);
                    $query->bindValue(':name', 'SQLite3_version');
                    self::$query[] = (self::$getSQL) ? $query->getSQL(self::$getSQL) : "$sql version";
                    $res = $query->execute()->fetchArray(SQLITE3_ASSOC);
                    if ($res == null) {
                        $this->setConfig('SQLite3_version', SQLite3::version()['versionString']);
                    }
                } catch (Exception $e) {
                    self::$getSQL = false;
                }

                $this->setConfig('getSQL', self::$getSQL);
            }
        }
    }


    static function createTablePage()
    {
        self::$count_query++;
        $sql = "CREATE TABLE IF NOT EXISTS page (
                id INTEGER UNIQUE, 
                revid INTEGER,
                user TEXT,
                title VARCHAR, 
                text TEXT,
                url TEXT, 
                updateAt DATETIME,  
                categories TEXT)";
        self::$query[] = $sql;
        return self::$db->exec($sql);
    }

    static function createTableConfig()
    {
        self::$count_query++;
        $sql = "CREATE TABLE IF NOT EXISTS config (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                name VARCHAR UNIQUE ,
                value varchar)";
        self::$query[] = $sql;
        return self::$db->exec($sql);
    }

    /**
     * получение параметров из БД
     * @return array резульат в виде массива
     */
    public function readConfigFromBD()
    {
        self::$count_query++;
        $sql = "SELECT * FROM config ";
        $query = self::$db->query($sql);
        self::$query[] = $sql;
        $result = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $result[$row['name']] = $row['value'];
        }
        self::$config = $result;
        return $result;
    }

    /**
     * получение параметра из БД
     * @param string $name имя параметра
     * @return string|false строковое значение параметра или false
     */
    public function getConfig(string $name)
    {
        return self::$config[$name];
    }

    /**
     * Запись или обновление параметра в БД
     * @param string $name имя парамтра
     * @param string $value значение параметра
     * @return bool
     */
    public function setConfig(string $name, string $value)
    {
        self::$count_query++;
        if ($this->getConfig($name) === false || $this->getConfig($name) === null) {
            $sql = "INSERT INTO config (name, value) 
                VALUES (:name, :value)";
        } else {
            $sql = "UPDATE config SET value = :value WHERE name = :name";
        }
        $query = self::$db->prepare($sql);
        $query->bindValue(':name', $name);
        $query->bindValue(':value', $value);
        self::$query[] = (self::$getSQL) ? $query->getSQL(self::$getSQL) : "$sql $name - $value";
        if (!$query->execute()) {
            return false;
        }
        self::$config[$name] = $value;
        return true;
    }

    public function getPageById(int $id)
    {
        self::$count_query++;
        $sql = "SELECT * FROM page WHERE id = :id;";
        $query = self::$db->prepare($sql);
        $query->bindValue(':id', $id);
        self::$query[] = (self::$getSQL) ? $query->getSQL(self::$getSQL) : "$sql $id";
        return $query->execute()->fetchArray(SQLITE3_ASSOC);
    }

    public function getPageByIds(array $ids)
    {
        if (count($ids) == 0) {
            return [];
        }
        self::$count_query++;
        $sql = "SELECT * FROM page WHERE id in (" . implode(',', $ids) . ");";
        $query = self::$db->query($sql);
        self::$query[] = $sql;
        $result = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $result[$row['id']] = $row;
        }
        return $result;
    }

    public function getEmptyPagesId()
    {
        self::$count_query++;
        $sql = "SELECT id FROM page WHERE revid is null";
        $query = self::$db->query($sql);
        self::$query[] = $sql;
        $result = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $result[$row['id']] = $row['id'];
        }
        return $result;
    }

    public function getEmptyUrl()
    {
        self::$count_query++;
        $sql = "SELECT id FROM page WHERE url is null;";
        $query = self::$db->query($sql);
        self::$query[] = $sql;
        $result = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $row['id'];
        }
        return $result;
    }

    public function getCountPage()
    {
        self::$count_query++;
        $sql = "SELECT COUNT() as count FROM page";
        $query = self::$db->query($sql);
        self::$query[] = $sql;
        return $query->fetchArray(SQLITE3_ASSOC)['count'];
    }

    public function getPageList(int $page, int $count):array
    {
        self::$count_query++;
        $offset = $count * $page;
        $sql = "SELECT * FROM page WHERE url not null ORDER BY updateAt ASC LIMIT {$count} OFFSET {$offset};";
        $query = self::$db->query($sql);
        self::$query[] = $sql;
        $result = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            if (preg_match('/Файл:.+/m', $row['title'])) {
                continue;
            }
            if (preg_match('/Категория:.+/m', $row['title'])) {
                continue;
            }
            if (preg_match('/Шаблон:.+/m', $row['title'])) {
                continue;
            }
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Заполняем таблицу ссылками на страницы
     * @param array $data массив с id страницы => ссылка страницы
     * @return bool
     */
    public function updateUrlByIds(array $data): bool
    {
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $id => $url) {
            self::$count_query++;
            $sql = "UPDATE page SET url = :url WHERE id = :id";
            $query = self::$db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->bindValue(':url', $url);
            self::$query[] = (self::$getSQL) ? $query->getSQL(self::$getSQL) : "$sql $id";
            if (!$query->execute()) {
                return false;
            }
        }
        return true;
    }

    private function clearText($text)
    {
        $templateClear = [
            '/(class|decoding|title|style|width|height)=".+"/mU',
            '~<!--(?!<!)[^\[>].*?-->~s',
            '/href="#[^"]+"/m',
            '/href="\/[^"]+"/m'
        ];
        foreach ($templateClear as $template) {
            $text = preg_replace($template, '', $text);
        }

        $replace = [
            '&#91;' => '[',
            '&#93;' => ']',
            '&lt;' => '<',
            '&gt;' => '>'
        ];
        $text = str_replace(array_keys($replace), array_values($replace), $text);

        return $text;
    }

    public function updateCache(cPage $page)
    {
        self::$count_query++;
        if ($this->getPageById($page->id)) {
            $sql = "UPDATE page 
            SET revid = :revid, user = :user, title = :title, text=:text, updateAt=:updateAt, categories=:categories 
            WHERE id = :id";
        } else {
            $sql = "INSERT INTO page (id,revid,user,title,text,updateAt,categories) 
                VALUES(:id,:revid,:user,:title,:text,:updateAt,:categories)";
        }
        $query = self::$db->prepare($sql);
        $query->bindValue(':id', $page->id);
        $query->bindValue(':revid', $page->revid);
        $query->bindValue(':user', $page->user);
        $query->bindValue(':title', $page->title);
        $query->bindValue(':text', $this->clearText($page->text));
        $query->bindValue(':updateAt', $page->updateAt);
        $query->bindValue(':categories', implode(',', $page->categories));
        self::$query[] = (self::$getSQL) ? $query->getSQL(self::$getSQL) : "$sql {$page->id}";
        return $query->execute();
    }


    public function __destruct()
    {
        self::$db->close();
    }
}