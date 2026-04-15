<?php

namespace App\Config;

use PDO;

class Database
{
  public static function connect(array $config): PDO
  {
    $pdo = new PDO(
      "mysql:host={$config["db_host"]};port={$config["db_port"]};dbname={$config["db_name"]};charset=utf8mb4",
      $config["db_user"],
      $config["db_pass"],
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
  }
}
