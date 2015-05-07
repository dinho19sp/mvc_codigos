<?php
/**
	* Description of PatiosController
	*
	* @author HM2
	*/
class PatiosController extends PatiosModel {
	
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

        $this->_view = new View(PATH_VIEW.'ListagemPatios.phtml');
        $this->_view->showContents();

    }

	public function NovoPatioAction(){

        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        if($this->__post("Salvar") && $this->__post("Salvar")== TRUE)
        {
            $this->Cadastrar();
            $this->app()->getApplication()->redirect($this->app()->getFunctions()->goToUrl('patios',null));
        }


        $this->_view = new View(PATH_VIEW.'local_patios.phtml');
        $this->_view->_message = $this->_message;
        $this->_view->showContents();

	}

    public function EditarPatioAction(){


        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        if($this->__post("Alterar") && $this->__post("Alterar")== TRUE)
        {
            $this->Atualizar();
            $this->app()->getApplication()->redirect($this->app()->getFunctions()->goToUrl('patios',null));
        }

        $this->_resource = $this->BuscarPatios();

        $this->_view = new View(PATH_VIEW.'local_patios.phtml',$this->_resource);
        $this->_view->_message = $this->_message;
        $this->_view->showContents();

    }

}