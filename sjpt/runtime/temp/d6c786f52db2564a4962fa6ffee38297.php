<?php /*a:1:{s:68:"D:\wamp64\www\sjpt\application\index\view\index\xmgl\dispicture.html";i:1571472625;}*/ ?>
<style>
    #pic {
        column-width: 200px;
        -webkit-column-width: 200px;
        -webkit-column-gap: 2px;
        column-gap: 2px;
        padding: 5px;
    }
    #pic a img {
        width: 200px;
        border-radius: 10px;
        margin: 2px;
    }
</style>
<script type="text/javascript">
    $(function(){
        $.post(ThinkPHP['MODULE']+'/underway/get_picdata',{kid:pro_info.kid}, function (data) {

            var picdata = JSON.parse(data);
            var vpic = "";
            if(picdata){
                $.each(picdata, function (index, v) {
                    vpic = vpic + '<a class="example-image-link" href= '+v.files_add+' data-lightbox="example-set"><img class="example-image" src='+v.files_add+'></a>';
                });
                $("#pic").html(vpic);
            }
            else{
                $.messager.alert('提示', '没有现场审计图片数据！');
                return;
            }
        });
    });

</script>
<div id="pic" class="image-row"></div>
<link rel="stylesheet" href="/static/index//js/lightbox/css/lightbox.css">
<script type="text/javascript" src="/static/index//js/lightbox/js/lightbox.js"></script>
