<?php
header("Content-type: text/html; charset=utf-8");
    session_start();
    require_once('classes/BD.class.php');
    BD::conn();

    if(!isset($_SESSION['email_logado'], $_SESSION['id_user'])){
        header("Location: index.php");
    }

    $pegaUser = BD::conn()->prepare("SELECT * FROM `usuarios` WHERE `email` = ?");
    $solicitacao = BD::conn()->prepare("SELECT * FROM usuarios INNER JOIN amigos ON usuarios.id!=amigos.id_amigo1 or usuarios.id!=amigos.id_amigo2 WHERE usuarios.id !=? ORDER BY RAND() LIMIT 1
");
    $solicitacao->execute(array($_SESSION['id_user']));
    $dadosSol = $solicitacao->fetch();
    $pegaUser->execute(array($_SESSION['email_logado']));
    $dadosUser = $pegaUser->fetch();

    if(isset($_GET['acao']) && $_GET['acao'] == 'sair'){
        unset($_SESSION['email_logado']);
        unset($_SESSION['id_user']);
        session_destroy();
        header("Location: index.php");
    }


    if(isset($_POST['curtir']) && $_POST['curtir'] == 'curtir'){
        $id_post =$_POST['id_post'];
        $verifica = BD::conn()->prepare("SELECT * FROM `curtidas` WHERE `id_post` = ? and `id_usuario` = ?");
        $verifica->execute(array($id_post,$_SESSION['id_user']));
        $verificacao = $verifica->fetch();
        if(empty($verificacao)){
            $curti = BD::conn()->prepare("INSERT INTO curtidas(id_usuario,id_post) VALUES (?,?)");
            $curti->execute(array($_SESSION['id_user'],$_POST['id_post']));
            header("Location: #$id_post");
        }
    }

    $pegaFotos= BD::conn()->prepare("SELECT fotoPost FROM `usuarios` INNER JOIN postagens ON usuarios.id=postagens.id_usuario inner join amigos ON postagens.id_usuario=amigos.id_amigo1 or postagens.id_usuario=amigos.id_amigo2 WHERE usuarios.id=? ORDER BY data ASC LIMIT 6
");

    $pegaFotos->execute(array($dadosUser['id']));
?>


<!DOCTYPE html>
<html>
<title>Rede Social</title>
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
            width:500px;
        }
        #form_pesquisa{
            margin-top:3px;
        }
        #FormPost{
          width: 700px;
        }
        #botaoPost{
          float: right
        }
    </style>


<?php require_once "header.php";?>

<!-- Page Container -->
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">    
  <!-- The Grid -->
  <div class="w3-row">
    <!-- Left Column -->
    <div class="w3-col m3">
      <!-- Profile -->
      <div class="w3-card-2 w3-round w3-white">
        <div class="w3-container">
         <a href="perfil.php?user=<?php echo $dadosUser['id'];?>"><h4 class="w3-center"><?php echo $dadosUser['nome'];?></h4></a>
         <p class="w3-center"><img src="img/<?php echo ($dadosUser['foto'] == '') ? 'default.jpg' : $dadosUser['foto'];?>" class="w3-circle" style="height:106px;width:106px" alt="Avatar"></p>
         <hr>
         <p><i class="fa fa-pencil fa-fw w3-margin-right w3-text-theme"></i> <?php echo ($dadosUser['profissao'] != '') ? $dadosUser['profissao'] : "Não informado";?></p>
         <p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i> <?php echo ($dadosUser['cidade'] != '') ? $dadosUser['cidade'] : "Não informado";?></p>
         <p><i class="fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme"></i> <?php echo $data=date("d F Y", strtotime($dadosUser['nascimento']));?></p>
        </div>
      </div>
      <br>
      
      <!-- Accordion -->
      <div class="w3-card-2 w3-round">
        <div class="w3-accordion w3-white">
          <a href="editar.php"><button onclick="myFunction('Demo1')" class="w3-btn-block w3-theme-l1 w3-left-align"><i class="fa fa-circle-o-notch fa-fw w3-margin-right"></i> Alterar dados</button></a>
          <button onclick="myFunction('Demo2')" class="w3-btn-block w3-theme-l1 w3-left-align"><i class="fa fa-calendar-check-o fa-fw w3-margin-right"></i> Meus eventos</button>
          <button onclick="myFunction('Demo3')" class="w3-btn-block w3-theme-l1 w3-left-align"><i class="fa fa-users fa-fw w3-margin-right"></i> Minhas fotos </button>
          <div id="Demo3" class="w3-accordion-content w3-container">
         <div class="w3-row-padding">
         <br>
         <?php while($row = $pegaFotos->fetch()){ ?>
           <div class="w3-half">
             <img src="img/<?php echo $row['fotoPost']; ?>" style="width:100%" class="w3-margin-bottom">
           </div>
           <?php } if(empty($pegaFotos)) echo "Não tem fotos";?>
         </div>
          </div>
        </div>      
      </div>
      <br>

      
      <!-- Alert Box -->
      <div class="w3-container w3-round w3-theme-l4 w3-border w3-theme-border w3-margin-bottom w3-hide-small">
        <span onclick="this.parentElement.style.display='none'" class="w3-hover-text-grey w3-closebtn">
          <i class="fa fa-remove"></i>
        </span>
        <p><strong>Hey!</strong></p>
        <p>Sejam bem vindos a sua rede social</p>
      </div>
    
    <!-- End Left Column -->
    </div>

    <span class="user_online" id="<?php echo $dadosUser['id'];?>"></span>
        <aside id="users_online">
            <ul>
            <?php
                $pegaUsuarios = BD::conn()->prepare("SELECT DISTINCT usuarios.id,foto,nome,horario,limite,id_amigo1,id_amigo2 FROM `usuarios` left join amigos ON usuarios.id=amigos.id_amigo1 or usuarios.id=amigos.id_amigo2 WHERE (usuarios.id != ?) AND id_amigo1=? OR id_amigo2=? AND (usuarios.id != ?)");
                $pegaUsuarios->execute(array($_SESSION['id_user'],$_SESSION['id_user'],$_SESSION['id_user'], $_SESSION['id_user']));
                while($row = $pegaUsuarios->fetch()){
                    $foto = ($row['foto'] == '') ? 'default.jpg' : $row['foto'];
                    $agora = date('Y-m-d H:i:s');
                        $status = 'on';
                        if($agora >= $row['limite']){
                            $status = 'off';
                        }
            ?>
                <li id="<?php echo $row['id'];?>">
                    <div class="imgSmall"><img src="img/<?php echo $foto;?>" border="0" /></div>

                        <a href="#" id="<?php echo $_SESSION['id_user'].':'.$row['id'];?>" class="comecar"><?php echo utf8_encode($row['nome']);?></a>
                    
                    <span id="<?php echo $row['id'];?>" class="status <?php echo $status;?>"></span>
                </li>
            <?php }?>
            </ul>
        </aside>

<aside id="chats">
            
        </aside>
    
    <!-- Middle Column -->
    <div class="w3-col m7">
    
      <div class="w3-row-padding">
        <div class="w3-col m12">
          <div class="w3-card-2 w3-round w3-white">
          <div class=caixaPesquisa><div id="contentLoading">
      </div>
      <section class="jumbotron">
        <div id="MostraPesq"></div>
      </section></div>
            <div class="w3-container w3-padding">
              <h6 class="w3-opacity">Publique algo</h6>
                  <form method="post" enctype="multipart/form-data" action="recebeUpload.php">
                        <input type="text" class="w3-border w3-padding" name="post" placeholder="Escreva aqui" id="FormPost" maxlength="144" >
                         <div class="input-group">
                        <span class="input-group-btn">
                            <input class="btn btn-default" type="file" value="Publicar" name="arquivo"></input>
                        </span>
                         <input class="w3-btn w3-theme" type="submit" value="Publicar" id="botaoPost"></input>
                    </div>
                  </form>
            </div>
          </div>
        </div>
      </div>
<?php
                    $pegaUsuarios = BD::conn()->prepare("SELECT DISTINCT usuarios.id, nome,conteudo,fotoPost,data,foto,id_post FROM `usuarios` INNER JOIN postagens ON usuarios.id=postagens.id_usuario LEFT join amigos ON postagens.id_usuario=amigos.id_amigo1 or postagens.id_usuario=amigos.id_amigo2 WHERE id_amigo1=? or id_amigo2=? OR usuarios.id=? ORDER BY data asc");
                    $pegaCurtidas = BD::conn()->prepare("SELECT count(*) AS curtidas FROM curtidas WHERE id_post=?
");
                    $pegaUsuarios->execute(array($_SESSION['id_user'],$_SESSION['id_user'],$_SESSION['id_user']));
                    while($row = $pegaUsuarios->fetch()){
                        $foto = ($row['foto'] == '') ? 'default.jpg' : $row['foto'];
                        $id_post = $row['id_post'];
                        $fotopost =  $row['fotoPost'];
                        $conteudo = $row['conteudo'];
                        $nome = $row['nome'];
                        $data = $row['data'];
                        $pegaCurtidas->execute(array($id_post));
                        while($coluna = $pegaCurtidas->fetch()){
                 ?>

        <div id="<?php echo $id_post;?>">
      <div class="w3-container w3-card-2 w3-white w3-round w3-margin"><br>

     
        <?php echo  "<img src='img/". $foto ."' alt='Avatar' class='w3-left w3-circle w3-margin-right' style='width:60px;height=80px'>"; ?>
        <span class="w3-right w3-opacity"><?php echo $data;?></span>
        <a href="perfil.php?user=<?php echo $row['id'];?>"><h4><?php echo $nome;?></h4><br></a>
        <p><?php echo $conteudo;?></p>
        <hr class='w3-clear'>

        <?php if($fotopost != '')  echo  "<img src='img/". $fotopost ."' style='width:100%' class='w3-margin-bottom'>"; ?>
        <form id="form_pesquisa" method="post" action="">
             <p class="w3-margin-bottom"><?php echo $coluna['curtidas'] ?> Curtidas</p>
             <input type="hidden" name="id_post" value="<?php echo $id_post ?>" placeholder="">
             <button type="submit" class="w3-btn w3-theme-d1 w3-margin-bottom" name="curtir" value="curtir" ><i class="fa fa-thumbs-up"></i> Curtir</button> 
             <button type="submit" class="w3-btn w3-theme-d2 w3-margin-bottom"><i class="fa fa-comment"></i> Comentar</button> 
        </form>
       
      </div> 
         </div>
      <?php }} ?>

    <!-- End Middle Column -->
    </div>

    <!-- End Right Column -->
    </div>
    
  <!-- End Grid -->
  </div>
  
<!-- End Page Container -->
</div>
<br>


 
<script>
// Accordion
function myFunction(id) {
    var x = document.getElementById(id);
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
        x.previousElementSibling.className += " w3-theme-d1";
    } else { 
        x.className = x.className.replace("w3-show", "");
        x.previousElementSibling.className = 
        x.previousElementSibling.className.replace(" w3-theme-d1", "");
    }
}

// Used to toggle the menu on smaller screens when clicking on the menu button
function openNav() {
    var x = document.getElementById("navDemo");
    if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
    } else { 
        x.className = x.className.replace(" w3-show", "");
    }
}
</script>
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/functions.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/jquery_play.js"></script>


</body>
</html> 
