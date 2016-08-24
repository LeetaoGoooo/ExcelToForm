<?php
/**
 * Doc:all applications' entrance
 */
 define('APPLICATION_PATH','D:/wamp/www/ExcelToForm');
 define('LOG4PHP_DIR',APPLICATION_PATH.'/lib/log4php');
 define('LOG_CONF',APPLICATION_PATH.'/log_conf.xml');
 //set the timezone
 date_default_timezone_set('Asia/Hong_Kong');
 require_once(LOG4PHP_DIR.'/Logger.php');
 require_once("./lib/PHPexcel/Classes/PHPExcel/IOFactory.php");
 Logger::configure(LOG_CONF);
 $logger = \Logger::getLogger(__CLASS__);

 //translate data  into json
 if(isset($_GET['c'])&&isset($_GET['a']))
 {
     //get the controller
     $c_str = $_GET['c'];
     //load the controller
     $c_name =  $c_str."Controller";
     //file path
     $c_path = APPLICATION_PATH."/Class/controller/".$c_name.'.class.php';
     //the method
     $method = $_GET['a'];
     //load the file
     require_once($c_path);
     //exec the method
     $controller = new $c_name;
     if(isset($_REQUEST['objstr']))
     {
        $res = $controller->$method($_REQUEST['objstr']);
     }else{
         $res = $controller->$method();
     }
     if($res)
     {
        //  $logger->debug(__FUNCTION__ ." ".__LINE__ ." results:".$res);
         echo $res;
     }else{
         $logger->debug(__FUNCTION__ ." ".__LINE__ ." get no results");
         echo false;
     }
 }else{
  $logger->debug(__FUNCTION__ ." ".__LINE__ ." lack params");
 }
?>
