<?php
/**
 * Created by PhpStorm.
 * User: rauflacerda
 * Date: 18/09/14
 * Time: 17:37
 */
error_reporting(E_ALL);
class PerfilController extends PerfilModel{

    private $view;
    private $edit;

    public function index()
    {
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $dados=$this->getPerfilUsuarios();
        $cat = $this->getCategoriasPerfilArquivos();
        $Data = array("Perfis" => $dados,"Categorias" => $cat);

        $this->view=new View(PATH_VIEW.'listaperfilusuarios.phtml',$Data);
        $this->view->showContents();

    }


    public function CategoriasPerfilAction()
    {
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $Data = $this->getCategoriasPerfilArquivos();

        $this->view=new View(PATH_VIEW.'listaperfilCategorias.phtml',$Data);
        $this->view->showContents();
    }



} 