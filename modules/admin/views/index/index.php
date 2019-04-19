<?php $this->beginBlock('content')?>

    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">用户总数</span>
                    <span class="info-box-number"><?=$user_count?></span>
                    <span class="info-box-text"></span>
                    <span class="info-box-text"></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">今日成交数量</span>
                    <span class="info-box-number"><?=$press_count?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <div class="row">

        <div class="col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">总<?=\Yii::$app->params['money_name']?>数量</span>
                    <span class="info-box-number"><?= $sum_money?></span>
                    <span class="info-box-text"></span>
                    <span class="info-box-text"></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">人均<?=\Yii::$app->params['money_name']?>数量:</span>
                    <span class="info-box-number"><?= $user_count>0?sprintf('%0.2f',$sum_money/$user_count):0?></span>
                    <span class="info-box-text"></span>
                    <span class="info-box-text"></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <div class="row">


        <div class="col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-calculator"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">上证指数(今日数量)</span>
                    <span class="info-box-number"><?=$sz_count?></span>
                    <span class="info-box-text">每天开盘时间</span>
                    <span class="info-box-text">
                        <?php foreach($sz_open_time as $key=>$vo){?>
                            <?=$key.'--'.$vo?>&nbsp;
                        <?php }?>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-calculator"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">德国指数(今日数量)</span>
                    <span class="info-box-number"><?=$gdaxi_count?></span>
                    <?php
                    foreach($gdaxi_open_time as $key=>$vo)
                    {
                        ?>

                        <span class="info-box-text">
                         每天开盘时间:<?=str_replace(['{"','"}','":"'],['','','--'],json_encode($vo[0]))?>
                         开盘月份:<?=json_encode($vo[1])?>
                    </span>
                        <?php
                    }
                    ?>

                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>


    </div>

    <div class="col-md-12">
        <!-- LINE CHART -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">交易信息</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div id="main" style="width: inherit;height:400px;"></div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->


    </div>
<?php $this->endBlock()?>

<?php $this->beginBlock('script')?>
<script type="text/javascript" src="/assets/js/echarts.min.js"></script>
<script>
// 基于准备好的dom，初始化echarts实例
var myChart = echarts.init(document.getElementById('main'));
option = {
    title: {
        text: '近30日交易数据'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data:<?=json_encode($charge_legend,JSON_UNESCAPED_UNICODE)?>
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    toolbox: {
        feature: {
            saveAsImage: {}
        }
    },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: <?=json_encode($charge_date,JSON_UNESCAPED_UNICODE)?>
    },
    yAxis: {
        type: 'value'
    },
    series: <?=json_encode($charge_data,JSON_UNESCAPED_UNICODE)?>
};




// 使用刚指定的配置项和数据显示图表。
myChart.setOption(option);
</script>
<?php $this->endBlock()?>