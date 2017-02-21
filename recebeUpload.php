<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload de arquivos</title>
</head>

<body>
<?php
session_start();
	require_once('classes/BD.class.php');
	$post = $_POST['post'];
	$agora = date('Y-m-d H:i:s');
// verifica se foi enviado um arquivo 
if(isset($_FILES['arquivo']['name']) && $_FILES["arquivo"]["error"] == 0)
{


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
		$destino = 'img/' . $novoNome; 
		
		// tenta mover o arquivo para o destino
		if( @move_uploaded_file( $arquivo_tmp, $destino  ))
		{
				$inserePost = BD::conn()->prepare("INSERT INTO postagens(id_usuario, fotoPost, conteudo, data) VALUES (?, ?, ?, ?) ");
				$inserePost->execute(array( $_SESSION['id_user'], $novoNome, $post, $agora ));
				header('Location: chat.php');
		}
		else
			echo "Erro ao salvar o arquivo. Aparentemente você não tem permissão de escrita.<br />";
	}
	else
		echo "Você poderá enviar apenas arquivos \"*.jpg;*.jpeg;*.gif;*.png\"<br />";
}
else{
				$inserePost = BD::conn()->prepare("INSERT INTO postagens(id_usuario, fotoPost, conteudo, data) VALUES (?, ?, ?, ?) ");
				$inserePost->execute(array( $_SESSION['id_user'], '', $post, $agora ));
				header('Location: chat.php');
}
?>
</body>
</html>