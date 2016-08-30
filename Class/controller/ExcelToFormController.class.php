<?php
require_once("./Class/exceltoform/ExcelToForm.class.php");

class ExcelToFormController{
  private $_excelForm;
  public function __construct(){
    $this->_excelForm = new ExcelToForm('./test.xls');
  }

  public function parseFormAction($filepath){
    $results = $this->_excelForm->genarateFormTemplates($filepath);
    return $results;
  }

  public  function  setCssSouceAction($params){
    return $this->_excelForm->setCssSouce($params);
  }
}
?>
