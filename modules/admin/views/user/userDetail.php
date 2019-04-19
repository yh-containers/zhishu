<?php
    $this->title = '用户管理';
    $this->params = [
            'current_active' => ['user','user/index'],
            'crumb'          => ['用户管理','用户详情'],
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
                <div class="col-sm-8">
                    <table class="layui-table"  lay-size="lg">
                        <colgroup>
                            <col width="100">
                            <col width="200">
                            <col width="100">
                            <col width="200">
                            <col width="100">
                            <col width="200">
                            <col>
                        </colgroup>

                        <tbody>
                        <tr>
                            <td>用户名</td>
                            <td><?=$model['username']?></td>
                            <td>邮箱</td>
                            <td><?=$model['email']?></td>
                            <td>状态</td>
                            <td><?=$model?$model->statusName:''?></td>
                        </tr>
                        <tr>
                            <td>用户类型</td>
                            <td><?=$model ? \app\models\User::getUserType($model['type'],'name'):''?></td>
                            <td></td>
                            <td></td>
                            <td><?=\Yii::$app->params['money_name']?></td>
                            <td><?= $model['money']?></td>
                        </tr>
                        <tr>
                            <td>注册时间</td>
                            <td><?= $model['create_time']?date('Y-m-d H:i:s',$model['create_time']):''?></td>
                            <td></td>
                            <td></td>
                            <td>邀请者id</td>
                            <td><a href="<?=\yii\helpers\Url::to(['user-detail','id'=>$model['fuid1']])?>"><?=$req_user_info['username']?></a></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col-sm-4">
                    <a class="btn  bg-olive margin" href="<?= \yii\helpers\Url::to(['user-charge','id'=>$model['id']])?>">查看交易明细</a>
                </div>
            </div>
           <div class="row">
                   <div class="col-sm-4">
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
                                       <th>邮箱</th>
                                       <th>加入时间</th>
                                   </tr>
                                   </thead>
                                   <tbody>
                                   <?php if($model) foreach($model['fuidOne'] as $vo) {?>
                                       <tr>
                                           <td><?= $vo['username']?></td>
                                           <td><?= $vo['email']?></td>
                                           <td><?= $vo['create_time']?date('Y-m-d H:i:s',$vo['create_time']):''?></td>
                                       </tr>
                                   <?php }?>

                                   </tbody>
                               </table>
                           </div>
                       </div>
                   </div>
                   <div class="col-sm-4">
                       <div class="box">
                           <div class="box-header with-border">
                               <h4>二级用户</h4>
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
                                       <th>邮箱</th>
                                       <th>加入时间</th>
                                   </tr>
                                   </thead>
                                   <tbody>
                                   <?php if($model)  foreach($model['fuidTwo'] as $vo) {?>
                                       <tr>
                                           <td><?= $vo['username']?></td>
                                           <td><?= $vo['email']?></td>
                                           <td><?= $vo['create_time']?date('Y-m-d H:i:s',$vo['create_time']):''?></td>
                                       </tr>
                                   <?php }?>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   </div>
                   <div class="col-sm-4">
                       <div class="box">
                           <div class="box-header with-border">
                               <h4>三级用户</h4>
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
                                       <th>邮箱</th>
                                       <th>加入时间</th>
                                   </tr>
                                   </thead>
                                   <tbody>
                                   <?php if($model)  foreach($model['fuidThree'] as $vo) {?>
                                       <tr>
                                           <td><?= $vo['username']?></td>
                                           <td><?= $vo['email']?></td>
                                           <td><?= $vo['create_time']?date('Y-m-d H:i:s',$vo['create_time']):''?></td>
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