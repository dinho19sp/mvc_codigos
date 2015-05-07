<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 04/09/14
 * Time: 16:02
 */
error_reporting(E_ALL);
class UsuariosController extends UsuariosModel {

    private $view;

    public function index()
    {

        Functions::is_logado();

        $validPage = new Functions();
        $validPage->blockPage();

        $this->view=new View(PATH_VIEW.'ListagemUsuarios.phtml');
        $this->view->showContents();
    }


    public function NovoUsuarioAction()
    {
        Functions::is_logado();

        $validPage = new Functions();
        $validPage->blockPage();


        if($this->__post('Salvar') && $this->__post('Salvar') == true)
        {
            $this->Cadastrar();
        }

        $this->view=new View(PATH_VIEW.'cadastrousuario.phtml');


        $this->view->showContents();

    }

    public function EditarUsuarioAction()
    {
        Functions::is_logado();

        $validPage = new Functions();
        $validPage->blockPage();
        $srcid = $this->app()->getApplication()->getParam('srcid');
        $query = $this->getDadosUsuarios($srcid);

        if($this->__post('Alterar') && $this->__post('Alterar') == true)
        {
            $this->Alterar();
        }

        $this->view=new View(PATH_VIEW.'cadastrousuario.phtml',$query);


        $this->view->showContents();

    }

    public function MeusDadosAction()
    {
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $Data2 = $this->getImgUser();




        if($this->__post('Salvar') && $this->__post('Salvar')=='Alterar')
        {
            $this->EditarDadosProfile();

                $this->app()->getFunctions()->getMessageScript('Dados alterados com sucesso');

        }

        if($this->__post('Salvar') && $this->__post('Salvar')=='AlerarSenha')
        {
            $ret = $this->EditarSenhaProfile();

            if($ret->sucesso == 1):

                $this->app()->getFunctions()->getMessageScript('Senha alterada com sucesso');

            endif;
        }

        $DataReader = array('DADOS_USER' => $Data,'USER_PROJECT' => $Data1,'IMG_USER' => $Data2);

			
        $this->view = new View(PATH_VIEW.'profile.phtml',$DataReader);
        $this->view->showContents();

    }

    public function MinhasNotificacoesAction()
    {
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $ListaNotificacao = new ContentsHelpers();

        $status = "";

        $status = $this->app()->getApplication()->getParam('view-list');

        $Lista = $ListaNotificacao->getListaNotificacaoUsuarios($status);

        $this->view = new View(PATH_VIEW.'notificacoes_usuario.phtml',$Lista);
        $this->view->showContents();
    }

    public function MidiasAction() {

        Functions::is_logado();
        $validPage = new Functions();
        $_session = Registry::getInstance('SessionStart');
        $validPage->blockPage();

        $id_obra = $this->app()->getApplication()->getParam('srcid');

        $_enc = new Encryption();

        $_session->setVars('OPUS_KEY',$_enc->encrypt($id_obra));

        if(isset($_COOKIE['historyBack']))
        {
            $this->app()->getApplication()->redirect($_COOKIE['historyBack']);

        }else{

            $this->app()->getElementosRoot()->removeCookieSite(array('historyBack'));

            $id_usuario = Functions::getUserId();

            if(!empty($id_obra) && !empty($id_usuario)) {

                $categorias = $this->midiasCategoria($id_usuario, $id_obra);
                $this->view = new View(PATH_VIEW.'midiausuario.phtml',$categorias);
                $this->view->showContents();

            }
        }

    }

    public function MidiaDetalhesAction() {

        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $host = $this->app()->getElementosRoot()->getHost();

        $id_midia = $this->app()->getApplication()->getParam('srcid');
        $id_usuario = Functions::getUserId();

        $usuarios_obras = $this->usuariosObras($id_midia, $id_usuario);

        if($usuarios_obras) {
            $this->view = new View(PATH_VIEW.'midiadetalhes.phtml',$usuarios_obras);
            $this->view->showContents();
        } else {
            $this->app()->getApplication()->redirect($host."usuarios/meus-dados/");
        }

    }

} 