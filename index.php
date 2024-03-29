<?php

$servidor = 'localhost';
$usuario  = 'root';
$senha    = '';
$banco    = 'bot';
$conn     = new mysqli($servidor, $usuario, $senha, $banco);

function numeroJaExistente($telefone, $conn) {
    $sql = "SELECT * FROM usuario WHERE telefone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $telefone);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

$

if (!$conn) {
    // se der erro na conexão
    die("Erro na conexão do BD: " . mysqli_connect_error());
} else {
    // Conectado recebendo os valores de numero e a ultima mensagem enviada do cliente
    $telefone = $_GET['telefone'];
    $msg      = $_GET['msg'];

    //cadastrar no banco de dados se nao estiver cadastrado
    // Verifica se o número já existe no banco de dados
    if (numeroJaExistente($telefone, $conn)) {
        echo "Este número já foi adicionado anteriormente. Tente outro.\n";
        echo $telefone;
    } else {
        // Insere o número na tabela do banco de dados usando prepared statement
        $sql = "INSERT INTO usuario (telefone) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $telefone);
        if ($stmt->execute()) {
            echo "Número de telefone adicionado com sucesso.\n";
        } else {
            echo "Erro ao adicionar número de telefone: " . $conn->error . "\n";
        }
    }
}

$conn->close();

?>
