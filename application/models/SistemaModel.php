<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 02/02/2015
 * Time: 18:46
 */

class SistemaModel extends phpDataReader{

    protected $_qtd_total;
    protected $_qtd_min;
    protected $_qtd_max;
    protected $_exclui_auto;
    protected $_salvar;
    public $_sql;
    public $_query;



    public function getConfig()
   {
       $sql = $this->queryDataSelect('tb_config');
       $qry = $this->queryDataRow($sql,phpDataReader::OBJ);

      return $qry;
   }

    public function getTotalRegistro()
    {
        $sql = $this->queryDataSelect('tb_sys_log','count(id_log) as Total');
        $qry = $this->queryDataRow($sql,phpDataReader::OBJ);

        $config = $this->getConfig();

        $this->_qtd_total = ($qry->Total*100);

        $tes = ($this->_qtd_total/$config->qtd_total_max);

        return array('LIMITE' => $config->qtd_total_max, 'PERC'=>round($tes,2),'TOTAL'=>$qry->Total);
    }

    public function pegarRegistrosLogExcluir()
    {
        $total = $this->getTotalRegistro();


        $this->_sql = '
                        SELECT IF(
                            (SELECT COUNT(*) FROM tb_sys_log) > '.$total['LIMITE'].',
                                id_log
                            , ""
                        ) AS id_logs_add_arquivo, log.*
                        FROM
                            tb_sys_log log
                        WHERE id_log <= (
                            SELECT MAX(id_log) - '.$total['LIMITE'].' FROM tb_sys_log
                        )';

        $this->_query = $this->queryDataExecute($this->_sql);
        $response = $this->Read($this->_query);


        return $response;
    }

    public function MontaColunaCsv()
    {
        $this->_sql ='
            SHOW COLUMNS FROM tb_sys_log
        ';
        $response = $this->queryDataExecute($this->_sql);
        $result = $this->Read($response);

        foreach($result as $k => $Field)
        {
            $cols[] = $Field->Field;
        }


            return $cols;
    }

    public function montarCsvLog($data)
    {
        $sis = new SystemLogHelper();

        $this->_query = $this->MontaColunaCsv();

        $_CSV_NAME = 'log-do_sistema-'.date("d-m-Y");

        $fp = fopen(PATH_CSV.'Files/'.$_CSV_NAME.'.csv', 'wb');

        /* Monta o nome das colunas  do csv*/

        $colunas = array(
            $this->_query[0] => mb_strtoupper("Codigo do log"),
            $this->_query[1] => mb_strtoupper("Usuario"),
            $this->_query[2] => mb_strtoupper("Ip do Usuario"),
            $this->_query[3] => mb_strtoupper("Porta Fisica"),
            $this->_query[4] => mb_strtoupper("Url"),
            $this->_query[5] => mb_strtoupper("Navegador"),
            $this->_query[6] => mb_strtoupper("Data do Evento"),
            $this->_query[7] => mb_strtoupper("Hora do evento"),
            $this->_query[8] => mb_strtoupper("Acao"),
            $this->_query[9] => mb_strtoupper('Tabela do evento'),
            $this->_query[10] => mb_strtoupper("Codigo do registro"),
            $this->_query[11] => mb_strtoupper("Dados anteriores"),
            $this->_query[12] => mb_strtoupper("Dados novos")
        );


        fputcsv($fp, array_values($colunas),";");

        /* Monta o corpo do arquivo csv*/

        foreach($data as $key => $rows)
        {


            $linhas = array(
                $rows->id_log,
                $rows->user_logged,
                $rows->user_ip,
                $rows->porta,
                $rows->url,
                $rows->browser,
                $rows->data_access,
                $rows->hora,
                $rows->action,
                $rows->tabela_of_action,
                $rows->id_of_registro,
                $rows->query_odl,
                $rows->query_new,
            );

            fputcsv($fp, array_values($linhas),";");



        }


       fclose($fp);

    }

    public function CsvGerar()
    {
        $dados = $this->pegarRegistrosLogExcluir();

        if($dados > 0)
        {
            $this->montarCsvLog($dados);

            foreach($dados as $k => $val)
            {

                $this->queryDataDelete('tb_sys_log','id_log ='.$val->id_logs_add_arquivo);

            }
        }


    }

    public function alteraLimiteLog($valor)
    {
        $logQuery = new Functions();

        $this->_sql = $logQuery->sys_log(Functions::_action('altera_valor_limite_log'),'tb_config',1,array('procedure'=>'','args' => array('qtd_total_max' =>  $valor)),'id_config = 1');

        return $this->_sql;
    }

    public function getCsvLog()
    {
        $host = new ElementosRoot();

        $app = new Functions();
        $thumb = "";
        $pasta  = PATH_CSV;
        if (is_dir($pasta)) {
            if ($dh = opendir($pasta)) {
                while (($file = readdir($dh)) !== false) {
                    if($file != ".." && $file != ".")
                    {

                        $titulo = explode(".",$file);
                        $text = explode("_",$titulo[0]);

                        $descricao = explode("-",$text[1]);


                        if($titulo[1]=="csv")
                        {


                            $thumb .= '<div class="col-sm-2 col-xs-4" data-tag="1d" style="text-align: center; margin-bottom: 10px;">';
                            $thumb .='<article class="image-thumb">';
                            $thumb .='<a href="'.$host->getHost().PATH_CSV.$file.'" class="image">';
                            $thumb .='<img src="'.$host->getHost().PATH_INCLUDE.'assets/images/excel.png" />';
                            $thumb .='</a>';
                            $thumb .='<div class="image-options">';
                            $thumb .='<a href="'.$host->getHost().PATH_CSV.$file.'" class="image"><i class="entypo-download"></i></a>';
                            if($app->permitido('del_log_sys_file') == true)
                            {
                                $thumb .='<a href="#" onclick="fn_xjx_deletaArquivoLog(\''.$file.'\');" class="delete"><i class="entypo-cancel"></i></a>';
                            }
                            else
                            {
                                $thumb .='<a href="#" onclick="fn_xjx_MessageNotification(\'alert-danger\',\'Usauário sem permissão para deletar o arquivo\');" class=""><i class="entypo-cancel"></i></a>';
                            }

                            $thumb .='</div>';
                            $thumb .='</article>';
                            $thumb .='<div class="alert alert-info" style="padding-top:-2px;">'.mb_strtoupper($text[0]." ".$descricao[0]."<br />".$descricao[1]."/".$descricao[2]."/".$descricao[3]."").'</div>';
                            $thumb .='</div>';

                        }
                    }

                }
                return $thumb;
                closedir($dh);
            }
        }
    }

}