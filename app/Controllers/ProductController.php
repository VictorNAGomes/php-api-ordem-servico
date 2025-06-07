<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Http\Response;
use App\Http\Request;

class ProductController 
{
    public function getAll()
    {
        $productModel = new ProductModel();
        $products = $productModel->getAll();
        
        if ($products === false) {
            Response::json([
                'error' => true,
                'message' => 'Error fetching products'
            ], 500);
            return;
        }

        Response::json([
            'error' => false,
            'data' => $products
        ]);
    }

    public function getOne($request, $response, $matches)
    {
        $id = $matches[0];
        $productModel = new ProductModel();
        $product = $productModel->getById($id);

        if ($product === false) {
            Response::json([
                'error' => true,
                'message' => 'Product not found'
            ], 404);
            return;
        }

        Response::json([
            'error' => false,
            'data' => $product
        ]);
    }

    public function create()
    {
        $data = Request::body();

        if (!isset($data['description']) || !isset($data['warranty_period'])) {
            Response::json([
                'error' => true,
                'message' => 'Description and warranty period are required'
            ], 400);
            return;
        }

        $productModel = new ProductModel();
        $productId = $productModel->create($data);

        if ($productId === false) {
            Response::json([
                'error' => true,
                'message' => 'Error creating product'
            ], 500);
            return;
        }

        $product = $productModel->getById($productId);
        
        Response::json([
            'error' => false,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    public function update($request, $response, $matches)
    {
        $id = $matches[0];
        $data = Request::body();

        if (!isset($data['description']) || !isset($data['warranty_period'])) {
            Response::json([
                'error' => true,
                'message' => 'Description and warranty period are required'
            ], 400);
            return;
        }

        $productModel = new ProductModel();
        
        // Check if product exists
        if ($productModel->getById($id) === false) {
            Response::json([
                'error' => true,
                'message' => 'Product not found'
            ], 404);
            return;
        }

        $success = $productModel->update($id, $data);

        if ($success === false) {
            Response::json([
                'error' => true,
                'message' => 'Error updating product'
            ], 500);
            return;
        }

        $product = $productModel->getById($id);
        
        Response::json([
            'error' => false,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    public function delete($request, $response, $matches)
    {
        $id = $matches[0];
        $productModel = new ProductModel();
        
        // Check if product exists
        if ($productModel->getById($id) === false) {
            Response::json([
                'error' => true,
                'message' => 'Product not found'
            ], 404);
            return;
        }

        $success = $productModel->delete($id);

        if ($success === false) {
            Response::json([
                'error' => true,
                'message' => 'Error deleting product'
            ], 500);
            return;
        }

        Response::json([
            'error' => false,
            'message' => 'Product deleted successfully'
        ]);
    }
}