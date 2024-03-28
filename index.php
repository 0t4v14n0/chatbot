<?php

$servidor = 'localhost';
$usuario  = 'root';
$senha    = '';
$banco    = 'bot';
$conn     = mysqli_connect($servidor, $usuario, $senha, $banco);

if (!$conn) {
    // se der erro na conexão
    die("Erro na conexão do BD: " . mysqli_connect_error());

} else {

    // Conectado recebendo os valores de numero e a ultima mensagem enviada do cliente
    // Validando e definindo como vazio caso não seja fornecido

    $telefone = $_GET['telefone'] ?? '';
    $msg      = $_GET['msg'] ?? ''; 

    // Validando se o número de telefone foi fornecido
    if(empty($telefone)) {
        die("Erro: Número de telefone não fornecido.");
    } if(empty($msg)) {
        die("Erro: mensagem não fornecido.");
    }

    //cadastrar no banco de dados se nao estiver cadastrado

    $sql_verificar = "SELECT * FROM usuario WHERE telefone = '$telefone'";
    $result_verificar = $conn->query($sql_verificar);

    if(!$result_verificar){
        $sql = "INSERT INTO usuario (telefone) VALUES ('$telefone')";
        $query = mysqli_query($conn,$sql);
        echo"1";
    } else{
        echo"2";
    }

}
?>
