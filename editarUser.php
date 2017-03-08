<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload de arquivos</title>
</head>

<body>
<?php
	require_once('classes/BD.class.php');
$pegaUser = BD::conn()->prepare("SELECT * FROM `usuarios` WHERE `email` = ?");
	session_start();
	$pegaUser->execute(array($_SESSION['email_logado']));
	$dadosUser = $pegaUser->fetch();
$nomePessoa = ($_POST['nome'] . ' '. $_POST['snome'] == ' ') ? $dadosUser['nome'] : $_POST['nome'] . ' '. $_POST['snome'];
$profissao = $_POST['profissao'];
$cidade = $_POST['cidade'];

// verifica se foi enviado um arquivo 
if(isset($_FILES['arquivo']['name']) && $_FILES["arquivo"]["error"] == 0){

	$arquivo_tmp = $_FILES['arquivo']['tmp_name'];
	$nome = $_FILES['arquivo']['name'];
	

	// Pega a extensao
	$extensao = strrchr($nome, '.');

	// Converte a extensao para mimusculo
	$extensao = strtolower($extensao);

	// Somente imagens, .jpg;.jpeg;.gif;.png
	// Aqui eu enfilero as extesões permitidas e separo por ';'
	// Isso server apenas para eu poder pesquisar dentro desta String
	if(strstr('.jpg;.jpeg;.gif;.png', $extensao))
	{
		// Cria um nome único para esta imagem
		// Evita que duplique as imagens no servidor.
		$novoNome = md5(microtime()) . $extensao;
		
		// Concatena a pasta com o nome
		$destino = "img/". $novoNome; 
		
		// tenta mover o arquivo para o destino
		if( @move_uploaded_file( $arquivo_tmp, $destino  )){
			echo "<img src=\"" . $destino . "\" />";
		}
		else
			echo "Erro ao salvar o arquivo. Aparentemente você não tem permissão de escrita.<br />";
	}
	else
		echo "Você poderá enviar apenas arquivos \"*.jpg;*.jpeg;*.gif;*.png\"<br />";
}
else{
	echo "Você não enviou nenhum arquivo!";
}

$edita = BD::conn()->prepare("UPDATE usuarios SET nome=?, cidade=?, profissao=?, foto=? WHERE id=?");
$edita->execute(array($nomePessoa,$cidade,$profissao,$novoNome,$_SESSION["id_user"]));
header("Location: chat.php");
?>
</body>
</html>
