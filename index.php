<?php

$servidor = 'localhost';
$usuario  = 'root';
$senha    = '';
$banco    = 'bot';
$conn     = mysqli_connect($servidor,$usuario,$senha,$banco);

if(!$conn){

}else{

}

#coletando variaveis
$telefone = $_GET['telefone'];
$msg      = $_GET['msg'];
#echo "Ola como vai ? $telefone ultima mensagem: $msg <br>";

$sql = "SELECT * FROM usuario WHERE telefone = '$telefone'";
$query = mysqli_query($conn,$sql);
$total = mysqli_num_rows($query);

if($total == 0){

    $sql = "INSERT INTO usuario (telefone,status) VALUES ('$telefone', '1')";
    $query = mysqli_query($conn,$sql);
    if($query){

        echo "seu numero foi salvo no meu banco de dados";    
        
    }

}

################################################################################################################

if($total == 1){

}

?>
