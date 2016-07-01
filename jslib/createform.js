/**
 * 将符合一定规范的JSON格式的数据，生成Web端表单
 *
 *
 * @author leetao
 * @version 1.0.0 2016/7/1
 */


var createForm = function () {
    'use strict';
    var ajaxUrl = './action.php?c=ExcelToForm&a=parseFormAction';

    /**
     * 创建表头元素
     *
     * @param   headData    表头的内容对象
     * @return    string    返回form的表头的html内容
     */
    generateFormHeader = function (headerDataObj) {
        var headerValue = headerDataObj.value;
        //style
        var headerfontSize = "font-size:" + headerDataObj.style.fontsize + ";";
        var headerfontColor = "color:#" + headerDataObj.style.fontcolor.substring(0, headerDataObj.style.fontcolor.length - 3) + ";";
        var style = "style='" + headerfontSize + headerfontColor + "'";

        //<p>
        var headerHTML = "<p " + style + ">" + headerValue + "</p>";

        //是否加粗
        if (headerDataObj.style.bold) {
            return "<b>" + headerHTML + "</b>";
        }
        return headerHTML;
    };

    /**
     * 创建除表头之外的所有内容
     *
     * @param   headerFlag    表头是否存在
     * @param   remainObj    表头以外的所有内容对象
     *
     * @return  string     表头以外的所有内容的HTML
     */
    generateFormRemain = function (headerFlag, remainObj) {
        var len = headerFlag ? remainObj.length - 1 : remainObj.length;
        var remainHTML = "";

        for (var i = 0; i < len; i++) {
            if (!(typeof remainObj[i][0].field == 'undefined')) {
                remainHTML += generateFormInAndLab(remainObj[i]) + "</br>";
            } else {
                remainHTML += generateFormButton(remainObj[i]);
            }
        }
        return remainHTML;
    };

    /**
     * 创建form标签和输入框
     *
     * @param   inData    标签和输入框的内容对象
     *
     * @return  string    返回标签和输入框内容
     */
    generateFormInAndLab = function(inDataObj) {
        var len = inDataObj.length;
        var InAndLabHTML = "";
        for (var i = 0; i < len; i++) {
            var inputHTML = "<input id='" + inDataObj[i].field + "' ></input>";
            var style = "style='font-size:" + inDataObj[i].style.fontsize + ";color:" + inDataObj[i].style.fontcolor.substring(0, inDataObj[i].style.fontcolor.length - 3) + "'";
            var labelHTML = inDataObj[i].style.bold ? "<label " + style + "><b>" + inDataObj[i].value + "</b></label>" : "<label " + style + ">" + inDataObj[i].value + "</label>";
            InAndLabHTML += labelHTML + inputHTML;
        }
        return InAndLabHTML;
    };

    /**
     * 创建form的按钮
     *
     * @param  btnData  按钮内容对象
     *
     * @return string   返回按钮的html内容
     */
    var generateFormButton = function(btnDataObj) {
        var len = btnDataObj.length;
        var btnsHTML = "";
        for (var i = 0; i < btnDataObj.length; i++) {
            var style = "style='font-size:" + btnDataObj[i].style.fontsize + ";color:" + btnDataObj[i].style.fontcolor.substring(0, btnDataObj[i].style.fontcolor.length - 3) + "'";
            var btnHTML = btnDataObj[i].style.bold ? "<button " + style + "><b>" + btnDataObj[i].value + "</b></button>" : "<button " + style + ">" + btnDataObj[i].value + "</button>";
            btnsHTML += btnHTML;
        }
        return btnsHTML;
    };

    return {

        /**
         * 设置请求form表单的地址
         *
         * @param   url   请求form表单的地址
         */
        setAjaxUrl: function(url) {
            ajaxUrl = url;
        },

        /**
         * 根据Ajax请求创建表单
         *
         * @param   formId    生成form的id
         *
         * @return  DOM节点   自动将表单添加到节点
         */
        generateFormByAjax: function(formId) {

            $.post(ajaxUrl, {},
                function(data) {
                    var jsonObj = JSON.parse(data);
                    var headerFlag = false;
                    if (!(typeof jsonObj.header == "undefined")) {
                        var headerHTML = generateFormHeader(jsonObj.header);
                        headerFlag = true;
                    }
                    //使用delete之后，jsonObj的长度保持不变
                    delete jsonObj.header;
                    var remainHTML = generateFormRemain(headerFlag, jsonObj);

                    var divEle = document.createElement("div");
                    divEle.setAttribute("id", formId);
                    divEle.innerHTML = headerFlag ? headerHTML + remainHTML : remainHTML;
                    document.body.appendChild(divEle);
                });
        },

        /**
         * 根据JSON格式创建表单
         *
         * @return    string    返回form
         */
        generateFormByJSON: function(jsonData) {
            var jsonObj = JSON.parse(data);
            var headerFlag = false;
            if (!(typeof jsonObj.header == "undefined")) {
                var headerHTML = generateFormHeader(jsonObj.header);
                headerFlag = true;
            }
            //使用delete之后，jsonObj的长度保持不变
            delete jsonObj.header;
            var remainHTML = generateFormRemain(headerFlag, jsonObj);

            var divEle = document.createElement("div");
            divEle.setAttribute("id", formId);
            divEle.innerHTML = headerFlag ? headerHTML + remainHTML : remainHTML;
            document.body.appendChild(divEle);
        }
    };
};
