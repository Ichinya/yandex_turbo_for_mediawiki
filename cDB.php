<?php


class cDB
{
    var $db;

    public function __construct()
    {
        $this->db = new SQLite3("cache.db");

    }

    public function createTable()
    {
//        $sql = "CREATE TABLE IF NOT EXISTS page (id id)";
        $sql = "CREATE TABLE users(id INTEGER, name TEXT, age INTEGER)";
//        $sql = $sql = "INSERT INTO users (name, age) VALUES ('SKDjh', 25)";
        $result = $this->db->exec($sql);
    }

    public function __destruct()
    {
        $this->db->close();
    }
}