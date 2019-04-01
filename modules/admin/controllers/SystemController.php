<?php
namespace app\modules\admin\controllers;

class SystemController extends DefaultController
{
    public function actionIndex()
    {
        return $this->render('index',[

        ]);
    }

    /*
     * 常规设置
     * */
    public function actionSetting()
    {
        $normal_content = \app\models\Setting::getContent('normal');
        $normal_content = json_decode($normal_content,true);
        //使用说明
        $use_content = \app\models\Setting::getContent('use');
        $use_content = json_decode($use_content,true);
        return $this->render('setting',[
            'normal_content'  => $normal_content,
            'use_content'  => $use_content
        ]);
    }

    /*
     * 保存动作
     * */
    public function actionSettingSave()
    {
        $type = \Yii::$app->request->post('type');
        $content = \Yii::$app->request->post('content');
        try{
            is_array($content)&& $content = json_encode($content);
            \app\models\Setting::setContent($type,$content);
            return $this->asJson(['code'=>1,'msg'=>'保存成功']);
        }catch (\Exception $e) {
            return $this->asJson(['code'=>0,'msg'=>'保存异常:'.$e->getMessage()]);
        }
    }

    /*
     * 管理员列表
     * */
    public function actionManage()
    {
        $query = \app\models\Manage::find();
        $count = $query->count();
        $pagination = new \yii\data\Pagination(['totalCount'=>$count]);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->all();
        return $this->render('manage',[
            'list'  => $list,
            'pagination' => $pagination
        ]);
    }

    /*
    * 管理员--新增
    * */
    public function actionManageAdd()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',0);
        $model = new \app\models\Manage();
        if($request->isAjax){
            $php_input = $request->post();
            if(empty($php_input['password']))  unset($php_input['password']);
//            var_dump($php_input);exit;
            $result = $model->actionSave($php_input);
            return $this->asJson($result);
        }

        $model = $model::findOne($id);
        return $this->render('manageAdd',[
            'model' => $model
        ]);
    }



    //管理员--删除
    public function actionManageDel()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id');
        $model = new \app\models\Manage();
        $result = $model->actionDel(['id'=>$id]);
        return $this->asJson($result);
    }

    //协议
    public function actionProtocol()
    {
        $content = \app\models\Setting::getContent('protocol');
        return $this->render('protocol',[
            'content'  => $content
        ]);
    }

    //协议
    public function actionProtocolReg()
    {
        $content = \app\models\Setting::getContent('protocol_reg');
        return $this->render('protocolReg',[
            'content'  => $content
        ]);
    }

    //帮助中心
    public function actionHelpCenter()
    {
        $query = \app\models\SysHelp::find();
        $count = $query->count();
        $pagination = new \yii\data\Pagination(['totalCount'=>$count]);
        $list = $query->offset($pagination->offset)->orderBy('sort asc')->limit($pagination->limit)->all();

        return $this->render('helpCenter',[
            'list'  => $list,
            'pagination' => $pagination
        ]);
    }

    //帮助中心
    public function actionHelpCenterAdd()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id',0);
        $model = new \app\models\SysHelp();
        if($request->isAjax){
            $php_input = $request->post();
            $result = $model->actionSave($php_input);
            return $this->asJson($result);
        }

        $model = $model::findOne($id);
        return $this->render('helpCenterAdd',[
            'model' => $model
        ]);
    }


    //帮助--删除
    public function actionHelpCenterDel()
    {
        $request = \Yii::$app->request;
        $id = $request->get('id');
        $model = new \app\models\SysHelp();
        $result = $model->actionDel(['id'=>$id]);
        return $this->asJson($result);
    }

    //用户投诉
    public function actionComplaint()
    {
        $query = \app\models\UserComplaint::find();
        $count = $query->count();
        $pagination = new \yii\data\Pagination(['totalCount'=>$count]);
        $list = $query->with(['linkUser','linkCoverUser'])->offset($pagination->offset)->orderBy('id desc')->limit($pagination->limit)->all();

        return $this->render('complaint',[
            'list'  => $list,
            'pagination' => $pagination
        ]);
    }
}