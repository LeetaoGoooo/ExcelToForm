### ExcelToForm
通过Excel配置从而动态生成Web的表单<p>预期普通用户可以通过Excel设计出好看的表单

### 格式定义
2016.8.25 重新定义格式<p>
正常的表格包括:标题、标签、输入框、单选框、复选框、下拉列表框、按钮。<p>
在Excel为了区分，依次定义格式输入:
 1.  标题: H:标题名
 2.  标签: L:标签名
 3.  输入框: I:输入框名称
 4.  单选框: R:选项1|选项2|选项3
 5.  复选框: C:选项1|选项2|选项3
 6.  下拉列表框: S:选项1|选项2|选项3
 7.  按钮: B:按钮名称

### 工具
开发语言: PHP(后续使用Java) <p> 
使用的库: 
 1. 关于Excel处理的使用**PHPExcel**
 2. 关于日志的使用**Log4php**,[Log4php的配置](http://www.cnblogs.com/leetao94/p/4692787.html)

### 进度
第一版Excel配置:<p>
![](http://ww2.sinaimg.cn/large/d9e82fa4jw1f75tsje7csj20b901idfx.jpg)<p>
第一版效果图:
![](http://ww2.sinaimg.cn/large/d9e82fa4jw1f75tt735zuj20wx07i0t0.jpg)<p>
移除createform.js,将html由后端直接生成，然后在前台实现预览

第二版Excel配置<p>
![](http://ww2.sinaimg.cn/large/d9e82fa4jw1f76x02ndloj20bb02l3yu.jpg)
第二版效果图:
![](http://ww2.sinaimg.cn/large/d9e82fa4jw1f76wz2p0yej20wf096dga.jpg)


### 关于表单样式
默认css样式<p>
表头 .page-header <p>
按钮 .btn <p>
输入框 .form-control <p>
单选框 .radio-inline <p>
多选框 .checkbox-inline <p>
标签 .label <p>
下拉列表框 .select <p>

### 功能
- [x] 读取指定Excel生成模板
- [x] 支持自定义模板样式
- [ ] 还原Excel布局
- [ ] 表格样式的缺省多样式设计

### 使用
1. 生成缺省样式的表单
```php
<?php
   $a = new ExcelToForm();
   $a->genarateFormTemplates($excelpath);
   //or
   $a = new ExcelToForm($excelpath);
   $a->genarateFormTemplates();
?>
```
2. 自定义样式的表单
```php
<?php
   $a = new ExcelToForm($excelpath);
   $a->setCssSouce($csspath);
   $a->genarateFormTemplates();
?>
```

产生的表单存放于调用的当前目录下的formTemplates的文件夹内