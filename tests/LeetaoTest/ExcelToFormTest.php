<?php
/**
 * Created by PhpStorm.
 * User: Leetao
 * Date: 2016/12/9
 * Time: 8:41
 */

namespace LeetaoTest;

use Leetao\ExcelToForm;

class ExcelToFormTest extends \PHPUnit_Framework_TestCase
{
    public $excelTest;

    function setUp()
    {
        $this->excelTest = new ExcelToForm();
    }

    function tearDown()
    {
        unset($this->excelTest);
    }

    function testgenarateFormTemplates()
    {
        $a = $this->excelTest->genarateFormTemplates();
        echo $a;
    }
}
