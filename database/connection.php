<?php
namespace database;

use PDO;
use PDOException;

class connection{
    public static function connect()
    {
        try{
            $pdo = new PDO('mysql:host=localhost;dbname=excel', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }
        catch(PDOException $e)
        {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
}