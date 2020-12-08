<?php


class cDB
{
    private $db;

    public function __construct()
    {
        $this->db = new SQLite3("cache.db");
        $this->createTablePage();
        $this->createTableConfig();
    }

    private function createTablePage()
    {
        $sql = "CREATE TABLE IF NOT EXISTS page (
                id INTEGER UNIQUE, 
                revid INTEGER,
                user TEXT,
                title VARCHAR, 
                text TEXT,
                url TEXT, 
                updateAt DATETIME,  
                categories TEXT)";
        return $this->db->exec($sql);
    }

    private function createTableConfig()
    {
        $sql = "CREATE TABLE IF NOT EXISTS config (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                name VARCHAR UNIQUE ,
                value varchar)";
        return $this->db->exec($sql);
    }

    public function getConfig($name)
    {
        $sql = "SELECT * FROM config WHERE name = :name;";
        $query = $this->db->prepare($sql);
        $query->bindValue(':name', $name);
        return $query->execute()->fetchArray(SQLITE3_ASSOC);
    }

    public function setConfig($name, $value)
    {
        if ($this->getConfig($name) == false) {
            $sql = "INSERT INTO config (name, value) 
                VALUES (:name, :value)";
        } else {
            $sql = "UPDATE config SET value = :value WHERE name = :name";
        }
        $query = $this->db->prepare($sql);
        $query->bindValue(':name', $name);
        $query->bindValue(':value', $value);
        if (!$query->execute()) {
            return false;
        }
        return true;
    }

    public function getPageById(int $id)
    {
        $sql = "SELECT * FROM page WHERE id = :id;";
        $query = $this->db->prepare($sql);
        $query->bindValue(':id', $id);
        return $query->execute()->fetchArray(SQLITE3_ASSOC);
    }

    public function getEmptyPagesId()
    {
        $sql = "SELECT id FROM page WHERE revid is null";
        $query = $this->db->query($sql);
        $result = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $result[$row['id']] = $row['id'];
        }
        return $result;
    }

    public function getEmptyUrl()
    {
        $sql = "SELECT id FROM page WHERE url is null;";
        $query = $this->db->query($sql);
        $result = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $row['id'];
        }
        return $result;
    }

    public function getCountPage()
    {
        $sql = "SELECT COUNT() as count FROM page";
        $query = $this->db->query($sql);
        return $query->fetchArray(SQLITE3_ASSOC)['count'];
    }

    public function getPageList(int $page, int $count)
    {
        $offset = $count * $page;
        $sql = "SELECT * FROM page WHERE url not null ORDER BY updateAt ASC LIMIT {$count} OFFSET {$offset};";
        $query = $this->db->query($sql);
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
            $sql = "UPDATE page SET url = :url WHERE id = :id";
            $query = $this->db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->bindValue(':url', $url);
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
            '/href="#[^"]+"/m'
        ];
        foreach ($templateClear as $template) {
            $text = preg_replace($template, '', $text);
        }
        return $text;
    }

    public function updateCache(cPage $page)
    {
        if ($this->getPageById($page->id)) {
            $sql = "UPDATE page 
            SET revid = :revid, user = :user, title = :title, text=:text, updateAt=:updateAt, categories=:categories 
            WHERE id = :id";
        } else {
            $sql = "INSERT INTO page (id,revid,user,title,text,updateAt,categories) 
                VALUES(:id,:revid,:user,:title,:text,:updateAt,:categories)";
        }
        $query = $this->db->prepare($sql);
        $query->bindValue(':id', $page->id);
        $query->bindValue(':revid', $page->revid);
        $query->bindValue(':user', $page->user);
        $query->bindValue(':title', $page->title);
        $query->bindValue(':text', $this->clearText($page->text));
        $query->bindValue(':updateAt', $page->updateAt);
        $query->bindValue(':categories', implode(',', $page->categories));

        return $query->execute();
    }

    public function __destruct()
    {
        $this->db->close();
    }
}