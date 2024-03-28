<?php

$servidor = 'localhost';
$usuario  = 'root';
$senha    = '';
$banco    = 'bot';
$conn     = mysqli_connect($servidor, $usuario, $senha, $banco);

function numeroJaExistente($telefone, $conn) {
    $sql = "SELECT * FROM usuario WHERE telefone = '$telefone'";
    $result = $conn->query($sql);
    return $result->num_rows > 0;
}

if (!$conn) {
    // se der erro na conexão
    die("Erro na conexão do BD: " . mysqli_connect_error());

} else {

    // Conectado recebendo os valores de numero e a ultima mensagem enviada do cliente

    $telefone = $_GET['telefone'] ?? '';
    $msg      = $_GET['msg'] ?? ''; 

    // Validando se o número de telefone foi fornecido
    if(empty($telefone)) {
        die("Erro: Número de telefone não fornecido.");
    } if(empty($msg)) {
        die("Erro: mensagem não fornecido.");
    }

    //cadastrar no banco de dados se nao estiver cadastrado

    // Verifica se o número já existe no banco de dados
    if (numeroJaExistente($telefone, $conn)) {
        echo "Este número já foi adicionado anteriormente. Tente outro.\n";
    } else {
        // Insere o número na tabela do banco de dados
        $sql = "INSERT INTO usuario (telefone) VALUES ('$telefone')";
        if ($conn->query($sql) === TRUE) {
            echo "Número de telefone adicionado com sucesso.\n";
        } else {
            echo "Erro ao adicionar número de telefone: " . $conn->error . "\n";
        }
    }

}
?>
