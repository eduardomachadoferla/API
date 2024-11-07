<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($url[1])) {
try {
    global $conn;
    $stmt = $conn->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($products)) {
        retorno(['mensagem' => 'Sem registros no banco de dados!']);
        exit;
    }
    retorno($products);
    exit;
} catch (PDOException $e) {
    logMe(['error' => $e->getMessage()], 'error');
    retorno(['error' => 'Ops! Ocorreu um erro ao tentar listar os produtos'], 400);
    exit;
}
}
 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($url[1])) {
if ($url[1] === 'products') {
    $cod_product = (int)$url[2];
    try {
        $stmt = $conn->prepare('SELECT * FROM products WHERE cod_product = :id');
        $stmt->bindParam(':id', $cod_product, PDO::PARAM_INT);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$produto) {
            retorno(['error' => 'Produto não encontrado.'], 404);
            exit;
        }
        retorno($produto);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => 'Produto não encontrado. Código =' . $cod_product], 'error');
        retorno(['error' => 'Produto não encontrado.'], 400);
        exit;
    }
}
 
// Funcionalidade de Busca
if ($url[1] === 'buscar') {
    $termo = (string)$url[2];
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :termo OR description LIKE :termo");
        $termo = '%' . $termo . '%';
        $stmt->bindParam(':termo', $termo, PDO::PARAM_STR);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($resultados)) {
            retorno(['mensagem' => 'Nenhum produto encontrado para a busca.']);
            exit;
        }
        retorno($resultados);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'Ocorreu um erro ao tentar realizar a busca.'], 400);
        exit;
    }
}
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$data = json_decode(file_get_contents('php://input'), true);
 
if (empty($data['name']) || empty($data['price']) || empty($data['cod_category'])) {
    retorno(['error' => 'Nome, preço e categoria são obrigatórios.'], 400);
    exit;
}
 
$name = $data['name'];
$description = $data['description'] ?? null;
$price = $data['price'];
$cod_category = $data['cod_category'];
$status = $data['status'] ?? 0;
 
try {
    global $conn;
    $stmt = $conn->prepare('INSERT INTO products (cod_category, name, description, price, status, created_at) VALUES (:cod_category, :name, :description, :price, :status, CURRENT_TIMESTAMP)');
    $stmt->bindParam(':cod_category', $cod_category, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->execute();
    $cod_product = $conn->lastInsertId();
    retorno(['id' => $cod_product, 'nome' => $name], 201);
    exit;
} catch (PDOException $e) {
    logMe(['error' => $e->getMessage()], 'error');
    retorno(['error' => 'Ocorreu um erro ao tentar salvar o produto no banco.'], 400);
    exit;
}
}
 
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
$data = json_decode(file_get_contents('php://input'), true);
 
if (empty($data['cod_product']) || empty($data['name']) || empty($data['price']) || empty($data['cod_category'])) {
    retorno(['error' => 'ID, nome, preço e categoria são obrigatórios.'], 400);
    exit;
}
 
$cod_product = $data['cod_product'];
$name = $data['name'];
$description = $data['description'] ?? null;
$price = $data['price'];
$cod_category = $data['cod_category'];
$status = $data['status'] ?? 0;
 
try {
    $stmt = $conn->prepare('UPDATE products SET cod_category = :cod_category, name = :name, description = :description, price = :price, status = :status, updated_at = CURRENT_TIMESTAMP WHERE cod_product = :cod_product');
    $stmt->bindParam(':cod_product', $cod_product, PDO::PARAM_INT);
    $stmt->bindParam(':cod_category', $cod_category);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->execute();
 
    retorno(['success' => 'Produto atualizado com sucesso']);
    exit;
} catch (PDOException $e) {
    logMe(['error' => $e->getMessage()], 'error');
    retorno(['error' => 'Não foi possível atualizar o produto.'], 400);
    exit;
}
}
 
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
$data = json_decode(file_get_contents('php://input'), true);
 
if (empty($data['cod_product'])) {
    retorno(['error' => 'O ID do produto é obrigatório.'], 400);
    exit;
}
 
$cod_product = $data['cod_product'];
 
try {
    $stmt = $conn->prepare('DELETE FROM products WHERE cod_product = :id');
    $stmt->bindParam(':id', $cod_product, PDO::PARAM_INT);
    $stmt->execute();
    retorno(['success' => true]);
    exit;
} catch (PDOException $e) {
    logMe(['error' => $e->getMessage()], 'error');
    retorno(['error' => 'Não foi possível deletar o produto.'], 400);
    exit;
}
}