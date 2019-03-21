<?php
namespace app\models;


use yii\behaviors\AttributesBehavior;
use yii\db\ActiveRecord;

class Manage extends BaseModel
{
    protected $_password;

    public static function tableName()
    {
        return '{{%sys_manage}}';
    }

    public function attributeLabels()
    {
        return [
            'name' => '用户名',
            'account' => '帐号',
            'password' => '密码',
        ];
    }


    public function getPassword($event)
    {
        if($this->isAttributeChanged('password') && !empty($this->password)){
            $salt = rand(10000,99999);
            $password = self::generatePwd($this->password,$salt);
            $this->setAttribute('salt',$salt);
            return $password;
        }else{
            return $this->oldAttributes['password'];
        }

    }
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        array_push($behaviors,[
            'class' => AttributesBehavior::className(),
            'attributes' =>  [
                'password'  =>[
                    ActiveRecord::EVENT_BEFORE_INSERT => [$this,'getPassword'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => [$this,'getPassword'],
                ],
            ]
        ]);

        return $behaviors;
    }

    public function rules()
    {
        return [
            [['name','account'], 'required','message'=>'{attribute}必须输入'],
            [['name'], 'string','length'=>[2,15],'tooLong'=>'{attribute}不得超过{max}个字符','tooShort'=>'{attribute}不得低于{min}个字符'],
            [['account'], 'string','length'=>[4,15],'tooLong'=>'{attribute}不得超过{max}个字符','tooShort'=>'{attribute}不得低于{min}个字符'],
            [['account'], 'unique','message'=>'{attribute}帐号已使用'],
            [['password'], 'string', 'when' => function ($model,$attribute) {
                return empty($model->$attribute);
            },'length'=>[6,15],'tooLong'=>'{attribute}不得超过{max}个字符','tooShort'=>'{attribute}不得低于{min}个字符'],
            [['password'],'required','when' => function ($model) {
                return empty($model->id);
            },'message'=>'{attribute}不能为空'],
            //默认值
            [['status'],'default', 'value' => 1]
        ];
    }

    /*
   * 生成用户密码
   * */
    public static function generatePwd($pwd,$salt)
    {
        return md5($salt.md5($pwd.$salt).$pwd);
    }
}