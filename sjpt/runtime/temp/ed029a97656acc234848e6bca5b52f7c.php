<?php /*a:2:{s:63:"D:\wamp64\www\sjpt\application\index\view\index\index\home.html";i:1571361752;s:58:"D:\wamp64\www\sjpt\application\index\view\public\head.html";i:1571361752;}*/ ?>
<!DOCTYPE html>
<link href="/static/index//img/logo.ico" rel="shortcut icon">
<html lang="en">
<head>
    <title>阳泉市政府投资项目审计监督平台</title>
    <meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<script type="text/javascript" src="/static/index//js/easyui/jquery.min.js"></script>
<script type="text/javascript" src="/static/index//js/easyui/jquery.easyui.min.js"></script>
<link rel="stylesheet" type="text/css" href="/static/index//js/easyui/themes/default/easyui.css"/>
<link rel="stylesheet" type="text/css" href="/static/index//js/easyui/themes/icon.css"/>
<script type="text/javascript" src="/static/index//js/easyui/locale/easyui-lang-zh_CN.js"></script>
<script type="text/javascript" src="/static/index//js/jquery/jquery.form.js"></script>
<script type="text/javascript" src="/static/index//js/jquery/jquery.validate.js"></script>
<script type="text/javascript" src="/static/index//js/jquery/jquery.easyui.validatebox.js"></script>
<script type="text/javascript" src="/static/index//js/jquery/jquery.json.min.js"></script>
<script type="text/javascript" src="/static/index//js/jquery/jquery.serializejson.js"></script>

<!--下面的JS是公用JS，调用数据在HTML5里批量赋值-->
<script type="text/javascript" src="/static/index//js/easyui/public.js"></script>
<script type="text/javascript" src="/static/index//js/common/gpy.js"></script>
<link rel="stylesheet" href="/static/index//css/public.css">

<script>
    var ThinkPHP = {
        'MODULE': '/index',
//        'PUBLIC':'__CONTROLLER__',
        'ORGNAME': '<?php echo htmlentities(app('session')->get('user.orgname')); ?>',//当前使用者类型，审计局，建设单位，市领导，审批单位
        'SESSION': '<?php echo htmlentities(app('session')->get('user.fullname')); ?>',//当前使用者的中文姓名
        'DEPART': '<?php echo htmlentities(app('session')->get('user.depart')); ?>', //当前使用者部门
        'USERID': '<?php echo htmlentities(app('session')->get('user.id')); ?>',
        'POSITION': '<?php echo htmlentities(app('session')->get('user.position')); ?>', //当前使用者职务
        'UPLOADS': '/static/uploads/',
        'DFPDF': '/static/pdf/',
        'IMG': '/static/index//img/',
        'USER': '<?php echo htmlentities(app('session')->get('user.user')); ?>',
        'UID': '<?php echo htmlentities(app('session')->get('user.uid')); ?>'
    };

</script>
    <script type="text/javascript" src="/static/index//js/common/addtabs.js"></script>
    <link rel="stylesheet" href="/static/index//css/index.css">
    <link rel="stylesheet" href="/static/index//css/frame.css">

    <!--先不要加入js，完全修复好了再载入进来-->
    <script type="text/javascript" src="/static/index//js/home/home.js"></script>

    <script>
        $(document).ready(function () {
            var d = new Date();
            var vYear = d.getFullYear();
            var vMon = d.getMonth() + 1;
            var vDay = d.getDate();
            var h = d.getHours();
            var m = d.getMinutes();
            var se = d.getSeconds();
            a = vYear + "-" + (vMon < 10 ? "0" + vMon : vMon) + "-" + (vDay < 10 ? "0" + vDay : vDay) + " " + (h < 10 ? "0" + h : h) + ":" + (m < 10 ? "0" + m : m) + ":" + (se < 10 ? "0" + se : se) + " ";
            $('.time').text(a);

            //显示用户手册
            var orgname = '<?php echo htmlentities(app('session')->get('user.orgname')); ?>';

            if (orgname == '建设单位') {
                $('.yhsc').attr('href', '/static/index//pdf/explain/建设单位操作手册.doc');
            } else {
                $('.yhsc').attr('href', '/static/index//pdf/explain/审计用户操作手册.doc');
            }


        });
    </script>
    <style>
        .inputHide {
            border: none;
            background: none;
        }
    </style>
</head>
<body class="easyui-layout">
<!-- S header logo -->
<div region="north" split="true" class="header">
    <a class="yhsc" href="#">《用户手册》</a>
    <a class="easyui-menubutton" data-options="menu:'#messages',iconCls:'icon-expert'" id="message"><span><?php echo htmlentities(app('session')->get('user.fullname')); ?></span>
        <span class="time"></span></a>
    <div id="messages" style="width:120px">
        <div id="mm" data-options="iconCls:'icon-edit'" onclick="alterPassword()">修改密码</div>
        <div id="logout" data-options="iconCls:'icon-undo'">注 销</div>
    </div>
</div>
<div id="changePassword" class="easyui-dialog" title="修改密码" data-options="closed:true,iconCls:'icon-tip'">
    <form id="editpwd_form">
        <table class="table paddingButton">
            <tr>
                <td class="bolder right width120">原密码：</td>
                <td class="borderLeft">
                    <input id="oldpwd" name="oldpwd" class="easyui-textbox width150" type="text"
                           data-options="required:true"/>
                </td>
            </tr>
            <tr>
                <td class="bolder right">新密码：</td>
                <td class="borderLeft">
                    <input id="newpwd" name="newpwd" type="password" class="easyui-textbox width150"
                           data-options="required:true,validType:'length[6,18]'">
                </td>
            </tr>
            <tr>
                <td class="bolder right">确认密码：</td>
                <td class="borderLeft">
                    <input id="newpwd1" type="password" class="easyui-textbox width150"
                           data-options="required:true"
                           validType="equalsTo['#newpwd']"
                           invalidMessage="两次密码必须一样"
                           name="newpwd1">
                </td>
            </tr>
        </table>
    </form>
    <div class="center">
        <a id="pswsave" class="easyui-linkbutton" iconCls="icon-ok">保 存</a>
        <a class="easyui-linkbutton" onclick="$('#changePassword').dialog('close')" iconCls="icon-cancel">关 闭</a>
    </div>
</div>
<!-- E header logo  -->

<!-- S navigation 左侧导航 -->
<div id="navigation" region="west" split="true" title="导航菜单" class="navigation" iconCls="icon-home">
    <div id="acchome" class="easyui-accordion" data-options="multiple:true"></div>
</div>
<!-- E navigation 左侧导航 -->

<!-- S frame 主页 -->
<div data-options="region:'center'">
    <div id="tabs" class="easyui-tabs" data-options="fit:true">
        <div class="sybj" data-options="title:' 首 页 '">
            <!--<iframe id="framemain" class="frame" src="frame.html" width="100%" height="99.6%" frameborder="0" name="content"></iframe>-->
            <div class="map">
                <iframe src="<?php echo url('index/showgis'); ?>" width="100%" height="100%" frameborder="0"></iframe>
            </div>
            <div id="sumdiv" class="system hide">
                <div class="countpanel  col-lg-3 inline margin-left border-radius shadow-below border-blue whiteBG text-color-blue">
                    <div class="information blueBG shadow-bottom radius-top align-right">
                        <p id="tzzs" class="font-size70 font-family margin-right"></p>
                    </div>
                    <div class="line-height font-size14 margin-left">投资项目总数</div>
                </div>
                <div class=" countpanel col-lg-3 inline margin-left border-radius shadow-below border-green whiteBG text-color-green">
                    <div class="information greenBG shadow-bottom radius-top align-right">
                        <p id="gzzs" class="font-size70 font-family text-color margin-right"></p>
                    </div>
                    <div class="line-height font-size14 text-color margin-left">进行审计跟踪</div>
                </div>
                <div class=" countpanel col-lg-3 inline margin-left border-radius shadow-below border-blue whiteBG text-color-blue">
                    <div class="information blueBG shadow-bottom radius-top align-right">
                        <p id="wgzzs" class="font-size70 font-family margin-right"></p>
                    </div>
                    <div class="line-height font-size14 text-color margin-left">未进行审计跟踪</div>
                </div>
                <div class=" countpanel col-lg-3 inline margin-left border-radius shadow-below border-green whiteBG text-color-green">
                    <div class="information greenBG shadow-bottom radius-top align-right">
                        <p id="wssl" class="font-size70 font-family text-color margin-right"></p>
                    </div>
                    <div class="line-height font-size14 text-color margin-left">已发布审计文书</div>
                </div>
            </div>
            <!-- 工作通知 -->
            <div class="left topDiv">
                <div id="gztz"></div>
            </div>
            <!-- 综合信息-->
            <div class="left leftDiv inline">
                <div id="inform"></div>
            </div>
            <!-- 待办事项-->
            <div class="rightDiv inline">
                <div id="backlog"></div>
                <div id="double_form" class="easyui-dialog padding"
                     data-options="width:1100,height:645,closed:true"></div>
                <div id="double_review" class="easyui-dialog padding"
                     data-options="width:1100,height:600,closed:true"></div>
                <div id="review_back" class="easyui-dialog padding"
                     data-options="width:1100,height:500,closed:true"></div>
                <div id="writ_file_send" class="easyui-dialog padding"
                     data-options="width:500,height:300,closed:true"></div>
                <div id="add_pro" class="easyui-dialog" data-options="width:1180,height:730,closed:true"></div>
                <div id="spjd_do" class="easyui-dialog" data-options="width:500,height:210,closed:true"></div>
                <div id="jues_do" class="easyui-dialog" data-options="width:800,height:535,closed:true"></div>
                <div id="jues_back" class="easyui-dialog" data-options="width:500,height:200,closed:true"></div>
                <div id="ydgl" class="easyui-dialog padding" data-options="width:1100,height:650,closed:true"></div>
            </div>
        </div>
    </div>
</div>
<div id="xmzhxx"></div>
<div id="gztzread"></div>

<!-- E frame 主页 -->

<!-- S footer 底部 -->
<div region="south" split="true" class="footer">
    版权所有：阳泉市审计局 (C)2015-2016 All Rights Reserved<br/>
    备案号：晋ICP备:15005140号
</div>

</body>
</html>