<?php
$this->title = '数据管理';
$this->params = [
    'current_active' => ['transaction','transaction/index'],
    'crumb'          => ['数据管理','信息'],
];
?>
<?php $this->beginBlock('content')?>



    <div class="box">
        <div class="box-header with-border">
            <h3>用户基本资料</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12">
                    <table class="layui-table"  lay-size="lg">
                        <colgroup>
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col width="200">
                            <col>
                        </colgroup>

                        <tbody>

                        <tr>
                            <td>上次收盘时间</td>
                            <td><?=$open_model['up_date'].' '.$open_model['up_time']?></td>
                            <td>上次收盘价格</td>
                            <td><?=$open_model['current_price']?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>收盘时间</td>
                            <td><?=$model['up_date'].' '.$model['up_time']?></td>
                            <td>收盘价格</td>
                            <td><?=$model['current_price']?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>对比状态</td>
                            <td><?=\app\models\Pan::getCompareInfo($model['compare'])?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4>一级用户</h4>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="layui-table">
                                <colgroup>
                                    <col width="150">
                                    <col width="200">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>用户名</th>
                                    <th>下注方案</th>
                                    <th>下注额度</th>
                                    <th>手续费比例</th>
                                    <th>不含手续费</th>
                                    <th>获得额度</th>
                                    <th>开奖状态</th>
                                    <th>输赢状态</th>
                                    <th>处理状态</th>
                                    <th>开奖时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if($model) foreach($model['linkVote'] as $vo) {?>
                                    <tr>
                                        <td><a href="<?=\yii\helpers\Url::to(['user/user-detail','id'=>$vo['linkUser']['id']])?>"><?= $vo['linkUser']['username']?></a></td>
                                        <td><?= \app\models\Vote::getVoteUp($vo['is_up'])?></td>
                                        <td><?= $vo['money']?></td>
                                        <td><?= $vo['per']?></td>
                                        <td><?= $vo['result_money']?></td>
                                        <td><?= $vo['get_money']?></td>
                                        <td><?= \app\models\Vote::getAwardState($vo['award_state'])?></td>
                                        <td><?= \app\models\Vote::getIsWin($vo['is_win'])?></td>
                                        <td><?= \app\models\Vote::getStatus($vo['status'])?></td>
                                        <td><?= $vo['open_time']?></td>
                                        <td>
                                            <?php if($vo['status']==1){?>
                                                <a  href="javascript:;" onclick="$.common.confirm('<?= \yii\helpers\Url::to(['open','id'=>$vo['id']])?>',{id:<?=$vo['id']?>,_csrf:'<?=\Yii::$app->request->csrfToken?>'},'是否进行开奖?')" class="ml-5">  开奖</a>
                                                <a  href="javascript:;" onclick="$.common.confirm('<?= \yii\helpers\Url::to(['back','id'=>$vo['id']])?>',{id:<?=$vo['id']?>,_csrf:'<?=\Yii::$app->request->csrfToken?>'},'是否原额返还?')" class="ml-5">  原额返还</a>
                                            <?php }?>
                                        </td>
                                    </tr>
                                <?php }?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <!-- /.box-body -->

    </div>



<?php $this->endBlock()?>