<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($url[1])) {
    try {
        global $conn;
        $stmt = $conn->query("SELECT * FROM users");
        $users = $stmt->fetchALL(PDO::FETCH_ASSOC);
        if (empty($users)) {
            retorno(['mensagem' => 'sem registro no banco de dados!']);
            exit;
        }
        retorno($users);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'ops! ocorreu um erro ao tentar listar os endereços'], 400);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($url[1])) {
    if ($url[1] === 'enderecos') {
        $cod_user = (int)$url[2];
        try {
            $stmt = $conn->prepare('SELECT * FROM address_user WHERE cod_user = :cod_user');
            $stmt->bindParam(':cod_user', $cod_user, PDO::PARAM_INT);
            $stmt->execute();
            $enderecos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            retorno($enderecos);
        } catch (PDOException $e) {
            logMe(['error' => 'endereços não encontrado. Código = ' . $cod_user], 'error');
            retorno(['error' => 'endereços não encontrado.'], 400);
            exit;
        }
    }

    if ($url[1] === 'buscar') {
        $buscar = (string)$url[2];
        try {
            $sql = "SELECT * FROM address_user WHERE `name` LIKE CONCAT('%', :buscar, '%') OR `address` LIKE CONCAT('%', :buscar , '%')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':buscar', $buscar);
            $stmt->execute();
            $enderecos = $stmt->fetchALL(PDO::FETCH_ASSOC);
            retorno($enderecos);
            exit;
        } catch (PDOException $e) {
            logMe(['error' => $e->getMessage()], 'error');
            retorno(['error' => 'endereços não encontrado.'], 400);
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['name'])) {
        logMe(['error' => 'o nome do endereço é obrigatório'], 'error');
        retorno(['error' => 'o nome do endereço é obrigatório'], 400);
        exit;
    }
    if (empty($data['email'])) {
        logMe(['error' => 'o email do endereço é obrigatório'], 'error');
        retorno(['error' => 'o email do endereço é obrigatório'], 400);
        exit;
    }
    $name = $data['name'];
    $email = $data['email'];
    $status = $data['status'] ?? 0;

    try {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO address_user (`cod_user`, `name`, `address`, `number`, `zip_code`,`status`) VALUES (:cod_user, :name, :address, :number, :zip_code, :status)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':status', $status, PDO::PARAM_BOOL);
        $stmt->execute();
        $endereco_id = $conn->lastInsertId();
        retorno(['id' => $endereco_id, 'nome' => $name, 'email' => $email], 201);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'ocorreu um erro ao tentar salvar os dados no banco.'], 400);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['name'])) {
        retorno(['error' => 'o nome do endereço é obrigatório'], 400);
        exit;
    }

    $cod_user = $data['id'];
    $name = $data['name'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    try {
        $stmt = $conn->prepare('UPDATE users SET name = :name, password = :password, email = :email WHERE cod_user = :cod_user');
        $stmt->bindParam(':cod_user', $cod_user, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        retorno(['success' => 'dados atualizados com sucesso']);
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'ocorreu um erro ao tentar atualizar os dados no banco.'], 400);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['endereco_id'])) {
        retorno(['error' => 'o ID do endereço é obrigatório'], 400);
        exit;
    }
    $endereco_id = $data['endereco_id'];
    try {
        $stmt = $conn->prepare('DELETE FROM users WHERE cod_user = :endereco_id');
        $stmt->bindParam(':endereco_id', $endereco_id, PDO::PARAM_INT);
        $stmt->execute();
        retorno(['success' => true]);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'não é possível deletar o usuário.'], 400);
        exit;
    }
}
