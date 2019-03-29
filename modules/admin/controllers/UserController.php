<?php
namespace app\modules\admin\controllers;

class UserController extends DefaultController
{

    /*
     * 用户列表
     * */
    public function actionIndex()
    {
        $query = \app\models\User::find();
        $count = $query->count();
        $pagination = new \yii\data\Pagination(['totalCount' => $count]);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->all();
        return $this->render('index', [
            'list' => $list,
            'pagination' => $pagination
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
        return $this->render('userAdd',[
            'model' => $model
        ]);
    }


    //管理员--删除
    public function actionUserDel()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id');
        $model = new \app\models\User();
        $result = $model->actionDel(['id'=>$id]);
        return $this->asJson($result);
    }

    //用户详情
    public function actionUserDetail()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',0);
        $model=\app\models\User::find()->with(['fuidOne','fuidTwo','fuidThree'])->where(['id'=>$id])->one();
//        var_dump($model);exit;
        return $this->render('userDetail',[
            'model' =>$model
        ]);
    }
}