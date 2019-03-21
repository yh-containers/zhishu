<?php

namespace app\controllers;


use yii\web\UploadedFile;

class ChatController extends CommonController
{
    public function actionIndex()
    {

        return $this->render('index');
    }


    //会员聊天功能
    public function actionTalk()
    {
        $f_uid = $this->request->get('id');
        $users = [$f_uid,$this->user_id];
        //我要聊天对象的信息
        $chat_obj_info = \app\models\User::find()->where(['in','id',$users])->all();
        $user_info = [];
        foreach ($chat_obj_info as $vo){
            $user_info[$vo['id']] = [
                'id'         =>  $vo['id'],
                'face'       =>  $vo['face'],
                'money'      =>  $vo['money'],
                'type'       =>  $vo['type'],
                'online'     =>  0,//在线状态 0离线 1在线
                'type_name'  =>  \app\models\User::getUserType($vo['type'],'name'),
                'level'      =>  $vo['level'],
                'level_name' =>  \app\models\User::getUserLevel($vo['level'],'name'),
            ];
        }
        return $this->render('talk',[
            'f_uid'=>$f_uid,
            'user_info' => $user_info,
        ]);
    }

    //发送聊天
    public function actionSay()
    {
        //发送的对象
        $rec_uid = (int)$this->request->post('rec_uid',0);

        $type = (int)$this->request->post('type',0); //0文本 1图片
        $content = $this->request->post('content','');
        if($type==1){
            $upload_info = (new UploadController($this->id,$this->module))->actionUpload(true);
            if($upload_info['code']!=1) throw new \yii\base\UserException($upload_info['msg']);
            $content = $upload_info['path'];
        }

        //处理用户聊天顺序
        $users = [$rec_uid,$this->user_id];
        sort($users);

        $model = new \app\models\UserChat();
        $model->uid1    =   $users[0];
        $model->uid2    =   $users[1];
        $model->suid    =   $this->user_id;
        $model->type    =   $type;
        $model->content =   strip_tags(htmlspecialchars_decode($content)); //过滤有所html标签
        $model->save();

        return $this->asJson(['code'=>0,'msg'=>'发送成功']);

    }

    //获取聊天记录
    public function actionRecord()
    {
        //当前页面保存的id
        $record_id = (int)$this->request->post('record_id',0);
        //对象
        $rec_uid = (int)$this->request->post('rec_uid',0);
        //处理用户聊天顺序
        $users = [$rec_uid,$this->user_id];
        sort($users);
        $where = [
            'uid1'    => $users[0],
            'uid2'    => $users[1],
        ];
        $model = \app\models\UserChat::find()->where($where);
        //更改查询位置
        $record_id && $model->andWhere(['>','id',$record_id]);
        $data=[];
        foreach($model->each() as $item){
            $data[]=[
                (string)$item['id'],date('Y-m-d H:i:s',$item['create_time']),(string)$item['suid'],(string)$item['type'],(string)$item['content']
            ];
            $item->is_read = 1;
            $item->read_time = date('Y-m-d H:i:s');
            $item->save();
        }
        return $this->asJson($data);
    }
}
