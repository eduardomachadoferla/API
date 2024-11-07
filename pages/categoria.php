<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($url[1])) {
    // Listar todas as categorias
    try {
        global $conn;
        $stmt = $conn->query("SELECT * FROM categories");
        $categories = $stmt->fetchALL(PDO::FETCH_ASSOC);
        if (empty($categories)) {
            retorno(['mensagem' => 'sem categorias no banco de dados!']);
            exit;
        }
        retorno($categories);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'Ops! Ocorreu um erro ao tentar listar as categorias'], 400);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($url[1])) {

    if ($url[1] === 'categoria') {
        $categoria = (int)$url[2];
        try {
            $stmt = $conn->prepare('SELECT * FROM users WHERE cod_user = :id');
            $stmt->bindParam(':id', $categoria);
            $stmt->execute();
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
            retorno($categoria);
        } catch (PDOException $e) {
            logME(['error' => 'categoria nao ancontrado. codigo =' . $categoria], 'error');
            retorno(['errror' => 'categoria nao encontrado.'], 400);
            exit;
        }
    }

    if ($url[1] === 'buscar') {
        $buscar = (string)$url[2];
        try {
            $sql = "SELECT * FROM users WHERE `name` like CONCAT('%', :buscar, '%' ) or `email` like CONCAT('%', :buscar , '%')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':buscar', $buscar);
            $stmt->execute();
            $categoria = $stmt->fetchALL(PDO::FETCH_ASSOC);
            retorno($categoria);
            exit;
        } catch (PDOException $e) {
            logMe(['error' => $e->getMessage()], 'error');
            retorno(['error' => 'categoria nao encontrado.'], 400);
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Criar nova categoria
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['name'])) {
        logMe(['error' => 'O nome da categoria é obrigatório'], 'error');
        retorno(['error' => 'O nome da categoria é obrigatório'], 400);
        exit;
    }
    $name = $data['name'];
    $status = $data['status'] ?? 0;

    try {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO categories (name, status) VALUES (:name, :status)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':status', $status, PDO::PARAM_BOOL);
        $stmt->execute();
        $category_id = $conn->lastInsertId();
        retorno(['id' => $category_id, 'name' => $name], 201);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'Ocorreu um erro ao tentar salvar a categoria no banco.'], 400);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Atualizar categoria
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['name']) || empty($data['cod_category'])) {
        retorno(['error' => 'O ID e o nome da categoria são obrigatórios'], 400);
        exit;
    }

    $cod_category = $data['cod_category'];
    $name = $data['name'];
    $status = $data['status'] ?? 0;

    try {
        $stmt = $conn->prepare('UPDATE categories SET name = :name, status = :status WHERE cod_category = :cod_category');
        $stmt->bindParam(':cod_category', $cod_category, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':status', $status, PDO::PARAM_BOOL);
        $stmt->execute();
        retorno(['success' => 'Categoria atualizada com sucesso']);
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'Ocorreu um erro ao tentar atualizar a categoria no banco.'], 400);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deletar categoria
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['cod_category'])) {
        retorno(['error' => 'O ID da categoria é obrigatório'], 400);
        exit;
    }
    $cod_category = $data['cod_category'];
    try {
        $stmt = $conn->prepare('DELETE FROM categories WHERE cod_category = :cod_category');
        $stmt->bindParam(':cod_category', $cod_category, PDO::PARAM_INT);
        $stmt->execute();
        retorno(['success' => true]);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'Não é possível deletar a categoria.'], 400);
        exit;
    }
}
