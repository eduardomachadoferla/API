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
        logMe(['error' => $e->getMESSAGE()], 'error');
        retorno(['error' => 'ops! ocorreu um erro ao tentar listar os produtos'], 400);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($url[1])) {

    if ($url[1] === 'produtos') {
        $cod_user = (int)$url[2];
        try {
            $stmt = $conn->prepare('SELECT * FROM address_user WHERE cod_user = :id');
            $stmt->bindParam(':id', $produtos);
            $stmt->execute();
            $produtos = $stmt->fetch(PDO::FETCH_ASSOC);
            retorno($produtos);
        } catch (PDOException $e) {
            logME(['error' => 'produtos nao ancontrado. codigo =' . $produtos], 'error');
            retorno(['errror' => 'produtos nao encontrado.'], 400);
            exit;
        }
    }

    if ($url[1] === 'buscar') {
        $buscar = (string)$url[2];
        try {
            $sql = "SELECT * FROM users WHERE `name` like CONCAT('%', :buscar, '%' ) or `address` like CONCAT('%', :buscar , '%')";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':buscar', $buscar);
            $stmt->execute();
            $produtos = $stmt->fetchALL(PDO::FETCH_ASSOC);
            retorno($produtos);
            exit;
        } catch (PDOException $e) {
            logMe(['error' => $e->getMessage()], 'error');
            retorno(['error' => 'produtos nao encontrado.'], 400);
            exit;
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['name'])) {
        logMe(['error' => ' o nome do produtos é obrigatorio'], 'error');
        retorno(['error' => 'o nome do produtos é obrigatorio'], 400);
        exit;
    }
    if (empty($data['email'])) {
        logMe(['error' => ' o email do edereco é obrigatorio'], 'error');
        retorno(['error' => 'o email do produtos é obrigatorio'], 400);
        exit;
    }
    $name = $data['name'];
    $email = $data['email'];
    $password = password(($data['password']));
    $status = $data['status'];
    try {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO users (`name`, `email`, `password`, `status`) VALUES (:name, :email, :password, :status)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $usuario_id = $conn->lastInsertId();
        retorno(
            ['id' => $usuario_id, 'nome' => $name, 'email' => $email],
            201
        );
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'ocoreu um error ao tentar salvar os dados no banco.'], 400);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['name'])) {
        retorno(['error' => 'o nome do produtos é obrigatorio'], 400);
        exit;
    }

    $cod_user = $data['id'];
    $name = $data['name'];
    $email = $data['email'];
    $password = password(($data['password']));
    try {
        $stmt = $conn->prepare('UPDATE users SET name = :name, password = :password, email = :email WHERE cod_user = :cod_user');
        $stmt->bindParam(':cod_user', $cod_user);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        retorno(['success' => 'dados atualizados com sucesso']);
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'ocoreu um error ao tentar atualizar os dados no banco.'], 400);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['usuario_id'])) {
        retorno(['error' => 'o id do produtos é obrigatorio'], 400);
        exit;
    }
    $usuario_id = $data['usuario_id'];
    try {
        $stmt = $conn->prepare('DELETE FROM users WHERE cod_user = :id');
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
        retorno(['success' => true]);
        exit;
    } catch (PDOException $e) {
        logMe(['error' => $e->getMessage()], 'error');
        retorno(['error' => 'nao é possivel deletar o produto.'], 400);
        exit;
    }
}
