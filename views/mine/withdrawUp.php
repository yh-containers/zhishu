<?php
$this->title = '上架';
$this->params = [
    'body_style' => 'style="background: #fff;"',
];
?>

<?php $this->beginBlock('content')?>
<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">上架</div>
</header>

<main class="main mgtop">
    <div class="shelf">
        <form id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <ul>
                <li>
                    <label>出售元宝数量</label>
                    <div class="right"><input type="number" name="money" value="" placeholder="<?=$user_model['money']?>">个</div>
                </li>
                <li>
                    <label>单价</label>
                    <div class="right"><input type="number" name="price" value="" placeholder="1">元</div>
                </li>
                <li>
                    <label>总价</label>
                    <div class="right"><input type="number" id="total" readonly>元</div>
                </li>
                <li>
                    <label>收款方式</label>
                    <dl>
                        <input type="hidden" name="label" value="1"/>
                        <dd data-label="1" class="on"><img src="/assets/images/pay01.png"></dd>
                        <dd data-label="2"><img src="/assets/images/pay02.png"></dd>
                        <dd data-label="3"><img src="/assets/images/pay03.png"></dd>
                    </dl>
                </li>
            </ul>
            <div class="btn"><input type="button" id="submit" name="" value="确定"></div>
        </form>
    </div>
</main>

<footer class="footer">
    <div class="income">
        <p><i class="icon iconfont icon-wodeyuanbao"></i><strong>我的<?=\Yii::$app->params['money_name']?>：<?=$user_model['money']?></strong></p>
    </div>
</footer>

<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script type="text/javascript">
    $(function(){
        $(".shelf ul li dd").click(function(){
                $(this).toggleClass("on").siblings().removeClass("on");
            $(this).parent().find("input").val($(this).hasClass('on')?$(this).data('label'):0)
        });
        $("input[name='money']").keyup(total)
        $("input[name='price']").keyup(total)

        $("#submit").click(function(){
            var index = layer.load()
            $.post($("#form").attr('action'),$("#form").serialize(),function (result) {

                layer.msg(result.msg)
                layer.close(index)
                if(result.code===1){
                    setTimeout(function(){window.history.back()},1000)
                }
            })
        })
    })


    //计算金额
    function total(){
        var money = $("input[name='money']").val()-0;
        var price = $("input[name='price']").val()-0;
        $("#total").val(money*price)
    }
</script>

<?php $this->endBlock()?>
