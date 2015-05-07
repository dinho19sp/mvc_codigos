<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 21/01/15
 * Time: 14:53
 */

class DashboardModel extends phpDataReader {

    private $_chart_file_category;

    public function DataChartCategory()
    {
       $category =  $this->queryDataSelect('tb_categorias','*,(SELECT COUNT(tb_midia.id_midia) FROM tb_midia WHERE tb_midia.id_categoria = tb_categorias.id_categoria) AS CAT',null,null,null);
       $q = $this->Read($category);
        return $q;
    }


    public function DataChartSegment()
    {

        $sql = $this->queryDataSelect('tb_obras GROUP BY id_segmento', 'id_segmento');
        $qry_segmento = $this->Read($sql);

        $chart = "[\n";
        $chart .= "['Segmento', 'Total Obras'],";

        foreach($qry_segmento as $key => $val) {
            $seg =  $this->queryDataSelect('tb_obras LEFT JOIN tb_obras_segmentos seg USING (id_segmento)','id_segmento, seg.segmento_nome, YEAR(data_cadastro) AS ano,(SELECT COUNT(id_segmento) FROM tb_obras obr WHERE obr.id_segmento = '.$val->id_segmento.' GROUP BY obr.id_segmento) AS total_segmento',  'id_segmento = '.$val->id_segmento.' GROUP BY YEAR(data_cadastro)');
            $qry = $this->queryDataRow($seg, phpDataReader::OBJ);

            $arr[] = "\n['".($qry->segmento_nome)."', ".$qry->total_segmento."]";

        }

        $chart.= implode(",", $arr)."]\n";

       //var_dump($chart);
        return $chart;

    }

    public function ChartCategory()
    {
        $data = $this->DataChartCategory();


            $DataChart = "[";
            $DataChart.="['Categorias','Arquivos'],";

            foreach ($data as $key => $value)
            {
                $dt[] = "['".$value->nome_categoria."',".$value->CAT."]";
            }

            $DataChart .= implode(",",$dt)."]";

            $this->_chart_file_category = $DataChart;



        return $this->_chart_file_category;
    }

    private function getObrasCount($status=NULL)
    {
        if($status == NULL)
        {
            $campoCount = 'id_obra';
        }
        else
        {
            $campoCount = 'id_status';
        }
        //$cond = "";
        if($status != NULL)
        {

            $_Filter = new phpCriteria();
            $_Filter->add(new phpFilter('id_status',"=",$status));
            $cond = $_Filter->dump();
        }


        $sql = $this->queryDataSelect('tb_obras','COUNT('.$campoCount.') AS total',$cond);
        $qry = $this->queryDataRow($sql,phpDataReader::OBJ);

        return (int) $qry->total;
    }

    public function qtdObrasStatus()
    {
        $dash = '
        <div class="row">
    <div class="col-sm-3">

        <div class="tile-stats tile-red">
            <div class="icon"><i class="entypo-info-circled"></i></div>
            <div class="num" data-start="0" data-end="'.$this->getObrasCount(3).'" data-postfix="" data-duration="1500" data-delay="0">'.$this->getObrasCount(3).'</div>

            <h3>Obras não iniciadas</h3>
            <p>Obras cadastradas e nao iniciadas</p>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-stats tile-green">
            <div class="icon"><i class="entypo-chart-bar"></i></div>
            <div class="num" data-start="0" data-end="'.$this->getObrasCount(4).'" data-postfix="" data-duration="1500" data-delay="600">'.$this->getObrasCount(4).'</div>

            <h3>Obras em andamento</h3>
            <p>Obras cadastradas e em andamento.</p>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-stats tile-aqua">
            <div class="icon"><i class="entypo-thumbs-up"></i></div>
            <div class="num" data-start="0" data-end="'.$this->getObrasCount(5).'" data-postfix="" data-duration="1500" data-delay="1200">'.$this->getObrasCount(5).'</div>

            <h3>Obras finalizadas</h3>
            <p>Obras cadastradas e finalizadas</p>
        </div>

    </div>

    <div class="col-sm-3">

        <div class="tile-stats tile-blue">
            <div class="icon"><i class="entypo-rss"></i></div>
            <div class="num" data-start="0" data-end="'.$this->getObrasCount().'" data-postfix="" data-duration="1500" data-delay="1800">'.$this->getObrasCount().'</div>

            <h3>Total obras cadastradas</h3>
            <p>Total geral das obras cadastradas.</p>
        </div>

    </div>
</div>';

        return $dash;

    }

    private function ObrasUsuarios()
    {
        $IdUsuario = Functions::getUserId();

        $query = new UsuariosModel();
        $sql = $query->getObrasUsuarios();


    }


    public function getMidiasUsuarios()
    {

        $_Filter = new phpCriteria();

        $_Filter->add(new phpFilter('uob.id_usuario', '=', Functions::getUserId()));
        $_Filter->add(new phpFilter('uob.id_midia', '!=', 0));


        $sql = $this->queryDataSelect("tb_usuarios_obras uob LEFT JOIN tb_midia md USING(id_midia) LEFT JOIN tb_categorias cat USING(id_categoria)", "uob.id_usuario, uob.id_midia, uob.id_obra, uob.id_usuario_obra, md.data_cadastro, md.nome_midia, md.id_categoria,cat.nome_categoria, md.midia_descricao, uob.data_add, uob.env_email", $_Filter->dump(),10,'md.id_midia DESC');
        $query = $this->Read($sql);


        $_tabela ='<div class="panel panel-primary">';
        $_tabela .='<div class="panel-heading"><div class="panel-title">Últimos 10 arquivos adicionado</div></div>';
        $_tabela .='<div class="panel-body">';

        $_tabela .= '<table class="table table-bordered table-striped">';
        $_tabela .= '<thead>';
        $_tabela .= '<tr>';
        $_tabela .= '    <th>';
        $_tabela .= '    </th>';
        $_tabela .= '    <th>ID</th>';
        $_tabela .= '    <th>Categoria</th>';
        $_tabela .= '    <th>Arquivo</th>';
        $_tabela .= '    <th>Data adicionado</th>';
        $_tabela .= '    <th>Ação</th>';
        $_tabela .= '</tr>';
        $_tabela .= '</thead>';
        $_tabela .= '<tbody>';

        foreach ($query as $grid => $data) {


            $_tabela .= '<tr style="cursor: pointer;">';
            $_tabela .= '<td></td>';
            $_tabela .= '<td>' . $data->id_midia . '</td>';
            $_tabela .= '<td>' . $data->nome_categoria . '</td>';
            $_tabela .= '<td>' . $data->nome_midia . '</td>';
            $_tabela .= '<td>' . Functions::fncDataPadrao($data->data_add) . '</td>';
            $_tabela .= '    <td >';
            $_tabela .= '        <a href="javascript:void(0)" onclick="window.location=\''. Functions::goToUrl('usuarios','midia-detalhes',array('srcid' => $data->id_midia)).'\'" class="btn btn-danger btn-sm pull-right">';
            $_tabela .= '            <i class="entypo-eye"></i>';
            $_tabela .= '        </a>&nbsp;';
            $_tabela .= '    </td>';
            $_tabela .= '</tr>';

        }


        $_tabela .= '</tbody>';
        $_tabela .= '</table>';
        $_tabela .='</div>';

        return $_tabela;
    }




} 