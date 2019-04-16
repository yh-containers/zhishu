<?php
$this->title = '首页';
$this->params = [
    'body_style'    => 'style="background: #fff;"',
    'current_active'=> 'index/index',
    'init_type'     => isset($type) ? $type : 0,
    'is_test'     => 1,
    'is_open'     => $is_open,
];
?>
<?php $this->beginBlock('style')?>
<style>
    .layui-layer .layui-layer-content{text-align: center;}
    .layui-layer .layui-layer-btn .layui-layer-btn0{float: right;background: #c50000;color: #fff;border: none;margin: 10px;}
    .layui-layer .layui-layer-btn .layui-layer-btn1{float: left;background: #00c500;color: #fff;border: none;margin: 10px;}
</style>
<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>
<header class="header index_header">
    <div class="content">
        <div class="left"><i class="icon iconfont icon-yuanbao-copy"></i><span id="user-money"><?=$user_info['money']?></span></div>
        <div class="right">
            <a href="<?=\yii\helpers\Url::to(['mine/money-logs'])?>"><i class="icon iconfont icon-jiaoyijilu-"></i><span>账单</span></a>
            <a href="<?=\yii\helpers\Url::to(['mine/recharge'])?>"><i class="icon iconfont icon-chongzhi1"></i><span>充值</span></a>
            <a href="<?=\yii\helpers\Url::to(['mine/withdraw'])?>"><i class="icon iconfont icon-tixian1"></i><span>提现</span></a>
            <a href="javascript:location.reload();"><i class="icon iconfont icon-shuaxin"></i><span>刷新</span></a>
        </div>
    </div>
</header>
<main class="main">
    <div class="compared">
        <div class="vs">
            <div class="plate left" id="open-pan">
                <div class="num">
                    <h2>00.00</h2>
                    <p>收盘价</p>
                </div>
                <div class="time">10:02</div>
            </div>
            <div class="center"><h2>VS</h2><p><?=$type?'德国指数一分钟线':'上证指数一分钟线'?><!--<span id="then-open-time">10：58</p>--></div>
            <div class="plate right" id="close-pan">
                <div class="num">
                    <h2>00.00</h2>
                    <p>收盘价</p>
                </div>
                <div class="time">10:04</div>
            </div>
        </div>
        <div class="zhishu">
            <div id="main" style="height:calc(100vh - 446px);width:100%;"></div>
        </div>

        <div class="look">
            <div class="position">
                <div class="bullish">
                    <div class="num"><i class="icon iconfont icon-yuanbao-copy"></i><span id="up-money"><?=($press_info['money'] && $press_info['type']==0)?$press_info['money']:0?></span></div>
                    <div class="red cylinder"><p></p></div>
                    <div class="name">看涨元宝</div>
                </div>
            </div>
            <div class="img"><img src="/assets/images/yuanbao.png"></div>
            <div class="position">
                <div class="bearish">
                    <div class="num"><i class="icon iconfont icon-yuanbao-copy"></i><span id="down-money"><?=($press_info['money'] && $press_info['type']==1)?$press_info['money']:0?></span></div>
                    <div class="green cylinder"><p></p></div>
                    <div class="name">看跌元宝</div>
                </div>
            </div>
        </div>
    </div>
    <div class="exponent">
        <div class="title"><i class="icon iconfont icon-xiadan"></i><p>下单<br />合约</p></div>
        <ul>
            <li <?=$type?'':'class="cur"'?> onclick="window.location.href='<?=\yii\helpers\Url::to(['','is_lock'=>1])?>'">上证指数一分钟线</li>
            <li <?=$type==1?'class="cur"':''?> onclick="window.location.href='<?=\yii\helpers\Url::to(['','is_lock'=>1,'type'=>1])?>'">德国指数一分钟线</li>
        </ul>
    </div>
    <div class="betting">
        <div class="rise">
            <div class="button" data-is_up="1">
                <h2>看涨</h2>
                <p>预收益率<span id="up_per">0.00</span>%</p>
            </div>
            <div class="price" id="press-up-money">0</div>
        </div>
        <div class="time">
            <p id="wait-name">下单时间</p>
            <table cellpadding="0" cellspacing="0" class="data-table">
                <tr>
                    <td class="colspan-a">
                        <div class="data-show-box" id="dateShow">
                            <span class="date-s-span s" id="vote-second">00</span>
                        </div>
                    </td>
                </tr>
            </table>
            <p>倒计时</p>
        </div>
        <div class="fall">
            <div class="button" data-is_up="2">
                <h2>看跌</h2>
                <p>预收益率<span id="down_per">0.00</span>%</p>
            </div>
            <div class="price"  id="press-down-money">0</div>
        </div>
    </div>
</main>



<div class="bet_pop">
    <div class="bet_list">
        <ul class="clearfix">
            <li data-money="100"><i class="icon iconfont icon-yuanbao-copy"></i>100</li>
            <li data-money="300"><i class="icon iconfont icon-yuanbao-copy"></i>300</li>
            <li data-money="600"><i class="icon iconfont icon-yuanbao-copy"></i>600</li>
            <li data-money="1000"><i class="icon iconfont icon-yuanbao-copy"></i>1000</li>
            <li data-money="2000"><i class="icon iconfont icon-yuanbao-copy"></i>2000</li>
            <li data-money="3000"><i class="icon iconfont icon-yuanbao-copy"></i>3000</li>
            <li data-money="4000"><i class="icon iconfont icon-yuanbao-copy"></i>4000</li>
            <li data-money="5000"><i class="icon iconfont icon-yuanbao-copy"></i>5000</li>
            <li data-money="6000"><i class="icon iconfont icon-yuanbao-copy"></i>6000</li>
        </ul>
        <div class="queding"><a href="javascript:;">确定</a></div>
    </div>
</div>

<div class="income_pop">
    <div class="my_income">
        <div class="title">我的收益</div>
        <div class="content">
            <i><img src="/assets/images/ingots.png"></i>
            <p id="up_get_money">+1800</p>
            <a href="javascript:;" class="close">确认</a>
        </div>
    </div>
</div>

<?= \app\widgets\Protocol::widget(['is_show'=>$is_show_protocol])?>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script type="text/javascript" src="/assets/js/leftTime.min.js"></script>
<script type="text/javascript" src="/assets/js/echarts.min.js"></script>
<script type="text/javascript">
    function isWeiXin(){
        var ua = window.navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i) == 'micromessenger'){
            return true;
        }else{
            return false;
        }
    }
</script>
<?php $this->endBlock()?>
