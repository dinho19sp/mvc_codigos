<?php
$_data_url = new ElementosRoot();
$data = new UsuariosModel();
$dados = $data->getDadosUsuarios();
$img = $data->getImgUser();
$fnc = new Functions();
$response = $dados[0];
?>

<div class="main-content">
<div class="row">

    <!-- Profile Info and Notifications -->
    <div class="col-md-6 col-sm-8 clearfix">

    <ul class="user-info pull-left pull-none-xsm">

        <!-- Profile Info -->
        <li class="profile-info dropdown"><!-- add class "pull-right" if you want to place this from right -->

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="<?=$img;?>" alt="" class="img-circle" width="44">
                <?=$response->user_nome;?>
            </a>

            <ul class="dropdown-menu">

                <!-- Reverse Caret -->
                <li class="caret"></li>

                <!-- Profile sub-links -->
                <li>
                    <a href="<?=$_data_url->getHost()."usuarios/meus-dados/";?>">
                        <i class="entypo-user"></i>
                        Meus dados
                    </a>
                </li>
                 <li>
                    <a href="<?=$_data_url->getHost()."login/encerrar-sistema/";?>">
                        <i class="entypo-clipboard"></i>
                        Encerrar
                    </a>
                </li>
            </ul>
        </li>

    </ul>

       <div id="ntf_rejeitado"></div>
       <div id="ntf_aprovados"></div>
       <div id="ntf_pendentes"></div>

    </div>


    <!-- Raw Links -->
    <div class="col-md-6 col-sm-4 clearfix hidden-xs">

        <ul class="list-inline links-list pull-right">






            <!-- Logout  -->
            <li>
                <a href="<?=$_data_url->getHost();?>login/encerrar-sistema/">
                    Log Out <i class="entypo-logout right"></i>
                </a>
            </li>
        </ul>

    </div>

</div>
<hr>

<?php
    Functions::breadcrumb();
    $app = new Application();
    $app->Dispatch();

?>



