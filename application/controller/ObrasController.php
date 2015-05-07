<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 22/10/14
 * Time: 13:47
 */

class ObrasController extends ObrasModel {

    private $_view;

    public function index()
    {
        /*ID DO USUARIO LOGADO */
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $obrasUsuarios = new UsuariosModel();
        $obras = $obrasUsuarios->getObrasUsuarios();

        $this->_view = new View(PATH_VIEW.'obras.phtml',$obras);
        $this->_view->showContents();
    }

    public function ArquivosPendentesAction()
    {
        $validPage = new Functions();
        $validPage->blockPage();
        $this->_view = new View(PATH_VIEW.'arquivospendentes.phtml');
        $this->_view->showContents();
    }

    public function UploadAction() {

        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $id_obra = $this->__post('id_obra');

        if(!empty($id_obra)) {
            $dados = $this->getDadosObras($id_obra);

            if(!empty($dados[0]->path_obra)) {

                $path_obra = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$dados[0]->path_obra.'/';

                $host = $this->app()->getElementosRoot()->getHost();

                $url_thumbnail = $host.$dados[0]->path_obra.'/';

                ob_end_clean();

                $handler = new UploadHandler(array('upload_dir' => $path_obra, 'upload_url' => $url_thumbnail));


                $file = array_keys($handler->image_objects);

                $fileData = $handler->get_response();

                $fileName = $handler->getFileProp();

                //verifica se o arquivo existe
                if(file_exists($file[0]) || file_exists($path_obra.$file[0]->name)) {
                    $this->addArquivos($fileData['files'], $fileName['name'][0], $dados[0]->path_obra);
                }

            } else {
                $this->app()->getFunctions()->getMessageScript('Erro ao fazer upload dos arquivos');

                return false;
            }

            echo $handler;
        }

    }

    public function downloadAction() {
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $host = $this->app()->getElementosRoot()->getHost();

        $id_midia = $this->app()->getApplication()->getParam('srcid');

        $qry_midia = $this->getMidia($id_midia);
        $midia = $qry_midia[0];

        // Fetch the file info.
        $filePath = $midia->path_midia.'/'.$midia->nome_midia;

        if(file_exists($filePath)) {
            $fileName = basename($filePath);
            $fileSize = filesize($filePath);

            switch($midia->tipo_midia) {
                case 'doc':
                    $type = "application/msword";
                    break;
                case 'docx':
                    $type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                    break;
                case 'ppt':
                    $type = "application/vnd.ms-powerpoint";
                    break;
                case 'pptx':
                    $type = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
                    break;
                case 'xls':
                    $type= "application/vnd.ms-excel";
                    break;
                case 'xlsx':
                    $type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                    break;
                case 'dwg':
                    $type = "image/x-dwg";
                    break;
                case 'pdf':
                    $type = "application/pdf";
                    break;
                case 'plt':
                    $type = "application/plt";
                    break;
                case 'jpg':
                case 'jpeg':
                    $type = "image/pjpg";
                    break;
                case 'png':
                    $type = "image/png";
                    break;
                case 'gif':
                    $type = "image/gif";
                    break;
                default:
                    $type = "application/force-download";
                    break;

            }
            ob_end_clean();

            // Output headers.
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: {$type}");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length: ".$fileSize);
            header("Content-Disposition: attachment; filename=".$fileName);

            // Output file.
            readfile ($filePath);
            exit();
        }
        else {
            $this->app()->getApplication()->redirect($host.'usuarios/midia-detalhes/srcid/'.$id_midia);
        }


    }

    public function NovaObraAction()
    {
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $this->_view=new View(PATH_VIEW.'cadastroobra.phtml');

        $obra_exists  = $this->checkDataExists('tb_obras',array('obra_nome'=> $this->__post('obra_nome')));
        $contrato_exists  = $this->checkDataExists('tb_obras',array('obra_contrato'=> $this->__post('obra_contrato')));

        if($this->__post('Confirmar'))
        {

            if($obra_exists == true)
            {
                $this->app()->getFunctions()->getMessageScript('Esta obra já existe');
            }
            elseif($contrato_exists == true)
            {
                $this->app()->getFunctions()->getMessageScript('Este contrato já existe');
            }

            else
            {
                if($this->app()->getFunctions()->permitido("cad_obras") == true)
                {

                   $id = $this->addObra();
                   //$this->app()->getApplication()->redirect($this->app()->getFunctions()->goToUrl('obras','novo',array('srcid'=> $id)));
                   $this->app()->getFunctions()->getMessageScript('Obra cadastrada com sucesso');
                }
                else
                {
                    $this->app()->getFunctions()->getMessageScript("Não é permitido cadastrar o a obra, você não tem permissão suficiente<br /> qualquer duvida favor entrar em contato Administrador",1,2);
                }
            }
        }


        if($this->__post('Alterar'))
        {
            if($this->app()->getFunctions()->permitido("alt_obras") == true)
            {
                $this->altObra();
                $this->app()->getFunctions()->getMessageScript('Dados da obra alterada com sucesso');
            }
            else
            {
                $this->app()->getFunctions()->getMessageScript("Não é permitido alterar os dados da obra, você não tem permissão suficiente<br /> qualquer duvida favor entrar em contato Administrador",1,2);
            }
        }


        $this->_view->showContents();

    }

    public function ObrasUsuariosAction()
    {
        Functions::is_logado();
        $validPage = new Functions();
        $validPage->blockPage();

        $this->_view = new View(PATH_VIEW."obrasusuarios.phtml");
        $this->_view->showContents();
  
    }


}
