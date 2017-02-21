<script type="text/javascript" src="js/jquery-2.1.0.js"></script>
 <script type="text/javascript">
  $(document).ready(function(){


    // aqui a função ajax que busca os dados em outra pagina do tipo html, não é json
    function load_dados(valores, page, div)
    {
        $.ajax
            ({
                type: 'POST',
                dataType: 'html',
                url: page,
                beforeSend: function(){//Chama o loading antes do carregamento

        },
                data: valores,
                success: function(msg)
                {

                    var data = msg;
              $(div).html(data).fadeIn();                       }
            });
    }

    //Aqui eu chamo o metodo de load pela primeira vez sem parametros para pode exibir todos
    load_dados(null, 'pesquisa.php', '#MostraPesq');


    //Aqui uso o evento key up para começar a pesquisar, se valor for maior q 0 ele faz a pesquisa
    $('#pesquisaCliente').keyup(function(){

        var valores = $('#form_pesquisa').serialize()//o serialize retorna uma string pronta para ser enviada

        //pegando o valor do campo #pesquisaCliente
        var $parametro = $(this).val();

        if($parametro.length >= 1)
        {
            load_dados(valores, 'pesquisa.php', '#MostraPesq');
        }else
        {
            load_dados(null, 'pesquisa.php', '#MostraPesq');
        }
    });

  });
  </script>


  <?php 
        $verificaSol = BD::conn()->prepare("SELECT * FROM `solicitacoes` INNER JOIN usuarios on usuarios.id=solicitacoes.id_usuario1 OR usuarios.ID=solicitacoes.id_usuario2 WHERE  usuarios.id != ? AND solicitacoes.id_usuario1 = ? or solicitacoes.id_usuario2 = ? and usuarios.id != ? LIMIT 3");
        $verificaSol->execute(array($_SESSION['id_user'], $_SESSION['id_user'], $_SESSION['id_user'], $_SESSION['id_user']));
        $numSol = $verificaSol->rowCount();
           

  ?>
<!-- Navbar -->
<div class="w3-top">
 <ul class="w3-navbar w3-theme-d2 w3-left-align w3-large">
  <li class="w3-hide-medium w3-hide-large w3-opennav w3-right">
    <a class="w3-padding-large w3-hover-white w3-large w3-theme-d2" href="javascript:void(0);" onclick="openNav()"><i class="fa fa-bars"></i></a>
  </li>
  <li><a href="#" class="w3-padding-large w3-theme-d4"><i class="fa fa-home w3-margin-right"></i>MyClub</a></li>
  <li class="w3-hide-small"><a href="chat.php" class="w3-padding-large w3-hover-white" title="News"><i class="fa fa-globe"></i></a></li>
  <li class="w3-hide-small"><a href="perfil.php?user=<?php echo  $_SESSION['id_user']; ?>" class="w3-padding-large w3-hover-white" title="Account Settings"><i class="fa fa-user"></i></a></li>
  <li class="w3-hide-small"><a href="#" class="w3-padding-large w3-hover-white" title="Messages"><i class="fa fa-envelope"></i></a></li>
  <li class="w3-hide-small w3-dropdown-hover">
    <a href="#" class="w3-padding-large w3-hover-white" title="Notifications"><i class="fa fa-bell"></i><span class="w3-badge w3-right w3-small w3-green"><?php echo $numSol; ?></span></a>
    <div class="w3-dropdown-content w3-white w3-card-4">
    <?php while ($row = $verificaSol->fetch()) {
            $id = $row['id_solicitacao'];
            $foto = ($row['foto'] == '') ? 'default.jpg' : $row['foto'];
            $id_amigo1 = $row['id_usuario1'];
            $id_amigo2 = $row['id_usuario2'];
            $nome = $row['nome'];
            echo  "<a href='#'></a><img src='img/". $foto ."' alt='Avatar' class='w3-left w3-circle w3-margin-right' style='width:30px'>";
            echo $nome." Enviou uma solicitação";
            echo "<div class='w3-half'><form method='post'> <button class='w3-btn w3-green w3-btn-block w3-section' name='botao' value='1' type='submit'><i class='fa fa-check'></i></button></div>". "<div class='w3-half'><button class='w3-btn w3-red w3-btn-block w3-section' name='botao' value='0' type='submit'><i class='fa fa-remove'></i></button></div></form>";
            if (isset($_POST['botao']) && $_POST['botao'] == "0" && !empty($verificaSol) && isset($id)){
                  $solicitacao = BD::conn()->prepare("DELETE FROM solicitacoes WHERE id_solicitacao=?");
                  $solicitacao->execute(array($id));
              }
       if (isset($_POST['botao']) && $_POST['botao'] == "1" && !empty($verificaSol)){
                  $solicitacao = BD::conn()->prepare("DELETE FROM solicitacoes WHERE id_solicitacao=?");
                  $solicitacao->execute(array($id));
                  $solicitacao3 = BD::conn()->prepare("INSERT INTO amigos(id_amigo1,id_amigo2) VALUES(?,?)");
                  $solicitacao3->execute(array($id_amigo1, $id_amigo2));
                  $solicitacao = BD::conn()->prepare("DELETE FROM solicitacoes WHERE id_usuario1=? AND id_usuario2=? or id_usuario2=? AND id_usuario1=? or id_usuario2=? and id_usuario2=? or id_usuario1=? and id_usuario1=?");
                  $solicitacao->execute(array($id_amigo1,$id_amigo2,$id_amigo2,$id_amigo1,$id_amigo1,$id_amigo1,$id_amigo2,$id_amigo2));
                  echo "<<script>window.location='chat.php';</script>";
              }
    }
      

    ?>
    </div>
  </li>
  <li class="w3-hide-small"> <form name="form_pesquisa" id="form_pesquisa" method="post" action="">
              <input type="text" name="pesquisaCliente" id="pesquisaCliente"  placeholder="Pesquisar usuario..."  class="w3-border w3-padding"/>
        </form></li>
  <li class="w3-hide-small w3-right"><a href="?acao=sair" class="w3-padding-large w3-hover-white" title="My Account"><img src="img/sair.png" class="w3-circle" style="height:25px;width:25px" alt="Avatar"></a></li>
 </ul>
</div>


<!-- Navbar on small screens -->
<div id="navDemo" class="w3-hide w3-hide-large w3-hide-medium w3-top" style="margin-top:51px">
  <ul class="w3-navbar w3-left-align w3-large w3-theme">
    <li><a class="w3-padding-large" href="#">Link 1</a></li>
    <li><a class="w3-padding-large" href="#">Link 2</a></li>
    <li><a class="w3-padding-large" href="#">Link 3</a></li>
    <li><a class="w3-padding-large" href="#">Link 4</a></li>
  </ul>
</div>




