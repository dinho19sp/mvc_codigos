<?php
/*
 *  Instancia os elementos de url , css, js
 *
 *  Definidos no arquivo app.host.config.xml
 *
 *  - Para inserir css basta usar:
 *      $environment->get_css("caminho-arquivo","arquivo-css");
 *
 */
$environment = new ElementosRoot;
ini_set("date.timezone", "America/Sao_Paulo");

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Neon Admin Panel" />
	<meta name="author" content="" />

	<title>Administrativo Mutual Construções</title>

    <?php

        # get files css
        $environment->get_css("jquery-ui-1.10.3.custom.min","includes/assets/js/jquery-ui/css/no-theme/");
        $environment->get_css("entypo","includes/assets/css/font-icons/entypo/css/");
        $environment->get_css("font-awesome.min","includes/assets/css/font-icons/font-awesome/css/");
        $environment->get_css("http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic",NULL,TRUE);
        $environment->get_css("bootstrap");
        $environment->get_css("neon-core");
        $environment->get_css("neon-theme");
        $environment->get_css("neon-forms");
        $environment->get_css("custom");
        $environment->get_css("jquery.fileupload");
        $environment->get_css("jquery.fileupload-ui");

        $environment->get_css("jquery.fancybox", "includes/assets/js/fancybox/");


        # get files javascript
        $environment->get_jsc("jquery-1.11.0.min");

        global $ajax;
        print $ajax->_xajax_js;


    define("MODAL_ALERT",'<div id="fade_msg" class="black_overlay">&nbsp;</div><div id="open_msg" class="white_content"></div>');
    ?>




    <!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->



</head>

    <body class="page-body" data-url="<?=$environment->getHost();?>">
    <?=MODAL_ALERT;?>
    <div class="page-container">
        <div class="row">



        </div>



