<div class="explanation_pop">
    <div class="explanation">
        <div class="title">用户协议</div>
        <div class="content">
            <p><?=$content?></p>
        </div>
        <div class="bottom">
            <div class="time"><span id="dd">15</span><span>秒</span>后关闭</div>
            <div class="close">跳过</div>
        </div>
    </div>
</div>


<script type="text/javascript" src="/assets/js/leftTime.min.js"></script>
<script>
    $(function(){
        //日期倒计时
        $.leftTime("2019/03/15 16:31:00",function(d){
            if(d.status){
                var $dateShow1=$("#dateShow");
                $dateShow1.find(".m").html(d.m);
                $dateShow1.find(".s").html(d.s);
            }
        });
    });

    var protocol_interval = setInterval(function(){
        var s = document.getElementById("dd");
        if(s.innerHTML == 0){
            $(".explanation_pop").hide();
            clearInterval(protocol_interval)
            return false;
        }
        s.innerHTML = s.innerHTML * 1 - 1;
    }, 1000);

    $(".registered .agree input").on("click",function(){
        $(this).toggleClass( "on_checkbox" );
    });

    $(".registered .agree a").click(function(){
        $(".protocol_pop").show();
    });
    $(".close").click(function(){
        $(".protocol_pop").hide();
    });
</script>