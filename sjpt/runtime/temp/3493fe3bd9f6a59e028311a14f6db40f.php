<?php /*a:2:{s:69:"D:\wamp64\www\sjpt\application\index\view\index\audit\auditTrail.html";i:1571361752;s:58:"D:\wamp64\www\sjpt\application\index\view\public\head.html";i:1571361752;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>进行审计跟踪项目</title>
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
    <script type="text/javascript" src="/static/index//js/audit/auditTrail.js"></script>
    <script type="text/javascript" src="/static/index//js/audit/jquery.jdirk.js"></script>
    <script type="text/javascript" src="/static/index//js/audit/jeasyui.extensions.combobox.getSelections.js"></script>
    <link rel="stylesheet" href="/static/index//css/synthesisTable.css">
</head>
<body>
    <!-- S 进行审计跟踪数据 -->
    <div id="auditTrailTitle">
        <form>
            <label>&nbsp;&nbsp;项目名称：</label><input id="seachval" name="seachval" class="easyui-textbox"/>
            <a id="seachdata" class="easyui-linkbutton" iconCls="icon-search" onclick="obj.search()"> 查 询 </a>
        </form>
    </div>
    <!-- 显示项目数据列表datagrid-->
    <div id="auditTrails"></div>

    <div class="center paddingButton">
        <a id="addAuditTrail" class="easyui-linkbutton buttonBottom" iconCls="icon-add"> 添加审计跟踪项目 </a>
    </div>
    <!-- E 进行审计跟踪数据 -->

    <!-- S 进行审计跟踪数据 双击弹出页面 -->
    <div id="alterAuditTrail"></div>
    <!-- 进行审计跟踪数据 (只读数据)双击弹出页面-->
    <div id="alertAuditTrailWatch"></div>
    <!-- E 进行审计跟踪数据 双击弹出页面 -->

    <!-- S 进行审计跟踪数据 点击进入按钮弹出页面 -->
    <div id="accessBtn"></div>
    <div id="accessBtnwatch"></div>
    <!-- E 进行审计跟踪数据 点击进入按钮弹出页面 -->
</body>
</html>