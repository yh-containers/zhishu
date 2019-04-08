<?php

namespace app\controllers;


use yii\web\UploadedFile;

class ChatController extends CommonController
{
    public function actionIndex()
    {
        $type = $this->request->get('type',1);
        return $this->render('index',[
            'type' =>$type
        ]);
    }


    //会员聊天功能
    public function actionTalk()
    {
        $f_uid = $this->request->get('id');
        $users = [$f_uid,$this->user_id];
        //好友状态
        $friend_info = \app\models\UserFriend::find()->where(['uid'=>$this->user_id,'f_uid'=>$f_uid])->one();
        //我要聊天对象的信息
        $users_all = \app\models\User::find()->where(['in','id',$users])->all();
        $user_info = [];
        foreach ($users_all as $vo){
            $user_info[$vo['id']] = [
                'id'         =>  $vo['id'],
                'username'   =>  $vo['username'],
                'face'       =>  $vo['face'],
                'money'      =>  $vo['money'],
                'type'       =>  $vo['type'],
                'online'     =>  $vo->getOnline(),//在线状态 0离线 1在线
                'type_name'  =>  \app\models\User::getUserType($vo['type'],'name'),
                'level'      =>  $vo['level'],
                'level_name' =>  \app\models\User::getUserLevel($vo['level'],'name'),
            ];
        }

        return $this->render('talk',[
            'f_uid'=>$f_uid,
            'friend_info' => $friend_info,
            'user_info' => $user_info,
            'chart_obj_info' => isset($user_info[$f_uid])?$user_info[$f_uid]:'',
        ]);
    }

    //获取好友状态
    public function actionFriendInfo()
    {
        $f_uid = $this->request->get('id');
        //好友状态
        $friend_info = \app\models\UserFriend::find()->where(['uid'=>$this->user_id,'f_uid'=>$f_uid])->one();
        return $this->asJson(['is_know']);
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
        \app\models\UserChat::say($this->user_id,$rec_uid,$content,$type);

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
            if($this->user_id==$item['rec_uid']){
                $item->is_read = 1;
                $item->read_time = date('Y-m-d H:i:s');
                $item->save();
            }
        }
        return $this->asJson($data);
    }
}
