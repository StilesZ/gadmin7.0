/**
 * 获取input 参数数据
 * @returns {boolean|string[]}
 */
function getParam() {
	if ($("input[name='ids']:checked").length > 1) {
		layer.msg('只能选择一个哟~~');
		return false;
	}
	var val = $(".layui-table-box input:checkbox:checked").val();
	if (val == '' || val == undefined) {
		layer.msg('请选择您要操作的单据~~');
		return false;
	}
	return val.split('/');
}

/**
 * 导出excel
 * @param title 导出文件名
 * @param count 总列数据
 */
function exp_excle(title,count){
	var top = $('#tabledata thead tr').children("th");
	var tophtml ='<tr>';
	for (var i=0;i<top.length;i++) {
		if(i>1 && i<top.length-1){
			var td = $(top[i]).html();
			tophtml += `<td>${td}</td>`;
		}
	}
	var heads = $('.layui-table-main .layui-table tbody').children("tr");
	var table ='';
	for (var i=0;i<heads.length;i++) {
		var Trhtml ='<tr>';
		var Tr = heads[i].cells;
		for (var ii=0;ii<Tr.length;ii++) {
			if(ii>1 && ii<Tr.length-1){
				var td = $(Tr[ii]).html();
				Trhtml += `<td>${td}</td>`;
			}
		}
		table = table + Trhtml+'</tr>';
	}
	var base64 = function (s) {
		return window.btoa(unescape(encodeURIComponent(s)));
	};
	var format = function (s, c) {
		return s.replace(/{(\w+)}/g,
			function (m, p) {
				return c[p];
			});
	}
	var uri = 'data:application/vnd.ms-excel;base64,';
	var tmp ='<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Sheet0</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table>';
	var uri = uri + base64(format(tmp, { table: tophtml+table })+'</html>');
	var link = document.createElement("a");
 		link.download = title+'.xls';
 		link.href = uri;
 		document.body.appendChild(link);
 		link.click();
 		document.body.removeChild(link);
 		delete link;
		//window.location.href = uri + base64(format(tmp, { table: tophtml+table })+'</html>');
}

function help_print(sid,id){
	sfdp.openfullpage("打印生成","/gadmin/sfdp/print_view?sid="+sid+"&id="+id);
}
function help_view(sid){
	sfdp.openfullpage("帮助系统","/gadmin/sfdp/help_view?sid="+sid);
}
/**
 * 打印表单
 */
function exp_print(){
	$.when(
		$.getScript( "/static/lib/jquery-migrate-1.2.1.min.js" ),
		$.getScript( "/static/lib/jquery.jqprint-0.3.js" ),
		$.Deferred(function( deferred ){
			$( deferred.resolve );
		})
	).done(function(){
		var top = $('#tabledata thead tr').children("th");
		var tophtml ='<tr>';
		for (var i=0;i<top.length;i++) {
			if(i>1 && i<top.length-1){
				var td = $(top[i]).html();
				tophtml += `<td>${td}</td>`;
			}
		}
		var heads = $('.layui-table-main .layui-table tbody').children("tr");
		var table ='';
		for (var i=0;i<heads.length;i++) {
			var Trhtml ='<tr>';
			var Tr = heads[i].cells;
			for (var ii=0;ii<Tr.length;ii++) {
				if(ii>1 && ii<Tr.length-1){
					var td = $(Tr[ii]).html();
					Trhtml += `<td>${td}</td>`;
				}
			}
			table = table + Trhtml+'</tr>';
		}
		$("#tabledata").jqprint('',`<link type='text/css' rel='stylesheet' href='/static/lib/jexcel/jquery.jexcel.css' media='print' /><table class="jexcel">${tophtml+table}</table>`);
	});
}
/**
 * 按钮重新布局
 */
function adjustButtons(){
	let sideBarOpen = window?.top?.vm?.sideBarOpen
	let classicalLayout = window?.top?.vm?.classicalLayout
	let width = window.top.document.body.clientWidth
	let w
	if(sideBarOpen){
		w = classicalLayout ? 282: 345
	}else{
		w = classicalLayout ? 122 : 125
	}

	document.getElementById('searchform').style.marginTop = width < 1000 ? '8px':'0'
	document.getElementById("tabledata").style.width = width - w   + 'px'

}

/**
 * 关闭弹出层
 */
function layer_close(){
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);
}

function g_popconfirm(content, follow, options){
	var icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#3491FA" stroke-width="4"/></svg>'
	var options = $.extend({type: 4,tips:3,icon:icon,shade:false,resize: false,fixed: false,text:'此修改将不可逆',skin:'info',btn: ['确认','取消']},options);
	switch (options.skin) {
		case 'warning':
			options.skin = 'layer-popconfirm layer-popconfirm-warning'
			options.icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#FC7C00" stroke-width="4"/></svg>'
			break;
		case 'danger':
			options.skin = 'layer-popconfirm layer-popconfirm-danger'
			options.icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#F53F3F" stroke-width="4"/></svg>'
			break;
		default:
			options.skin = 'layer-popconfirm'
			break;
	}
	options.content = ['<i class="layer-popconfirm-icon">'+options.icon+'</i>'+'<div class="layer-popconfirm-body"><div class="layui-popconfirm-title">'+content+'</div>'+'<div class="layui-popconfirm-disc">'+options.text+'</div></div>',follow];
	return parent.layer.open(options);
}
// 提示
function g_toast(content,options){
	var icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#3491FA" stroke-width="4"/></svg>'
	var options = $.extend({title:false,btn:0,shade:false,type:1,resize: false,time:3*1000,text:'此修改将不可逆',skin:'success'},options);
	switch (options.skin) {
		case 'warning':
			icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#FC7C00" stroke-width="4"/></svg>'
			options.skin = 'layer-toast  layer-toast-warning'
			break;
		case 'error':
			icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#F53F3F" stroke-width="4"/></svg>'
			options.skin = 'layer-toast layer-toast-error'
			break;
		case 'success':
			icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18z" stroke="#00B42A" stroke-width="4"/><path d="M15 22l7 7 11.5-11.5" stroke="#00B42A" stroke-width="4"/></svg>'
			options.skin = 'layer-toast layer-toast-success'
			break;
		case 'info':
			icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#4E5969" stroke-width="4"/></svg>'
			options.skin = 'layer-toast layer-toast-info'
			break;
		default:
			options.skin = 'layer-toast'
			break;
	}
	options.content = '<div class="nprogress"></div><i class="layer-toast-icon">'+icon+'</i>'+'<div class="layer-toast-body">'+content+'</div>';
	return parent.layer.open(options);
}
// 通知
function g_notice(content,options){
	var icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#3491FA" stroke-width="4"/></svg>'
	var options = $.extend({type:1,title:'通知',shade:false,resize: false,type:1,fixed: false,time:3*1000,offset:'rt',skin:'info'},options);
	switch (options.skin) {
		case 'warning':
			options.skin = 'layer-notice layer-notice-warning'
			icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#FC7C00" stroke-width="4"/></svg>'
			break;
		case 'error':
			options.skin = 'layer-notice layer-notice-error'
			icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18zM24 20v14M24 18v-4" stroke="#F53F3F" stroke-width="4"/></svg>'
			break;
		case 'success':
			options.skin = 'layer-notice layer-notice-success'
			icon = '<svg width="24" height="24" viewBox="0 0 48 48" fill="none"><path d="M42 24c0 9.941-8.059 18-18 18S6 33.941 6 24 14.059 6 24 6s18 8.059 18 18z" stroke="#00B42A" stroke-width="4"/><path d="M15 22l7 7 11.5-11.5" stroke="#00B42A" stroke-width="4"/></svg>'
			break;
		default:
			options.skin = 'layer-notice'
			break;
	}
	options.content = '<div class="nprogress"></div><i class="layer-notice-icon">'+icon+'</i>'+'<div class="layer-notice-body"><div class="layui-notice-title">'+options.title+'</div>'+'<div class="layui-notice-disc">'+content+'</div></div>';
	options.title   = false
	return parent.layer.open(options);
}



/**
 * ajax 提交表单返回数据
 * @param data
 * @param callback
 * @param param
 */
function ajax_progress(data, callback, param) {
    if (data.code === 0) {
		layer.msg(data.msg,{icon:1,time: 1500},function(){
			parent.location.reload(); // 父页面刷新
        });          
    } else {
       layer.alert(data.msg, {title: "错误信息", icon: 2});
    }
}

/**
 * 打开页面
 * @param title
 * @param url
 * @param w
 * @param h
 */
function layer_show(title,url,w,h){
	if (title == null || title == '') {
		title=false;
	};
	if (w == null || w == '') {
		w=80;
	};
	if (h == null || h == '') {
		h=80;
	};
	layer.open({
		type: 2,
		area: [w+'%', h +'%'],
		fix: false, //不固定
		maxmin: true,
		shade:0.4,
		title: title,
		content: url
	});
}

/**
 * 结束工作流
 */
function end_flow(){
	var Param =  getParam();
	if(!Param){
		return;
	}
	if(Param[1]==1){
		var index = layer.load(1, {
			shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
		$.ajax({
			type: "GET",
			url: "/gadmin/wf/wfdo?act=endflow&bill_id="+Param[0]+'&bill_table='+Param[2],
			dataType: "json",
			success: function(data){
				if(data.code==1){
					layer.close(index);
					layer.alert(data.msg);
				}else{
					window.location.reload();
				}
			}
		});
	}else{
		layer.msg('选中的单据已经被审批或者未发起工作流~~');return;
	}
}

/**
 * 取消工作流
 */
function cancel_flow(){
	var Param =  getParam();
	if(!Param){
		return;
	}
	if(Param[1]==2){
		var index = layer.load(1, {
			shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
		$.ajax({
			type: "GET",
			url: "/gadmin/wf/wfdo?act=cancelflow&bill_id="+Param[0]+'&bill_table='+Param[2],
			dataType: "json",
			success: function(data){
				if(data.code==1){
					layer.close(index);
					layer.alert(data.msg);
				}else{
					layer.msg(data.msg,{icon:1,time: 1500},function(){
						window.location.reload();
					});
				}
			}
		});
	}else{
		layer.msg('选中的单据已经被审批或者未发起工作流~~');return;
	}
}
/**
 * 不足位数补充0
 */
function g_PrefixZero(num, n) {
	return (Array(n).join(0) + num).slice(-n);
}

/**
 * 不满十，前面加0
 */
function g_Appendzero(obj)
{
	if(obj<10) return "0" +""+ obj;
	else return obj;
}
/**
 * 日期时间函数
 */
function g_GetCurrentDate(type) {
	var myDate = new Date;
	var year = myDate.getFullYear();
	var mon = myDate.getMonth() + 1;
	var date = myDate.getDate();
	var h = myDate.getHours();
	var m = myDate.getMinutes();
	var s = myDate.getSeconds();
	if(type == 'date'){
		return year+'-'+g_Appendzero(mon)+'-'+g_Appendzero(date);
	}
	if(type == 'datetime'){
		return year+'-'+g_Appendzero(mon)+'-'+g_Appendzero(date)+' '+g_Appendzero(h)+':'+g_Appendzero(m)+':'+g_Appendzero(s);
	}
	if(type == 'year'){
		return year;
	}
	if(type == 'year-month'){
		return year+'-'+g_Appendzero(mon);
	}
}

//求和
function g_sumArr(arr){
	var sumText=0;
	for(var i=0,len=arr.length;i<len;i++){
		sumText+=arr[i];
	}
	return sumText
}
// 平均值
function g_covArr(arr){
	var sumText=sumArr(arr);
	var covText=sumText/arr.length;
	return covText
}

//获取url中的参数
function g_getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg);  //匹配目标参数
	if (r != null) return unescape(r[2]); return null; //返回参数值
}

//时间戳转为日期
function g_timestampToTime(timestamp) {
	var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
	Y = date.getFullYear() + '-';
	M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
	D = date.getDate() + ' ';
	h = date.getHours() + ':';
	m = (date.getMinutes() < 10 ? '0'+(date.getMinutes()) : date.getMinutes()) + ':';
	s = (date.getSeconds() < 10 ? '0'+(date.getSeconds()) : date.getSeconds());
	return Y+M+D+h+m+s;
}
//获取两个数中的随机数
function g_RandomNumber(start, end) {
	return Math.floor(Math.random() * (end - start) + start)
}
//表单提交校验
function g_checkForm(obj,isReload=true){
	obj.Validform({
		tiptype: function (msg, o, cssctl) {
			if (o.type == 3) {
				$(".savedata").attr('is_post',0);
				layer.msg(msg, {time: 800});
			}
		},
		postonce:true,
		ajaxPost: true,
		showAllError: false,
		callback: function (ret) {
			if (ret.code === 0) {
				$(".savedata").attr('is_post',0);
			}
			if(isReload){
				ajax_progress(ret);
			}else{
				if (ret.code === 0) {
					g_toast(ret.msg,{skin:'success'});
				}else{
					layer.alert(ret.msg, {title: "错误信息", icon: 2});
				}
			}

		}
	});

}

//ajax 数据请求
function g_ajax(url, type='post',data={}, setActive){
	$.ajax({
		type: type,
		url: url,
		data: data,
		dataType: "json",
		success: function(ret){
			if(ret.code==0){
				if(setActive===undefined){
					layer.msg('保存成功');return;
				}
				setActive(ret);
				return ret;
			}else{
				setActive(ret);

				layer.alert(ret.msg, {title: ret.msg, icon: 2});
			}
		}
	});
}

//将数组转换为树
function g_ltt(oldArr){
	oldArr.forEach(element => {
		let parentId = element.pid;
		if(parentId !== 0){
			oldArr.forEach(ele => {
				if(ele.id == parentId){
					if(!ele.inc){
						ele.inc = [];
					}
					ele.inc.push(element);
				}
			});
		}
	});
	oldArr = oldArr.filter(ele => ele.pid === 0);
	return oldArr;
}
//Gadmin帮助系统
function g_help(type){
	layer_show("系统帮助","/gadmin/sys/help?act="+type,"75","56");
}

//log调试
function g_log(log){
	console.log('%c Welcome to Use Gadmin %c 6.0 %c DeBug Log Time:' + g_GetCurrentDate('datetime'), 'background:#35495e ; padding: 1px; border-radius: 3px 0 0 3px;  color: #fff', 'background:#41b883 ; padding: 1px; border-radius: 0 3px 3px 0;  color: #fff','');
	console.log('---------STAR---------');
	console.log(log)
	console.log('---------END---------');
}

//构建二维码能力
function g_qrcode(obj,val,w=78,h=78){
	$("#"+obj).html("");
	new QRCode($("#"+obj)[0],{
		text: val, //内容
		width:w, //宽度
		height:h, //高度
		correctLevel: 3,//二维码纠错级别
		background: "#ffffff",//背景颜色
		foreground: "#000000"//二维码颜色
	})
}

//监听鼠标移入事件
$(".g_tips").mouseover(function(){
	var con = $(this).attr('data-tips');
	layer.tips(con, this,{
		tips: [1, '#3d3b3b']
	});
});
//监听鼠标移入事件
$(".g_tips").mouseout(function(){
	layer.close(layer.tips());
});