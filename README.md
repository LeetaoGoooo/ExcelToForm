### ExcelToForm  ![](http://ww4.sinaimg.cn/large/d9e82fa4jw1fakbixx1hkj202i00kt8h.jpg)

According parse Excel And then genarate Forms 

### Dependencies
1. PHP
2. PHPExcel

### 关于表单样式
default styles<p>
header .page-header <p>
button .btn <p>
input .form-control <p>
radio .radio-inline <p>
checkbox .checkbox-inline <p>
label .label <p>
select .select <p>


### TODO
- [x] tran excel to form
- [x] support custom style form
- [x] restore the layer of excel
- [x] support configure  by ExcelToFormConf.json

### How to use

Firstly,create a new Excel file, obey follow rules:

```shell
header: H:header name
label: L:label name
input: I:input name
radio: R:radio-1|radio-2|radio-3
checkbox: C:checkbox-1|checkbox-2|checkbox-3
select: S:select-1|select-2|select-3
button: B:button name
```

then,you should genarate configuration firstly

```php
<?php
    $a = new ExcelToForm(); // or $a = new ExcelToForm($path/to/excel);
    $a->genarateConf()
?>
``` 

after this,you can configure it to fit your requirements

```json
{
    "tempHtmlPath": "template.html", 
    "isDefaultStyleEnable": true,
    "defaultStylePath": "default-style.css",
    "inlineStyle": {
        "header": {
            "id": "header"
        },
        "button": {
            "id": "button"
        },
        "input": {
            "id": "input"
        },
        "radio": {
            "id": "radio"
        },
        "checkbox": {
            "id": "checkbox"
        },
        "label": {
            "id": "label"
        },
        "select": {
            "id": "select"
        }
    }
}
```
Finally,genarate your own form

```php
    $a->parseFormConf(); // parse configuration
    $a->genarateFormTemplates($path/to/excel);
```

