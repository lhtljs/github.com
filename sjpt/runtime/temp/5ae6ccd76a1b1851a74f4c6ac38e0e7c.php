<?php /*a:1:{s:65:"D:\wamp64\www\sjpt\application\index\view\index\index\xmzhxx.html";i:1571361752;}*/ ?>
<title>综合信息详情</title>
<link rel="stylesheet" href="/static/index//css/synthesisTable.css"/>
<style>
    .xg {
        overflow: hidden;
    }

</style>
<script type="text/javascript">
    $(function () {

//        console.log(xmzhxx);


//        综合信息获取资金到位总计、资金支付总计（已完成）
        $.post('/index/underway/get_zjdwdata', {kid: xmzhxx.kid}, function (data) {
            var zjdwzj = JSON.parse(data);
            if (zjdwzj) {
                $('#inPlace').val(zjdwzj.inPlaces);
                $('#pay').val(zjdwzj.pays);
            }
        });

//        获取审批进度状态，（已完成）
        get_spjd_state();

//        获取项目投资总数和显示项目基本信息（已完成）
        $('#sum').val(xmzhxx.pro_cost);
        $('#basicForm').form("load", convertJsonToForm(xmzhxx));


//        获取项目现场审计图片（已完成）
        $('#picture').click(function () {
            $.post('/index/underway/get_picdata', {kid: xmzhxx.kid}, function (data) {
                var iscz = JSON.parse(data);
                if (iscz == '') {
                    $.messager.alert('提示', '还没有添加现场审计图片数据！');
                    return false;
                }
                else {
                    $('#picdiv').window({
                        title: '图片资料',
                        width: 800,
                        height: 500,
                        maximized: true,
                        left: 0,
                        top: 0,
                        href: '/index/underway/show_index_pic',
                        resizable: false,
                        modal: true,
                        closed: false
                    });
                }
            });
        });

//      远程视频。（已完成）
        $('#video').click(function () {
            $.post('/index/index/get_camVideo', {kid: xmzhxx.kid}, function (data) {
                var camVideo = JSON.parse(data);

                if (xmzhxx.pro_name == "阳泉市养老公益苑建设项目" || xmzhxx.pro_name == "阳泉市全国二青会射击射箭比赛场馆建设工程") {
                    if (camVideo) {
                        if (xmzhxx.pro_name == "阳泉市养老公益苑建设项目") {
                            $('#videodiv').window({
                                title: '远程视频',
                                width: 800,
                                height: 500,
                                maximized: true,
                                left: 0,
                                top: 0,
                                href: '/index/underway/remote_video',
                                resizable: false,
                                modal: true,
                                closed: false,
                                onClose: function () {
                                    //parentdataobj = undefined;
                                }
                            });
                        } else {
                            $('#videodiv').window({
                                title: '远程视频',
                                width: 800,
                                height: 500,
                                maximized: true,
                                left: 0,
                                top: 0,
                                href: '/index/underway/remote_video_eqh',
                                resizable: false,
                                modal: true,
                                closed: false,
                                onClose: function () {
                                    //parentdataobj = undefined;
                                }
                            });
                        }

                    }
                } else {
                    if (camVideo) {
                        var Ruser = camVideo.video_user;
                        var Rpass = camVideo.video_pass;
                        var Rurl = camVideo.video_ip;

                        var RemoteUrl = Rurl + "/dispatch.asp?user=" + Ruser + "&pass=" + Rpass + "&page=preview.asp[&slice=9&open={1,2,3,4,5,6,7,8}]"
                        var content = '<iframe src= ' + RemoteUrl + ' width="100%" height="99%" frameborder="0" scrolling="no"></iframe>';
                        if (Rurl == null) {
                            $.messager.alert('提示', '还没有填写远程视频地址！');
                            return false;
                        }
                        if (camVideo.video_ip.indexOf("http") >= 0) {
                            $('#videodiv').window({
                                title: '远程视频',
                                width: 800,
                                height: 500,
                                maximized: true,
                                left: 0,
                                top: 0,
                                content: content,
                                resizable: false,
                                modal: true,
                                closed: false,
                                onClose: function () {
                                    //parentdataobj = undefined;
                                }
                            });
                        }
                        else {
                            $.messager.alert('提示', '还没有填写远程视频地址或地址格式不正确！');
                            return false;
                        }
                    }
                }

            });
        });


//        获取3D图片，(已完成)
        $('#model').click(function () {
            $.post('/index/underway/get_3Ddata', {kid: xmzhxx.kid}, function (data) {
                var isin = JSON.parse(data);
//                console.log(isin.length)
                if (isin.length < 1) {
                    $.messager.alert('提示', '还没有上传项目3D效果图！');
                    return false;
                }
                else {
                    $('#model').click(function () {
                        $('#3ddiv').dialog({
                            title: '3D模型',
                            width: 1000,
                            height: 700,
                            href: '/index/underway/show_index_3D',
                            resizable: false,
                            modal: true,
                            closed: false,
                            onClose: function () {
                                //parentdataobj = undefined;
                            }
                        });
                    });
                }
            });
        });

//      审计正式文书 ，(已完成)
        $('#writs').datagrid({
            url: '/index/underway/get_writlist?project_kid=' + xmzhxx.kid,
            pageSize: 5,
            pageList: [5, 10, 15, 20],
            pagination: true,
            rownumbers: true,
            columns: [[
                {field: 'num', title: '文书编号', width: 120},
                {field: 'writ_type', title: '文书类型', width: 100},
                {
                    field: 'files', title: '审计文书', width: 716, formatter: function (index, rowdata) {
                    var r_data = [];
                    $.each(rowdata.fil, function (i, v) {
                        if ((rowdata.writ_type == v.type) && (rowdata.id == v.nkid)) {
                            r_data[i] = "<a target='_blank' href='" + v.files_add + "'>" + v.files_old_name + "</a>" + '<br>';
                        }
                    });
                    return r_data.join('');
                }
                },
                {field: 'createtime', title: '上传时间', width: 180}
            ]]
        });
    });

    //    获取审批进度，(已完成)
    function get_spjd_state() {
        $.post('/index/underway/spjd_state', {kid: xmzhxx.kid}, function (respon) {
            var state = JSON.parse(respon);
            //项目准备，大图标
            switch (state.xmzb) {
                case 1:
                    $('#xmzb').removeClass().addClass('divUnderway');
                    break;
                case 2:
                    $('#xmzb').removeClass().addClass('divAccomplish');
                    break;
                default :
//                    console.log('xmzb_state')
            }
            //可研阶段，大图标
            switch (state.kyjd) {
                case 0:
                    $('#kyjd').removeClass().addClass('divNoUnderway');
                    break;
                case 1:
                    $('#kyjd').removeClass().addClass('divUnderway');
                    break;
                case 2:
                    $('#kyjd').removeClass().addClass('divAccomplish');
                    break;
                default :
//                    console.log('kyjd_state')
            }
            //初步设计，大图标
            switch (state.cbsj) {
                case 0:
                    $('#cbsj').removeClass().addClass('divNoUnderway');
                    break;
                case 1:
                    $('#cbsj').removeClass().addClass('divUnderway');
                    break;
                case 2:
                    $('#cbsj').removeClass().addClass('divAccomplish');
                    break;
                default :
//                    console.log('cbsj_state')
            }
            //施工许可，大图标
            switch (state.sgxk) {
                case 0:
                    $('#sgxk').removeClass().addClass('divNoUnderway');
                    break;
                case 1:
                    $('#sgxk').removeClass().addClass('divUnderway');
                    break;
                case 2:
                    $('#sgxk').removeClass().addClass('divAccomplish');
                    break;
                default :
//                    console.log('sgxk_state')
            }
            //工程施工，大图标
            switch (state.gcsg) {
                case 0:
                    $('#gcsg').removeClass().addClass('divNoUnderway');
                    break;
                case 1:
                    $('#gcsg').removeClass().addClass('divUnderway');
                    break;
                case 2:
                    $('#gcsg').removeClass().addClass('divAccomplish');
                    break;
                default :
//                    console.log('gcsg_state')
            }
            //竣工验收，大图标
            switch (state.jgys) {
                case 0:
                    $('#jgys').removeClass().addClass('divNoUnderway');
                    break;
                case 1:
                    $('#jgys').removeClass().addClass('divUnderway');
                    break;
                case 2:
                    $('#jgys').removeClass().addClass('divAccomplish');
                    break;
                default :
//                    console.log('jgys_state')
            }

        });
    }
</script>

<div class="padding" title="综合信息详情">
    <!-- S 项目基本信息 -->
    <div id="basic" class="easyui-panel marginBottom" title="项目基本信息" data-options="fitColumns:true">
        <form id="basicForm">
            <table class="table center">
                <tr class="bolder">
                    <td>项目名称</td>
                    <td>项目编号</td>
                    <td>建设单位</td>
                    <td>项目性质</td>
                    <td>项目投资额（万元）</td>
                    <td>建设工期</td>

                </tr>
                <tr class="inputCenter">
                    <td><input id="itemsName" name="pro_name" class="inputHide width300" type="text" readonly/></td>
                    <td><input id="itemsNumber" name="aduit_num" class="inputHide width180" type="text" readonly/></td>
                    <td><input id="unit" name="dw_name" class="inputHide width240" type="text" readonly/></td>
                    <td><input id="property" name="pro_category" class="inputHide width120" type="text" readonly/></td>
                    <td><input id="invest" name="pro_cost" class="inputHide width120" type="text" readonly/></td>
                    <td><input id="duration" name="pro_time" class="inputHide width80" type="text" readonly/></td>
                </tr>
            </table>
        </form>
    </div>
    <!-- E 项目基本信息 -->
    <!-- S 审批阶段进度 -->
    <div id="examineStage" class="easyui-panel marginBottom" title="审批阶段进度" data-options="fitColumns:true">
        <table>
            <tr>
                <td>
                    <div id="xmzb" class="divUnderway">项目准备阶段</div>
                </td>
                <td>
                    <div id="kyjd" class="divNoUnderway">项目可研阶段</div>
                </td>
                <td>
                    <div id="cbsj" class="divNoUnderway">初步设计阶段</div>
                </td>
                <td>
                    <div id="sgxk" class="divNoUnderway">施工许可阶段</div>
                </td>
                <td>
                    <div id="gcsg" class="divNoUnderway">工程施工阶段</div>
                </td>
                <td>
                    <div id="jgys" class="divNoUnderway">竣工验收阶段</div>
                </td>
            </tr>
        </table>
    </div>
    <!-- E 审批阶段进度 -->
    <!-- S 工程形象进度 -->
    <div id="visualize" class="easyui-panel marginBottom" title="工程形象进度" data-options="fitColumns:true">
        <form id="visualizeForm">
            <table class="table center">
                <tr>
                    <td>图片资料</td>
                    <td>远程视频</td>
                    <td>3D模型</td>
                </tr>
                <tr>
                    <td><a href="#" id="picture"><img class="img" src="/static/index//img/yst.png"></a></td>
                    <td><a href="#" id="video"><img class="img" src="/static/index//img/sxt.png"></a></td>
                    <td><a href="#" id="model"><img class="img" src="/static/index//img/3D.png"></a></td>
                </tr>
            </table>
            <div id="picdiv"></div>
            <div id="videodiv"></div>
            <div id="3ddiv"></div>
        </form>
    </div>
    <!-- E 工程形象进度 -->
    <!-- S 资金管理进度 -->
    <div id="fund" class="easyui-panel marginBottom" title="资金管理进度" data-options="fitColumns:true">
        <form id="fundForm">
            <table class="table center">
                <tr>
                    <td>项目总投资额（万元）</td>
                    <td>当前资金到位（元）</td>
                    <td>当前资金支付（元）</td>
                </tr>
                <tr>
                    <td><input id="sum" name="sum" class="inputHide width98 center" type="text" disabled/></td>
                    <td><input id="inPlace" name="inPlace" class="inputHide width98 center" type="text" disabled/></td>
                    <td><input id="pay" name="pay" class="inputHide width98 center" type="text" disabled/></td>
                </tr>
            </table>
        </form>
    </div>
    <!-- E 资金管理进度 -->
    <!-- S 已发布的审计文书11111111111111111111 -->
    <div id="writ" class="easyui-panel" title="已发布的审计文书" data-options="fitColumns:true,height:212">
        <div id="writs"></div>
    </div>
    <!-- E 已发布的审计文书 -->
</div>
