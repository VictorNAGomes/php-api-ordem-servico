<?php

namespace App\Models;

use PDO;
use PDOException;

use App\Models\Database;
use App\Core\Validator;

class ClientModel extends Database
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
      $stm = $this->pdo->query("
        SELECT c.*, u.name, u.email, u.role 
        FROM clients c 
        INNER JOIN users u ON c.user_id = u.id
      ");

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
      $stm = $this->pdo->prepare("
        SELECT c.*, u.name, u.email, u.role 
        FROM clients c 
        INNER JOIN users u ON c.user_id = u.id 
        WHERE c.id = ?
      ");
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
      // Valida o CPF
      if (!Validator::validateCPF($data['cpf'])) {
        throw new PDOException("Invalid CPF");
      }

      $this->pdo->beginTransaction();

      // Verifica se o CPF já existe
      $cpfStm = $this->pdo->prepare("SELECT id FROM clients WHERE cpf = ?");
      $cpfStm->execute([$data['cpf']]);
      if ($cpfStm->rowCount() > 0) {
        throw new PDOException("CPF already registered");
      }

      // Inserir na tabela users
      $userStm = $this->pdo->prepare("
        INSERT INTO users (name, email, password, role) 
        VALUES (?, ?, ?, 'client')
      ");
      
      $userStm->execute([
        $data['name'],
        $data['email'],
        password_hash($data['password'], PASSWORD_DEFAULT)
      ]);

      $userId = $this->pdo->lastInsertId();
      
      // Inserir na tabela clients
      $clientStm = $this->pdo->prepare("
        INSERT INTO clients (cpf, address, user_id) 
        VALUES (?, ?, ?)
      ");
      
      $clientStm->execute([
        $data['cpf'],
        $data['address'],
        $userId
      ]);
      
      $clientId = $this->pdo->lastInsertId();

      $this->pdo->commit();
      return $clientId;

    } catch (PDOException $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }

  public function update($id, $data)
  {
    try {
      // Se estiver atualizando o CPF, valida ele
      if (isset($data['cpf'])) {
        if (!Validator::validateCPF($data['cpf'])) {
          throw new PDOException("Invalid CPF");
        }

        // Verifica se o CPF já existe em outro registro
        $cpfStm = $this->pdo->prepare("SELECT id FROM clients WHERE cpf = ? AND id != ?");
        $cpfStm->execute([$data['cpf'], $id]);
        if ($cpfStm->rowCount() > 0) {
          throw new PDOException("CPF already registered");
        }
      }

      $this->pdo->beginTransaction();

      // Buscar o client para obter o user_id
      $client = $this->getById($id);
      if (!$client) {
        throw new PDOException("Client not found");
      }

      // Atualizar a tabela users
      if (isset($data['name']) || isset($data['email']) || isset($data['password'])) {
        $userFields = [];
        $userValues = [];

        if (isset($data['name'])) {
          $userFields[] = "name = ?";
          $userValues[] = $data['name'];
        }
        if (isset($data['email'])) {
          $userFields[] = "email = ?";
          $userValues[] = $data['email'];
        }
        if (isset($data['password'])) {
          $userFields[] = "password = ?";
          $userValues[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $userValues[] = $client['user_id'];

        $userStm = $this->pdo->prepare("
          UPDATE users 
          SET " . implode(", ", $userFields) . "
          WHERE id = ?
        ");
        $userStm->execute($userValues);
      }

      // Atualizar a tabela clients
      if (isset($data['cpf']) || isset($data['address'])) {
        $clientFields = [];
        $clientValues = [];

        if (isset($data['cpf'])) {
          $clientFields[] = "cpf = ?";
          $clientValues[] = $data['cpf'];
        }
        if (isset($data['address'])) {
          $clientFields[] = "address = ?";
          $clientValues[] = $data['address'];
        }

        $clientValues[] = $id;

        $clientStm = $this->pdo->prepare("
          UPDATE clients 
          SET " . implode(", ", $clientFields) . "
          WHERE id = ?
        ");
        $clientStm->execute($clientValues);
      }

      $this->pdo->commit();
      return true;

    } catch (PDOException $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }

  public function delete($id)
  {
    try {
      $this->pdo->beginTransaction();

      // Buscar o client para obter o user_id
      $client = $this->getById($id);
      if (!$client) {
        throw new PDOException("Client not found");
      }

      // Deletar o client (o user será deletado automaticamente pelo ON DELETE CASCADE)
      $stm = $this->pdo->prepare("DELETE FROM clients WHERE id = ?");
      $stm->execute([$id]);

      $this->pdo->commit();
      return true;

    } catch (PDOException $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }
}
