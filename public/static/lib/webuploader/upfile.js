var upfileGrid;
var state = 'pending';
var initfilesize=0;
var md5value="";
var isUpFile=false;//判断是否有文件上传成功，来提示dialog进行下部操作
$(function(){

	upfileGrid = $("#upfileGrid").DataTable({
		data: dataJson,
		ordering: false,
		searching : false,
		idField: 'fileId',
		paging: false,
		info: false,
		columns:[
			{ title: 'fileId', data: 'fileId',hidden:true,width:100},
			{ title: '文件名称',data: 'fileName',width: 230,fixed:true},
			{ title: '文件大小',data: 'fileSize',width: 80,fixed:true},
			{ title: '上传进度',data: 'progress',width: 180,fixed:true,formatter: function (value, rec){
					var htmlstr = '<div class="easyui-progressbar progressbar" style="width: 170px; height: 20px;" value="'+value +'" text="'+value+'%">'+
						'<div class="progressbar-text" style="width: 170px; height: 20px; line-height: 20px;">'+value+'%</div>'+
						'<div class="progressbar-value" style="width: '+value+'%; height: 20px; line-height: 20px;">'+
						'<div class="progressbar-text" style="width: 170px; height: 20px; line-height: 20px;">'+value+'%</div>'+
						'</div>'+
						'</div>';
					return htmlstr;
				}},
			{ title: '上传状态',data: 'fileState',width: 120,fixed:true},
		]
	});

	$('#upfileGrid tbody').on('click', 'tr', function () {
		if ($(this).hasClass('selected') ) {
			$(this).removeClass('selected');
		} else {
			upfileGrid.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');
		}
	});

	uploader = WebUploader.create({
		// 不压缩image
		resize: false,
		// swf文件路径
		swf:  '/0.1.5/Uploader.swf',
		// 默认文件接收服务端。
		server: '/gadmin/common/uploding',
		// 选择文件的按钮。可选。
		// 内部根据当前运行是创建，可能是input元素，也可能是flash.
		pick: '#chooseFile',
		fileSingleSizeLimit:100*1024*1024,//单个文件大小
		accept:[{
			title: 'file',
			extensions: 'doc,docx,pdf,xls,xlsx,ppt,pptx,gif,jpg,jpeg,bmp,png,rar,zip,ebidx,edox',
			mimeTypes: '.doc,.docx,.pdf,.xls,.xlsx,.ppt,.pptx,.gif,.jpg,.jpeg,.bmp,.png,.rar,.zip,.ebidx,.edox'
		}]
	});

	// 当有文件添加进来的时候 tim
	uploader.on( 'fileQueued', function( file ) {
		var fileSize = Math.trunc(file.size/1024)+'Kb';
		var row =  [{fileId:file.id,fileName:file.name,fileSize:fileSize,progress:0,fileState:"等待上传"}];
		upfileGrid.rows.add(row).draw();
	});

	// 文件上传过程中创建进度条实时显示。
	uploader.on( 'uploadProgress', function( file, percentage ) {
		var tbl = $("#upfileGrid").dataTable();
		var nTrs = tbl.fnGetNodes();
		for(var i = 0; i < nTrs.length; i++){
			if(upfileGrid.row( nTrs[i] ).data().fileId==file.id){
				var d = upfileGrid.row( nTrs[i] ).data();
				d.progress = (percentage * 100).toFixed(2);
				upfileGrid.row( nTrs[i] ).data( d ).draw();
			}
		}
	});

	//文件上传成功
	uploader.on( 'uploadSuccess', function( file,response ) {
		if(response.code==0){
			var tbl = $("#upfileGrid").dataTable();
			var nTrs = tbl.fnGetNodes();
			for(var i = 0; i < nTrs.length; i++){
				if(upfileGrid.row( nTrs[i] ).data().fileId==file.id){
					var d = upfileGrid.row( nTrs[i] ).data();
					d.fileState = '上传成功<input type="hidden" name="ids" value='+response.data+'> <h3><i class="Hui-iconfont Hui-iconfont-del3" onclick=delfile('+response.data+')></i></h3>';
					upfileGrid.row( nTrs[i] ).data( d ).draw();
				}
			}
			$("#chooseFile,#removeUpFile,#removeUpFile,#addUpFile,#clup").css({"display":"none"});
			layer.msg('上传成功！请点击完成！');
			isUpFile=true;
		}else{
			layer.alert(response.data);
		}
	});
	//文件上传失败
	uploader.on( 'uploadError', function( file ) {
		var tbl = $("#upfileGrid").dataTable();
		var nTrs = tbl.fnGetNodes();
		for(var i = 0; i < nTrs.length; i++){
			if(upfileGrid.row( nTrs[i] ).data().fileId==file.id){
				var d = upfileGrid.row( nTrs[i] ).data();
				d.fileState = '上传失败';
				upfileGrid.row( nTrs[i] ).data( d ).draw();
			}
		}
	});

	uploader.on( 'uploadComplete', function( file ) {

	});

	uploader.on('uploadFinished',function(){//成功后

	});

	uploader.on('error', function(handler){
		if(handler=='F_EXCEED_SIZE'){
			layer.msg('error','上传的单个文件不能大于'+initfilesize+'。<br>操作无法进行,如有需求请联系管理员');

		}else if(handler=='Q_TYPE_DENIED'){
			layer.msg('不允许上传此类文件!。<br>操作无法进行,如有需求请联系管理员');
		}
	});
});//$(function(){})结束

/*从队列中移除文件*/
function removeFile(fileId){
	var tbl = $("#upfileGrid").dataTable();
	var nTrs = tbl.fnGetNodes();
	for(var i = 0; i < nTrs.length; i++){
		if($(nTrs[i]).hasClass('selected')){
			upfileGrid.row(nTrs[i]).remove().draw();
		}else{
			layer.msg('没有选中要移除的文件!');
		}
	}
}

function uploadToServer(){
	if(uploader.getFiles().length<=0){
		layer.msg('没有上传的文件!');
		return;
	}
	if ( state === 'uploading' ) {
		uploader.stop();
	}
	else {
		uploader.upload();
	}
}

//初始化上传参数
function initUpLoad(args){
	var opts={};
	if(args){
		if(args.url!=null&&args.url!=""){
			opts.server=args.url;
		}
		if(args.size!=null&&args.size!=""){
			initfilesize=args.size;
			opts.fileSingleSizeLimit=args.size;
		}
		if(args.args!=null&&args.args!=""){
			opts.formData=args.args;
		}
		if(opts){
			$.extend(uploader.options,opts);
		}
	}
}

function delfile(id){
	$.ajax({
		url:'/gadmin/common/filedel?id='+id,
		type:'post',
		dataType:'json',
		success:function(ret) {
			if(ret.code==0){
				removeFile(id);
				layer.msg('操作成功！！');
			}else{
				layer.alert(ret.msg, {title: "错误信息", icon: 2});
			}
		},
		error : function() {
			layer.alert('请求出错！', {title: "错误信息", icon: 2});
		}
	});
}

function callback(){
	var ids =[];
	$("input[name='ids']").each(function(){
		ids.push($(this).val());
	})
	if(ids.length<=0){
		layer.msg('您没有上传附件！');
		return;
	}
	var data = ids.join(",");
	var linkLoc = parent.$('#label_'+callbackId).attr("onclick");
	var array = linkLoc.split('&value=');
	var news = array[0] + "&value=" + data + '",{w:"90%",h:"60%"})';
	parent.$('#label_'+callbackId).attr('onclick',news);
	parent.$('#'+callbackId).val(data);
	var index = parent.layer.getFrameIndex(window.name);
	layer.msg('恭喜，上传成功！');
	setTimeout(function(){parent.layer.close(index)}, 1000);
}

function callclose(){
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);;
}

function getSuccess(){
	return isUpFile;
}