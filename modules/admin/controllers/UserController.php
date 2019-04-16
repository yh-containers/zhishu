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

        $model = new \app\models\User();
        var_dump($id);exit;
        $result = $model->actionDel(['in','id',$id]);
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
}