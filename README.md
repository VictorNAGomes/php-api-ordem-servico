# PHP API - Ordem de Serviço

### Passo a passo para a instalação

Clone o Repositório

```sh
git clone https://github.com/VictorNAGomes/php-api-ordem-servico.git
```

```sh
cd php-api-ordem-servico
```

Suba os containers do projeto

```sh
docker-compose up -d
```

Acesse o container app

```sh
docker-compose exec app bash
```

Instale as dependências do projeto

```sh
composer install
```

Importe o banco de dados

```sh
mysql -h db -u root -psecret service_order < database.sql
```

Acesse o projeto
[http://localhost:3000](http://localhost:3000)

---

# Documentação da API

## Autenticação

### POST /auth/login

Realiza o login do usuário.

**Request:**

-   Headers:
    -   Content-Type: application/json
-   Body:
    ```json
    {
        "email": "string",
        "password": "string"
    }
    ```

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "token": "string"
    }
    ```
-   401 Unauthorized

---

## Clientes

### GET /clients

Retorna todos os clientes.

**Headers:**
- Authorization: Bearer {token}

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "data": [
            {
                "id": "integer",
                "name": "string",
                "email": "string",
                "cpf": "string",
                "address": "string",
                "role": "string"
            }
        ]
    }
    ```
-   401 Unauthorized

### GET /clients/{id}

Retorna um cliente específico pelo ID.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID do cliente

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "data": {
            "id": "integer",
            "name": "string",
            "email": "string",
            "cpf": "string",
            "address": "string",
            "role": "string"
        }
    }
    ```
-   401 Unauthorized
-   404 Not Found

### POST /clients

Cria um novo cliente.

**Headers:**
- Authorization: Bearer {token}

**Request:**
-   Body:
    ```json
    {
        "name": "string",
        "email": "string",
        "password": "string",
        "cpf": "string",
        "address": "string"
    }
    ```

**Response:**

-   201 Created
    -   Body:
    ```json
    {
        "error": false,
        "message": "Client created successfully",
        "data": {
            "id": "integer",
            "name": "string",
            "email": "string",
            "cpf": "string",
            "address": "string",
            "role": "string"
        }
    }
    ```
-   400 Bad Request
-   401 Unauthorized

### PUT /clients/{id}

Atualiza um cliente específico.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID do cliente

**Request:**
-   Body:
    ```json
    {
        "name": "string",
        "email": "string",
        "password": "string",
        "cpf": "string",
        "address": "string"
    }
    ```

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "message": "Client updated successfully",
        "data": {
            "id": "integer",
            "name": "string",
            "email": "string",
            "cpf": "string",
            "address": "string",
            "role": "string"
        }
    }
    ```
-   400 Bad Request
-   401 Unauthorized
-   404 Not Found

### DELETE /clients/{id}

Remove um cliente específico.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID do cliente

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "message": "Client deleted successfully"
    }
    ```
-   401 Unauthorized
-   404 Not Found

---

## Produtos

### GET /products

Retorna todos os produtos.

**Headers:**
- Authorization: Bearer {token}

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "data": [
            {
                "id": "integer",
                "description": "string",
                "warranty_period": "integer",
                "status": "string"
            }
        ]
    }
    ```
-   401 Unauthorized

### GET /products/{id}

Retorna um produto específico pelo ID.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID do produto

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "data": {
            "id": "integer",
            "description": "string",
            "warranty_period": "integer",
            "status": "string"
        }
    }
    ```
-   401 Unauthorized
-   404 Not Found

### POST /products

Cria um novo produto.

**Headers:**
- Authorization: Bearer {token}

**Request:**
-   Body:
    ```json
    {
        "description": "string",
        "warranty_period": "integer",
        "status": "string"
    }
    ```

**Response:**

-   201 Created
    -   Body:
    ```json
    {
        "error": false,
        "message": "Product created successfully",
        "data": {
            "id": "integer",
            "description": "string",
            "warranty_period": "integer",
            "status": "string"
        }
    }
    ```
-   400 Bad Request
-   401 Unauthorized

### PUT /products/{id}

Atualiza um produto específico.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID do produto

**Request:**
-   Body:
    ```json
    {
        "description": "string",
        "warranty_period": "integer",
        "status": "string"
    }
    ```

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "message": "Product updated successfully",
        "data": {
            "id": "integer",
            "description": "string",
            "warranty_period": "integer",
            "status": "string"
        }
    }
    ```
-   400 Bad Request
-   401 Unauthorized
-   404 Not Found

### DELETE /products/{id}

Remove um produto específico.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID do produto

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "message": "Product deleted successfully"
    }
    ```
-   401 Unauthorized
-   404 Not Found

---

## Ordens de Serviço

### GET /service-orders

Retorna todas as ordens de serviço.

**Headers:**
- Authorization: Bearer {token}

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "data": [
            {
                "id": "integer",
                "opening_date": "timestamp",
                "client_id": "integer",
                "product_id": "integer",
                "status_id": "integer",
                "description": "string",
                "created_by": "integer"
            }
        ]
    }
    ```
-   401 Unauthorized

### GET /service-orders/{id}

Retorna uma ordem de serviço específica pelo ID.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID da ordem de serviço

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "data": {
            "id": "integer",
            "opening_date": "timestamp",
            "client_id": "integer",
            "product_id": "integer",
            "status_id": "integer",
            "description": "string",
            "created_by": "integer"
        }
    }
    ```
-   401 Unauthorized
-   404 Not Found

### POST /service-orders

Cria uma nova ordem de serviço.

**Headers:**
- Authorization: Bearer {token}

**Request:**
-   Body:
    ```json
    {
        "client_id": "integer",
        "product_id": "integer",
        "description": "string"
    }
    ```

**Response:**

-   201 Created
    -   Body:
    ```json
    {
        "error": false,
        "message": "Service order created successfully",
        "data": {
            "id": "integer",
            "opening_date": "timestamp",
            "client_id": "integer",
            "product_id": "integer",
            "status_id": "integer",
            "description": "string",
            "created_by": "integer"
        }
    }
    ```
-   400 Bad Request
-   401 Unauthorized

### PUT /service-orders/{id}

Atualiza uma ordem de serviço específica.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID da ordem de serviço

**Request:**
-   Body:
    ```json
    {
        "client_id": "integer",
        "product_id": "integer",
        "status_id": "integer",
        "description": "string"
    }
    ```

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "message": "Service order updated successfully",
        "data": {
            "id": "integer",
            "opening_date": "timestamp",
            "client_id": "integer",
            "product_id": "integer",
            "status_id": "integer",
            "description": "string",
            "created_by": "integer"
        }
    }
    ```
-   400 Bad Request
-   401 Unauthorized
-   404 Not Found

### DELETE /service-orders/{id}

Remove uma ordem de serviço específica.

**Headers:**
- Authorization: Bearer {token}

**Parameters:**
-   Path:
    -   id (integer): ID da ordem de serviço

**Response:**

-   200 OK
    -   Body:
    ```json
    {
        "error": false,
        "message": "Service order deleted successfully"
    }
    ```
-   401 Unauthorized
-   404 Not Found