<?php
$fnc = new Functions();

if($_SESSION['return'] && $_SESSION['return'] == 1)
{

   //$fnc->returnMessageScript("A pagina que você está tentando acessar é de acesso restrito ou inválida,<br /> se acredita que a pagina seja válida por favor contactar o administrador do sistema");
?>
    <script type="text/javascript">
        fn_xjx_MessageNotification('alert-default','Esta página não esta acessivel ou caminho digitado esta incorreto');

    </script>

<?php
}
?>


<div class="page-error-404">


    <div class="error-symbol">
        <i class="entypo-attention"></i>
    </div>

    <div class="error-text">
        <h2>404</h2>
        <p>Page not found!</p>
    </div>

    <hr />
    <div class="panel">
        <div class="col-md-12"><div id="msg"></div></div>
    </div>
    <div class="error-text">

        <a href="javascript:void(0);" onclick="history.go(-1);">Voltar</a>

        <br />
        <br />



    </div>

</div>