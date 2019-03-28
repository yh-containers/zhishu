<?php $this->beginBlock('content')?>

    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">用户总数</span>
                    <span class="info-box-number"><?=$user_count?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>

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
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">德国指数(今日数量)</span>
                    <span class="info-box-number"><?=$gdaxi_count?></span>
                    <span class="info-box-text">每天开盘时间</span>
                    <span class="info-box-text">
                         <?php foreach($gdaxi_open_time as $key=>$vo){?>
                             <?=$key.'--'.$vo?>&nbsp;
                         <?php }?>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
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
<?php $this->endBlock()?>