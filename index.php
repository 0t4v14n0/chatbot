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

function status_Atualizar($st,$telefone,$conn){
    $sql = "UPDATE usuario SET status = '$st' WHERE telefone = '$telefone'";
    $query = mysqli_query($conn, $sql);
}

function escolha_Atualizar($es,$telefone,$conn){
    $sql = "UPDATE usuario SET escolha = '$es' WHERE telefone = '$telefone'";
    $query = mysqli_query($conn, $sql);
}

$status,$es = 0;

// Correção do erro na linha 20
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
        //echo "Este número já foi adicionado anteriormente. Tente outro.\n";
        //echo $telefone;
    } else {
        // Insere o número na tabela do banco de dados usando prepared statement
        $sql = "INSERT INTO usuario (telefone) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $telefone);
        if ($stmt->execute()) {
            //echo "Número de telefone adicionado com sucesso.\n";
        } else {
            //echo "Erro ao adicionar número de telefone: " . $conn->error . "\n";
        }
    }
    
    $sql = "SELECT * FROM usuario WHERE telefone = '$telefone'";
    $query = mysqli_query($conn,$sql);
    $total = mysqli_num_rows($query);

    while($rows_usuarios = mysqli_fetch_array($query)){

        $status = $rows_usuarios['status'];
    }

    if($status == 0){

        echo("Ola,tudo bem ? sou o bot de Celio Holanda
        1-Consulta
        2-Treinamento
        3-Direito de Trabalho
        4-Assessoria Juridica
        5-Registro de Marcas");
        $st = 1;

        status_Atualizar($st,$telefone,$conn);

    }else{
        if ($msg >= 1 && $msg <= 5) {
            switch ($msg) {
                case 1:
                    $es = 1
                    echo("Quer marca uma data ?");
                    break;
                case 2:
                    $es = 2
                    echo("escolheu a 2");
                    break;
                case 3:
                    $es = 3
                    echo("escolheu a 3");
                    break;
                case 4:
                    $es = 4
                    echo("escolheu a 4");
                    break;
                case 5:
                    $es = 5
                    echo("escolheu a 5");
                    break;
            }
        } else {
            $st = 0;
            status_Atualizar($st,$telefone,$conn);
        }

    }

}

$conn->close();

?>
