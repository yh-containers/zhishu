<?php
namespace app\models;

class Vote extends BaseModel
{
    public static function tableName()
    {
        return '{{%vote}}';
    }


    /**
     * 获取压盘类型
     * */
    public static function getPushType($type=null)
    {
        $push_type = ['平','涨','跌'];
        if(is_null($type)){
            return $push_type;
        }else{
            if(isset($push_type[$type])){
                return $push_type[$type];
            }
            return false;
        }
    }

    //下注方案
    public static function getVoteUp($type=null)
    {
        $data = ['未知','涨','跌'];
        if(is_null($data)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }
    }
    //开奖状态
    public static function getAwardState($type=null)
    {
        $data = ['未开奖','涨','跌'];
        if(is_null($data)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }
    }
    //开奖状态
    public static function getStatus($type=null)
    {
        $data = ['未知','创建','已开奖'];
        if(is_null($data)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }
    }
    //获胜状态
    public static function getIsWin($type=null)
    {
        $data = ['未知','赢','输'];
        if(is_null($data)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }
    }

    /*
     * 获取交易费率
     * */
    public static function getPer()
    {
        $content = Setting::getContent('normal');
        $content = $content?json_decode($content,true):[];
        $per = isset($content['per'])?$content['per']:0;
        return $per;
    }

    /*
     * 下注用户信息
     * */
    public function getlinkUser()
    {
        return $this->hasOne(User::className(),['id'=>'uid']);
    }
}