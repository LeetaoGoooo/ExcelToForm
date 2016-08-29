<?php
/**
 *  文件上传类
 */



class Uploadfile {

    private $_storeexcelpath;
    private $_storecsspath;

    public function __construct() {
        $this->_storeexcelpath = 'excel';
        $this->_storecsspath  = 'css/form-style';
    }


    /**
     * 根据type和id将文件存放到指定位置
     *
     * @param   string      JSON格式数据,$params:{"type":'excel|css',id:'xxx'}
     *
     * @return  bool        上传成功则返回true，否则返回false
     */
    public function saveExcelAndCss($params){
        $paramObj = json_decode($params);
        if ($paramObj->type == 'excel'){
            $filePath = APPLICATION_PATH."/".$this->_storeexcelpath;
            $destination = $filePath."/".$paramObj->id.".excel";
            $file = $_FILES['excel'];
        }
        if ($paramObj->type == 'css') {
            $filePath = APPLICATION_PATH."/".$this->_storecsspath;
            $destination = $filePath."/".$paramObj->id.".css";
            $file = $_FILES['css'];
        }
        return $this->_saveFile(array("filepath"=>$filePath,"destination" => $destination,"file" => $file));

    }

    /**
     * 将文件保存到 相应文件目录下
     * 如果该文件夹不存在，则创建
     *
     *  @param     array     $params,格式如下: array("destination" => $destination,"file" => $file)
     *
     *  @return   boolean   保存成功返回true,否则返回false
     */
    private function _saveFile($params) {
        if(!file_exists($params['filepath'])) {
            if(!mkdir($params['filepath'],0777,true)) {
                return false;
            }
        }
        if (isset($_FILES)) {
            if (isset($params['file'])) {
                $tmp_name = $params['file']['tmp_name'];
                $this->_logger(__FUNCTION__ ." ".__LINE__ ." 移动路径:".$params['destination']);
                if($params['file']['error'] == UPLOAD_ERR_OK) {
                    if (move_uploaded_file($tmp_name,$params['destination'])) {
                              return true;
                    }
                    $this->_logger(__FUNCTION__ ." ".__LINE__." 文件移动失败");
                    return false;
                }else{
                    $this->_logger(__FUNCTION__ ." ".__LINE__ ." 上传出错:".$params['file']['error']);
                    return false;
                }
            }else{
                $this->_logger(__FUNCTION__ ." ".__LINE__ ." 文件不存在!");
                return false;
            }
        }
        return false;
    }

    /**
     *  简单日志功能,将信息输出到文本中
     *
     *  @param    $msg    需要输出的文本消息
     */
    private function _logger($msg) {
        $handle = fopen(APPLICATION_PATH."/log/log.txt","a+");
        fwrite($handle,$msg."\n");
        fclose($handle);
    }

}

?>
