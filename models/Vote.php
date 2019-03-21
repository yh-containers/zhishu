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
}