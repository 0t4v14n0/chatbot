<?php

$servidor = 'localhost';
$usuario  = 'root';
$senha    = '';
$banco    = 'bot';
$conn     = new mysqli($servidor, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Quick and Dirty

//Limpar variaveis kkkk
$escolha = 0;
$status = 0;
$id = 0;

//Opcoes validas para validacao
$opcoes = ["1", "2", "3", "4", "5", "6", "7", "*"];

//Opcoes validas para validacao
$opcoesHorarios = ["1", "2", "3", "4", "5", "6"];

function atualizar($coluna, $valor, $telefone, $conn) {
    $sql = "UPDATE usuario SET $coluna = ? WHERE telefone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $valor, $telefone);
    return $stmt->execute(); // Retorna true se a atualização for bem-sucedida
}

function buscaUsuario($bu, $telefone, $conn) {
    // Prepara a consulta para evitar SQL Injection
    $sql = "SELECT $bu FROM usuario WHERE telefone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $telefone);
    $stmt->execute();
    
    // Obtém o resultado
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        // Retorna o valor do campo solicitado
        return $row[$bu];
    }
    
    // Retorna null se não houver resultado
    return null;
}

function primeiraMSG($nome) {

    $opcoes = "
    Digite o número da opção que deseja para que possamos marcar um horário:
    1- Direito de Família (Divórcio, Pensão, etc)
    2- Servidores Públicos
    3- Direito Trabalhista
    4- Direito Previdenciário (Pensão, aposentadoria, benefícios, etc)
    5- Registro de Marca
    6- Direito Empresarial (Assessoria Jurídica, Execuções, etc)
    7- Outros Segmentos";

    if ($nome == null) {
        echo("Olá, tudo bem? Sou um assistente virtual do escritório Holanda Advogados Associados.");
    } else {
        echo("Olá, tudo bem? $nome, que bom falar com você novamente!
        Se precissar mudar o nome envie '*'.");
    }
    echo($opcoes);
}

function segundaMSG(){
    echo("Qual e o seu nome ?");
}

function validarDataUtil($data, $conn) {
    $sql = "SELECT eh_dia_util FROM calendario WHERE data = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $data);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['eh_dia_util'] == 1; // retorna TRUE se e dia util
    }
    return false; // retorna FALSE se a data nao existir
}

function buscaHorarios($dataAtual,$horaAtual,$conn) {// retorna apenas horarios disponiveis

    $sql = "
    SELECT h.hora, c.data
    FROM horarios h
    JOIN calendario c ON h.id_calendario = c.id
    LEFT JOIN marcacoes m ON h.id = m.id_horario  -- Verifica se o horário já foi marcado
    WHERE c.data = ?  -- Apenas a data atual
    AND h.hora > ?  -- Apenas horários futuros
    AND m.id IS NULL  -- Apenas horários não marcados
    ORDER BY h.hora ASC;  -- Ordena pelos horários
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $dataAtual, $horaAtual);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result;

}

function validacaoDiaeHora($result, $dataAtual, $conn){

    if (validarDataUtil($dataAtual, $conn)) {
        if ($result->num_rows > 0) {
            return TRUE;
        } else {
            echo "Não há horários disponíveis para hoje.";
            return FALSE;
        }

    } else {
        echo "Só trabalhamos com marcação diária. Não trabalhamos finais de semana ou feriados.";
        return FALSE;
    }

}

function horariosDisponiveis($conn) {

    $dataAtual = (new DateTime())->format('Y-m-d');  // A data atual no formato Y-m-d
    $horaAtual = (new DateTime())->format('H:i:s');  // A hora atual no formato H:i:s

    // Simulação de data e hora
    //$dataAtual = '2024-12-02'; // Segunda-feira
    //$horaAtual = '06:00:00';  // Horário fixo para teste

    $result = buscaHorarios($dataAtual,$horaAtual,$conn);

    if(validacaoDiaeHora($result, $dataAtual, $conn)){//REDUCAO DE CODIGO
        echo "A consulta esta no valor de 50R$.
        Escolha uma das opções abaixo:\n";
        $opcao = 1; // inicia o contador
        while ($row = $result->fetch_assoc()) {
            echo "$opcao - " . $row['hora'] . "\n";
            $opcao++;
                    
            if ($opcao > 6) break;//garante apenas 6 opcoes
        }
    }
}

function agendarHorario($telefone,$msg,$conn) {

    $dataAtual = (new DateTime())->format('Y-m-d');  // A data atual no formato Y-m-d
    $horaAtual = (new DateTime())->format('H:i:s');  // A hora atual no formato H:i:s

    // Simulação de data e hora
    // $dataAtual = '2024-12-02'; // Segunda-feira
    // $horaAtual = '06:00:00';  // Horário fixo para teste

    $result = buscaHorarios($dataAtual, $horaAtual, $conn);

    if(validacaoDiaeHora($result, $dataAtual, $conn)){//REDUCAO DE CODIGO

        if (isset($horarios[$msg])) {

                        $idHorario = $horarios[$msg];

            // Marca o horário no banco de dados
            $sql = "INSERT INTO marcacoes (id_usuario, id_horario) VALUES (
                (SELECT id FROM usuario WHERE telefone = ?), ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $telefone, $idHorario);
            if ($stmt->execute()) {
                echo "Horário agendado com sucesso!";
            } else {
                echo "Erro ao agendar o horário. Tente novamente.";
            }

            return TRUE;

        }else{
            echo "Opção inválida. Escolha um horário válido.";
            return FALSE;
        }

    }

}

function criarQR() {
    echo("QR CODE: ...");
}

if (!$conn) {

    die("Erro na conexão do BD: " . mysqli_connect_error());

} else {

    if (isset($_GET['telefone']) && isset($_GET['msg'])) {
        $telefone = $_GET['telefone'];
        $msg      = $_GET['msg'];
    } else {
        echo "Valor null";
    }

    if (buscaUsuario("telefone", $telefone, $conn) == null) {
        // Certifique-se de que $nome e $status têm valores válidos antes de inserir
        $sql = "INSERT INTO usuario (telefone, nome, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $telefone, $nome, $status); // 'ssi' indica dois strings e um inteiro
        $stmt->execute(); // Execute a instrução preparada
    }

    $status = buscaUsuario("status",$telefone,$conn);
    $nome = buscaUsuario("nome",$telefone,$conn);

    if ($status == 0) {
        primeiraMSG($nome);
        atualizar("status",2,$telefone,$conn);
    } elseif ($status == 2) {
        if (in_array($msg, $opcoes)) {//if para somente aceiar msg validas no array predefinido
            atualizar("msg",$msg,$telefone,$conn);
            if ($nome == null || $msg == "*") {//Primeiro uso ou mudanca de nome no bot
                segundaMSG();//
                atualizar("status",3,$telefone,$conn);
            } else{// usuario ja recorrente
                horariosDisponiveis($conn);
                atualizar("status",4,$telefone,$conn);
            }
        }else{
            echo("Digite uma opçao valida...");
        }
    } elseif ($status == 3) {
        $umsg = buscaUsuario("msg",$telefone,$conn);
        if($nome == null || buscaUsuario("msg",$telefone,$conn) == "*"){
            atualizar("nome",$msg,$telefone,$conn);

            if($umsg == "*"){
                atualizar("status",0,$telefone,$conn);
                echo("A mudanca foi feia com sucesso $msg.
                Mande um OK");
            }else{
                horariosDisponiveis($conn);
                atualizar("status",4,$telefone,$conn);
            }
        }
    } elseif ($status == 4) {// enviar cod pix

        if(in_array($msg, $opcoesHorarios)){

            if(agendarHorario($msg,$conn)){

                criarQR();
    
                //pegar msg e criar uma marcacao
        
                //gerar e enviar pix copiar e colar
        
                atualizar("status",0,$telefone,$conn);//zera a sessao do usuario

            }

        }else{
            echo("Digite uma opçao valida...");
        }
    }

}

$conn->close();

?>
