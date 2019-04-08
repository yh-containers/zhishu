<?php
namespace app\models;


class UserChat extends BaseModel
{

    public static function tableName()
    {
        return '{{%user_chat}}';
    }


    public static function say($suid,$rec_uid,$content,$type=0)
    {
        $exist_chat_obj = UserFriend::find()->where(['uid'=>$suid,'f_uid'=>$rec_uid])->count();
        if(empty($exist_chat_obj)){
            //添加--陌生人
            $user_info = User::findOne($suid);
            !empty($user_info) && $user_info->knowFriend($rec_uid,0);
            //我添加为它的--陌生人
            $rec_user_info = User::findOne($rec_uid);
            !empty($rec_user_info) && $rec_user_info->knowFriend($suid,0);
        }

        $model = new self();
        //处理用户聊天顺序
        $users = [$rec_uid,$suid];
        sort($users);

        $model->uid1    =   $users[0];
        $model->uid2    =   $users[1];
        $model->suid    =   $suid;
        $model->rec_uid =   $rec_uid;
        $model->type    =   $type;
        $model->content =   strip_tags(htmlspecialchars_decode($content)); //过滤有所html标签
        $model->save();
    }

}