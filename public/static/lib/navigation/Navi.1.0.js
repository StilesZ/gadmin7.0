var Navi = {
	Init : function(processData) {
		_this.append('<input type="hidden" id="wf_active_id" value="0"/>');
		_this.append('<div id="wf_process_info"></div>');
		var aConnections = [];
		var setConnections = function(conn, remove) {
			if (!remove) aConnections.push(conn);
			else {
				var idx = -1;
				for (var i = 0; i < aConnections.length; i++) {
					if (aConnections[i] === conn) {
						idx = i; break;
					}
				}
				if (idx !== -1) aConnections.splice(idx, 1);
			}
			if (aConnections.length > 0) {
				var s = "";
				for ( var j = 0; j < aConnections.length; j++ ) {
					var from = $('#'+aConnections[j].sourceId).attr('process_id');
					var target = $('#'+aConnections[j].targetId).attr('process_id');
					s = s + "<input type='hidden' value=\"" + from + "," + target + "\">";
				}
				$('#wf_process_info').html(s);
			} else {
				$('#wf_process_info').html('');
			}
			//jsPlumb.repaintEverything();//重画 此处的重画貌似无必要
		};
		var initEndPoints = function(){
			$(".process-flag").each(function(i,e) {
				var p = $(e).parent();
				jsPlumb.makeSource($(e), {
					parent:p,
					anchor:"Continuous",
					endpoint:[ "Dot", { radius:1 } ],
					connector:[ "Flowchart", { stub:[5, 5] } ],
					connectorStyle:{lineWidth:2,strokeStyle:"#2d6dcc",joinstyle:"round"},
					hoverPaintStyle:{lineWidth:2,strokeStyle:"#0300FF"},
					dragOptions:{},
					maxConnections:-1
				});
			});
		}
		jsPlumb.importDefaults({
			DragOptions : { cursor: 'pointer'},
			EndpointStyle : { fillStyle:'#225588' },
			Endpoint : [ "Dot", {radius:1} ],
			ConnectionOverlays : [
				[ "Arrow", { location:1 } ],
				[ "Label", {location:0.1,id:"label",cssClass:"aLabel"}]
			],
			Anchor : 'Continuous',
			ConnectorZIndex:5,
			HoverPaintStyle:{lineWidth:2,strokeStyle:"#0300FF"}
		});
		console.log($.browser);
		if( 'undefined' == typeof(document.body.style.maxHeight)){ //ie9以下，用VML画图
			jsPlumb.setRenderMode(jsPlumb.VML);
		} else { //其他浏览器用SVG
			jsPlumb.setRenderMode(jsPlumb.SVG);
		}
		var lastProcessId=0;
		$.each(processData.list, function(i,row) {
			var nodeDiv = document.createElement('div');
			var nodeId = "window" + row.id;
			$(nodeDiv).attr("id",nodeId)
				.attr("style",row.style +'text-align: left;')
				.attr("process_to",row.process_to)
				.attr("process_id",row.id)
				.addClass("process-step wf_btn")
				.html('<span class="process-flag" style="text-align: left;"><img src="'+Navi.Ico()+'" width=18px alt=""></span>&nbsp;' +row.process_name + '<br/><img alt="" src="'+Navi.Ico(1)+'" width=18px>&nbsp;' +row.name + '' );
			_this.append(nodeDiv);
			lastProcessId = row.id;
		});
		//双击事件
		$('.process-step').mousedown(function(e){
			if(e.which===3){
				if(confirm("你确定删除步骤吗？")){
					var activeId = $(this).attr("process_id");//右键当前的ID
					$.post(Server_Url+'?act=del',{"flow_id":the_flow_id,"id":activeId},function(data){
						if(data.code===0){
							if(activeId>0){
								jsPlumb.detachAllConnections($("#window"+activeId));//删除相关连线
								$("#window"+activeId).remove();
								//这里要添加一个步骤：删除<div id='wf_process_info'>中对应的的input标签，否则保存的时候依然会将已删除的节点id保存进数据库
								$('#wf_process_info').find('input').each(function (i,e) {
									if($(e).attr('value').includes(activeId)){
										$(e).remove();
									};
								});
							}
							Navi.Api('save');
						}
						layer.msg(data.msg);
					},'json');
				}
			}
			return false;//阻止链接跳转
		})
		//单击事件
		$(".process-step").live('click',function(){
			//alert(111);
			//_this.find('#wf_active_id').val($(this).attr("process_id"));
			//Navi.DClick($(this).attr("process_id"))
		})
		jsPlumb.draggable(jsPlumb.getSelector(".process-step"),{containment: 'parent'});//允许拖动
		initEndPoints();
		jsPlumb.bind("jsPlumbConnection", function(info) {
			setConnections(info.connection)
		});
		//绑定删除connection事件
		jsPlumb.bind("jsPlumbConnectionDetached", function(info) {
			setConnections(info.connection, true);
		});
		//绑定删除确认操作
		jsPlumb.bind("click", function(c) {
			if(confirm("你确定取消连接吗?"))
				jsPlumb.detach(c);
		});
		jsPlumb.makeTarget(jsPlumb.getSelector(".process-step"), {
			dropOptions:{ hoverClass:"hover", activeClass:"active" },
			anchor:"Continuous",
			maxConnections:-1,
			endpoint:[ "Dot", { radius:1 } ],
			paintStyle:{ fillStyle:"#ec912a",radius:1 },
			hoverPaintStyle:{lineWidth:3,strokeStyle:"#da4f49"},
			beforeDrop:function(params){
				if(params.sourceId === params.targetId) return false;/*不能链接自己*/
				var j = 0;
				$('#wf_process_info').find('input').each(function(i){
					var str = $('#' + params.sourceId).attr('process_id') + ',' + $('#' + params.targetId).attr('process_id');
					if(str == $(this).val()){
						j++;
						return;
					}
				})
				if( j > 0 ){
					//defaults.fnRepeat();//无效函数，注释掉才能正常实现禁用重复链接功能
					return false;
				} else {
					return true;
				}
			}
		});
		$('.process-step').each(function(i){
			var sourceId = $(this).attr('process_id');
			var prcsto = $(this).attr('process_to');
			var toArr = prcsto.split(",");
			$.each(toArr,function(j,targetId){
				if(targetId!='' && targetId!=0){
					//检查 source 和 target是否存在
					var is_source = false,is_target = false;
					$.each(processData.list, function(i,row){
						if(row.id == sourceId){
							is_source = true;
						}else if(row.id == targetId){
							is_target = true;
						}
						if(is_source && is_target)
							return true;
					});
					if(is_source && is_target){
						jsPlumb.connect({
							source:"window"+sourceId,
							target:"window"+targetId,
						});
						return ;
					}
				}
			})

		});
	},
	show : function(processData) {
		_this.append('<div id="wf_process_info"></div>');
		var initEndPoints = function(){
			$(".process-flag").each(function(i,e) {
				var p = $(e).parent();
				jsPlumb.makeSource($(e), {
					parent:p,
					anchor:"Continuous",
					endpoint:[ "Dot", { radius:1 } ],
					connector:[ "Flowchart", { stub:[5, 5] } ],
					connectorStyle:{lineWidth:2,strokeStyle:"#2d6dcc",joinstyle:"round"},
					hoverPaintStyle:{lineWidth:3,strokeStyle:"#da4f49"},
					dragOptions:{},
					maxConnections:-1
				});
			});
		}
		jsPlumb.importDefaults({
			DragOptions : { cursor: 'pointer'},
			EndpointStyle : { fillStyle:'#225588' },
			Endpoint : [ "Dot", {radius:1} ],
			ConnectionOverlays : [
				[ "Arrow", { location:1 } ],
				[ "Label", {location:0.1,id:"label",cssClass:"aLabel"}]
			],
			Anchor : 'Continuous',
			ConnectorZIndex:5,
			HoverPaintStyle:{lineWidth:3,strokeStyle:"#da4f49"}
		});
		if( $.browser.msie && $.browser.version < '9.0' ){ //ie9以下，用VML画图
			jsPlumb.setRenderMode(jsPlumb.VML);
		} else { //其他浏览器用SVG
			jsPlumb.setRenderMode(jsPlumb.SVG);
		}
		//单击事件
		$(".process-step").live('click',function(){
			layer_show('新增'+$(this).attr("process_title"),$(this).attr("process_url"))
		})
		var lastProcessId=0;
		$.each(processData.list, function(i,row) {
			var nodeDiv = document.createElement('div');
			var nodeId = "window" + row.id;
			$(nodeDiv).attr("id",nodeId)
				.attr("style",row.style +'text-align: left;')
				.attr("process_to",row.process_to)
				.attr("process_id",row.id)
				.attr("process_title",row.name)
				.attr("process_url",row.url)
				.addClass("process-step wf_btn")
				.html('<span class="process-flag" style="text-align: left;"><img src="'+Navi.Ico()+'" width=18px></span>&nbsp;' +row.process_name + '<br/><img src="'+Navi.Ico(1)+'" width=18px>&nbsp;' +row.name + '' );
			_this.append(nodeDiv);
			lastProcessId = row.id;
		});

		initEndPoints();
		$('.process-step').each(function(i){
			var sourceId = $(this).attr('process_id');
			var prcsto = $(this).attr('process_to');
			var toArr = prcsto.split(",");
			$.each(toArr,function(j,targetId){
				if(targetId!='' && targetId!=0){
					//检查 source 和 target是否存在
					var is_source = false,is_target = false;
					$.each(processData.list, function(i,row){
						if(row.id == sourceId){
							is_source = true;
						}else if(row.id == targetId){
							is_target = true;
						}
						if(is_source && is_target)
							return true;
					});
					if(is_source && is_target){
						jsPlumb.connect({
							source:"window"+sourceId,
							target:"window"+targetId,
						});
						return ;
					}
				}
			})
		});
	},
	Api : function(Action) {
		var reload = false;
		switch(Action) {
			case 'save':
				var PostData = {"flow_id":the_flow_id,"process_info":Navi.GetJProcessData()};//获取到步骤信息
				break;
			case 'Refresh':
				location.reload();return;
				break;
		}
		var Url = Server_Url+'?act='+Action;
		Navi.sPost(Url,PostData,reload);
	},
	sPost : function(Post_Url,PostData,reload=true) {
		$.post(Post_Url,PostData,function(data){
			if(data.code==0){
				layer.msg(data.msg,{icon:1,time: 1500},function(){
					if(reload){
						location.reload();
					}
				});
			}else{
				layer.msg(data.msg, {
					time: 2000, //20s后自动关闭
				});

			}
		},'json');
	},
	GetJProcessData : function(){
		try{
			var aProcessData = {};
			$("#wf_process_info input[type=hidden]").each(function(i){
				var processVal = $(this).val().split(",");
				if(processVal.length==2)
				{
					if(!aProcessData[processVal[0]])
					{
						aProcessData[processVal[0]] = {"top":0,"left":0,"process_to":[]};
					}
					aProcessData[processVal[0]]["process_to"].push(processVal[1]);
				}
			})
			_this.find("div.process-step").each(function(i){ //生成Json字符串，发送到服务器解析
				if($(this).attr('id')){
					var pId = $(this).attr('process_id');
					var pLeft = parseInt($(this).css('left'));
					var pTop = parseInt($(this).css('top'));
					if(!aProcessData[pId])
					{
						aProcessData[pId] = {"top":0,"left":0,"process_to":[]};
					}
					aProcessData[pId]["top"] =pTop;
					aProcessData[pId]["left"] =pLeft;
				}
			})
			return JSON.stringify(aProcessData);
		}catch(e){
			return '';
		}
	},
	Ico : function(id=0) {
		var data = ['data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAQAAABKfvVzAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QA/4ePzL8AAAAHdElNRQfbBRIXBAD72CbZAAADCElEQVQ4y13US2xVZRSG4effe7elFGhpoVWgUArtQe5XkUg0BC8hkagQExPjAAIDB4wcOXFg4tihA2Vk1IEaBt5RxEaDYmtTqglIa0hpS1qIRWiF9pzu8zvoCRi/2cq6fMmbtVZQ0UGoVbDDPgUrBCMGdPklXtp751WZALJKKaxzxBOxPaSVuFOnA4bCmdmPs4szN09rskNWSdY57FjcVhOabUlXpU2BiTicX8jH1jR2HHp28puBN9PhkTkH1HnFidi0NjxVtTtbGKoqU0pVU7FvtikptDu+suHaib03SA/CS15LFj+SHqvZldWGVMkd0xLVasPqtCUJ8tDd9snohp5haYG1Xrd2T3K0ZkUaRcGfTjlvvmUiAnpnT8axpd/97Eai2svl7e1erG5Joku63HXHkCtuyZ3Tq4wHksXRNkfVZtY5UJPuz9pSBp1y3aQmqUTJDz5TJ7HViuTpqqFi8UkfZLbFjua4J4OoqKRLs5Kgx5S7MmUBu7LPi1fa7E7sC+nGtD6ZA/+C5WaMmBGNmdTokB1gYdicCvZlNtORVqHPNdE8iVjBGtQa94VGO2U6UiVbMi3UB+jRLUgl7mvUVbmCLTKLAlqy+8kFGiVm3b3nwHzVcosqezS3S+MW3orwqI1mnDPwH4elHrPAfNW4HTGe6GcwL6FVwd9GlAUBQTRuXLt2iVmDOfoTXTH/LZ/z+N3XpjTYoFai04Omfa9HxGS8kIvOJn4NA9fDTyVYbo0Gz9gpKFvvOc1WahPQPTvKkPOZS76cWXfGprQtbfa8CQX9FagbzFOtFSPl06VpybcupoXcSNh1c/mtcke6INRZIigJlum0RKN6/FX+sNhbDn3ecC0tMIE9ozXXy61JQwL1HrLJkgqp4fJ7Mz/m5X+85SvSywoMyOPm4drBnMYkC0mFe8lkPDf7frE3xtve9q7ip0Llpuscdjxur4kttqat6dLARLya9+VjpkO44B0fmeJ+A6x3xP64+t4TgLKrzjpZ4TDX8L8387DHdVolGHHZWd3+mJs9p38BQHkS326FepQAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTgtMDYtMjhUMjI6Mzg6MjQrMDg6MDCBECHGAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDExLTA1LTE4VDIzOjA0OjAwKzA4OjAwRsh68wAAAEN0RVh0c29mdHdhcmUAL3Vzci9sb2NhbC9pbWFnZW1hZ2ljay9zaGFyZS9kb2MvSW1hZ2VNYWdpY2stNy8vaW5kZXguaHRtbL21eQoAAAAYdEVYdFRodW1iOjpEb2N1bWVudDo6UGFnZXMAMaf/uy8AAAAXdEVYdFRodW1iOjpJbWFnZTo6SGVpZ2h0ADMyKPT49AAAABZ0RVh0VGh1bWI6OkltYWdlOjpXaWR0aAAzMtBbOHkAAAAZdEVYdFRodW1iOjpNaW1ldHlwZQBpbWFnZS9wbmc/slZOAAAAF3RFWHRUaHVtYjo6TVRpbWUAMTMwNTczMTA0MJzV/YIAAAARdEVYdFRodW1iOjpTaXplADEzMzhCRu5XfwAAAGB0RVh0VGh1bWI6OlVSSQBmaWxlOi8vL2hvbWUvd3d3cm9vdC9uZXdzaXRlL3d3dy5lYXN5aWNvbi5uZXQvY2RuLWltZy5lYXN5aWNvbi5jbi9zcmMvNTA0Ny81MDQ3NTMucG5nxUdQRAAAAABJRU5ErkJggg==','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAxCAYAAACcXioiAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QAAAAAAAD5Q7t/AAAACXBIWXMAAAD9AAAA/QCVfLMFAAAAB3RJTUUH4wEJAzcWevkOigAABRFJREFUaN7N2mmoVVUUB/DfUx9laaXNIRGVaVHUS3vSZJQRkUhzFg00CFGZUV8qkigriqBIKaPpQ0WQDahpHyKTRtMnZfNoijaYCmVmmaXPPqx95XA69/nuufe+14LLhXPu3vv/33udtf5rnduiwXbdxEld3n/k4WkNXa+lycBb0/c/zSJTN4Ec8L4YjhNxNPZL11bjU7yDj7GpUSTqIpADfziux1kYgFUJ+Fbskch0Yh4ewruNIFGaQAZ8C8bjXuyKF/AivsBvicAAHIxxuBT9cTcekdyrLIm+dYKHK/EwvsMEPIpl2JDAbcaf+AFv4HUMxdX4G+9ja3v7KIs7FtWMpV/ZE0h2Ku5HRwK/onIjv6MZ0p/hikR6Mr7GzLIAanahDJDBmI29cSa+rAa+YCzsjzniFMZiTVdjq1mfsszTosfgge6Ar9zL3F+JB3EETi8LoiyBVpwjfH12d8BXsdeE241V8nksS2BPHIkF+LlW8JnfrsGHIgQPYvuZvC4Cmcn3SAt+Wcv4AuvE8jTf4DITlDmBVlyI3bCeurPpqkRgctqUphMYgWswA6+WRZ0h/TymiWQ4uicIDMWOeAI/lSWQsbUic28RcqPpBLYI30XD5PFOQnKs6wkCa9P3XtQeNarYIWlTvu8JAivwO0ZWLpQhkRnTD2PwI77tCQIrReweJ+J3PdaKy0UmniPkd9MJ/IXHhAs9jQOp7RQyvx0tpMhHac6arWwmnoMbcJgQcmXtDCHmJmIpTRZzmck7hQT+SGiiXejeKWR+s7vw/Q5R/JSymk8gQ2K9iN8jcEKJtY/DMMyVauQyIbkeOQ2zxIM3ATvQ9Slk7rWK0nK1UKSlrZSEXdyxSHv7KPhVhMEJIgwugaLyMEfsPNyEqXiF8gmx3hOAp7AQd4gC5z+Ac+APw13C75+sd/HSBDI7tk4oyRZR0B9dBTi04XGhOicrWUY2hEDOFmIS9hXq8vgCEseKh/4g0T+a14iF6yKQ27mZuFiE1GsL5r5AFC2XCileNEfPEiiwb0Q/qMi2pE/NcqFpBHI+vjNuxRBR6HTmfj5XhM+7hasVzVGz1dMXytqhuB3nigf5FmwkXCSN6Suek3uwGLfhPVEHbLNaXapbBKqA3kn0O88UeWCgEGZTJTeqgMlJ54sxRfRLn8VzIqT+kV+gO2S6JFAAfKBop5ySPkeIDDw/gX+7sqNdtBalcTfi7IThE9E3nS/a7+u7S6SQQAHwIaJtPh5HiYbtF3hTNGs7RAO3ywVz87aKvHCGEHWHpxP6BC8LmbJse0RatrPIbrhMdCEOSLszS8TwrxREnO4ce8EGDRDCboxwyTbRMHtSZPrV1eZv6WLio3AfThYvI6aLY15XBnQ3iRB55ETRfj8NH+BmVV6ItFSZ7CSR8ncVYe8ZNfhlg8j0x/lCY+0gip5tbfjK+kXvB0aKY9ssfP6tZgLPz5shsjFt3OdpM6fjlzyeltygwUKvDE/gG/Ieq4zlTqQt4VorgsnqCqZ8Jr5ICLEpvQm+YM0luDMRuSR7I0tgkGhxLBaKstfAV1l7pmjnXyLq6SCQOaoRotiYId4u9ir4AhIb8JLo4rURbpY9gRHireKC3gbdhS1KGNsqF7JRaJjo1S+vXGhQ37ORtkJUcUPzBPoInbMLrhLdt4b9j6JBtlXkhoFCIfRBZ5bAUuwjep7/N/BZEkuFRuqbJbBZZLxGV2jNsk7pLwrZZ2BTubl61/4FsR6JKpzlaNkAAAAldEVYdGRhdGU6Y3JlYXRlADIwMTktMDEtMDlUMDM6NTU6MjIrMDg6MDDkMab6AAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE5LTAxLTA5VDAzOjU1OjIyKzA4OjAwlWweRgAAAEN0RVh0c29mdHdhcmUAL3Vzci9sb2NhbC9pbWFnZW1hZ2ljay9zaGFyZS9kb2MvSW1hZ2VNYWdpY2stNy8vaW5kZXguaHRtbL21eQoAAABTdEVYdHN2Zzpjb21tZW50ACBHZW5lcmF0b3I6IFNrZXRjaCAzLjAuMyAoNzg5MSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggbh/q1QAAABB0RVh0c3ZnOnRpdGxlAFBlcnNvbjwcGLsAAAAYdEVYdFRodW1iOjpEb2N1bWVudDo6UGFnZXMAMaf/uy8AAAAYdEVYdFRodW1iOjpJbWFnZTo6SGVpZ2h0ADI0NvDYdVgAAAAXdEVYdFRodW1iOjpJbWFnZTo6V2lkdGgAMjQx/U2wpgAAABl0RVh0VGh1bWI6Ok1pbWV0eXBlAGltYWdlL3BuZz+yVk4AAAAXdEVYdFRodW1iOjpNVGltZQAxNTQ2OTc3MzIyCp5w8AAAABF0RVh0VGh1bWI6OlNpemUANjg1N0Kg2mwTAAAAYnRFWHRUaHVtYjo6VVJJAGZpbGU6Ly8vaG9tZS93d3dyb290L25ld3NpdGUvd3d3LmVhc3lpY29uLm5ldC9jZG4taW1nLmVhc3lpY29uLmNuL2ZpbGVzLzExOS8xMTk4NTgxLnBuZ+cNqJ8AAAAASUVORK5CYII='];
		return data[id];
	}
}