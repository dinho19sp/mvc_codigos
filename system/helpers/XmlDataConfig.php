<?php
/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 28/05/14
 * Time: 14:55
 */

class XmlDataConfig {

    private $config;

    private function getXmlConfig($name)
    {
        if(!file_exists(PATH_CONFIG.$name.".xml"))
        {
            trigger_error("O arquivo de configuracao xmlconfig nao encontrado ou corrompido",E_USER_WARNING);
        }
        else
        {

            $this->config = simplexml_load_file(PATH_CONFIG.$name.".xml");
        }

        return $this->config;
    }

    public function xmlData($name)
    {
        $xml = $this->getXmlConfig($name);

        return $xml;

    }

    public function getDataLocal()
    {
        $xml = $this->getXmlConfig("app.host.config");
        $local = get_object_vars($xml->local);
        $local = str_replace('"',"",$local);
        return $local['host'];

    }

    public function getDataFacebook()
    {
        $xml = $this->getXmlConfig("app.host.config");
        $local = get_object_vars($xml->producao);
        $local = str_replace('"',"",$local);
        return $local['host_https'];

    }

    public function getDataDevloper()
    {
        $xml = $this->getXmlConfig("app.host.config");
        $developer = get_object_vars($xml->developer);
        $developer = str_replace('"',"",$developer);
        return $developer['host'];

    }

    public function getDataProduction()
    {
        $xml = $this->getXmlConfig("app.host.config");
        $producao = get_object_vars($xml->producao);
        $producao = str_replace('"',"",$producao);
        return $producao['host'];

    }

} 