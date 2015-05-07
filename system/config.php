<?php
/*
 *  Versao martillo 2014
 *  Author Francisco Nascimento
 *
 *  Arquivo Nome: config.php
 *  Local: system/
 *
 *  Define as Constantes com valor dos diretorios da aplicação
 *
 */


define("PATH_ROOT" , "/NovoMartillo/");
//define("PATH_BASE" ,       );
define("PATH_APP"     ,     "application/");
define("PATH_CONTROLLER",   "application/controller/");
define("PATH_MODEL",        "application/models/");
define("PATH_VIEW",         "application/views/");
define("PATH_SYSTEM"  ,     "system/");
define("PATH_INCLUDE" ,     "includes/");
define("PATH_CSS"    ,      "includes/assets/css/");
define("PATH_IMAGES"    ,   "includes/images/");
define("PATH_JSC"    ,      "includes/assets/js/");
define("PATH_HELPERS" ,     "system/helpers/");
define("PATH_CORE"    ,     "system/core/");
define("PATH_PLUGINS"    ,  "system/plugins/");
define("PATH_DATABASE" ,    "system/database/");
define("PATH_APIS"    ,     "system/apis/");
define("PATH_AJAX"      ,   "system/xajax/");
define("PATH_CONFIG"      , "system/app.config/");
define("PATH_CSV"      , "arquivos/");

define("AUTOLOAD_CLASS","autoload.php");

define('E_7874', serialize(array('MSG'=>'Erro em tempo de execução, O objeto não exixte','CODE'=>7874)));
define('E_1001', serialize(array('MSG'=>'Algum parametro não foi informado corretamente','CODE'=>1001)));
//

/**
 *  Define as acoes a serem gravadas no log do sistema
 *
 *  Não alterar antes de verificar seus impactos
 *
 *  Esta diretamente relacionado ao metodo sys_log() no arquivo helpers/Funcitons.php
 *
 *  Pode ser alterado o nome das ações desde que para cada chamada do metodo sys_log seja altedao tambem.
 *
 */


define("LOAD_ACTIONS_LOG" ,
    serialize(
        array(
            'adicionar_novo_perfil_de_usuario'           => array('Adicionar novo perfil de usuario'                        => '_CAD'),
            'alterar_perfil_de_usuario'                  => array('Alterar perfil de usuario'                               => '_ALT'),
            'excluir_perfil_de_usuario'                  => array('Excluir perfil de usuario'                               => '_DEL'),
            'adicionar_novo_usuario'                     => array('Adicionar novo usuario'                                  => '_CAD'),
            'adicionar_dados_acesso_novo_usuario'        => array('Adicionar dados de acesso novo usuario (automatico)'     => '_CAD'),
            'alterar_dados_do_usuario'                   => array('Alterar dados do usuario'                                => '_ALT'),
            'alterar_dados_de_acesso_usuario'            => array('Alterar dados de acesso do usuario'                      => '_ALT'),
            'recover_passoword_usuario'                  => array('Usuario redefiniu sua senha'                             => '_ALT'),
            'excluir_usuario'                            => array('Excluir usuario'                                         => '_DEL'),
            'remover_acao_pagina_usuario'                => array('Remove acao-pagina usuario (Usuario)'                    => '_DEL'),
            'remover_acao_pagina_usuario_sis'            => array('Remove acao-pagina usuario (Sistema)'                    => '_DEL'),
            'remover_permissao_pagina_usuario'           => array('Remove permissao de pagina usuario'                      => '_DEL'),
            'adicionar_permissao_pagina_usuario'         => array('Adicionar permissao de pagina usuario'                   => '_CAD'),
            'adicionar_acao_pagina_usuario'              => array('Adicionar acao-pagina usuario'                           => '_CAD'),
            'vincular_usuario_a_obra'                    => array('Vincular usuario a obra'                                 => '_CAD'),
            'desvincular_usuario_da_obra'                => array('Desvincular usuario da obra'                             => '_DEL'),
            'adicionar_nova_categoria_de_arquivo'        => array('Adicionar nova categoria de arquivos'                    => '_CAD'),
            'alterar_categoria_de_arquivo'               => array('Alterar categoria de arquivos'                           => '_ALT'),
            'excluir_categoria_de_arquivo'               => array('Excluir categoria de arquivos'                           => '_DEL'),
            'desvincular_perfil_da_categoria_de_arquivo' => array('Desvincular perfil de usuario da categoria de arquivos'  => '_DEL'),
            'vincular_perfil_da_categoria_de_arquivo'    => array('Vincular perfil de usuario da categoria de arquivos'     => '_CAD'),
            'excluir_arquivos_da_obra'                   => array('Excluir arquivos da obra'                                => '_DEL'),
            'adicionar_arquivos_da_obra'                 => array('Arquivos arquivos da obra'                               => '_CAD'),
            'excluir_obra'                               => array('Excluir obra'                                            => '_DEL'),
            'adicionar_nova_obra'                        => array('Adicionar nova da obra'                                  => '_CAD'),
            'alterar_dados_da_obra'                      => array('Alterar dados da obra'                                   => '_ALT'),
            'aprovar_arquivos_pendentes_da_obra'         => array('Aprovar arquivos pendentes da obra'                      => '_ALT'),
            'rejeitar_arquivos_pendentes_da_obra'        => array('Rejeitar arquivos pendentes da obra'                     => '_ALT'),
            'adicionar_arquivo_para_usuario'             => array('Permitir arquivos para o usuario'                        => '_CAD'),
            'remover_arquivo_para_usuario'               => array('Remover permissao dearquivos para o usuario'             => '_DEL'),
            'altera_valor_limite_log'                    => array('Alterou quantidade limite de registros do log'           => '_ALT')

        )
    ));