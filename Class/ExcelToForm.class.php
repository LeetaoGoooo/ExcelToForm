<?php
/**
 * ExcelToForm
 *
 *
 * 通过对Excel的配置，
 * 实现Web端的表单自动生成
 * xls格式如下:
 * ***********************************************
 *            header(optional)
 *   lablename [fieldname]  lablename [fieldname]
 *   ...
 *     btn1         btn2
 *************************************************
 * 生成Form表单保持原有格式，将[fieldname]替换输入框
 * 所有单元格的字体样式保持不变
 * 解析xls完成返回JSON格式数据如下:
 * [  "header":{"value":"xxx","style":{"font-size":"xxx",...}},
 *    "cols0":[{"value":"xxx","field":"xxxx","style":{"font-size":"xxx",...}},{...},...],
 *    "cols1":["type":'button',{},{}]]
 *  如果存在button，则存在字段type为button
 *  @author leetao
 *  @version 1.0.0 2016/7/1
 */





class ExcelToForm
{

    private $_logger;
    private $_loggerflag = true;
    private $_objPHPExcel;
    private $_excelSheet;

    public function __construct($path = null)
    {
        $this->_logger = \Logger::getLogger(__CLASS__);
        if ($this->_checkExcelFile($path)) {
          $this->_objPHPExcel = \PHPExcel_IOFactory::load($path);
          $this->_excelSheet = $this->_objPHPExcel->getSheet(0);
        }
    }


    /**
    * 解析xls表格,根据读取的Excel表单解析生成前台约定的JSON数据
    *
    * @return   string    JSON格式的parseFormData
    */
    public function parseForm()
    {
        $headerFlag = false;
        
        $results = array();
        $colTemp = array();

        //获取excel的行数和列数
        $rowNum = $this->_excelSheet->getHighestRow();
        $colAlph = $this->_excelSheet->getHighestColumn();
        $colNum = PHPExcel_Cell::columnIndexFromString($colAlph);

        if ($this->_getHeader($colNum)) {
          $results['header'] = $this->_getHeader($colNum);
          $headerFlag = true;
        }

        $rowStart = $headerFlag ? 2 : 1;
        $index = 0;
        for ($row = $rowStart; $row <= $rowNum; $row++) {
            $colTemp = array();
          for ($col = 0; $col < $colNum ; $col++) {
              $this->_loggerMsg(__FUNCTION__ ." ".__LINE__ ." row:".$row." col".$col);
              $cellObj = $this->_excelSheet->getCellByColumnAndRow($col,$row);
              if (!is_null($cellObj->getValue())) {
                  $cellNearObj = $this->_excelSheet->getCellByColumnAndRow($col+1,$row);
                  array_push($colTemp,$this->_getCellProperty($cellObj,$cellNearObj));

                  if (is_null($cellNearObj->getValue())) {
                    $col -= 1;
                  }
                  $col += 2;
              }
          }
          $results[$index++] = $colTemp;
        }
        $this->_loggerMsg(__FUNCTION__ ." ".__LINE__ ." parseForm results:".json_encode($results));
        return json_encode($results);
    }

      /**
       * 判断form表单是否存在表头,表头只可能存在excel的第一行
       *
       * @param   $colNum   列数
       *
       * @return boolean or object    false or headerObj {"value":xxx,style:{...}}
       */
      private function _getHeader($colNum)
      {
          for ($col = 0; $col < $colNum; $col++) {
              $cellObj = $this->_excelSheet->getCellByColumnAndRow($col,1);

              if (!is_null($cellObj->getValue())) {
                  $cellNearObj = $this->_excelSheet->getCellByColumnAndRow($col+1,1);

                  if (is_null($cellNearObj->getValue())) {
                    return $this->_getCellProperty($cellObj);
                  }
                  return false;
              }
          }
      }

      /**
      * 获取单元格的属性,包括单元格值，字体大小，字体颜色，是否加粗
      *
      * @param   $cellObj   单元格对象
      * @param   $cellNearObj   该单元格右侧相邻的单元格对象
      *
      * @return  array   {"value":{},style:{"font-size":xxx,["field":xxx](可选),font-color":yyy,"bold":zzz}}
      *
      */
      private function _getCellProperty($cellObj,$cellNearObj = null)
      {
          $res = array();
          $style = array();
          $field = array();

          $res['value'] = $cellObj->getValue();

          $style['fontsize'] = $cellObj->getStyle()->getFont()->getSize();
          $style['fontcolor'] = $cellObj->getStyle()->getFont()->getColor()->getARGB();
          $style['bold'] = $cellObj->getStyle()->getFont()->getBold();
          $res['style'] = $style;

          if (!is_null($cellNearObj->getValue())) {
            $res['field'] = $cellNearObj->getValue();
          }

          return $res;
      }


      /**
      * 检测excel文件是否正确
      *
      * @param:   $path   excel文件路径
      *
      * @return:  如果文件存在则返回true，否则返回false
      */
      private function _checkExcelFile($path)
      {
          if  (isset($path))  {
              if  (file_exists($path))  {
                return true;
              }
              $this->_loggerMsg(__FUNCTION__ ." ".__LINE__ ." 文件路径不正确");
            }
            $this->_loggerMsg(__FUNCTION__ ." ".__LINE__ ." 路径未设置");
            return false;
      }


      /**
      * 是否启用日志功能
      * @param:   $flag   true|false
      */
      public function setLogFlag($flag)
      {
          $this->_loggerflag = $flag;
      }


    /**
     * 日志功能,当启用日志功能时候记录信息
     *  @param:   $msg    记录的信息
     */
    private function _loggerMsg($msg)
    {
        if  ($this->_loggerflag)  {
            $this->_logger->debug($msg);
          }
        }
    }
?>
