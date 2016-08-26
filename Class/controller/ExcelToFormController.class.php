genarateFormTemplates<?php
require_once("./Class/ExcelToForm.class.php");
class ExcelToFormController{
  private $__excelForm;
  public function __construct(){
    $this->__excelForm = new ExcelToForm('./test.xls');
  }

  public function parseFormAction(){
    $results = $this->__excelForm->genarateFormTemplates();
    return $results;
  }
}
?>
