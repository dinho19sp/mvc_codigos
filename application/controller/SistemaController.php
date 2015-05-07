<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 02/02/2015
 * Time: 18:46
 */

class SistemaController extends SistemaModel{

    private $_view;

    public function PermissaoAcessosUsuariosAction()
    {
        Functions::is_logado();

        $validPage = new Functions();
        $validPage->blockPage();
        $this->_view = new View(PATH_VIEW.'permissao_acessos_usuarios.phtml');
        $this->_view->showContents();
    }

    public function errorAction()
    {
        $this->_view = new View(PATH_INCLUDE.'404.php');
        $this->_view->showContents();
    }

    public function ListaLogSalvoAction()
    {
        $arq = $this->getCsvLog();
        $this->_view = new View(PATH_VIEW.'extra-gallery-single.phtml',$arq);
        $this->_view->showContents();
    }

    public function logSistemaAction()
    {
        Functions::is_logado();

        $validPage = new Functions();
        $validPage->blockPage();

        $perc = $this->getTotalRegistro();
        $this->_view = new View(PATH_VIEW.'log-do-sistema.phtml',$perc);
        $this->_view->showContents();
    }

    public function CsvAction()
    {
        $this->CsvGerar();
    }
} 