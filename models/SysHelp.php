<?php

namespace app\models;

use yii\behaviors\AttributesBehavior;
use yii\db\ActiveRecord;

class SysHelp extends BaseModel
{

    public static function tableName()
    {
        return '{{%sys_help}}';
    }

    public function attributeLabels()
    {
        return [
            'title' => '标题',
            'content' => '内容',
            'sort' => '排序',
        ];
    }


    public function rules()
    {
        $rules = [
                [['title','content','sort'],'required','message'=>'{attribute}不能为空'],
                //默认值
                [['sort'],'default', 'value' => 100],
                [['status'],'default', 'value' => 1]
            ];
        return $rules;
    }
}
