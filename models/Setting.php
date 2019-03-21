<?php
namespace app\models;

use yii\db\ActiveRecord;

class Setting extends BaseModel
{
    protected $use_create_time=false;

    public static function tableName()
    {
        return '{{%sys_setting}}';
    }

    public static function getContent($type)
    {
        $cache_name = 'setting_'.$type;
        $cache = \Yii::$app->cache;
        $data = $cache->getOrSet($cache_name, function ()use($type) {
            $data = self::findOne($type);
            return $data?$data['content']:'';
        });

        return $data;
    }


    public static function setContent($type,$content)
    {
        //删除缓存
        $cache_name = 'setting_'.$type;
        $cache = \Yii::$app->cache;
        $cache->delete($cache_name);

        $model = new self();
        return $model->updateAll(['content'=>$content],['type'=>$type]);
    }
}