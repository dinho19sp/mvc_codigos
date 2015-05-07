<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 04/09/14
 * Time: 16:02
 */

class UsuariosModel extends phpDataReader{

    private $cmpArray;
    private $setFilter;
    private $cmpTbUsuarios;
    private $cmpTbUsuariosAcessos;

    private $_table = 'tb_usuarios';

    public $_sql;                       // Recebe uma sql

    public $_query;                     // retorna uma consulta de sql

    public $_DataForm;                  // pega o campos do formulario

    public $_condicao;

    private $_Filter;

    private $_response_array;

    /* Retorna os elementos da paginação */

    private $_paginator;

    /* Composição */

    private $_DataReader;

    /* DataSet para DataReader */

    private $_DataSet;

    /* comosicao */

    private $_paginacao;


    private $_rows;


    public function app()
    {
        $app = new onCreateClass();

        return $app;
    }


    private function DataReader()
    {
        $this->_DataReader = new phpDataReader();

        return $this->_DataReader;
    }

    private function DataFilter()
    {
        $this->_Filter = new phpCriteria();

        return $this->_Filter;

    }

    private function DataPaginate()
    {
        $this->_paginator = new xjxHelperPagiacao();

        return $this->_paginator;
    }

    public function Cadastrar()
    {

            $id = $this->addUsuario();

            $this->addUsuarioAcessos($id);


    }

    public function returnStatus($intStatus)
    {
        switch($intStatus)
        {
            case 1:
                $class = "badge-success";
                break;
            case 2:
                $class = "badge-danger";
                break;
        }
        return $class;

    }

    // Verificar esta logica

    public function Alterar()
    {
        $this->altUsuario();

        if($this->__post('user_login') != "" &&  $this->__post('user_pass') != "" &&  $this->__post('user_pass_2') != "")
        {
            $this->altUsuarioAcessos();
        }
    }

    public function Deletar($usuario_id)
    {
        $this->_Filter = new phpCriteria();

        if(is_array($usuario_id))
        {

            foreach ($usuario_id as $key => $value)
            {

                $this->queryDataUpdate($this->_table,array("is_deleted" => "S"),"(id_usuario = {$value})",true);

            }

        }
        else
        {
            $this->_Filter->add(new phpFilter("id_usuario"," = ", $usuario_id));

            $this->queryDataUpdate("tb_usuarios",array("is_deleted" => "S"),$this->_Filter->dump());

        }
    }

    private function getUserGrupo($id)
    {
        $sql = $this->queryDataSelect('tb_perfil',null,'id_perfil ='.$id.'',null,null);
        $query = $this->queryDataRow($sql,phpDataReader::OBJ);


        return $query->user_grupo;


    }

    public function getListaUsuarios(Array $options)
    {
        $this->_DataSet = $this->DataReader();
        $this->_Filter  = $this->DataFilter();
        $this->_paginacao = $this->DataPaginate();

        # Aplica filtro conforme o que for digitado

        $this->_Filter->add(new phpFilter('tbl.is_deleted','=','N'),phpExpression::AND_OPERATOR);

        if($options['txtSearch'] != "")
        {
            $this->_Filter->add(new phpFilter('tbl.user_nome','LIKE','%'.$options['txtSearch'].'%'));
            $this->_Filter->add(new phpFilter('tbl.cpf','LIKE','%'.$options['txtSearch'].'%'),phpExpression::OR_OPERATOR);
            $this->_Filter->add(new phpFilter('tbl.cpf','LIKE','%'.$options['txtSearch'].'%'),phpExpression::OR_OPERATOR);

        }

        # Definindo pagina inicial

        if( $options['PaginaInicial'] == 0   ) {  $pg = 1;  }  else  { $pg = $options['PaginaInicial']; }

        $this->_paginacao->setSetPaginaAtual($pg);
        $this->_paginacao->setTotalPorPagina($options['TotalPorPagina']);
        $this->_paginacao->setIncio( $this->_paginacao->_set_pagina_atual, $this->_paginacao->_qtd_por_pagina);



        $this->_query = $this->_DataSet->queryDataSelect($this->_table.' tbl
                        LEFT JOIN tb_status ts ON tbl.id_status=ts.id_status',
            'tbl.id_usuario as IdL,
             tbl.user_nome,
                tbl.data_cadastro_user,
                tbl.id_status as Status,
                tbl.cpf,
                tbl.ddd_r,
                tbl.telefone_r,
                ts.id_status,
                ts.status_nome,
                tbl.is_deleted',
            $this->_Filter->dump(), $this->_paginacao->_qtd_inicio.",".$this->_paginacao->_qtd_por_pagina,null);
        $this->_rows = $this->_DataSet->Read($this->_query);


        # Retorna o total de registros afetados conforme condicao

        $this->_paginacao->setTotalRegistros($this->_table .' tbl
                   LEFT JOIN tb_status ts ON tbl.id_status=ts.id_status',$this->_Filter->dump(),'tbl.id_usuario as IdL,
                tbl.user_nome,
                tbl.data_cadastro_user,
                tbl.id_status as Status,
                tbl.cpf,
                tbl.ddd_r,
                tbl.telefone_r,
                ts.id_status,
                ts.status_nome,
                tbl.is_deleted');

        $paginator = ceil($this->_paginacao->_total_registros/$this->_paginacao->_qtd_por_pagina);


        for($i=1;$i<=$paginator;$i++)
        {
            if($i==$this->_paginacao->_set_pagina_atual)
            {
                $numeroPaginacao[] = "<li class='active'><a onclick=''>".$i."</a></li>";
            }
            else
            {
                if(isset($options['txtSearch']) && $options['txtSearch'] != "")
                {
                    if(is_int($options['txtSearch']))
                    {
                        $opt = ','.$options['txtSearch'].'';
                    }else{

                        $opt = ',\''.$options['txtSearch'].'\'';
                    }

                }

                $numeroPaginacao[] ='<li style="cursor:pointer;" ><a  onclick="fn_Paginacao(\'fn_ajax_DataGrid_Usuarios\','.$i.','.$options['TotalPorPagina'].$opt.');$(\'#pgs_usuario\').prop(\'value\','.$i.');">'.$i.'</a></li>';
            }
        }

        $paginate = '<div class="dataTables_paginate paging_bootstrap">';
        $paginate .='<ul class="pagination pagination-sm">';
        $paginate .= implode("",$numeroPaginacao);
        $paginate .= '</ul></div>';

        $this->_response_array = array(

            'DataRow' => $this->_rows,
            'Paginacao'  => $paginate

        );


        return $this->_response_array;

    }

    protected function addUsuario()
    {
        $userId = Functions::getUserId();
        $usrGroup = $this->getUserGrupo($this->__post('id_perfil'));

        $this->cmpTbUsuarios = $this->TablesColumns('tb_usuarios');
        $campos = $this->arrayTransform($this->cmpTbUsuarios);
        $this->updateArrayField($campo,'data_nascimento',Functions::fncDataInversa($this->__post('data_nascimento')));

        $qry = $this->queryDataInsert('tb_usuarios',$campos,null,null);

        if($usrGroup == 1)
        {
            $this->addPermissaoPadraoAdministrador($qry);
        }

        return $qry;

    }

    private function addPermissaoPadraoAdministrador($idUsuario)
    {

        $sqlPermissao = $this->queryDataSelect('tb_menu mn LEFT JOIN tb_menu_sub mns USING(id_menu)','mn.id_menu,mns.id_sub_menu');
        $query = $this->Read($sqlPermissao);

        $response = $query;

        if($response != null)
        {

            foreach($response as $key => $values)
            {
                if($values->id_sub_menu == "")
                {
                    $values->id_sub_menu = 0;
                }

               $sql =  $this->queryDataProcedure('SP_MT_ADD_PERMISSAO_PADRAO',array("id_menu"=>$values->id_menu,"id_sub_menu"=>$values->id_sub_menu,"id_usuario"=>$idUsuario));
            }

        }

        $this->queryDataInsert("tb_menu_permissao",array("id_menu"=> 1,"id_sub_menu"=> 0,"id_usuario"=>$idUsuario,"permitido"=>"S"));

    }

    public function BuscaUsuarioRecoverSenha($doc)
    {
        $_Filter = new phpCriteria();

        $_Filter->add(new phpFilter('us.cpf_cnpj','=',$doc));

        $sql = $this->queryDataSelect('tb_usuarios us LEFT JOIN tb_usuarios_acessos us_ac USING(id_usuario)','us.id_usuario,us.nome_usuario,us.cpf_cnpj,us.usuario_email,us_ac.*',$_Filter->dump());
        $query = $this->queryDataRow($sql,phpDataReader::OBJ);

        return $query;
    }

    public function securityCardUsuario($data)
    {
        $host = new ElementosRoot();
        $images = $host->getHost().'includes/images/Codigo/';

        $message = '<table style="display: inline-table;" border="0" cellpadding="0" cellspacing="0" width="680">
            <!-- fwtable fwsrc="Untitled" fwpage="Page 1" fwbase="codigo_seguranca.jpg" fwstyle="Dreamweaver" fwdocid = "1419326382" fwnested="0" -->
              <tr>
               <td colspan="3">Olá , você esta recebendo um código de segurança, referente ao seu cadastro na area do usuário da Mutual construções, este código é para caso esqueça a senha de acesso, você precisara deste para redefinir sua senha.</td>
              </tr>
              <tr>
               <td colspan="3">O código abaixo deve ser digitado no campo solicitado.</td>
              </tr>
              <tr>
               <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
               <td colspan="3"  style="text-align:center;">

               <table style="display: inline-table;-webkit-box-shadow: 2px 3px 14px 2px #CCCCCC;-moz-box-shadow: 2px 3px 14px 2px #CCCCCC;box-shadow: 2px 3px 14px 2px #CCCCCC;" border="0" cellpadding="0" cellspacing="0" width="380">
            <!-- fwtable fwsrc="Untitled" fwpage="Page 1" fwbase="codigo_seguranca.jpg" fwstyle="Dreamweaver" fwdocid = "1419326382" fwnested="0" -->
              <tr>
               <td colspan="3"><img name="codigo_seguranca_r1_c1" src="'.$images.'codigo_seguranca_r1_c1.jpg" width="380" height="140" id="codigo_seguranca_r1_c1" alt="" /></td>
              </tr>
              <tr>
               <td><img name="codigo_seguranca_r2_c1" src="'.$images.'codigo_seguranca_r2_c1.jpg" width="30" height="30" id="codigo_seguranca_r2_c1" alt="" /></td>
               <td width="319" height="30" style="text-align:center;">'.$data->usuario_security.'</td>
               <td width="31" height="30"><img name="codigo_seguranca_r2_c3" src="'.$images.'codigo_seguranca_r2_c3.jpg" width="31" height="30" id="codigo_seguranca_r2_c3" alt="" /></td>
              </tr>
              <tr>
               <td colspan="3" width="380" height="30"><img name="codigo_seguranca_r3_c1" src="'.$images.'codigo_seguranca_r3_c1.jpg" width="380" height="30" id="codigo_seguranca_r3_c1" alt="" /></td>
              </tr>
            </table>

               </td>
              </tr>
            </table>';


        return $message;
    }

    protected  function addUsuarioAcessos($userId)
    {
        $code = new Encryption();
        $security_code_user = $code->setSecurityCode();

        $this->cmpTbUsuariosAcessos = array("id_usuario","user_login","user_pass","user_security");
        $campo = $this->arrayTransform($this->cmpTbUsuariosAcessos);

        $salt = Bcrypt::generateRandomSalt();
        $hash = Bcrypt::hash($this->__post("user_pass"));



        if($this->__post('user_pass') != "" && $this->__post('user_pass_2') != "" )
        {
            $this->updateArrayField($campo,'user_security',$security_code_user);
            $this->updateArrayField($campo,'user_pass',$hash);
        }

        $this->updateArrayField($campo,'id_usuario',$userId);

        /* Executa a query de inserção */

        $qry = $this->queryDataInsert('tb_usuarios_acessos',$campo);

        return $qry;
    }

    protected function altUsuario()
    {
        $idUsuario = $this->app()->getApplication()->getParam('srcid');
        $this->cmpTbUsuarios = $this->TablesColumns('tb_usuarios');
        $campos = $this->arrayTransform($this->cmpTbUsuarios);

        $this->updateArrayField($campos,'data_nascimento',Functions::fncDataInversa($this->__post('data_nascimento')));

        $userGrupo = $this->getUserGrupo($this->__post('id_perfil'));

        $this->setFilter = new phpCriteria();

        $this->setFilter->add(new phpFilter('id_usuario','=',$idUsuario));

        $query = $this->queryDataUpdate('tb_usuarios',$campos,$this->setFilter->dump());
        return $query;

    }

    protected function altUsuarioAcessos()
    {
        $code = new Encryption();
        $security_code_user = $code->setSecurityCode();

        $this->cmpTbUsuariosAcessos = array("user_login","user_pass","user_security");
        $campo = $this->arrayTransform($this->cmpTbUsuariosAcessos);

        $salt = Bcrypt::generateRandomSalt();
        $hash = Bcrypt::hash($this->__post('user_pass'));

        if($this->__post('user_pass') != "" && $this->__post('user_pass_2') != "" )
        {
            $this->updateArrayField($campo,'user_security',$security_code_user);
            $this->updateArrayField($campo,'user_pass',$hash);
        }

        $idUsuario = $this->app()->getApplication()->getParam('srcid');

       // $this->updateArrayField($campo,'id_usuario',$this->__post('id_usuario'));


        $this->setFilter = new phpCriteria();

        $this->setFilter->add(new phpFilter('id_usuario','=',(int)$idUsuario));

        $query = $this->queryDataUpdate('tb_usuarios_acessos',$campo,$this->setFilter->dump());


        return $query;
    }

    public function checkDataExists($tabela,Array $value)
    {
        $_Fill = new phpCriteria();
        if(is_array($value))
        {
            foreach($value as $key => $val)
            {
                $_Fill->add(new phpFilter($key,"=",$val),phpExpression::OR_OPERATOR);
            }
        }

        $query = $this->queryDataSelect($tabela,NULL,$_Fill->dump());

        if($query->RowCount() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getDadosUsuarios($id_usuario=NULL) {

        if($id_usuario == "" OR $id_usuario == NULL)
        {
            $user_id = Functions::getUserId();
        }
        else
        {
            $user_id = $id_usuario;
        }


        $data = $this->queryDataProcedure('SP_MT_GET_DADOS_USUARIO',array($user_id));

        return $data;

    }

    public function getImgUser()
    {
        $id_usuario = Functions::getUserId();

        $_Filter = new phpCriteria();
        $_Filter->add(new phpFilter('proprietario','=','tb_usuarios'));
        $_Filter->add(new phpFilter('id_proprietario','=',$id_usuario),phpExpression::AND_OPERATOR);

        $sql = $this->queryDataSelect('tb_images','id_proprietario,nome_image,path_image,proprietario',$_Filter->dump(),null,null);
        $query = $this->queryDataRow($sql,phpDataReader::OBJ);
        $no_image = $this->app()->getElementosRoot()->getHost().'includes/images/noavatar.gif';
        if($query)
        {
            if(file_exists(PATH_IMAGES.$query->nome_image))
            {
                return $this->app()->getElementosRoot()->getHost().PATH_IMAGES.$query->nome_image;
            }
            else
            {
                return $no_image;
            }

        }
        else
        {
            return $no_image;
        }




    }

    public function EditarDadosProfile()
    {
        $id_usuario = Functions::getUserId();

        $campos = array(
            'id_usuario' => $id_usuario,
            'nome_usuario' => $this->__post('nome_usuario'),
            'usuario_email' => $this->__post('email_usuario'),
            'telefone' => $this->__post('telefone'),
            'celular' => $this->__post('celular'),
            'id_perfil' => ""
        );

        if($this->__post('id_perfil')):

            $this->updateArrayField($campos,'id_perfil',$this->__post('id_perfil'));

        endif;

        $this->UploadAvatarUser();

        $sql = $this->queryDataProcedure('SP_MT_ALT_DADOS_USUARIO',$campos);

        $return = $sql[0];

        if($return->sucesso == 1):

            $this->app()->getApplication()->redirect($this->app()->getFunctions()->goToUrl('usuarios','meus-dados'));

        endif;
    }

    public function EditarSenhaProfile($id=NULL)
    {
        if($id==NULL):

            $id_usuario = Functions::getUserId();

        else:

            $id_usuario = $id;

        endif;

        $security = new Encryption();
        $code = $security->setSecurityCode();


        $campos = array(
            'id_usuario'=> $id_usuario,
            'usuario_security' => $code,
            'usuario_pass' => md5($this->__post('usuario_pass')));



        $sql = $this->queryDataProcedure('SP_MT_ALT_SENHA_USUARIO',$campos,true);


        $return = $sql[0];

        return $return;
    }

    public function FilesUpload()
    {
        $up = new Uploads();
        $_File = new DataFileUpload('foto',PATH_IMAGES);
        $USER_ID = Functions::getUserId();

        for($i=0;$i < $_File->image_count;$i++)
        {
            $_File->setImageExtensao($i);

            switch($_File->image_extensao)
            {
                case 'jpeg':
                case 'jpg' :
                    $ext = '.jpg';
                    break;
                case 'gif':
                    $ext = '.gif';
                    break;
                case 'png':
                    $ext = '.png';
                    break;
            }

            # Separa as imagens da Home e detalhes
            # colocando um identificador para distinguir as imagens principais

            $rename_image_explode = explode(" ",$this->__post('nome_usuario'));
            $rename_image_implode = implode("-",$rename_image_explode);
            $_File->setImageRename($rename_image_implode.$ext);

            if(is_file($_File->image_temp[$i]))
            {
                $up->foto(strtolower($_File->image_rename), $_File->image_temp[$i]);
                $up->upload_imagem($_File->image_path);

                $campos_media = array(
                    'nome_image'       => strtolower($_File->image_rename),
                    'path_image'       =>  $_File->image_path,
                    'id_proprietario'   => (int)$USER_ID,
                    'proprietario' => 'tb_usuarios'
                );

                $this->queryDataProcedure('SP_GRAVA_IMAGEM',$campos_media);

            }
        }
    }

    public function UploadAvatarUser()
    {

        $up = new Uploads();
        $_avatar = new DataFileUpload('foto',PATH_IMAGES);
        $USER_ID = Functions::getUserId();

        for($i=0;$i < $_avatar->image_count;$i++)
        {
            $_avatar->setImageExtensao($i);

            $ext = '';

            switch($_avatar->image_extensao)
            {
                case 'jpeg':
                case 'jpg' :
                    $ext = '.jpg';
                    break;
                case 'gif':
                    $ext = '.gif';
                    break;
                case 'png':
                    $ext = '.png';
                    break;
            }


            $rename_image_explode = explode(" ",$this->__post('nome_usuario'));
            $rename_image_implode = implode("-",$rename_image_explode);
            $_avatar->setImageRename($rename_image_implode.$ext);

            if(is_file($_avatar->image_temp[$i]))
            {

                $up->img_resize( $_avatar->image_temp[$i] , 494 , $_avatar->image_path , strtolower($_avatar->image_rename));

                $campos_media = array(
                    'nome_image'       => strtolower($_avatar->image_rename),
                    'path_image'       =>  $_avatar->image_path,
                    'id_proprietario'   => (int)$USER_ID,
                    'proprietario' => 'tb_usuarios'
                );

                $this->queryDataProcedure('SP_MT_GRAVA_IMAGEM',$campos_media);

            }

        }

    }


} 