<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 31/03/15
 * Time: 23:12
 */

class DeviceSniffer {

    private $userAgent;
    private $device;
    private $browser;
    private $deviceLength;
    private $browserLength;

    public function getDevice()
    {
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->userAgent = strtolower($this->userAgent);

        $this->device = array(
            'iphone',
            'ipad',
            'android',
            'samsung',
            'balckberry',
            'lenovo'

        );

        $this->browser= array(
            'firefox',
            'chrome',
            'opera',
            'msie',
            'safari',
            'blackberry',
            'trident'

        );

        $this->deviceLength = count($this->device);
        $this->browserLength= count($this->browser);

    }

    public function Device()
    {
        $this->getDevice();

        for($oDevice = 0; $oDevice < $this->deviceLength;$oDevice++)
        {

            if(strstr($this->userAgent,$this->device[$oDevice]))
            {
                return $this->device[$oDevice];
            }

        }

    }

    public function Browser()
    {
        $this->getDevice();

        for($oBrowser = 0; $oBrowser < $this->browserLength; $oBrowser++)
        {
            if(strstr($this->userAgent,$this->browser[$oBrowser]) == true)
            {
                return $this->browser[$oBrowser];
            }

        }
    }

    public function getProperty()
    {
        if($this->Device() == "")
        {
            $Device = "Não identificado";
        }
        else
        {
            $Device = $this->Device();
        }

        if($this->Browser() == "")
        {
            $Browser = "Não identificado";
        }
        else
        {
            $Browser = $this->Browser();
        }

        return 'Navegador: '.$Browser.' :: Dispositivo: '.$Device;
    }

} 