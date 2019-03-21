<div class="protocol_pop">
    <header class="header">
        <a href="javascript:;" class="header_left close"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
        <div class="header_title">用户协议</div>
    </header>

    <main class="main mgtop">
        <div class="protocol wrap">
            <p><?=$content?></p>
        </div>
    </main>
</div>

<script>
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