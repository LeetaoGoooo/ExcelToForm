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
![](http://ww2.sinaimg.cn/large/d9e82fa4jw1f75tt735zuj20wx07i0t0.jpg)

### 关于表单样式
默认css样式<p>
表头 .page-header <p>
按钮 .btn <p>
输入框 .form-control <p>
单选框 .radio-inline <p>
多选框 .checkbox-inline <p>
标签 .label <p>
下拉列表框 .select <p>

