<?php
    //用于显示左侧栏目选中状态
    $this->params = [
        'current_active' => ['user','user/index'],
        'crumb'          => ['用户管理','用户操作'],
    ];
?>
<?php $this->beginBlock('content'); ?>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">管理员操作</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form class="form-horizontal" id="form">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input name="id" type="hidden" value="<?= $model['id'] ?>">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">用户名</label>

                        <div class="col-sm-10">
                            <input type="text" maxlength="20" class="form-control" name="username" value="<?= $model['username'] ?>" placeholder="用户名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">邮箱</label>

                        <div class="col-sm-10">
                            <input type="text" maxlength="20" class="form-control" name="email" value="<?= $model['email'] ?>" placeholder="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label"><?= \Yii::$app->params['money_name']?></label>

                        <div class="col-sm-10">
                            <input type="number" maxlength="15" class="form-control" name="money" value="<?= $model['money'] ?>" placeholder="0.00">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">状态</label>

                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="status"  value="1" <?= $model['status']!=2?'checked':'' ?>>
                                    正常
                                </label>
                                <label>
                                    <input type="radio" name="status" value="2" <?= $model['status']==2?'checked':'' ?>>
                                    关闭
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">等级</label>

                        <div class="col-sm-10">
                            <select name="type" id="" class="form-control">
                                <?php foreach($user_type as $key=>$vo){?>
                                <option value="<?=$key?>" <?=$model['type']==$key?'selected':''?> ><?=$vo['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">帐号密码</label>

                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="password" value=""  placeholder="****" maxlength="30">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">支付密码</label>

                        <div class="col-sm-10">
                            <input type="password" class="form-control" name="pay_pwd" value=""  placeholder="****" maxlength="30">
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <button type="button" class="btn btn-info col-sm-offset-1 " id="submit"  onclick="$.common.formSubmit('',false)">保存</button>
        </div>
        <!-- /.box-footer -->
    </form>
</div>


<?php $this->endBlock(); ?>

<?php $this->beginBlock('script');?>
<script>
    $(function(){

    })
</script>
<?php $this->endBlock();?>

