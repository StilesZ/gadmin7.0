/**
 *
 * @type {string[]}
 */
var dataType = ['yyyy', 'MM-dd', 'yyyy-MM-dd', 'yyyyMMdd', 'yyyy-MM','yyyy-MM-dd HH:mm:ss'];
var dateType_format= ['date', 'datetime', 'time', 'year', 'month'];


var sfdpPlug = {
    PlugInit:function (type,data,is_sub= false,Curd='add') {
        var maste ='';
        var read ='';
        var rows_label ='';
        if (data.tpfd_must == 0) {
            maste = ' datatype="*" nullmsg="请填写'+data.tpfd_name+'"';
            rows_label = sfdp.ui.rows_label + ' g-require'
        }else{
            rows_label = sfdp.ui.rows_label
        }
        if (data.tpfd_read == 0) {
            read = 'readonly';
        }
        var field_att = read + maste;
        if (typeof (data['tpfd_name']) == 'undefined') {
            return sfdpPlug.view_default(type, data);
        } else {
            if (data.tpfd_help == '' || data.tpfd_help == undefined) {
                var tpfd_help = '"';
            }else{
                var tpfd_help = `sfdp_tips" sfdp-tip-data="${data.tpfd_help}"`;
            }
            var lab = `<label title="${data.tpfd_name}" class="${rows_label} ${tpfd_help} ><b>${data.tpfd_name}</b></label><div class="${sfdp.ui.rows_block}">`;
            if (is_sub) {
                lab = '';
            }
            if(type=='group'){
                lab =`<fieldset class="layui-elem-field layui-field-title" style="margin: 0px;"><legend>${data.tpfd_name}</legend></fieldset>`;
            }
            if (s_type==1) {
                lab = `<div>`;
            }
            if(data.tpfd_lable==0){
                lab =``;
            }
            var html = lab + sfdpPlug.common(type,data,field_att,Curd);
        }
        return html + '</div>';
    },
    PlugList:function (type,item) {
        if(item.field =='status'){
            type ='dropdown';
            item.type_data =  '{"-1": "退回修改", "0": "保存中", "1": "流程中", "2": "审核通过"}';
        }
        var data ='';
        if(type==='radio'||type==='checkboxes'){
            item.tpfd_db = item.field;
            item.tpfd_data = eval('(' + item.type_data + ')');
        }
        if(type==='dropdown' || type==='suphelp' ) {
            item.tpfd_db = item.field;
            item.tpfd_data = item.type_data;
            data = JSON.parse(item.tpfd_data)
        }
        var fieldArray = {
            text : '<input  type="text"  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            tag : '<input  type="text"  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            billno : '<input  type="text"  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            scan : '<input  type="text"  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            money : '<input  type="text"  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            number :'<input  type="number" name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            radio : sfdpPlug.view_select(item.tpfd_data, item.tpfd_db, '' , '', item.id,'','',item),
            //checkboxes : sfdpPlug.checkboxes_clss(item.id,'checkboxes','',item.field,'',item.tpfd_data,data.tpfd_check).replace(/<a>(.+?)<\/a>/g,''),
            textarea  : '<input  type="text"  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            dropdown  : sfdpPlug.view_select(data, item.tpfd_db, '' , '', item.id,'','',item),
            suphelp  : sfdpPlug.view_select(data, item.tpfd_db, '' , '', item.id,'','',item),
            //upload: sfdpPlug.view_upload(data, item.field, 0),
            date : '<input  type="text"  placeholder="'+item.name+'起" data-format="yyyy-MM-dd" data-type="date" class="datetime sfdp-input layui-input"  name="@' + item.field + '[]"></div><div class="layui-input-inline" style="width:90px"><input  type="text"  placeholder="'+item.name+'止" data-format="yyyy-MM-dd" data-type="date" class="datetime sfdp-input layui-input"  name="@' + item.field + '[]">',
            //group : ''
        }
        return fieldArray[type] || '组件不支持';
    },
    common:function (type,data,att,Curd) {
        if(data.tpfd_yztype && data.tpfd_yztype!=''){
            var att =`datatype="${data.tpfd_yztype}" errormsg="${data.tpfd_yztip}"`;
        }

        var name = data.tpfd_db,radios='',checkboxes='',view_selects='';
        var placeholder ='placeholder="'+(data.tpfd_moren || '')+'"';
        if(Curd==='add'){
            var value = sfdp.default_data((data.tpfd_zanwei || ''));
        }else{
            var value =(data.value || '');
        }
        if(type=='radio'){
            radios = sfdpPlug.checkboxes_clss(data.tpfd_id,'radio',att,name,value,data.tpfd_data,data.tpfd_check);
        }
        if(type=='checkboxes'){
            checkboxes = sfdpPlug.checkboxes_clss(data.tpfd_id,'checkboxes',att,name,value,data.tpfd_data,data.tpfd_check);
        }
        if(type=='dropdown'){
            view_selects = sfdpPlug.view_select(data.tpfd_data, name, value , att, data.tpfd_id,'',data.rvalue,data);
        }
        if(type=='dropdowns'){
            view_selects = sfdpPlug.view_select(data.tpfd_data, name, value , att, data.tpfd_id,1,data.rvalue,data);
        }
        if(type=='cascade'){//级别联动
            selectTree[data.tpfd_id] =data.tpfd_data;
            view_selects = sfdpPlug.view_select2(data.tpfd_data, name, value , att, data.tpfd_id);
        }
        if(type=='money'){
            var max_num= [1,10,100, 1000, 10000, 100000, 1000000, 10000000, 100000000, 1000000000, 10000000000, 100000000000, 1000000000000, 10000000000000, 100000000000000, 1000000000000000, 10000000000000000];
            var szcd = ((data.tpfd_dbcd).split(','))[0] || 0
            var xscd = ((data.tpfd_dbcd).split(','))[1] || 0
            var max_number = max_num[szcd-2] || 10;
        }
        if(type=='suphelp'){
            view_selects = sfdpPlug.view_select(data.tpfd_data, name, value , att, data.tpfd_id,'',data.rvalue,data);
        }
        var fieldArray = {
            process : `<div id="${data.tpfd_id}" class="${data.tpfd_id} scrollBar"></div><input type="hidden" ${att} value="${value}"  name="${name}"  ${placeholder} id="${data.tpfd_id}_input" class="${sfdp.ui.input_input}">`,
            suphelp : view_selects,
            billno: '<input type="text" readonly ' + att + ' value="' + value + '" name="' + name + '"placeholder="保存后自动生成编码" id="' + data.tpfd_id + '" class="' + sfdp.ui.input_input + '">',
            scan: '<input style="width:85%;" type="text" readonly ' + att + ' value="' + value + '" name="' + name + '"placeholder="快速识别二维码功能" id="' + data.tpfd_id + '" class="' + sfdp.ui.input_input + '"><a href="javascript:" class="layui-btn  layui-btn-primary layui-border-blue" onclick=sfdp.openpage("扫描","'+sfdp.url.api+'?act=scan&id=' + data.tpfd_id + '&value=' + (data.value || '') + '",{w:"510px",h:"610px"})><i class="layui-icon layui-icon-camera"></i> 扫描</a>',
            sign: '<span id="' + data.tpfd_id + '_sign" style="height:38px;padding-left: 10px;"><img width="100px" src="'+(data.value || '')+'" onclick=sfdp.view_img("'+(data.value || '')+'")></span><input type="hidden" readonly ' + att + ' value="' + value + '" name="' + name + '"placeholder="签名信息" id="' + data.tpfd_id + '" class="' + sfdp.ui.input_input + '"><a href="javascript:" class="layui-btn  layui-btn-primary layui-border-blue" onclick=sfdp.openpage("签名信息","'+sfdp.url.api+'?act=sign&id=' + data.tpfd_id + '",{w:"610px",h:"510px"})><i class="layui-icon layui-icon-edit"></i> 签名</a>',
            text : `<input type="text" ${att}  value="${value}" name="${name}"  ${placeholder} id="${data.tpfd_id}" class="${sfdp.ui.input_input}">`,
            tag : `<input type="text" ${att}  value="${value}" name="${name}"  ${placeholder} id="${data.tpfd_id}" class="${sfdp.ui.input_input} tagInput">`,
            number :'<input type="number" ' + att + ' value="' + value + '" name="' + name + '"  '+placeholder+' id="' + data.tpfd_id + '" class="' + sfdp.ui.input_input + '">',
            money :'<input type="text" ' + att + ' value="' + value + '" name="' + name + '"  '+placeholder+' id="' + data.tpfd_id + '" class="' + sfdp.ui.input_input + '" onkeyup="this.value=/^[0-9]*\\.?[0-9]{0,'+(xscd || 0)+'}$/.test(this.value) ? this.value : this.value.substring(0,this.value.length-1)" onblur="if(this.value>'+(max_number || 0)+'){layer.msg(\'不符合，最大值为'+(max_number || 0)+'。\');this.value='+(max_number-1 || 0)+';}">',
            radio : radios,
            checkboxes : checkboxes,
            textarea  : '<textarea id="' + data.tpfd_id + '" name="' + name + '" ' + att + ' class="' + sfdp.ui.input_textarea + '">' + value +'</textarea>',
            dropdown  : view_selects,
            dropdowns  : view_selects,
            cascade  : view_selects,
            upload: sfdpPlug.view_upload(data, name, 0),
            date : '<input type="text" ' + att + ' data-type="' + data.tpfd_dblx + '" data-format="' + dataType[data.xx_type] + '" class="datetime  layui-input"  name="' + name + '"  id="' + data.tpfd_id + '" value="' + value + '">',
            time_range : '<input type="text" ' + att + ' data-type="' + data.tpfd_dblx + '" data-format="' + dateType_format[data.xx_type] + '" class="time_range layui-input"  name="' + name + '"  id="' + data.tpfd_id + '" value="' + value + '">',
            html : '<div style="min-height: 38px;line-height: 38px;">'+ (data.value || data.tpfd_moren) + '</div>',
            wenzi : '<div style="min-height: 38px;line-height: 38px;">'+ (data.value || data.tpfd_moren) + '</div>',
            links : `<div style="min-height: 38px;line-height: 38px;"><a href="//${data.tpfd_moren}" target="${data.tpfd_zanwei}" class="layui-btn layui-btn-primary layui-border-blue">${data.tpfd_name}</a></div>`,
            system_user:'<input name="' + name + '"  type="hidden" readonly id="' + data.tpfd_id + '" value="' + value + '"><input  type="text" readonly id="' + data.tpfd_id + '_text" value="' + (data.text || '请选择') +'" style="width:85%;" class="layui-input"><a  href="javascript:" class="layui-btn  layui-btn-primary layui-border-blue" style="width:15%;" onclick=sfdp.openpage("用户组件","'+sfdp.url.user+'?id=' + data.tpfd_id + '&value=' + (data.value || '') + '",{w:"90%",h:"60%"})><i class="layui-icon layui-icon-friends"></i> 选择</a>',
            system_role:'<input name="' + name + '"  type="hidden" readonly id="' + data.tpfd_id + '" value="' + value + '"><input  type="text" readonly id="' + data.tpfd_id + '_text" value="' + (data.text || '请选择') +'" style="width:85%;" class="layui-input"><a  href="javascript:" class="layui-btn  layui-btn-primary layui-border-blue" style="" onclick=sfdp.openpage("用户组件","'+sfdp.url.role+'?id=' + data.tpfd_id + '&value=' + (data.value || '') + '",{w:"90%",h:"60%"})><i class="layui-icon layui-icon-group"></i> 选择</a>',
            upload_img:sfdpPlug.view_upload_img(data,att),
            edit:'<div id="div' + data.tpfd_id + '">' + value +'</div><textarea style="display:none" id="' + data.tpfd_id + '" name="' + name + '" ' + att + ' class="' + sfdp.ui.input_textarea + '">' + value +'</textarea><script></script><script type="text/javascript">' +
                '$.getScript("/static/lib/wangEditor.min.js",function(){var html = $(\'#' + data.tpfd_id + '\').val();const E = window.wangEditor; const editor = new E(\'#div' + data.tpfd_id + '\');editor.config.zIndex = 500;const $text1 = $(\'#' + data.tpfd_id + '\');  editor.config.onchange = function (html) {$text1.val(html)};editor.config.uploadImgServer="/gadmin/common/eupload";editor.config.uploadFileName ="file";editor.create();$text1.val(editor.txt.html());}); </script>',
            group:'',
        }
        return fieldArray[type];
    },
    view_select2: function (data, field, value, attr, selectId,is_dx=0,rvalue='') {
        var tags = `<select name="${field}" ${attr} id="${selectId}" class="this-select222" data-widget="select2"  data-value="${value}" lay-search>`
        tags += `</select>`
        return tags
    },
    view_select: function (data, field, value, attr, selectId,is_dx=0,rvalue='',jsonData='') {
        if(is_dx==1){
            var tags = `<input id="${selectId}_val" type="hidden" value="${rvalue}" name="${field}"><select  ${attr} id="${selectId}" class="this-select" data-widget="select2"   data-rvalue="${rvalue}" data-value="${value.replace(/<[^>]+>/g,'')}" lay-search multiple="true">`
        }else{
            var tags = `<select name="${field}" ${attr} id="${selectId}" class="this-select" data-widget="select2" data-rvalue="${rvalue}"   data-value="${value}" lay-search>`
            tags += ` <option value="">选择${jsonData.tpfd_name || jsonData.name}</option>`;
        }
        if (value !== undefined ) {
            var strs = (value).split(",");
        }
        for (const key in data) {
            var check ='';
            if(data.hasOwnProperty(key)){
                if (value !== undefined  && sfdpPlug.isInArray(strs, data[key])) {
                    check = 'selected';
                }
                tags += `<option value="${key}" ${check}>${data[key]}</option>`;
            }
        }
        if(attr=='0'){
            tags += `</select><a class="layui-btn layui-btn-primary" onclick="sfdpPlug.supHelp(${jsonData.tpfd_suphelp},'${jsonData.tpfd_name}','${selectId}')" style="border-left-width: 0px;"><i class="layui-icon">&#xe604;</i></a>`
            }else{
            tags += `</select>`
        }
        return tags
    },
    supHelp:function(sid,title,selectId='',act = 'select'){
        if(act=='select'){
            sfdp.openpage(title, 'index?sid='+sid+'&supHelp='+selectId);
        }
        if(act=='show'){
            sfdp.openpage(title, 'view?sid='+sid+'&id='+selectId);
        }
    },
    view_upload_img: function (data,att) {
        let html= '<input  type="hidden"  name="' + data.tpfd_db + '" style="width:85%;"  ' + att + ' id="' + data.tpfd_id + '" value=' + (data.value || '') + '><span id="' + data.tpfd_id + '_img"></span>  <a id="' + data.tpfd_id + '_btn" href="javascript:" class="layui-btn layui-btn-radius layui-btn-primary layui-border-blue" style="" onclick=sfdp.H5uploadhtml("' + data.tpfd_id + '",1)><i class="layui-icon layui-icon-upload-drag"></i> <span id="' + data.tpfd_id + '_text">选择图片</span></a>';
        return html;
    },
    view_upload: function (data) {
        var html = '<input type="text" style="width:85%;"class="layui-input" name="' + data.tpfd_db + '" readonly id="' + data.tpfd_id + '" value=' + (data.value || '') + '><span>' + '<a id="' + data.tpfd_id + '_btn" href="javascript:" class="layui-btn layui-btn-primary layui-border-blue" style="" onclick=sfdp.H5uploadhtml("' + data.tpfd_id + '")><i class="layui-icon layui-icon-upload-drag"></i> <span id="' + data.tpfd_id + '_text">上传</span></a></span>';
        if (data.tpfd_upload_type == 1) {
            if(data.tpfd_upload_api==0){
                var upload_url = uploadurls;
            }else{
                var upload_url = data.tpfd_upload_action;
            }
            var html = '<input type="text" style="width:85%;"class="layui-input" name="' + data.tpfd_db + '" readonly id="' + data.tpfd_id + '" value=' + (data.value || '') + '><span>' + '<a class="layui-btn layui-btn-primary layui-border-blue" style="" id="label_' + data.tpfd_id + '" onclick=sfdp.openpage("多文件上传","' + upload_url + '?id=' + data.tpfd_id + '&value=' + (data.value || '') + '",{w:"90%",h:"60%"})><i class="layui-icon layui-icon-upload-drag"></i>上传</a></span>';
        }
        return html;
    },
    checkboxes_clss:function (id, type = 'checkbox', att,name,value,data,def_check) {
        if(type==='checkboxes'){
            type = 'checkbox';
        }
        var htmls = '',check='';
        if (value !== undefined ) {
            var strs = (value).split(",");
        }
        for (y in data) {
            check ='';
            if (value !== undefined  && sfdpPlug.isInArray(strs, data[y])) {
                check = 'checked';
            }
            if(isNaN(id)){
                if(value==='' || value == undefined){
                    if (y == def_check && type=='radio') {
                        check = 'checked';
                    }
                    if (sfdpPlug.isInArray(def_check,y) && type=='checkbox') {
                        check = 'checked';
                    }
                }
            }
            htmls += '<input ' + att + ' ' + check + ' name="' + name + '[]" value=' + y + ' type="' + type + '" title="'+data[y]+'"><a>&nbsp &nbsp' + data[y] + '&nbsp &nbsp</a>';
        }
        return '<div style="height: 38px;line-height: 38px;" id="' + id + '"><div style="padding-left: 15px;">'+htmls+'</div></div>';
    },
    isInArray:function (arr, value) {
        for (var i = 0; i < arr.length; i++) {
            if (value === arr[i]) {
                return true;
            }
        }
        return false;
    },
    view_default: function (type) {
        var fieldArray = {
            text :'<label>文本控件：</label><input type="text"  placeholder="请输入信息~" >',
            upload :'<label>上传控件：</label>上传',
            checkboxes :'<label>多选控件：</label>选项1<input type="checkbox"  placeholder="" > 选项2<input type="checkbox"  placeholder="" >',
            radio :'<label>单选控件：</label>选项1<input type="radio"  placeholder="" > 选项2<input type="radio"  placeholder="" >',
            date :'<label>时间日期：</label><input type="text"  placeholder=""  >',
            dropdown :'<label>下拉选择：</label><select ><option value ="请选择">请选择</option></select>',
            textarea :'<label>多行控件：</label><textarea   ></textarea>',
            html :'<label>HTML控件：</label><b style="color: blue;">Look this is a HTML</b>',
            wenzi :'<label>文字控件：</label>默认现实的文本',
        }
        return fieldArray[type];
    },
    view_show: function (type,data,is_sub = false) {
        var html = '';
        if (typeof (data['tpfd_name']) == 'undefined') {
            return sfdpPlug.view_default(type, data);
        } else {
            if (data.tpfd_help == '' || data.tpfd_help == undefined) {
                var tpfd_help = '"';
            }else{
                var tpfd_help = `sfdp_tips" sfdp-tip-data="${data.tpfd_help}"`;
            }
            var lab = `<div><label class="${sfdp.ui.rows_label} ${tpfd_help} id="label_${data.tpfd_id}"><b>${data.tpfd_name}：</b></label><div class="${sfdp.ui.rows_block}" id="${sfdpPlug.htmlEncode(data.value)}">`;
            if (is_sub || data.tpfd_lable==0) {
                lab = '';
            }
            if(type==='upload'){
                if (data.tpfd_upload_type == 1) {
                    if(data.value==null || data.value==''){
                        html = lab + `<div style="min-height: 38px;line-height: 38px;">无</div>`;
                    }else{
                        if(data.tpfd_upload_api==0){
                            var upload_url = uploadurls;
                        }else{
                            var upload_url = data.tpfd_upload_action;
                        }
                    html = lab + `<label id="label_${data.tpfd_id}" onclick=sfdp.openpage("附件阅读","${upload_url}?act=view&id=${data.tpfd_id}&value=${data.value}",{w:"98%",h:"98%"})><span style="font-size: 16px; color: #5e7ce0;">查看附件<span style="color: #f00;font-size: 16px;position: absolute;left: 62px;top: -1px;">${data.value.split(',').length}</span></></label>`;
                    }
                }else{
                    if(data.value==null || data.value==''){
                        html = lab + `<div style="min-height: 38px;line-height: 38px;">无</div>`;
                    }else {
                        html = lab + '<a target="_blank" href="/' + data.value + '" >' + sfdp.Ico(1) + '</a>';
                    }
                }
            }else if(type==='links'){
                html = lab + `<div style="min-height: 38px;line-height: 38px;"><a href="//${data.tpfd_moren}" target="${data.tpfd_zanwei}" class="layui-btn layui-btn-primary layui-border-blue">${data.tpfd_name}</a></div>`;
            }else if(type==='upload_img'){
                if(data.value==null || data.value==''){
                    html = lab + `<div style="min-height: 38px;line-height: 38px;">无</div>`;
                }else{
                    var mulu ='/';
                    if(data.value.indexOf('data:image/')!=-1){
                        mulu ='';
                    }
                    html = lab + `<div style="min-height: 38px;line-height: 38px;"><img src="${mulu}${data.value}" width="100px" onclick="window.open($(this).attr('src'))">'</div>`;
                }
            }else if(type==='system_user' || type==='system_role'){
                html = lab + `<div style="min-height: 38px;line-height: 38px;">${data.text}</div>`;
            }else if(type==='group'){
                html = `<fieldset class="layui-elem-field layui-field-title" style="margin: 0px;"><legend>${data.tpfd_name}</legend></fieldset>`;
            }else if(type==='process'){
                html = lab +`<div id="progressBar"><div id="progressBar_Track" style="width: ${data.value}%;"></div><a class="action-value" style="left: ${data.value-2}%">${data.value}</a></div>`;
            }else if(type==='suphelp') {
                html = lab + `<div style="min-height: 38px;line-height: 38px;" id="${data.tpfd_id}" data-value="${data.rvalue}">${data.value}  <a class="layui-btn layui-btn-primary" onclick="sfdpPlug.supHelp(${data.tpfd_suphelp},'${data.tpfd_name}',${data.rvalue},'show')" style="border: 0px;color: #5e7ce0;"><i class="layui-icon">&#xe604;</i></a></div>`;
            }else if(type==='sign'){
                html = lab + '<span id="' + data.tpfd_id + '_sign" style="height:38px;padding-left: 10px;"><img width="100px"src="'+(data.value || '')+'"onClick=sfdp.view_img("'+(data.value || '')+'")></span>';
            }else{
                var data_value = sfdpPlug.htmlEncode(data.value);
                html = lab + `<div style="min-height: 38px;line-height: 38px;" id="${data.tpfd_id}" data-value="${data_value}">${data.value}
</div>`;
            }
        }
        return html + '</div></div>';
    },
    /*1.用浏览器内部转换器实现html转码*/
    htmlEncode: function (html) {
        var temp = document.createElement("div");
        (temp.textContent != undefined ) ? (temp.textContent = html) : (temp.innerText = html);
        var output = temp.innerHTML;
        temp = null;
        return output;
    },
    /*2.用浏览器内部转换器实现html解码*/
    htmlDecode: function (text) {
        var temp = document.createElement("div");
        temp.innerHTML = text;
        var output = temp.innerText || temp.textContent;
        temp = null;
        return output;
    },
    sliderSet:function(){
        var obj = $(".scrollBar");
        $.each(obj,function(){
            sfdpPlug.slider($(this).attr('id'));
        })
    },
    slider: function (self_id) {
        /**
         * 特别鸣谢作者，根据作者代码进行细节优化，重新封装
         * @Author 无趣
         */
        var self = $('#'+self_id);
        var value = $('#'+self_id+'_input').val();
        var conf = {
            active: false, mousedownEvent: {}, sliderRange: null,
            slider: null, sliderActive: null,
            sliderValue: null,
            sliderRangeWidth: 0,
            sliderHalfWidth: 0,
            minValue: 0,
            maxValue: 100,
            diffValue: 0,
            initValue: value || 0
        };
        self.html(`<div class="slider-range"><span class="slider"></span><span class="line"></span><span class="active-line"></span><span class="action-value"></span></div>`);
        conf.sliderRange = self.find('.slider-range');
        conf.slider = conf.sliderRange.find('.slider');
        conf.mousedownEvent = {};
        conf.sliderActive = conf.sliderRange.find('.active-line');
        conf.sliderValue = conf.sliderRange.find('.action-value');
        conf.sliderRangeWidth = conf.sliderRange.find('.line').width();
        conf.sliderHalfWidth = conf.slider.outerWidth(true)/2;
        conf.diffValue = conf.maxValue - conf.minValue;
        conf.sliderValue.text(conf.minValue);
        conf.sliderValue.text(conf.minValue).css({
            left: parseInt(conf.slider.css('left')) - (conf.sliderValue.outerWidth(true)/2) + conf.sliderHalfWidth
        });
        if (conf.initValue >= conf.minValue && conf.initValue <= conf.maxValue){
            var x = parseInt( (conf.initValue/conf.maxValue * conf.sliderRangeWidth - conf.sliderHalfWidth)*100) / 100;
            conf.sliderValue.text(conf.initValue).css({
                left: x - (conf.sliderValue.outerWidth(true)/2) + conf.sliderHalfWidth
            });
            conf.sliderValue.text(conf.initValue);
            self.data('sliderValue',conf.initValue);
            conf.sliderActive.css({
                width: x + conf.sliderHalfWidth
            });
            conf.slider.css({
                left: x
            });
        }
        conf.slider.bind('mousedown',function (e) {
            e.preventDefault();
            e.stopPropagation();
            conf.active = true;
            conf.mousedownEvent.x = e.clientX;
            conf.mousedownEvent.y = e.clientY;
            conf.mousedownEvent.left = parseInt($(this).css('left'));
            conf.mousedownEvent.top = parseInt($(this).css('top'));
        });
        $(document).bind('mousemove',function (ev) {
            if(conf.active === false){
                return conf.active;
            }
            var x = ev.clientX - conf.mousedownEvent.x + conf.mousedownEvent.left;
            if (x < -conf.sliderHalfWidth){
                x = -conf.sliderHalfWidth;
            }
            if (x > conf.sliderRangeWidth - conf.sliderHalfWidth){
                x = conf.sliderRangeWidth - conf.sliderHalfWidth;
            }
            var value = parseInt(conf.minValue + ( x + conf.sliderHalfWidth) / conf.sliderRangeWidth * conf.diffValue);
            if (value < conf.minValue) {
                value = conf.minValue;
            }
            if (value > conf.maxValue){
                value = conf.maxValue;
            }
            $('#'+self_id+'_input').val(value);
            conf.sliderValue.text(value).css({
                left: x - (conf.sliderValue.outerWidth(true)/2) + conf.sliderHalfWidth
            });
            conf.sliderActive.css({
                width: x + conf.sliderHalfWidth
            });
            conf.slider.css({
                left: x
            });
        });
        $(document).bind('mouseup',function () {
            conf.active = false;
        });
        return this;
    }
}