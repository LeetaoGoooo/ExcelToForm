<?php

/**
 * Created by PhpStorm.
 * User: leetao
 * Date: 2016/8/29
 * Time: 15:59
 */
require_once ('./Class/uploadfile/Uploadfile.class.php');

class UploadfileController
{
    private $_upload;

    public  function __construct()
    {
        $this->_upload = new Uploadfile();
    }

    public  function saveFileAction($params){
        return $this->_upload->saveExcelAndCss($params);
    }
}