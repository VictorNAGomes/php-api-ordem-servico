<?php

namespace App\Controllers;

use App\Models\ServiceOrderModel;
use App\Http\Response;
use App\Http\Request;
use PDOException;

class ServiceOrderController 
{
    public function getAll()
    {
        $serviceOrderModel = new ServiceOrderModel();
        $serviceOrders = $serviceOrderModel->getAll();
        
        if ($serviceOrders === false) {
            Response::json([
                'error' => true,
                'message' => 'Error fetching service orders'
            ], 500);
            return;
        }

        Response::json([
            'error' => false,
            'data' => $serviceOrders
        ]);
    }

    public function getOne($request, $response, $matches)
    {
        $id = $matches[0];
        $serviceOrderModel = new ServiceOrderModel();
        $serviceOrder = $serviceOrderModel->getById($id);

        if ($serviceOrder === false) {
            Response::json([
                'error' => true,
                'message' => 'Service order not found'
            ], 404);
            return;
        }

        Response::json([
            'error' => false,
            'data' => $serviceOrder
        ]);
    }

    public function create()
    {
        $data = Request::body();

        // Validar dados obrigatórios
        if (!isset($data['product_id']) || !isset($data['client_cpf']) || !isset($data['description'])|| !isset($data['created_by'])) {
            Response::json([
                'error' => true,
                'message' => 'Product ID, client CPF, description and created by are required'
            ], 400);
            return;
        }

        // Se for criar um novo cliente, validar dados adicionais
        // if (!isset($data['client_name']) || !isset($data['client_email']) || !isset($data['client_password']) || !isset($data['client_address'])) {
        //     Response::json([
        //         'error' => true,
        //         'message' => 'Client data (name, email, password, address) is required to create new client'
        //     ], 400);
        //     return;
        // }

        $serviceOrderModel = new ServiceOrderModel();
        try {
            $serviceOrderId = $serviceOrderModel->create($data);
            $serviceOrder = $serviceOrderModel->getById($serviceOrderId);
            
            Response::json([
                'error' => false,
                'message' => 'Service order created successfully',
                'data' => $serviceOrder
            ], 201);
        } catch (PDOException $e) {
            $message = 'Error creating service order';
            $status = 500;

            if ($e->getMessage() === 'Product not found') {
                $message = 'Product not found';
                $status = 404;
            }
            if ($e->getMessage() === 'Client data is required to create new client') {
                $message = 'Client data (name, email, password, address) is required to create new client';
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

        if (!isset($data['status_id']) && !isset($data['description'])) {
            Response::json([
                'error' => true,
                'message' => 'Status ID or description are required'
            ], 400);
            return;
        }

        $serviceOrderModel = new ServiceOrderModel();
        try {
            $success = $serviceOrderModel->update($id, $data);
            $serviceOrder = $serviceOrderModel->getById($id);
            
            Response::json([
                'error' => false,
                'message' => 'Service order updated successfully',
                'data' => $serviceOrder
            ]);
        } catch (PDOException $e) {
            $message = 'Error updating service order';
            $status = 500;

            if ($e->getMessage() === 'Service order not found') {
                $message = 'Service order not found';
                $status = 404;
            } else if ($e->getMessage() === 'Invalid status') {
                $message = 'Invalid status';
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
        $serviceOrderModel = new ServiceOrderModel();
        
        try {
            $success = $serviceOrderModel->delete($id);

            Response::json([
                'error' => false,
                'message' => 'Service order deleted successfully'
            ]);
        } catch (PDOException $e) {
            $message = 'Error deleting service order';
            $status = 500;

            if ($e->getMessage() === 'Service order not found') {
                $message = 'Service order not found';
                $status = 404;
            }

            Response::json([
                'error' => true,
                'message' => $message
            ], $status);
        }
    }
} 