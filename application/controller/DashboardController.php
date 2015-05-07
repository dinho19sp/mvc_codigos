<?php
/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 02/09/14
 * Time: 21:55
 */

class DashboardController extends DashboardModel {

    public $_view;

    public function app()
    {
        $ret = new onCreateClass();

        return $ret;
    }
    public function index()
    {
        $Session = Registry::getInstance('SessionStart');
        /*$Chart1 = $this->ChartCategory();
        $Chart2 = $this->DataChartSegment();
        $Dash =     $this->qtdObrasStatus();*/


        /*if(Functions::getUserGroup() != 1)
        {
            $this->app()->getApplication()->redirect($this->app()->getFunctions()->goToUrl('usuarios','meus-dados'));

        }
        else
        {
            $Data = array('InfoOPus' => $Dash,'Chart' => $Chart1, 'Chart2' => $Chart2);
        }*/


        $this->_view = new View(PATH_VIEW."dashboard.phtml");
        $this->_view->showContents();
    }


} 