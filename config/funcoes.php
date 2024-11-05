<?php

function logMe($msg, $type = 'info')
{
    $file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
        . 'log' . DIRECTORY_SEPARATOR . 'log-' . date("Y-m-d") . 'txt';
    $fp = fopen($file, 'w+');
    $now = date("Y-m-d H:i:s");
    $txt = "[$now][$type]: " . json_encode($msg) . "\n";
    $escrever = fwrite($fp, $txt);
    fclose($fp);
}

function retorno($msg, $code = 200)
{
    header('content-type: application/json');
    http_response_code($code);
    switch ($code) {
        case $code > 199 && $code < 300;
            echo json_encode($msg);
            break;
        default:
            echo json_encode($msg);
            break;
    }
}

function password($password)
{
    $option = ['cost' => 11];
    return password_hash($password, PASSWORD_BCRYPT, $option);
}

function clear()
{
    $path_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'install';
    if (is_dir($path_dir)) {
        delTree($path_dir);
        echo "instalaçao finalizada com sucesso!";
    } else {
        header('location: erro/404.phtml');
    }
}

function delTree($dir)
{
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
            delTree($dir . DIRECTORY_SEPARATOR . $file);
        } else {
            unlink($dir . DIRECTORY_SEPARATOR . $file);
        }
    }
    return rmdir($dir);
}
