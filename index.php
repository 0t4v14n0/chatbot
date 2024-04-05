<?php

$servidor = 'localhost';
$usuario  = 'root';
$senha    = '';
$banco    = 'bot';
$conn     = new mysqli($servidor, $usuario, $senha, $banco);

function addNome($valor,$telefone,$conn){
    $sql = "UPDATE horario SET disponibilidade = 0 WHERE id = '$valor'";
    $query = mysqli_query($conn, $sql);
}

function addValor($valor,$telefone,$conn){
    //um if se o valor for 0 indica quer zerar o historico...
    if ($valor == 0){
        atualizar_stes("status",$valor,$telefone,$conn);
    }
    atualizar_stes("escolha",$valor,$telefone,$conn);
    atualizar_stes("escolha2",$valor,$telefone,$conn);
}

// se o clinete escolher uma data ela nao ira mais aparecer para o proximo...
function autalizaHora($valor,$telefone,$conn){
    $sql = "UPDATE horario SET disponibilidade = 0 WHERE id = '$valor'";
    $query = mysqli_query($conn, $sql);
}

//mostra os horarios dispooniveis com um if...
function mostrarHorario($conn){
    echo("Quer marca uma hora ?\n");

    $sql = "SELECT * FROM horario";

    // Executa a consulta SQL
    $resultado = mysqli_query($conn, $sql);

    // Verifica se há registros retornados
    if (mysqli_num_rows($resultado) > 0) {
    // Exibe os dados
        while ($linha = mysqli_fetch_assoc($resultado)) {
            if($linha["disponibilidade"] == 1){

                echo $linha["id"] . " - " .  
                $linha["hora"] . " horas \n";

            }
        }
    } else {
        echo "Nenhum resultado encontrado.";
    }
}

//cadastra uma ves so o numero do clkiente
function numeroJaExistente($telefone, $conn) {
    $sql = "SELECT * FROM usuario WHERE telefone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $telefone);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// atualiza os status de escolhas
function atualizar_stes($campo, $valor, $telefone, $conn){
    $sql = "UPDATE usuario SET $campo = '$valor' WHERE telefone = '$telefone'";
    $query = mysqli_query($conn, $sql);
}

//busca valores
function busca($bu,$telefone,$conn){
    $sql = "SELECT * FROM usuario WHERE telefone = '$telefone'";
    $query = mysqli_query($conn,$sql);
    $total = mysqli_num_rows($query);

    while($rows_usuarios = mysqli_fetch_array($query)){
        $status = $rows_usuarios[$bu];
    }
    return $status;
}

//reiniciando as variaveis, boa pratica
$escolha = 0;
$status = 0;

if (!$conn) {
    // se der erro na conexão
    die("Erro na conexão do BD: " . mysqli_connect_error());
} else {
    //recebe os valores
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

    $status = busca("status",$telefone,$conn);

    if($status == 0){

        echo("Ola, tudo bem ? sou um bot...
        Como posso ajudar ?
        1-Consulta
        2-Treinamento
        3-Direito de Trabalho
        4-Assessoria Juridica
        5-Registro de Marcas");

        atualizar_stes("status",1,$telefone,$conn);

    }else{

        $escolha = busca("escolha",$telefone,$conn);

        if ($escolha == 0){
            if ($msg >= 1 && $msg <= 5) {
                switch ($msg) {
                    case 1:
                        addValor(1,$telefone,$conn);
                        mostrarHorario($conn);
                        break;
                    case 2:
                        echo("treinamneto");
                        $escolha = 2;
                        break;
                    case 3:
                        echo("direitos do trabalho");
                        $escolha = 3;
                        break;
                    case 4:
                        echo("Assessoria Juridica");
                        $escolha = 4;
                        break;
                    case 5:
                        echo("Registro de Marcas");
                        $escolha = 5;
                        break;
                }
            }
            else {
            }

        }
        else if ($escolha == 1){

            $escolha2 = busca("escolha",$telefone,$conn);

            if ($escolha2 == 1){
                if ($msg >= 1 && $msg <= 6) {
                    switch ($msg) {
                        case 1:
                            // reduzir codigo, fazer uma funcao pra zerar, com um for talvez,ou secao com uma update
                            //echo("horario marcado");
                            autalizaHora(1,$telefone,$conn);
                            atualizar_stes("escolha2",2,$telefone,$conn);
                            // add valor 0 zera o historico de mensagens do cliente
                            break;
                        case 2:
                            //echo("horario marcado");
                            autalizaHora(2,$telefone,$conn);
                            // add valor 0 zera o historico de mensagens do cliente
                            break;
                        case 3:
                            //echo("horario marcado");
                            autalizaHora(3,$telefone,$conn);
                            // add valor 0 zera o historico de mensagens do cliente
                            break;
                        case 4:
                            //echo("horario marcado");
                            autalizaHora(4,$telefone,$conn);
                            // add valor 0 zera o historico de mensagens do cliente
                            break;
                        case 5:
                            //echo("horario marcado");
                            autalizaHora(5,$telefone,$conn);
                            // add valor 0 zera o historico de mensagens do cliente
                            break;
                        case 6:
                            //echo("horario marcado");
                            autalizaHora(6,$telefone,$conn);
                            // add valor 0 zera o historico de mensagens doi cliente
                            break;
                    }
                }
                else {
                }
                echo("Qual e seu nome ?");
            }
            else{
                addNome($msg,$telefone,$conn);
                addValor(0,$telefone,$conn);
            }

        }
        else if ($escolha == 2){

        }
        else if ($escolha == 3){
            
        }
        else if ($escolha == 4){
            
        }
        else if ($escolha == 5){
            
        }
         else{

        }
    }
}

$conn->close();

?>
