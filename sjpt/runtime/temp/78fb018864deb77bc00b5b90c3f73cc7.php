<?php /*a:1:{s:64:"D:\wamp64\www\sjpt\application\index\view\index\login\login.html";i:1571361752;}*/ ?>
<!DOCTYPE html>
<link href="/static/index//img/logo.ico" rel="shortcut icon">
<html lang="en">
<!--<?php echo htmlentities(app('config')->get('app.__CSS__')); ?>-->
<head>
    <meta charset="UTF-8">
    <title>阳泉市政府投资项目审计监督平台</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <link rel="stylesheet" href="/static/index//js/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/index//css/login.css">
    <script language="javascript">
        if (top != window)
            top.location.href = "<?php echo url('index/login','',''); ?>";
    </script>
</head>
<body>
<header></header>
<article>
    <div class="article">
        <div class="message"></div>
        <div id="defaultForm" class="bs-example bs-example-form">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="输入您的用户名"
                           autofocus required data-bv-notempty-message="请您输入正确的用户名"/>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="输入密码"
                           required data-bv-notempty-message="请您输入正确的密码"/>
                </div>
            </div>
            <div class="form-group">
                <button id="submit" class="btn button_btn btn-block">登 录</button>
            </div>
        </div>
    </div>
</article>
<footer>
    <div class="reminder">
        注意事项：&nbsp;
        建议使用【360浏览器极速模式】、【谷歌】等浏览器
        <a href="http://se.360.cn/" target="_blank">【360浏览器下载】</a> -
        <a href="http://www.firefox.com.cn/" target="_blank">【火狐浏览器下载】</a>
        建议您升级IE11 -
        <a href="http://windows.microsoft.com/zh-cn/internet-explorer/ie-11-worldwide-languages/" target="_blank">【IE11下载】</a>
    </div>
    版权所有：阳泉市审计局 (C)2015-2016 All Rights Reserved<br/>
    备案号：晋ICP备:15005140号
</footer>

<script src="/static/index//js/bootstrap/js/jquery-3.1.1.min.js"></script>
<script src="/static/index//js/jquery/jQuery.cookie.js"></script>
<script src="/static/index//js/bootstrap/js/bootstrap.min.js"></script>
<script src="/static/index//js/bootstrap/js/bootstrapValidator.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#defaultForm').bootstrapValidator();
        $("#username").focus();
        if ($.cookie("username12") == "" || $.cookie("username12") == null) {
            $("#username").focus();
        } else {
            $("#username").val($.cookie("username12"));

            $("#password").focus();
        }

        $("#submit").click(function () {
            var user = $.trim($("#username").val());
            var pwd = $.trim($("#password").val());

            var info = false;
            if (user == "") {
                info = true;
            }
            if (pwd == "") {
                info = true;
            }
            if (info == true) {
                if (user == "") {
                    $("#username").focus();
                } else {
                    $("#password").focus();
                }
                return false;

            } else {
                var para = {username: user, password: pwd};
                $.post("<?php echo url('index/log'); ?>", para, function (respon) {
//                    console.log(respon)
                    if (parseInt(respon) < 0) {
                        alert('用户名或密码错误');
                        return false;
                    } else {
                        window.top.location.replace("<?php echo url('index/showhome'); ?>");
                    }
                });
            }
        });
        $("#username").keydown(function () {
            if (event.keyCode == 13) {
                $("#password").focus();
            }
        });
        $("#password").keydown(function () {
            if (event.keyCode == 13) {
                $("#submit").trigger("click");
            }
        });
    });
</script>
</body>
</html>