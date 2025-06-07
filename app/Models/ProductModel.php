<?php

namespace App\Models;

use PDO;
use PDOException;

use App\Models\Database;

class ProductModel extends Database
{
  private $pdo;

  public function __construct()
  {
    $conn = $this->getConnection();
    $this->pdo = $conn;
  }

  public function getAll()
  {
    try {
      $stm = $this->pdo->query("SELECT * FROM products");

      if ($stm->rowCount() > 0) {
          return $stm->fetchAll(PDO::FETCH_ASSOC);
      } else {
        return false;
      }
    } catch (PDOException $e) {
      return false;
    }
  }

  public function getById($id)
  {
    try {
      $stm = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
      $stm->execute([$id]);

      if ($stm->rowCount() > 0) {
        return $stm->fetch(PDO::FETCH_ASSOC);
      } else {
        return false;
      }
    } catch (PDOException $e) {
      return false;
    }
  }

  public function create($data)
  {
    try {
      $stm = $this->pdo->prepare(
        "INSERT INTO products (description, warranty_period, status) VALUES (?, ?, ?)"
      );
      
      $stm->execute([
        $data['description'],
        $data['warranty_period'],
        $data['status'] ?? 'active'
      ]);

      return $this->pdo->lastInsertId();
    } catch (PDOException $e) {
      return false;
    }
  }

  public function update($id, $data)
  {
    try {
      $stm = $this->pdo->prepare(
        "UPDATE products SET description = ?, warranty_period = ?, status = ? WHERE id = ?"
      );
      
      $stm->execute([
        $data['description'],
        $data['warranty_period'],
        $data['status'] ?? 'active',
        $id
      ]);

      return $stm->rowCount() > 0;
    } catch (PDOException $e) {
      return false;
    }
  }

  public function delete($id)
  {
    try {
      $stm = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
      $stm->execute([$id]);
      
      return $stm->rowCount() > 0;
    } catch (PDOException $e) {
      return false;
    }
  }
}
