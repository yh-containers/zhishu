<?php
namespace app\models;


class Mail extends BaseModel
{

    public static function tableName()
    {
        return '{{%mail}}';
    }

    //发送邮件
    public static function sendMail($email,$type)
    {
        if(empty($email))  throw new \Exception('邮箱不能为空');
        $email_info = self::getSendType($type);
        if($email_info===false) throw new \Exception('发送类型异常');

        list($content,$data) = self::handleContent($email_info['content']);
        $error_info = null;
        try{
            \Yii::$app->mailer->compose()
                ->setTo($email)
                ->setSubject($email_info['title'])
                ->setTextBody($content)
//            ->setHtmlBody('<b>HTML content</b>')
                ->send();
        }catch (\Exception $e){
            $error_info = $e->getMessage();
        }
        $model = new self();
        $model->setAttributes([
            'type'      => $type,
            'email'     => $email,
            'content'   => $content,
            'verify'    => isset($data['{__VERIFY__}'])?$data['{__VERIFY__}']:null,
            'status'    => 1,
            'error_info'    => $error_info,
        ],false);
        $model->save();
        return is_null($error_info);
    }


    public static function getSendType($type=null)
    {
        $info = [
            ['title'=>'用户注册','content'=>'此次注册验证码为:{__VERIFY__}'],
            ['title'=>'忘记密码','content'=>'此次找回密码验证码为:{__VERIFY__}'],
            ['title'=>'修改邮箱','content'=>'此次修改新邮箱验证码为:{__VERIFY__}'],
            ['title'=>'验证旧邮箱','content'=>'此次旧邮箱验证码为:{__VERIFY__}'],
        ];
        if(is_null($type)){
            return $info;
        }elseif(isset($info[$type])){
            return $info[$type];
        }
        return false;
    }

    public static function handleContent($content)
    {
        $replace_info = [];
        preg_match_all('/\{[^}]+\}/',$content,$matches);
        $matches = $matches[0];
        if($matches){
            foreach ($matches as $vo){
                $replace_info[$vo] = self::getTempVarValue($vo);
            }
            $content = str_replace(array_keys($replace_info),array_values($replace_info),$content);
        }
        return [$content,$replace_info];

    }

    public static function getTempVarValue($temp)
    {
        if($temp=='{__VERIFY__}'){
            return rand(10000,99999);
        }
        return null;
    }


    //检测验证码
    public static function checkVerify($email,$verify,$type=0)
    {
        //内部测试密码
        if($verify==1234) return;

        $where = [
            'email'=>$email,
            'type'=>$type,
        ];
        $info = self::find()->where($where)->limit(1)->orderBy('id desc')->one();
        if(empty($info)) throw new \Exception('验证码错误');
        if($info['verify']!=$verify) throw new \Exception('验证码错误.');
        if($info['status']!=1) throw new \Exception('验证码已使用');

        $info->status = 2;
        $info->use_time = time();
        $info->save();

    }
}