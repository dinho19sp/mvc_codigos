<?php
/**
	* Description of ComitenteController
	*
	* @author HM2
	*/
class ComitenteController extends ComitenteModel {
	
	protected   $_view;
	public      $_app;
	public      static $_data;
	public      $_resource;
	public      $_message;
	public      $_action;
	private     $_log;
	private     $_session;
	private     $_helper;



    private function app()
    {
        $this->_app = new onCreateClass();
        return $this->_app;
    }


    public function index()
    {
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();
        $this->_view = new View(PATH_VIEW.'ListagemComitente.phtml');
        $this->_view->showContents();

    }

	public function NovoComitenteAction(){

        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        if($this->__post("Salvar") && $this->__post("Salvar")== TRUE)
        {
            $this->Cadastrar();
            $this->app()->getApplication()->redirect($this->app()->getFunctions()->goToUrl('comitente',null));
        }


        $this->_view = new View(PATH_VIEW.'comitente.phtml');
        $this->_view->_message = $this->_message;
        $this->_view->showContents();

	}

    public function EditarComitenteAction(){

        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        if($this->__post("Alterar") && $this->__post("Alterar")== TRUE)
        {
            $this->Atualizar();
            $this->app()->getApplication()->redirect($this->app()->getFunctions()->goToUrl('comitente',null));
        }

        $this->_resource = $this->BuscarComitente();

        $this->_view = new View(PATH_VIEW.'comitente.phtml',$this->_resource);
        $this->_view->_message = $this->_message;
        $this->_view->showContents();

    }

}