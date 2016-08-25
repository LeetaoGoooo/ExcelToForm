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
    //css样式设置
    private $_configCss;

    public function __construct($path = null)
    {
        $this->_logger = \Logger::getLogger(__CLASS__);
        if ($this->_checkExcelFile($path)) {
          $this->_objPHPExcel = \PHPExcel_IOFactory::load($path);
          $this->_excelSheet = $this->_objPHPExcel->getSheet(0);
        }
        $this->_configCss = array(
                                    "header" => '.page-header',
                                    "button" => '.btn',
                                    "input" => '.form-control',
                                    "radio" => '.radio-inline',
                                    "checkbox" => '.checkbox',
                                    "label" => '.label',
                                    "select" => '.select'
                                  );
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
       * 检测是否存在单元格内容为 H:标题名
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
     * 生成相应的form表头
     *
     * @param string $header,表头文字
     *
     * @return string   返回生成的表头html
     */
      private function _genarateFormHeader($header='Header') {
          $headerHTML = '<div class="'.$this->_configCss['header'].'"><h2>'.$header.'</h2></div>';
          return $headerHTML;
      }

    /**
     * 生成相应的form的按钮
     *
     * @param string $button
     *
     * @return  string  返回生成的按钮html
     */
      private function _genarateFormButton($button='Button') {
          $buttonHTML = '<button class="'.$this->_configCss['button'].'">'.$button.'</button>';
          return $buttonHTML;
      }

    /**
     * 生成相应form的标签
     *
     * @param string $label
     *
     * @return  string  返回生成的标签html
     */
      private function _genarateFormLabel($label='Label') {
          $labelHTML = '<label class="'.$this->_configCss['label'].'">'.$label.'</label>';
          return $labelHTML;
      }


    /**
     * 生成的相应的form的输入框
     *
     * @return  string  返回生成的html
     */
      private function _genarateFormInput() {
          $inputHTML = '<input type="text" class="'.$this->_configCss['input'].'"/>';
          return $inputHTML;
      }

    /**
     * 生成相应的form的单选框
     *
     * @param string $radio,格式如下:Radio1|Radio2|Radio3
     *
     * @return string 返回生成的单选框html
     */
      private function _genarateFormRadio($radio='Radio') {
          $RadioNameArr = explode("|",$radio);
          $RadioHTML = '';
          foreach($RadioNameArr as $radios) {
              $RadioHTML = $RadioHTML.'<label class="'.$this->_configCss['radio'].'"><input type="radio" value="'.$radios.'">'.$radios.'</label>';
          }
          return $RadioHTML;
      }

    /**
     * 生成相应的form复选框
     *
     * @param string $checkbox,格式如下:CheckBox1|CheckBox2|CheckBox3
     *
     * @return  string  返回生成的复选框html
     */
      private function _genarateFormCheckBox($checkbox="CheckBox") {
          $CheckNameArr = explode("|",$checkbox);
          $CheckBoxHTML = '';
          foreach ($CheckNameArr as $checkboxs) {
              $CheckBoxHTML = $CheckBoxHTML.'<lable class="'.$this->_configCss['checkbox'].'"><input type="checkbox" value="'.$checkboxs.'">'.$checkboxs.'</label>';
          }
          return $CheckBoxHTML;
      }


    /**
     * 生成的相应的form下拉列表
     *
     * @param string $select,格式如下:Select1|Select2|Select3
     *
     * @return string   返回生成的下拉列表html
     */
      private function _genarateFormSelect($select='Select') {
          $SelectArr = explode("|",$select);
          $SelectHTML = '<select class="select">';
          foreach($SelectArr as $selects) {
              $SelectHTML = $SelectHTML.'<option value="'.$selects.'">'.$selects.'</select>';
          }
          $SelectHTML = $SelectHTML."</select>";
          return $SelectHTML;
      }


    /**
     * 根据单元格的值,生成相应的html代码
     *
     * @param   string     $cellvalue,格式:H:标题名,L:标签名,I:输入框名称,R:选项1|选项2|选项3,
     *                                     C:选项1|选项2|选项3,S:选项1|选项2|选项3,B:按钮名称
     *
     * @return string   返回生成的html代码
     */
      private function _genarateFormComponent($cellvalue) {
          $startWithStr = substr($cellvalue,0,2);
          $remainStr =  substr($cellvalue,2);
          switch($startWithStr){
              case 'H:':
                  return $this->_genarateFormHeader($remainStr);
                  break;
              case 'L:':
                  return $this->_genarateFormLabel($remainStr);
                  break;
              case 'I:':
                  return $this->_genarateFormInput();
                  break;
              case 'C:':
                  return $this->_genarateFormCheckBox($remainStr);
                  break;
              case 'S:':
                  return $this->_genarateFormRadio($remainStr);
                  break;
              case 'B:':
                  return $this->_genarateFormButton($remainStr);
                  break;
              default:
                  return false;
          }
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
