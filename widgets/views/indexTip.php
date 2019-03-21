<div class="explanation_pop">
    <div class="explanation">
        <div class="title">使用说明</div>
        <div class="content">
            <p><?=$content?></p>
        </div>
        <div class="bottom">
            <div class="time"><span id="dd"><?=$second?></span><span>秒</span>后关闭</div>
            <div class="close">跳过</div>
        </div>
    </div>
</div>

<script>
    $(".explanation_pop .close").click(function(){
        $(".explanation_pop").hide();
    });
    function run(){
        var s = document.getElementById("dd");
        if(s.innerHTML == 0){
            $(".explanation_pop").hide();
            return false;
        }
        s.innerHTML = s.innerHTML * 1 - 1;
    }
    window.setInterval("run();", 1000);
</script>