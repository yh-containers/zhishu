<?php

namespace app\controllers;


class MineController extends CommonController
{
    /**
     * 用户模型
     * @var \app\models\User
     * */
    protected $user_model;

    public function behaviors()
    {
        $this->user_model = \app\models\User::findOne($this->user_id);
        if(empty($this->user_model)){
            echo header('Location:/index/login');exit;
            //不存在--重新登录
//            $this->redirect(\yii\helpers\Url::to(['index/logout']));
        }
        return parent::behaviors(); // TODO: Change the autogenerated stub
    }
    /*
     * 主页
     * */
    public function actionIndex()
    {
        return $this->render('index',[
            'user_model' => $this->user_model,
        ]);
    }

    /*
     * 分销
     * */
    public function actionDistribution()
    {
        //分销一级
        $one_count = $this->user_model->getFuidOne()->count();
        //分销二级
        $two_count = $this->user_model->getFuidTwo()->count();
        //分销三级
        $three_count = $this->user_model->getFuidThree()->count();
        $total_count = $one_count+$two_count+$three_count;//汇总
        return $this->render('distribution',[
            'user_model' => $this->user_model,
            'one_count' => $one_count,
            'two_count' => $two_count,
            'three_count' => $three_count,
            'total_count' => $total_count,
        ]);
    }

    /*
     * 分销列表
     * */
    public function actionDisList()
    {
        $state = (int)$this->request->get('state',0);

        if($this->request->isAjax){
            $query = \app\models\User::find()->where(['status'=>1]);
            if($state==1){
                $query->andWhere(['fuid1'=>$this->user_id]);
            }elseif($state==2){
                $query->andWhere(['fuid2'=>$this->user_id]);
            }elseif ($state==3){
                $query->andWhere(['fuid3'=>$this->user_id]);
            }else{
                $query->andWhere(['or',['fuid1'=>$this->user_id],['fuid2'=>$this->user_id],['fuid3'=>$this->user_id]]);
            }

            $count = $query->count();
            $pagination = \Yii::createObject(array_merge(\Yii::$app->components['pagination'],['totalCount' => $count]));
            $uid = $this->user_id;
            $list = $query->with(['baseComSum'=>function($query)use($uid){
                return $query->where(['type'=>\app\models\UserMoneyLogs::TYPE_COMMISSION,'uid'=>$uid]);
            }])->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();

            $data = [];
            foreach($list as $vo){
                $data[] = [
                    'id'         =>  $vo['id'],
                    'face'       =>  $vo['face'],
                    'money'      =>  $vo['money'],
                    'form_money' =>  $vo->linkComSum,
                    'type'       =>  $vo['type'],
                    'online'     =>  $vo->getOnline(),//在线状态 0离线 1在线
                    'type_name'  =>  \app\models\User::getUserType($vo['type'],'name'),
                    'level'      =>  $vo['level'],
                    'level_name' =>  \app\models\User::getUserLevel($vo['level'],'name'),
                ];
            }

            return $this->asJson(['code'=>1,'msg'=>'获取成功','data'=>$data,'page'=>$pagination->pageCount]);
        }


        return $this->render('disList',[
            'user_model' => $this->user_model,
            'state' => $state,
        ]);
    }

    /*
     * 我的推荐码
     * */
    public function actionReqCode()
    {
        return $this->render('reqCode',[
            'user_model' => $this->user_model,
        ]);
    }

    /*
     * 重置邮箱
     * */
    public function actionRestMail()
    {
        if($this->request->isAjax){
            $php_input = $this->request->post();
            $this->user_model->scenario=\app\models\User::SCENARIO_MOD_EMAIL;
            $result = $this->user_model->actionSave($php_input);
            return $this->asJson($result);

        }

        return $this->render('restMail',[
            'user_model' => $this->user_model,
        ]);
    }

    /*
     * 重置密码
     * */
    public function actionRestPwd()
    {
        if($this->request->isAjax){
            $php_input = $this->request->post();
            $this->user_model->scenario=\app\models\User::SCENARIO_REST_PWD;
            $result = $this->user_model->actionSave($php_input);
            return $this->asJson($result);

        }
        return $this->render('restPwd',[
            'user_model' => $this->user_model,
        ]);
    }

    /*
     * 支付密码--设置
     * */
    public function actionRestPayPwd()
    {
        if($this->request->isAjax){
            $php_input = $this->request->post();
            $this->user_model->scenario=\app\models\User::SCENARIO_REST_PAY_PWD;
            $result = $this->user_model->actionSave($php_input);
            return $this->asJson($result);

        }

        return $this->render('restPayPwd',[
            'user_model' => $this->user_model,
        ]);
    }

    /*
     * 投注
     * */
    public function actionVote()
    {
        $money = $this->request->post('money',0);
        $is_up = $this->request->post('is_up');
        $type = $this->request->post('type',0);//下注类型
        $id = $this->request->post('id',0);//待开奖id
        if($money<=0) throw new \yii\base\UserException('下注金额异常');
        try{
            $this->user_model->vote($id,$money,$is_up,$type);
            $this->asJson(['code'=>1,'msg'=>'投票成功']);
        }catch (\Exception $e){
            $this->asJson(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /**
     * 获取用户列表
     * */
    public function actionShowList()
    {
        $type = $this->request->get('type',0);
        $query = \app\models\User::find()->joinWith(['linkChat'=>function($query){
            return $query
                ->onCondition(['{{%user_chat}}.is_read' =>0]);
        }]);
        if($type){
            //我的好友
            $query
                ->joinWith(['rightFriends'],true,' right join ')
                ->where(['{{%user_friend}}.uid'=>$this->user_id,'{{%user}}.status'=>1]);
            if($type==3){//黑名单
                $query->andWhere(['{{%user_friend}}.is_black'=>1]);

            }elseif ($type==2){//陌生人
                $query->andWhere(['{{%user_friend}}.is_know'=>0]);

            }else{
                $query->andWhere(['{{%user_friend}}.is_black'=>0,'{{%user_friend}}.is_know'=>1]);
            }
        }else{
            $query->where(['{{%user}}.status'=>1])->andWhere(['!=','{{%user}}.id',$this->user_id]);

        }

        $count = $query->groupBy('{{%user}}.id')->count();
        $pagination = \Yii::createObject(array_merge(\Yii::$app->components['pagination'],['totalCount' => $count]));
        $list = $query
            ->select(['{{%user}}.*','chat_count'=>'count({{%user_chat}}.id)'])
            ->groupBy('{{%user}}.id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('count({{%user_chat}}.id) desc')
            ->all();
        $data = [];
        foreach($list as $vo){
            $data[] = [
                'id'         =>  $vo['id'],
                'face'       =>  $vo['face'],
                'money'      =>  $vo['money'],
                'type'       =>  $vo['type'],
                'chat_count' =>  $vo['chat_count'],
                'online'     =>  $vo->getOnline(),//在线状态 0离线 1在线
                'type_name'  =>  \app\models\User::getUserType($vo['type'],'name'),
                'level'      =>  $vo['level'],
                'level_name' =>  \app\models\User::getUserLevel($vo['level'],'name'),
            ];
        }

        return $this->asJson(['code'=>1,'msg'=>'获取成功','data'=>$data,'page'=>$pagination->pageCount]);
    }

    /*
     * 添加好友
     * */
    public function actionAddFriend()
    {
        $f_uid = $this->request->post('f_uid',0);
        try{
            $this->user_model->addFriend($f_uid);
            $this->asJson(['code'=>1,'msg'=>'添加成功']);
        }catch (\Exception $e){
            $this->asJson(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /*
     * 好友加入/移除黑名单
     * */
    public function actionBlackFriend()
    {
        $f_uid = $this->request->post('f_uid',0);
        $state = $this->request->post('state',1);
        try{
            $this->user_model->blackFriend($f_uid,$state);
            $this->asJson(['code'=>1,'msg'=>'添加成功']);
        }catch (\Exception $e){
            $this->asJson(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /*
     * 好友加入/移除陌生人
     * */
    public function actionKnowFriend()
    {
        $f_uid = $this->request->post('f_uid',0);
        $state = $this->request->post('state',0);
        try{
            $this->user_model->knowFriend($f_uid,$state);
            $this->asJson(['code'=>1,'msg'=>'添加成功']);
        }catch (\Exception $e){
            $this->asJson(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    /*
     * 交易记录
     * */
    public function actionMoneyLogs()
    {
        if($this->request->isAjax){
            $query = \app\models\UserMoneyLogs::find()->where(['uid'=>$this->user_id]);

            $count = $query->count();
            $pagination = \Yii::createObject(array_merge(\Yii::$app->components['pagination'],['totalCount' => $count]));
            $list = $query->orderBy('id desc')->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
            $data = [];

            foreach ($list as $vo){
                $cr_date = substr($vo['create_time'],0,7);
                if(isset($date) && $cr_date == $date){
                    $data[]=[1,$vo['money'],$vo['create_time'],$vo['intro'] ];

                }else{
                    //重计时间
                    $date = $cr_date;
                    $map =['like','create_time',$date.'%',false];
                    //支出
                    $out = \app\models\UserMoneyLogs::find()->where(['uid'=>$this->user_id])->andWhere($map)->andWhere(['<','money',0])->sum('money');
                    //收入
                    $in = \app\models\UserMoneyLogs::find()->where(['uid'=>$this->user_id])->andWhere($map)->andWhere(['>','money',0])->sum('money');
                    $data[]=[0,$date,$out?abs($out):0.00,$in?$in:0.00];
                    $data[]=[1,$vo['money'],$vo['create_time'],$vo['intro'] ];
                }
            }
            return $this->asJson(['code'=>1,'msg'=>'获取成功','data'=>$data,'page'=>$pagination->pageCount]);
        }
        //总收入
        $in_total =\app\models\UserMoneyLogs::find()->where(['uid'=>$this->user_id])->andWhere(['>','money',0])->sum('money');
        //总支出
        $out_total =\app\models\UserMoneyLogs::find()->where(['uid'=>$this->user_id])->andWhere(['<','money',0])->sum('money');
        return $this->render('moneyLogs',[
            'in_total' => $in_total?$in_total:0.00,
            'out_total' => $out_total?abs($out_total):0.00,
        ]);
    }

    /*
     * 转账动作
     * */
    public function actionTransfer()
    {
        if($this->request->isAjax){
            $money = $this->request->post('money',0);
            $to_uid = $this->request->post('to_uid',0);
            $pay_pwd = $this->request->post('pay_pwd');
            try{
                $this->user_model->transfer($to_uid,$money,$pay_pwd);
                return $this->asJson(['code'=>1,'msg'=>'转账成功']);
            }catch (\Exception $e){
                return $this->asJson(['code'=>0,'msg'=>$e->getMessage()]);
            }
        }

        $uid = $this->request->get('uid',0);
        $charge_user_info = \app\models\User::findOne($uid);
        return $this->render('transfer',[
            'charge_user_info' => $charge_user_info,
            'user_model'    => $this->user_model
        ]);
    }

    /*
     * 投诉
     * */
    public function actionComplaint()
    {
        if($this->request->isAjax){
            $php_input = $this->request->post();
            $php_input['uid']  = $this->user_id;//投诉者id
            isset($php_input['img']) && $php_input['img']= ($php_input['img']?implode(',',$php_input['img']):'');
            $model = new \app\models\UserComplaint();
            $model->scenario = \app\models\UserComplaint::SCENARIO_COMPLAINT;
            $result = $model->actionSave($php_input);
            return $this->asJson($result);

        }

        $uid = $this->request->get('uid',0);
        //投诉对象
        $complaint_user_info = \app\models\User::findOne($uid);

        return $this->render('complaint',[
            'complaint_user_info' => $complaint_user_info,
        ]);
    }

    /*
     * 提现
     * */
    public function actionWithdraw()
    {
        return $this->render('withdraw',[
            'user_model' => $this->user_model,
        ]);
    }

    /*
     * 提现
     * */
    public function actionWithdrawUp()
    {
        if($this->request->isAjax){
            $php_input = $this->request->post();
            $php_input['uid']  = $this->user_id;//
            $php_input['my_money']  = $this->user_model['money'];//我的余额
            $model = new \app\models\UserWithdraw();
            $model->scenario = \app\models\UserWithdraw::SCENARIO_UP;
            $result = $model->actionSave($php_input);
            return $this->asJson($result);

        }

        return $this->render('withdrawUp',[
            'user_model' => $this->user_model,
        ]);
    }

    /*
     * 可提现列表
     * */
    public function actionWithdrawList()
    {
        //指定用户
        $uid = $this->request->get('uid',0);
        $where=[];
        //按用户查询
        if($uid){
            $where['uid'] = $uid;
        } else{
            $where = ['!=','uid',$this->user_id];
        }

        $query = \app\models\UserWithdraw::find()
            ->joinWith(['userInfo'])->where($where);
        $count = $query->count();
        $pagination = \Yii::createObject(array_merge(\Yii::$app->components['pagination'],['totalCount' => $count]));
        $list = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $data = [];
        foreach ($list as $vo){
            $data[] =[
                'id'            => $vo['id'],
                'uid'           => $vo['uid'],
                'money'         => $vo['money'],
                'price'         => $vo['price'],
                'label'         => $vo['label'],
                'update_time'   => $vo['update_time'],
                'face'          => $vo['userInfo']['face'],
                'type'          => $vo['userInfo']['type'],
                'type_name'     => $vo['userInfo']->getTypeName(),
                'level'         => $vo['userInfo']['level'],
                'level_name'    => $vo['userInfo']->getLevelName(),
            ];
        }
        return $this->asJson(['code'=>1,'msg'=>'获取成功','data'=>$data,'page'=>$pagination->pageCount]);
    }

    /*
     * 删除提现列
     * */
    public function actionWithdrawDel()
    {
        //指定
        $id = $this->request->post('id',0);
        $model = new \app\models\UserWithdraw();
        $result = $model->actionDel(['uid'=>$this->user_id,'id'=>$id]);
        return $this->asJson($result);
    }

    /*
     * 删除提现列
     * */
    public function actionRecharge()
    {
        return $this->render('recharge');
    }

    /*
     * 修改用户信息
     * */
    public function actionModInfo()
    {
        $php_input = $this->request->post();
        if(isset($php_input['_csrf']))unset($php_input['_csrf']);
        $limit_key = ['face'];
        foreach($php_input as $key=>$vo){
            if(in_array($key,$limit_key)){
                $this->user_model->$key = $vo;
            }
        }
        $this->user_model->save();
        return $this->asJson(['code'=>1,'msg'=>'操作成功']);
    }
}
