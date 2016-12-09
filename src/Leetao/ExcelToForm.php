<?php
/**
 * ExcelToForm
 *
 *
 * 通过对Excel的配置，
 * 实现Web端的表单自动生成
 *
 *  如果存在button，则存在字段type为button
 *  @author Leetao
 *  @version 0.0.1 2016/7/1
 *           0.0.2 2016/12/8
 *
 *  @change 支持通过配置文件完成配置,配置包括:
 *               tempHtmlPath (生成文件的位置):'./formTemplates/template.html'
 *               isDefaultStyleEnable (是否启用外联样式):true,
 *               defaultStylePath (外联式文件位置):'./default-style.css'
 *               inlineStyle:{
 *                             "header": {},
 *                             "button": {},
 *                             "input": {},
 *                             "radio": {},
 *                             "checkbox": {},
 *                             "label": {},
 *                             "select": {}
 *                           }
 *
 *
 */

namespace Leetao;


class ExcelToForm
{

    private $_execlPath;
    private $_objPHPExcel;
    private $_excelSheet;

    //css样式设置
    private $_configCss;
    private $_configCssKeys;
    //默认的css文件存放位置
    private $_deCssPath;

    // 是否启用外联样式
    private $_isDefaultStyleEnable;

    //模板的头,身,尾以及引用资源
    private $_tempheader;
    private $_tempbody;
    private $_tempfooter;
    private $_tempsourece;

    //生成文件存放位置
    private $_tempHTMLName;

    //配置文件位置
    private $_confpath;

    //内联样式
    private $_inlineStyle;


    public function __construct($path = null)
    {
        if ($this->_checkExcelFile($path)) {
            $this->_execlPath = $path;
        }
        $this->_configCss = array(
            "header" => 'page-header',
            "button" => 'btn',
            "input" => 'form-control',
            "radio" => 'radio-inline',
            "checkbox" => 'checkbox-inline',
            "label" => 'label',
            "select" => 'select'
        );
        $this->_configCssKeys = array("header", "button", "input", "radio", "checkbox", "label", "select");
        $this->_tempheader = <<<Header
            <!DOCTYPE html>
            <html>
                <title>表单定制</title>
            <head>
Header;
        $this->_tempbody = <<<Body
            </head>
            <body>
            <div class="container">
              <form class='form-inline'>
Body;
        $this->_tempfooter = <<<Footer
            </form>
            </div>
            </body>
            </html>
Footer;

        $this->_deCssPath = 'default-style.css';

        $this->_tempsourece = "<link rel='stylesheet' href='" . $this->_deCssPath . "'>";

        $this->_tempHTMLName = 'template.html';
        $this->_confpath =  'ExcelToFormConf.json';
        $this->_isDefaultStyleEnable = true;
    }

    /**
     * 根据excel文件路径生成相应的表单模板
     *
     * @param null $path ,excel文件路径
     *
     * @return  string|bool  错误则返回错误信息或者返回false,成功返回true
     */
    public function genarateFormTemplates($path = null)
    {
        if (is_null($path)) {
            if (is_null($this->_execlPath)) {
                return json_encode(array("msg" => "设置Excel路径"));
            } else {
                return $this->_parseForm();
            }
        } else {
            $this->_execlPath = $path;
            return $this->_parseForm();
        }
    }

    /**
     * 解析xls表格,根据读取的Excel表单解析生成前台约定的JSON数据
     *
     * @return   string    JSON格式的parseFormData
     */
    private function _parseForm()
    {
        $resultHTML = '';

        $this->_objPHPExcel = \PHPExcel_IOFactory::load($this->_execlPath);
        $this->_excelSheet = $this->_objPHPExcel->getSheet(0);

        //获取excel的行数和列数
        $rowNum = $this->_excelSheet->getHighestRow();
        $colAlph = $this->_excelSheet->getHighestColumn();
        $colNum = PHPExcel_Cell::columnIndexFromString($colAlph);
        //每列宽度百分比
        $width = 100 / $colNum;
        for ($row = 1; $row <= $rowNum; $row++) {
            $rowHTML = '';
            for ($col = 0; $col < $colNum; $col++) {
                $cellObj = $this->_excelSheet->getCellByColumnAndRow($col, $row);
                if (!is_null($cellObj->getValue())) {
//                  $resultHTML = $resultHTML.$this->_genarateFormComponent($cellObj->getValue());
                    $rowHTML = $rowHTML . $this->_genarateFormComponent($cellObj->getValue());
                } else {
                    $rowHTML = $rowHTML . "<span style='margin-left:" . $width . "%'></span>";
                }
            }
            $resultHTML = $resultHTML . '<div>' . $rowHTML . '</div>';
        }
        if ($this->_isDefaultStyleEnable) {
            $tempHTML = $this->_tempheader . $this->_tempsourece . $this->_tempbody . $resultHTML . $this->_tempfooter;
        } else {
            $tempHTML = $this->_tempheader . $this->_tempbody . $resultHTML . $this->_tempfooter;
        }
        return $this->_writeTemplate($tempHTML);
    }


    /**
     * 设置生成的文件位置
     *
     * @param string $path 生成html的位置
     * @return void
     */
    public function setTemplateBirthPath($path)
    {
        $this->_tempHTMLName = $path;
    }


    /**
     * 将生成的html写入到相应位置
     *
     * @param   string $html 生成的html代码
     * @return  bool    成功返回true,失败返回false
     */
    private function _writeTemplate($html)
    {

        if (file_put_contents($this->_tempHTMLName, $html)) {
            return true;
        }
        return false;
    }

    /**
     * 修改Css的配置文件
     * 检测是否所有key都包含,
     * 不包含的,则使用缺省样式
     *
     * @param   array $config 配置文件
     *
     * @return  bool|string    设置成功返回true,否则返回错误信息
     */
    public function setConfigCss($config)
    {
        if (!is_array($config)) {
            return json_encode(array('msg' => 'css配置参数类型错误!'));
        }
        foreach ($this->_configCssKeys as $value) {
            if (!array_key_exists($value, $config)) {
                $config[$value] = $this->_configCss[$value];
            }
        }
        $this->_configCss = $config;
    }

    /**
     * 设置css的引用
     *
     * @param   string $csspath css的引用路径
     * @return  void
     */
    public function setCssSouce($csspath)
    {
        $this->_deCssPath = $csspath;
    }

    /**
     * 生成相应的form表头
     *
     * @param string $header ,表头文字
     *
     * @return string   返回生成的表头html
     */
    private function _genarateFormHeader($header = 'Header')
    {
        $hearderAttributes = $this->_genarateFormAttributes($this->_inlineStyle->header);
        $headerHTML = '<h2 class="' . $this->_configCss['header'] .'" '. $hearderAttributes . '>' . $header . '</h2>';
        return $headerHTML;
    }

    /**
     * 生成相应的form的按钮
     *
     * @param string $button
     * @return  string  返回生成的按钮html
     */
    private function _genarateFormButton($button = 'Button')
    {
        $buttonAttributes = $this->_genarateFormAttributes($this->_inlineStyle->button);
        $buttonHTML = '<div><button class="' . $this->_configCss['button'] .'" '. $buttonAttributes . '>' . $button . '</button></div>';
        return $buttonHTML;
    }

    /**
     * 生成相应form的标签
     *
     * @param string $label
     * @return  string  返回生成的标签html
     */
    private function _genarateFormLabel($label = 'Label')
    {
        $labelAttributes = $this->_genarateFormAttributes($this->_inlineStyle->label);
        $labelHTML = '<label class="' . $this->_configCss['label'] .'" '. $labelAttributes. '>' . $label . '</label>';
        return $labelHTML;
    }


    /**
     * 生成的相应的form的输入框
     *
     * @return  string  返回生成的html
     */
    private function _genarateFormInput()
    {
        $inputAttributes = $this->_genarateFormAttributes($this->_inlineStyle->input);
        $inputHTML = '<input type="text" class="' . $this->_configCss['input'] . '" '. $inputAttributes . '/>';
        return $inputHTML;
    }

    /**
     * 生成相应的form的单选框
     *
     * @param string $radio ,格式如下:Radio1|Radio2|Radio3
     * @return string 返回生成的单选框html
     */
    private function _genarateFormRadio($radio = 'Radio')
    {
        $radioAttributes = $this->_genarateFormAttributes($this->_inlineStyle->radio);
        $RadioNameArr = explode("|", $radio);
        $RadioHTML = '';
        foreach ($RadioNameArr as $radios) {
            $RadioHTML = $RadioHTML . '<label class="' . $this->_configCss['radio'] .'" '. $radioAttributes . '><input type="radio" name="optionsRadios" value="' . $radios . '">' . $radios . '</label>';
        }
        return $RadioHTML;
    }

    /**
     * 生成相应的form复选框
     *
     * @param string $checkbox ,格式如下:CheckBox1|CheckBox2|CheckBox3
     * @return  string  返回生成的复选框html
     */
    private function _genarateFormCheckBox($checkbox = "CheckBox")
    {
        $checkboxAttributes = $this->_genarateFormAttributes($this->_inlineStyle->checkbox);
        $CheckNameArr = explode("|", $checkbox);
        $CheckBoxHTML = '';
        foreach ($CheckNameArr as $checkboxs) {
            $CheckBoxHTML = $CheckBoxHTML . '<label class="' . $this->_configCss['checkbox'] . '" '.  $checkboxAttributes . '><input type="checkbox" value="' . $checkboxs . '">' . $checkboxs . '</label>';
        }
        return $CheckBoxHTML;
    }


    /**
     * 生成的相应的form下拉列表
     *
     * @param string $select ,格式如下:Select1|Select2|Select3
     * @return string   返回生成的下拉列表html
     */
    private function _genarateFormSelect($select = 'Select')
    {
        $selectAttributes = $this->_genarateFormAttributes($this->_inlineStyle->select);
        $SelectArr = explode("|", $select);
        $SelectHTML = '<select class="select" '. $selectAttributes .'>';
        foreach ($SelectArr as $selects) {
            $SelectHTML = $SelectHTML . '<option value="' . $selects . '">' . $selects . '</option>';
        }
        $SelectHTML = $SelectHTML . "</select>";
        return $SelectHTML;
    }



    /**
    * 生成form指定元素的属性
    *
    * @param object $attributeObj 属性数组
    * @return string 属性字符串 
    */
    private function _genarateFormAttributes($attributeObj)
    {
        $attributesStr = '';

        $attributes = get_object_vars($attributeObj);

        //检测是否为空,为空则返回""
        if (empty( $attributes )) {
            return $attributesStr;
        }

        foreach ($attributes as $key => $value) {
            $attributesStr .= ' '.$key.'="'.$value.'"';
        }
        return $attributesStr;
    }

    /**
     * 根据单元格的值,生成相应的html代码
     *
     * @param   string $cellvalue ,格式:H:标题名,L:标签名,I:输入框名称,R:选项1|选项2|选项3,
     *                                     C:选项1|选项2|选项3,S:选项1|选项2|选项3,B:按钮名称
     * @return string   返回生成的html代码
     */
    private function _genarateFormComponent($cellvalue)
    {
        $startWithStr = substr($cellvalue, 0, 2);
        $remainStr = substr($cellvalue, 2);
        switch ( strtoupper($startWithStr) ) {
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
            case 'R:':
                return $this->_genarateFormRadio($remainStr);
                break;
            case 'B:':
                return $this->_genarateFormButton($remainStr);
                break;
            case 'S:':
                return $this->_genarateFormSelect($remainStr);
                break;
            default:
                return false;
        }
    }

    /**
     * 检测excel文件是否正确
     *
     * @param   string $path excel文件路径
     * @return  boolean   如果文件存在则返回true，否则返回false
     */
    private function _checkExcelFile($path)
    {
        if (isset($path)) {
            if (file_exists($path)) {
                return true;
            }
        }
        return false;
    }


    /**
     * 生成配置文件
     *
     * @return bool 成功返回true,否则返回false
     */
    public function genarateConf()
    {
        $conf = array(
            "tempHtmlPath" =>  $this->_tempHTMLName,
            "isDefaultStyleEnable" => true,
            "defaultStylePath" => $this->_deCssPath,
            "inlineStyle" => array(
                "header" => array("id" => "header"),
                "button" => array("id" => "button"),
                "input" => array("id" => "input"),
                "radio" => array("id" => "radio"),
                "checkbox" => array("id" => "checkbox"),
                "label" => array("id" => "label"),
                "select" => array("id" => "select")
            )
        );
        if (file_put_contents($this->_confpath, json_encode($conf))) {
            return true;
        }
        return false;
    }

    /**
    * 设置生成的配置文件路径
    * 
    * @return void
    */
    public function setFormConfPath($conf)
    {
        $this->_confpath = $conf;
    }

    
    /**
    * 解析配置文件
    *
    * @return void 
    */
    public function parseFormConf()
    {
        $conf = file_get_contents($this->_confpath);
        $confObj = json_decode($conf);
        $this->_tempHTMLName = $confObj->tempHtmlPath;
        $this->_isDefaultStyleEnable = $confObj->isDefaultStyleEnable;
        if ($this->_isDefaultStyleEnable) {
                $this->_tempsourece = "<link rel='stylesheet' href='" . $confObj->defaultStylePath . "'>";
        }
        $this->_inlineStyle = $confObj->inlineStyle;
    }

}
?>