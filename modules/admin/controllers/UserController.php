<?php
namespace app\modules\admin\controllers;

class UserController extends DefaultController
{

    /*
     * 用户列表
     * */
    public function actionIndex()
    {
        $keyword = $this->request->get('keyword','');
        $keyword = trim($keyword);

        $o_type = $this->request->get('o_type','');
        $o_money = $this->request->get('o_money','');
        $o_update_time = $this->request->get('o_update_time','');

        $where = [];
//        !empty($keyword) && $where[]= ['like','username',$keyword];
        $query = \app\models\User::find();
        $query = $query->where($where);
        !empty($keyword) && $query=$query->andwhere(['or',['like','username',$keyword],['like','email',$keyword]]);
        $count = $query->count();
        $pagination = new \yii\data\Pagination(['totalCount' => $count]);
        //更新时间-默认排序
        $query->orderBy('update_time desc');
        if($o_type){
            //用户类型
            $query->orderBy('type '.($o_type=='asc'?'asc':'desc'));
        }
        if($o_money){
            //用户余额
            $query->orderBy('money '.($o_money=='asc'?'asc':'desc'));
        }
        if($o_update_time){
            //更新时间
            $query->orderBy('update_time '.($o_update_time=='asc'?'asc':'desc'));
        }

        $list = $query->offset($pagination->offset)->limit($pagination->limit)->all();
        return $this->render('index', [
            'list' => $list,
            'pagination' => $pagination,
            'keyword' => $keyword,
            'o_type' => $o_type,
            'o_money' => $o_money,
            'o_update_time' => $o_update_time,
        ]);
    }

    /*
     * 用户新增
     * */
    public function actionUserAdd()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',0);
        $model = new \app\models\User();
        if($request->isAjax){
            $money = $request->post('money');
            if($money<0) return $this->asJson(['code'=>0,'msg'=>\Yii::$app->params['money_name'].'只能为正数']);
            
            $php_input = $request->post();

//            if(empty($php_input['password']))  unset($php_input['password']);
//            if(empty($php_input['pay_pwd']))  unset($php_input['pay_pwd']);
//            var_dump($php_input);exit;
            $result = $model->actionSave($php_input);
            return $this->asJson($result);
        }

        $model = $model::findOne($id);
        //邀请者信息
        $req_user_info = \app\models\User::findOne($model['fuid1']);
        return $this->render('userAdd',[
            'model' => $model,
            'req_user_info' => $req_user_info,
            'user_type' => \app\models\User::getUserType(),

        ]);
    }


    //用户--删除
    public function actionUserDel()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id');
        if($request->isPost){
            $id = $request->post('id');
        }

        $users = \app\models\User::find()->where(['in','id',$id])->all();

        foreach ($users as $user){
            $state = $user->delete();
        }
        if($state) {
            $result = ['code'=>1,'msg'=>'删除成功'];
        }else{
            $result = ['code'=>0,'msg'=>'删除异常'];
        }
        return $this->asJson($result);
    }

    //用户详情
    public function actionUserDetail()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',0);
        $model=\app\models\User::find()->with(['fuidOne','fuidTwo','fuidThree'])->where(['id'=>$id])->one();
//        var_dump($model);exit;
        //邀请者信息
        $req_user_info = \app\models\User::findOne($model['fuid1']);
        return $this->render('userDetail',[
            'model' =>$model,
            'req_user_info' => $req_user_info,
        ]);
    }


    //交易明细
    public function actionUserCharge()
    {
        $id = $this->request->get('id',0);
        $type = $this->request->get('type',null);

        $query = \app\models\UserMoneyLogs::find();
        $query = $query->where(['uid'=>$id]);

        if(!is_null($type)){
            $query = $query->andWhere(['type'=>$type]);
        }

        !empty($keyword) && $query=$query->andwhere(['or',['like','username',$keyword],['like','email',$keyword]]);
        $count = $query->count();
        $pagination = new \yii\data\Pagination(['totalCount' => $count]);
        //更新时间-默认排序
        $query->orderBy('id desc');

        $list = $query->offset($pagination->offset)->limit($pagination->limit)->all();
        //交易类型
        $logs_type = \app\models\UserMoneyLogs::getType();
        $logs_type = array_column($logs_type,null,'type');

        return $this->render('userCharge',[
            'id' => $id,
            'list' => $list,
            'pagination' => $pagination,
            'type'  => $type,
            'logs_type'  => $logs_type,
        ]);
    }

    //导出
    public function actionUserExport()
    {
        $filename = '用户邮箱导出';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.csv"');
        header('Cache-Control: max-age=0');

        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        $header_data = [
            'email' =>'邮箱',
        ];

        mb_convert_variables('GBK', 'UTF-8', $header_data);
        fputcsv($fp, $header_data);
        foreach (\app\models\User::find()->batch() as $users){
            foreach ($users as $vo){
                $temp_data=[];
                foreach($header_data as $key=>$item){
                    $temp_data[] = isset($vo[$key])? iconv('utf-8', 'gb2312',$vo[$key]):'';
                }
                mb_convert_variables('GBK', 'UTF-8', $temp_data);
                fputcsv($fp, $temp_data);
            }
        }
        fclose($fp);
        exit;
    }

}