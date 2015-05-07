<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 26/02/15
 * Time: 11:34
 */

class ContentsHelpers {

    public  $_DataTable;
    public  $_TotalArquivosRejeitados;
    public  $_TotalAarquivosAprovados;

    /**
     * @param mixed $DataTableUsuarios
     */
    private function setDataTableUsuarios($DataTable)
    {
        $this->_DataTable = $DataTable;
    }

    /**
     * @return mixed
     */
    private function getDataTable()
    {
        return $this->_DataTable;
    }

    /**
     * @param mixed $TotalAarquivosAprovados
     */
    private function setTotalAarquivosAprovados($TotalAarquivosAprovados)
    {
        $this->_TotalAarquivosAprovados = $TotalAarquivosAprovados;
    }

    /**
     * @return mixed
     */
    private function getTotalAarquivosAprovados()
    {
        return $this->_TotalAarquivosAprovados;
    }

    /**
     * @param mixed $TotalArquivosRejeitados
     */
    private function setTotalArquivosRejeitados($TotalArquivosRejeitados)
    {
        $this->_TotalArquivosRejeitados = $TotalArquivosRejeitados;

    }

    /**
     * @return mixed
     */
    private function getTotalArquivosRejeitados()
    {
        return $this->_TotalArquivosRejeitados;
    }


    public function getListaNotificacaoUsuarios($status)
    {
        switch($status)
        {
            case 3:
                $Data = $this->getArquivosRejeitadosUsuarios();
                break;
            case 1:
                $Data = $this->getArquivosAprovadosUsuarios();
                break;
            case 0:
                $Data = $this->getArquivosPendentesUsuarios();
                break;
        }

        return $Data;

    }

    public function MontaNotificacoesUsuario($status)
    {

        switch($status)
        {
            case 3:
                $Data = $this->getArquivosRejeitadosUsuarios();
                break;
            case 1:
                $Data = $this->getArquivosAprovadosUsuarios();
                break;
            case 0:
                $Data = $this->getArquivosPendentesUsuarios();
                break;
        }

        $host = new ElementosRoot();
        $qtd = $Data['Qtd_arquivos'];
        $file = $Data['Lista_arquivos'];
        $app = new Functions();
        switch($status)
        {
            case 3:
                $options = array('icon'=> 'entypo-attention','info'=> 'badge-info','text'=> ' Você tem <strong>'.$qtd.'</strong> arquivos rejeitados .','link'=> "<a href='javascript:void(0);' onclick='window.location=\"".$app->goToUrl('usuarios','minhas-notificacoes',array('view-list'=>3))."\"'>Ir para notificaçoes</a>");

                break;
            case 1:
                $options = array('icon'=> 'entypo-box','info'=> 'badge-success','text'=> ' Você tem <strong>'.$qtd.'</strong> arquivos aprovados .','link'=> "<a href='javascript:void(0);' onclick='window.location=\"".$app->goToUrl('usuarios','minhas-notificacoes',array('view-list'=>1))."\"'>Ir para notificaçoes</a>");
                break;
            case 0:
                $options = array('icon'=> 'entypo-clock','info'=> 'badge-secondary','text'=> ' Você tem <strong>'.$qtd.'</strong> arquivos pendentes para aprovação.','link'=> "<a href='javascript:void(0);' onclick='window.location=\"".$app->goToUrl('usuarios','minhas-notificacoes',array('view-list'=>0))."\"'>Ir para notificaçoes</a>");
                break;
        }


        if($qtd)
        {
            $mostra_quantidade = '<span class="badge '.$options['info'].'">'.$qtd.'</span>';
        }
        else
        {
            $mostra_quantidade = null;
        }
        $notification = '';

//xjx_notify_delete($status,$id=NULL,$userId=NULL,$idObra=NULL)
            if($file)
            {
            $notification = '
            <ul class="user-info pull-left pull-right-xs pull-none-xsm">

                <!-- Midias rejeitados -->
                <li class="notifications dropdown">

                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="'.$options['icon'].'"></i>
                        '.$mostra_quantidade.'
                    </a>

                    <ul class="dropdown-menu">

                        <li class="top">
                            <p class="small">

                            '.$options['text'].'
                            </p>
                        </li>

                        <li>
                            <ul class="dropdown-menu-list scroller">';
            foreach($file as $k => $_value):

                switch($_value->tipo_midia)
                {

                    case 'xls':
                    case 'xlsx':
                        $imagem = "<img height='28' src='".$host->getHost()."includes/assets/images/excel.png'>";
                        break;
                    case 'pdf':
                        $imagem = "<img height='28' src='".$host->getHost()."includes/assets/images/pdf.png'>";
                        break;
                    case 'doc':
                    case 'docx':
                        $imagem = "<img height='28' src='".$host->getHost()."includes/assets/images/word.png'>";
                        break;
                    case 'ppt':
                    case 'pptx':
                        $imagem = "<img height='28' src='".$host->getHost()."includes/assets/images/ppt.png'>";
                        break;
                    default:
                        $imagem = "<img height='28' src='".$host->getHost()."includes/assets/images/foto.png'>";
                        break;
                }



                $notification .= '<li class="unread notification-success">
                                    <a href="#">
                                    <i class="fa fa-eye pull-right  tooltip-secondary" onclick="fn_xjx_notify_delete('.$status.','.$_value->id_midia.','.Functions::getUserId().','.$_value->id_obra.')" data-original-title="Marcar como visualizado" title="" data-placement="left" data-toggle="tooltip"></i>
                                        <span class="line">
                                           '.$imagem.' <strong>'.$_value->nome_midia.'</strong>
                                        </span>
                                        <span class="line small">
                                            Adicionada em '.Functions::fncDataPadrao($_value->data_cadastro).' '.$_value->obra_nome.'
                                        </span>
                                    </a>
                                </li>';
            endforeach;

            $notification .= '</ul>
                        </li>

                        <li class="external">
                           '.$options['link'].'
                        </li>
                    </ul>
                </li>
            </ul>';
        }


        return $notification;

    }

    private function getArquivosRejeitadosUsuarios()
    {
        $query = new phpDataReader();
        $idObra = Functions::getOpusKey();
        $idUser = Functions::getUserId();
        $app = new Functions();


        $_Filter = new phpCriteria();

        if($idObra != "" || $idObra)
        {
            $_Filter->add(new phpFilter('ob.id_obra', '=', $idObra));
        }

        $_Filter->add(new phpFilter('md.id_usuario_upload', '=', $idUser));
        $_Filter->add(new phpFilter('md.midia_status', '=', 3));
        $_Filter->add(new phpFilter('md.id_midia', '!=', 0));
        $_Filter->add(new phpFilter('ntf.visualizado', '=', 0));


        $sql = $query->queryDataSelect("tb_notificacao ntf
            LEFT JOIN	tb_midia md ON ntf.id_midia = md.id_midia
            LEFT JOIN	tb_notificacao_visualizado ntv USING(id_usuario)
            LEFT JOIN tb_categorias cat USING (id_categoria)
            LEFT JOIN tb_midia_obra mob ON (ntf.id_obra = mob.id_obra)
            LEFT JOIN tb_obras ob ON (ob.id_obra = mob.id_obra)",
            "ntf.id_notificacao,md.id_midia,
            md.id_usuario_upload,
            md.midia_status,
            md.nome_midia,
            md.tipo_midia,
            md.motivo_descricao,
            md.id_usuario_alteracao,
            md.data_alteracao,
            md.id_categoria,
            ob.obra_nome,
            ob.id_obra,
            md.data_cadastro,
            cat.nome_categoria,
            md.midia_descricao","(select COUNT(id_notify_visualizados) FROM tb_notificacao_visualizado WHERE id_midia = ntf.id_midia AND id_usuario = ".$idUser." AND id_obra = ntf.id_obra AND status = 3) = 0 AND ". $_Filter->dump().$_Filter->groubBy("ntf.id_midia"),10,'md.id_midia DESC');

        $this->setDataTableUsuarios($query->Read($sql));
        $this->setTotalArquivosRejeitados($sql->RowCount()+1);


        $response = array('Lista_arquivos' => $this->_DataTable, 'Qtd_arquivos' => $this->_TotalArquivosRejeitados);

        return $response;
    }


    private function getArquivosAprovadosUsuarios()
    {
        $query = new phpDataReader();
        $idObra = Functions::getOpusKey();
        $idUser = Functions::getUserId();
        $app = new Functions();


        $_Filter = new phpCriteria();

        if($idObra != "" || $idObra)
        {
            $_Filter->add(new phpFilter('ob.id_obra', '=', $idObra));
        }

        $_Filter->add(new phpFilter('md.id_usuario_upload', '=', $idUser));
        $_Filter->add(new phpFilter('md.midia_status', '=',1));
        $_Filter->add(new phpFilter('md.id_midia', '!=', 0));
        $_Filter->add(new phpFilter('ntf.visualizado', '=', 0));

        $sql = $query->queryDataSelect("tb_notificacao ntf
            LEFT JOIN	tb_midia md ON ntf.id_midia = md.id_midia
            LEFT JOIN	tb_notificacao_visualizado ntv USING(id_usuario)
            LEFT JOIN tb_categorias cat USING (id_categoria)
            LEFT JOIN tb_midia_obra mob ON (ntf.id_obra = mob.id_obra)
            LEFT JOIN tb_obras ob ON (ob.id_obra = mob.id_obra)",
            "ntf.id_notificacao,md.id_midia,
            md.id_usuario_upload,
            md.midia_status,
            md.nome_midia,
            md.tipo_midia,
             md.motivo_descricao,
            md.id_usuario_alteracao,
            md.data_alteracao,
            md.id_categoria,
            ob.obra_nome,
            ob.id_obra,
            md.data_cadastro,
            cat.nome_categoria,
            md.midia_descricao","(select COUNT(id_notify_visualizados) FROM tb_notificacao_visualizado WHERE id_midia = ntf.id_midia AND id_usuario = ".$idUser." AND id_obra = ntf.id_obra AND status = 1) = 0 AND ". $_Filter->dump().$_Filter->groubBy("ntf.id_midia"),10,'md.id_midia DESC');

        $this->setDataTableUsuarios($query->Read($sql));
        $this->setTotalArquivosRejeitados($sql->RowCount());


        $response = array('Lista_arquivos' => $this->_DataTable, 'Qtd_arquivos' => $this->_TotalArquivosRejeitados);

        return $response;
    }

    private function getArquivosPendentesUsuarios()
    {
        $query = new phpDataReader();
        $idObra = Functions::getOpusKey();
        $idUser = Functions::getUserId();
        $app = new Functions();


        $_Filter = new phpCriteria();

        if($idObra != "" || $idObra)
        {
            $_Filter->add(new phpFilter('ob.id_obra', '=', $idObra));
        }

        if(Functions::getUserGroup() != 1 || Functions::getUserGroup() != 2)
        {

        }
        else
        {
            $_Filter->add(new phpFilter('md.id_usuario_upload', '!=', $idUser));
        }



        $_Filter->add(new phpFilter('md.midia_status', '=', 0));
        $_Filter->add(new phpFilter('md.id_midia', '!=', 0));
        $_Filter->add(new phpFilter('ntf.visualizado', '=', 0));

        $sql = $query->queryDataSelect("tb_notificacao ntf
            LEFT JOIN	tb_midia md ON ntf.id_midia = md.id_midia
            LEFT JOIN	tb_notificacao_visualizado ntv USING(id_usuario)
            LEFT JOIN tb_categorias cat USING (id_categoria)
            LEFT JOIN tb_midia_obra mob ON (ntf.id_obra = mob.id_obra)
            LEFT JOIN tb_obras ob ON (ob.id_obra = mob.id_obra)",
            "ntf.id_notificacao,md.id_midia,
            md.id_usuario_upload,
            md.midia_status,
            md.nome_midia,
            md.tipo_midia,
             md.motivo_descricao,
            md.id_usuario_alteracao,
            md.data_alteracao,
            md.id_categoria,
            ob.obra_nome,
            ob.id_obra,
            md.data_cadastro,
            cat.nome_categoria,
            md.midia_descricao","(select COUNT(id_notify_visualizados) FROM tb_notificacao_visualizado WHERE id_midia = ntf.id_midia AND id_usuario = ".$idUser." AND id_obra = ntf.id_obra AND status = 0) = 0 AND ". $_Filter->dump().$_Filter->groubBy("ntf.id_midia"),10,'md.id_midia DESC');

        $this->setDataTableUsuarios($query->Read($sql));
        $count = ($sql->RowCount());


        $response = array('Lista_arquivos' => $this->_DataTable, 'Qtd_arquivos' => $count);

        if($app->is_action('aprov_arquivo') == true || $app->is_action('rejeitar_arquivo') == true)
        {
            return $response;
        }

    }

} 