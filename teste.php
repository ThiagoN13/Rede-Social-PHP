<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload de arquivos</title>
</head>

<body>
    <form method="post" enctype="multipart/form-data" action="editarUser.php">
      <input name="nome" type="text" />
      <input name="cidade" type="text" />
      <input name="profissao" type="text" />

       Selecione uma imagem: <input name="arquivo" type="file" />
     <br />
       <input type="submit" value="Salvar" />
    </form>
</body>
</html>