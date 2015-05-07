<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors',0);
error_reporting(E_ERROR);
//if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
include "system/config.php";

# Autoload Class

if(file_exists(PATH_CORE.AUTOLOAD_CLASS))
{
    if(is_file(PATH_CORE.AUTOLOAD_CLASS))
    {
        require_once(PATH_CORE.AUTOLOAD_CLASS);
    }
    else
    {
        user_error(ERROR_FILE_INVALID,E_USER_WARNING);
    }
}
else
{
    user_error(ERROR_FILE_NOFOUND,E_USER_WARNING);
}


# Dados de Configuração

$config = new Connection();
$Configuration = $config->xmlData("bootstrap");

# Session Class

$classSession = (string)$Configuration->sessionClass;
$session = new $classSession(TRUE);

$ajax = new XajaxController(true);
$xajax = new xjxHelperPagiacao();

ob_start();

## Imprimir variaveis de sessao ativa;
## Para mostrar as veriaveis de sessao descomente a linha abaixo;


//print $session->PrintSessionVars();


## Imprimir variaveis de sessao ativa;
## Para destruir as veriaveis de sessao descomente a linha abaixo;



//$session->SessionDestroy(True);


## Define qual ambiente esta sendo executado a aplicação

$environment = new ElementosRoot();
$environment->setHost(''.trim($Configuration->environment->local).'');


/*
$criteria = new phpCriteria();
$criteria->add(new phpFilter($teste->_FiedsTable[3],"=",'cpf'));
$criteria->add(new phpFilter('id_usuario',"=",87),phpExpression::OR_OPERATOR);

$te = $teste->queryDataDelete('tb_usuario',$criteria->dump());
$teste->debug($te,"P");
$teste->debug($teste->_FiedsTable,"P");
*/

if(!$session->getVars('USER_ID') && !$session->getVars('USER_TOKEN'))
{
    $route = new Application();
    $route->Dispatch();
}
else
{


    if(is_file(PATH_INCLUDE.$Configuration->includes->header))
    {
        include (PATH_INCLUDE.$Configuration->includes->header);
    }
    else
    {
        user_error($Configuration->includes->header. " nao encontrado",E_USER_WARNING);
    }

    # SIDEBAR

    if(is_file(PATH_INCLUDE.$Configuration->includes->sidebar))
    {
        include (PATH_INCLUDE.$Configuration->includes->sidebar);
    }
    else
    {
        user_error($Configuration->includes->sidebar. " nao encontrado",E_USER_WARNING);
    }

    # CONTENTS


    if(is_file(PATH_INCLUDE.$Configuration->includes->contents))
    {
        include (PATH_INCLUDE.$Configuration->includes->contents);
    }
    else
    {
        user_error($Configuration->includes->contents. " nao encontrado",E_USER_WARNING);
    }


    # CONTENTS


    if(is_file(PATH_INCLUDE.$Configuration->includes->footer))
    {
        include (PATH_INCLUDE.$Configuration->includes->footer);
    }
    else
    {
        user_error($Configuration->includes->footer. " nao encontrado",E_USER_WARNING);
    }
}