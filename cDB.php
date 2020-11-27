<?php


class cDB
{
    private $db;

    public function __construct()
    {
        $this->db = new SQLite3("cache.db");
        return $this->createTable();
    }

    public function createTable()
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

    public function getPageById(int $id)
    {
        $sql = "SELECT * FROM page WHERE id = :id;";
        $query = $this->db->prepare($sql);
        $query->bindValue(':id', $id);
        return $query->execute()->fetchArray(SQLITE3_ASSOC);
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

    public function updateCache(cPage $page)
    {
        if ($this->getPageById($page->id)) {
            $sql = "UPDATE page 
            SET revid = :revid, user = :user, title = :title, text=:text,updateAt=:updateAt,categories=:categories 
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
        $query->bindValue(':text', $page->text);
        $query->bindValue(':updateAt', $page->updateAt);
        $query->bindValue(':categories', implode(',', $page->categories));

        return $query->execute();
    }

    public function __destruct()
    {
        $this->db->close();
    }
}