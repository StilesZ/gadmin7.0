function edit(sid,wh){
    var Param =  getParam();
    if(!Param){
        return;
    }

    var status = Param[1];
    if(status==1 || status==2 ){
        layer.msg('流程审批中，或者已被审批通过~~');
        return  false;
    }
    sfdp.openpage("修改",System_Url+"edit?sid="+sid +'&id='+Param[0],wh);
}
function status_ok(sid,st,msg){
    var Param =  getParam();
    if(!Param){
        return;
    }
    var status = Param[1];
    var table = Param[2];
    if(status==st){
        layer.msg('该单据已被'+ msg);
        return  false;
    }
    sfdp.Askshow(System_Url+"status?sid="+sid +'&id='+Param[0]+'&table='+table+'&status='+st,"您确定要执行【"+msg+"】吗?该操作无法撤销！")
}
/*删除操作*/
function del(sid){
    var Param =  getParam();
    if(!Param){
        return;
    }
    var status = Param[1];
    var table = Param[2];
    if(status==1 || status==2 ){
        layer.msg('流程审批中，或者已被审批通过~~');
        return  false;
    }
    sfdp.Askshow(System_Url+"del?sid="+sid +'&id='+Param[0]+'&table='+table,"删除须谨慎，确认要删除吗?")
}
/*超级组件回调事件*/
function ent_select(obj){
    var Param =  getParam();
    if(!Param){
        return;
    }
    var rowData = JSON.parse(localStorage.getItem("suphelpselect"))
    parent.$('#'+obj).val(Param[0]).trigger("change");
    try{
        parent.load_suphelp_fun(obj,rowData,Param);
    }catch(e){
        g_log('page no load end fun ~');
    }
    layer_close();
}