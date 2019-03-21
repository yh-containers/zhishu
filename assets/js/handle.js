
$.common={
    //发送验证码
    sendVerify(obj,point){
        var email = point.val()
        var type = $(obj).data('type')
        var time=60
        if(!email) {
            alert('请输入邮箱')
            return false;
        }
        $.get('/index.php/index/send-mailer',{email:email,type:type},function(result){
            $(obj).attr('disabled',"true")
            alert(result.msg)
            var interval= setInterval(function(){
                if(time>0){
                    $(obj).text('请等待('+time+')')
                }else{
                    clearInterval(interval)
                    $(obj).text('获取验证码')
                    $(obj).removeAttr('disabled')
                }
                --time;
            },1000)
        })

    }

}