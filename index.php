<?php 
session_start();
    require_once('classes/BD.class.php');
    BD::conn();
?>

<!DOCTYPE html>
<html>
<title>W3.CSS Template</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="http://www.w3schools.com/lib/w3-theme-blue-grey.css">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5 {font-family: "Open Sans", sans-serif}
#users_online{position:fixed; top:50px;}
</style>
<body class="w3-theme-l5">
   <style type="text/css">
        #pesquisaCliente{
            width:200px;
            height: 30px;
        }
        #form_pesquisa{
            margin-top:9px;
            float: right;
            right: 30px;
            margin-right: 10px
        }
        #bottom {
          margin-bottom: 5px;
          margin-right: 10px
        }
        #create {
           margin-top:9px;
            float: right: ;
            right: 30px;
            margin-left: 860px
        }
        #register {
           margin-top:9px;
            float: right;
            margin-right: 90px
        }

    </style>

         <?php
                if(isset($_POST['acao']) && $_POST['acao'] == 'logar'){
                    $email = strip_tags(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING)));
                    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
                    if($email == ''){
                        header("Location: #openModal");
                    }if($senha == ''){
                        header("Location: #openModal1");
                    }else{
                        $pegaUser = BD::conn()->prepare("SELECT * FROM `usuarios` WHERE `email` = ? AND `senha` = ?");
                        $pegaUser->execute(array($email, $senha));

                        if($pegaUser->rowCount() == 0){
                            echo 'Não encontramos este login!';
                        }else{
                            $agora = date('Y-m-d H:i:s');
                            $limite = date('Y-m-d H:i:s', strtotime('+2 min'));
                            $update = BD::conn()->prepare("UPDATE `usuarios` SET `horario` = ?, `limite` = ? WHERE `email` = ? AND `senha` = ?");
                            if( $update->execute(array($agora, $limite, $email, $senha)) ){
                                while($row = $pegaUser->fetchObject()){
                                    $_SESSION['email_logado'] = $email;
                                    $_SESSION['id_user'] = $row->id;
                                    header("Location: chat.php");
                                }
                            }//se atualizou
                        }
                    }
                }
                 if(isset($_POST['action']) && $_POST['action'] == 'registrar'){
                      $snome = $_POST['snome'];
                      $name = $_POST['nome'];
                      $nome = $name . ' '. $snome;
                      $email = $_POST['email'];
                      $senha = base64_encode($_POST['senha']);
                      $sexo = $_POST['sexo'];
                      $nascimento = $_POST['nascimento'];
                      if($email == ''){
                          header("Location: #openModal");
                      }if($senha == ''){
                          header("Location: #openModal1");
                      }else{
                          $pegaUser = BD::conn()->prepare("SELECT * FROM `usuarios` WHERE `email` = ?");
                          $pegaUser->execute(array($email));

                          if($pegaUser->rowCount() >= 1){
                              echo 'Email já registrado!';
                          }else{
                              $agora = date('Y-m-d H:i:s');
                              $limite = date('Y-m-d H:i:s', strtotime('+2 min'));
                              $update = BD::conn()->prepare("INSERT INTO usuarios(nome, email, senha, nascimento, horario, limite, sexo) VALUES(?, ?, ?, ?, ?, ?, ?)");
                              $update->execute(array($nome, $email, $senha, $nascimento, $agora, $limite, $sexo));
                              $pegaUser->execute(array($email));
                                  while($row = $pegaUser->fetch()){
                                      $_SESSION['email_logado'] = $email;
                                      $_SESSION['id_user'] = $row['id'];
                                      echo "<script>alert('$senha')</script>";
                                      header("Location: chat.php");
                                  }
                              }//se atualizou
                          }
                    }
                  
            ?>

<div class="formulario">
<!-- Navbar -->
<div class="w3-top">
 <ul class="w3-navbar w3-theme-d2 w3-left-align w3-large">
  <li class="w3-hide-medium w3-hide-large w3-opennav w3-right">
    <a class="w3-padding-large w3-hover-white w3-large w3-theme-d2" href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a>
  </li>
  <li><a href="#" class="w3-padding-large w3-theme-d4"><i class="fa fa-home w3-margin-right"></i>MyClub</a></li>
   <form name="form_pesquisa" id="form_pesquisa" method="post" action="">
              <input type="text" name="email" id="pesquisaCliente"  placeholder="Email"  class="w3-border w3-padding"/>
              <input type="password" name="senha" id="pesquisaCliente"  placeholder="Senha"  class="w3-border w3-padding"/>
              <input type="hidden" name="acao" value="logar" />
              <input type="submit" name="Entrar" value="Entrar" class="w3-btn w3-theme" id="bottom" class="botao right">
        </form> </ul>
        </div>
</div>
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">
<div>
  <h1 id="create">Criar uma nova conta</h1>
</div>
    <form name="form_pesquisa" id="register" method="post" action="">
                  <input type="text" name="nome" style="width: 200px" placeholder="Nome"  class="w3-border w3-padding" required />
                  <input type="text" name="snome" style="width: 200px" placeholder="Sobrenome"  class="w3-border w3-padding" required /><br><br>
                  <input type="email" name="email" style="width: 400px" placeholder="Email"  class="w3-border w3-padding" required/><br><br>
                  <input type="password" name="senha" style="width: 400px" placeholder="Senha"  class="w3-border w3-padding" required/><br><br>
                  <input type="date" name="nascimento" style="width: 400px"  class="w3-border w3-padding" required/><br><br>
                  <input type="radio" name="sexo" value="0" />
                  <span>Feminino</span>
                  <input type="radio" name="sexo" value="1" />
                  <span>Masculino</span><br>
                  <input type="hidden" name="action" value="registrar" /><br>
                  <input type="submit" name="cadastrar" value="Criar conta" class="w3-btn w3-theme  w3-padding" id="bottom" class="botao right">
            </form> 
</div>

  <!-- End Grid -->
  </div>
  
<!-- End Page Container -->
</div>
<br>

<div id="openModal" class="modalDialog">
  <div>
    <a href="#close" title="Close" class="close1">X</a>
    <h2>Erro</h2>
    <p>Informe o seu endereço de email corretamente!</p>
  </div>
</div>


<div id="openModal1" class="modalDialog">
  <div>
    <a href="#close" title="Close" class="close1">X</a>
    <h2>Erro</h2>
    <p>Informe a sua senha corretamente!</p>
  </div>
</div>

    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/functions.js"></script>
    <script type="text/javascript" src="js/pesquisa.js"></script>
    <script type="text/javascript" src="js/jquery-2.1.0.js"></script>


</body>
</html> 
