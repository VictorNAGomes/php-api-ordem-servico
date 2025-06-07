<?php

namespace App\Models;

use PDO;
use PDOException;


class Database
{
  private static $instance = null;
  private static $connection = null;

  private function __construct()
  {
    $host = DB_HOST;
    $dbname = DB_NAME;
    $username = DB_USER;
    $password = DB_PASS;

    try {
      self::$connection = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
      );
    } catch (\PDOException $e) {
      die("Connection failed: " . $e->getMessage());
    }
  }

  public static function getConnection()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$connection;
  }
}
