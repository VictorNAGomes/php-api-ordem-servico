<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Http\Response;
use App\Http\Request;
use PDOException;

class ClientController 
{
    public function getAll()
    {
        $clientModel = new ClientModel();
        $clients = $clientModel->getAll();
        
        if ($clients === false) {
            Response::json([
                'error' => true,
                'message' => 'Error fetching clients'
            ], 500);
            return;
        }

        Response::json([
            'error' => false,
            'data' => $clients
        ]);
    }

    public function getOne($request, $response, $matches)
    {
        $id = $matches[0];
        $clientModel = new ClientModel();
        $client = $clientModel->getById($id);

        if ($client === false) {
            Response::json([
                'error' => true,
                'message' => 'Client not found'
            ], 404);
            return;
        }

        Response::json([
            'error' => false,
            'data' => $client
        ]);
    }

    public function create()
    {
        $data = Request::body();

        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password']) || 
            !isset($data['cpf']) || !isset($data['address'])) {
            Response::json([
                'error' => true,
                'message' => 'Name, email, password, CPF and address are required'
            ], 400);
            return;
        }

        $clientModel = new ClientModel();
        try {
            $clientId = $clientModel->create($data);
            $client = $clientModel->getById($clientId);
            
            Response::json([
                'error' => false,
                'message' => 'Client created successfully',
                'data' => $client
            ], 201);
        } catch (PDOException $e) {
            $message = 'Error creating client';
            $status = 500;

            if ($e->getMessage() === 'Invalid CPF') {
                $message = 'Invalid CPF format';
                $status = 400;
            } else if ($e->getMessage() === 'CPF already registered') {
                $message = 'This CPF is already registered';
                $status = 400;
            }

            Response::json([
                'error' => true,
                'message' => $message
            ], $status);
        }
    }

    public function update($request, $response, $matches)
    {
        $id = $matches[0];
        $data = Request::body();

        $clientModel = new ClientModel();
        
        try {
            // Check if client exists
            if ($clientModel->getById($id) === false) {
                Response::json([
                    'error' => true,
                    'message' => 'Client not found'
                ], 404);
                return;
            }

            $success = $clientModel->update($id, $data);
            $client = $clientModel->getById($id);
            
            Response::json([
                'error' => false,
                'message' => 'Client updated successfully',
                'data' => $client
            ]);
        } catch (PDOException $e) {
            $message = 'Error updating client';
            $status = 500;

            if ($e->getMessage() === 'Invalid CPF') {
                $message = 'Invalid CPF format';
                $status = 400;
            } else if ($e->getMessage() === 'CPF already registered') {
                $message = 'This CPF is already registered';
                $status = 400;
            }

            Response::json([
                'error' => true,
                'message' => $message
            ], $status);
        }
    }

    public function delete($request, $response, $matches)
    {
        $id = $matches[0];
        $clientModel = new ClientModel();
        
        try {
            // Check if client exists
            if ($clientModel->getById($id) === false) {
                Response::json([
                    'error' => true,
                    'message' => 'Client not found'
                ], 404);
                return;
            }

            $success = $clientModel->delete($id);

            Response::json([
                'error' => false,
                'message' => 'Client deleted successfully'
            ]);
        } catch (PDOException $e) {
            Response::json([
                'error' => true,
                'message' => 'Error deleting client'
            ], 500);
        }
    }
}