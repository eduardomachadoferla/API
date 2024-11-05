<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .'config.inc.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .'funcoes.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR .'conexao.php';

$url = (!empty(filter_input(INPUT_GET, 'param', FILTER_DEFAULT))) ? filter_input(INPUT_GET, 'param', FILTER_DEFAULT) : 'home';
$url = array_filter(explode('/' , $url));

if($url[0] === 'remove'){
    clear();
}
$arquivo = 'pages' . DIRECTORY_SEPARATOR . $url[0] . '.php';

if(is_file($arquivo)){
    include $arquivo;
} else {
    retorno('not found', 404);
}