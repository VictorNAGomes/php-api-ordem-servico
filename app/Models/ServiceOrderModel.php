<?php

namespace App\Models;

use PDO;
use PDOException;

use App\Models\Database;

class ServiceOrderModel extends Database
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
        SELECT 
          so.*,
          c.cpf as client_cpf,
          c.address as client_address,
          u.name as client_name,
          u.email as client_email,
          p.description as product_description,
          p.warranty_period as product_warranty,
          s.name as status_name,
          cb.name as created_by_name
        FROM service_orders so
        INNER JOIN clients c ON so.client_id = c.id
        INNER JOIN users u ON c.user_id = u.id
        INNER JOIN products p ON so.product_id = p.id
        INNER JOIN status s ON so.status_id = s.id
        INNER JOIN users cb ON so.created_by = cb.id
        ORDER BY so.opening_date DESC
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
        SELECT 
          so.*,
          c.cpf as client_cpf,
          c.address as client_address,
          u.name as client_name,
          u.email as client_email,
          p.description as product_description,
          p.warranty_period as product_warranty,
          s.name as status_name,
          cb.name as created_by_name
        FROM service_orders so
        INNER JOIN clients c ON so.client_id = c.id
        INNER JOIN users u ON c.user_id = u.id
        INNER JOIN products p ON so.product_id = p.id
        INNER JOIN status s ON so.status_id = s.id
        INNER JOIN users cb ON so.created_by = cb.id
        WHERE so.id = ?
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
      $this->pdo->beginTransaction();

      // Verificar se o produto existe
      $productStm = $this->pdo->prepare("SELECT id FROM products WHERE id = ?");
      $productStm->execute([$data['product_id']]);
      if ($productStm->rowCount() === 0) {
        throw new PDOException("Product not found");
      }

      // Verificar se o cliente existe pelo CPF
      $clientStm = $this->pdo->prepare("SELECT id FROM clients WHERE cpf = ?");
      $clientStm->execute([$data['client_cpf']]);
      $client = $clientStm->fetch(PDO::FETCH_ASSOC);
      $clientId = null;
    
      
      if (!$client) {
          // Criar novo cliente
          if (!isset($data['client_name']) || !isset($data['client_email']) || !isset($data['client_password'])) {
              throw new PDOException("Client data is required to create new client");
        }

        // Criar usuário
        $userStm = $this->pdo->prepare("
          INSERT INTO users (name, email, password, role) 
          VALUES (?, ?, ?, 'client')
        ");
        $userStm->execute([
          $data['client_name'],
          $data['client_email'],
          password_hash($data['client_password'], PASSWORD_DEFAULT)
        ]);
        $userId = $this->pdo->lastInsertId();

        // Criar cliente
        $clientStm = $this->pdo->prepare("
          INSERT INTO clients (cpf, address, user_id) 
          VALUES (?, ?, ?)
        ");
        $clientStm->execute([
          $data['client_cpf'],
          $data['client_address'],
          $userId
        ]);
        $clientId = $this->pdo->lastInsertId();
      } else {
        $clientId = $client['id'];
      }

      // Buscar o status inicial (Aberto)
      $statusStm = $this->pdo->prepare("SELECT id FROM status WHERE name = 'Aberto'");
      $statusStm->execute();
      $status = $statusStm->fetch(PDO::FETCH_ASSOC);

      // Criar ordem de serviço
      $soStm = $this->pdo->prepare("
        INSERT INTO service_orders (client_id, product_id, status_id, description, created_by) 
        VALUES (?, ?, ?, ?, ?)
      ");
      $soStm->execute([
        $clientId,
        $data['product_id'],
        $status['id'],
        $data['description'],
        $data['created_by']
      ]);
      $soId = $this->pdo->lastInsertId();

      // Criar log
      $logStm = $this->pdo->prepare("
        INSERT INTO service_order_logs (service_order_id, user_id, action, description) 
        VALUES (?, ?, 'created', 'Service order created')
      ");
      $logStm->execute([$soId, $data['created_by']]);

      $this->pdo->commit();
      return $soId;

    } catch (PDOException $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }

  public function update($id, $data)
  {
    try {
      $this->pdo->beginTransaction();

      // Verificar se a ordem existe
      $so = $this->getById($id);
      if (!$so) {
        throw new PDOException("Service order not found");
      }

      $logStatus = $data['status_id'] ?? null;
      $logDescription = $data['description'] ?? null;

      $data['description'] = $data['description'] ?? $so['description'];
      $data['status_id'] = $data['status_id'] ?? $so['status_id'];

      // Atualizar status se fornecido
      if (isset($data['status_id'])) {
        $statusStm = $this->pdo->prepare("SELECT name FROM status WHERE id = ?");
        $statusStm->execute([$data['status_id']]);
        $status = $statusStm->fetch(PDO::FETCH_ASSOC);
        
        if (!$status) {
          throw new PDOException("Invalid status");
        }

        $updateStm = $this->pdo->prepare("
          UPDATE service_orders 
          SET status_id = ?, description = ? 
          WHERE id = ?
        ");
        $updateStm->execute([
          $data['status_id'],
          $data['description'],
          $id
        ]);

        $msg = "";
        if ($logStatus and !$logDescription) {
          $msg = "Status updated to: " . $status['name'] . " by: " . $data['updated_by'];
        } elseif ($logDescription and !$logStatus) {
          $msg = "Description updated to: " . $data['description'] . " by: " . $data['updated_by'];
        } else {
          $msg = "Status and description updated to: " . $status['name'] . " and " . $data['description'] . " by: " . $data['updated_by'];
        }

        // Criar log de atualização
        $logStm = $this->pdo->prepare("
          INSERT INTO service_order_logs (service_order_id, user_id, action, description) 
          VALUES (?, ?, 'updated', ?)
        ");
        $logStm->execute([
          $id, 
          $data['updated_by'],
          $msg
        ]);
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

      // Verificar se a ordem existe
      $so = $this->getById($id);
      if (!$so) {
        throw new PDOException("Service order not found");
      }

      // Deletar ordem (os logs serão deletados automaticamente pelo ON DELETE CASCADE)
      $stm = $this->pdo->prepare("DELETE FROM service_orders WHERE id = ?");
      $stm->execute([$id]);

      $this->pdo->commit();
      return true;

    } catch (PDOException $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }
} 