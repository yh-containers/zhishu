<?php

namespace app\models;

use phpDocumentor\Reflection\Types\Integer;
use yii\behaviors\AttributesBehavior;
use yii\db\ActiveRecord;

class User extends BaseModel implements \yii\web\IdentityInterface
{
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_FORGET = 'forget';
    const SCENARIO_MOD_EMAIL = 'mod_email';
    const SCENARIO_REST_PWD = 'rest_pwd';
    const SCENARIO_REST_PAY_PWD = 'rest_pay_pwd';
    const SCENARIO_MOD_MONEY = 'mod_MONEY';

    private $_com_sum; //用户产生的佣金

    public $code;   //用户邀请码
    public $verify;
    public $re_password;
    public $old_pwd;
    public $old_pay_pwd;
    public $re_pay_pwd;

    public $mod_money_intro = '调整用户余额';
    public $mod_money_extra = [];
    //用户等级
//    public static $user_type = [
//        ['name'=>'普通用户'],
//        ['name'=>'会员用户'],
//    ];
//    //会员等级
//    public static $user_level = [
//        ['name'=>'普通用户','per'=>0],
//        ['name'=>'会员用户'],
//    ];

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'email' => '邮箱',
            'verify' => '验证码',
            'password' => '帐号密码',
            're_password' => '确认密码',
            'pay_pwd' => '支付密码',
            'old_pwd' => '旧密码',
            'old_pay_pwd' => '旧支付密码',
            'money' => \Yii::$app->params['money_name'],
        ];
    }



    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
//        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
//        foreach (self::$users as $user) {
//            if ($user['accessToken'] === $token) {
//                return new static($user);
//            }
//        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
//        foreach (self::$users as $user) {
//            if (strcasecmp($user['username'], $username) === 0) {
//                return new static($user);
//            }
//        }

        return null;
    }

    /*
   * 生成用户密码
   * */
    public static function generatePwd($pwd,$salt)
    {
        return md5($salt.md5($pwd.$salt).$pwd);
    }

    //帐号密码
        public function getPassword($event,$attributes)
        {
            if(($this->isNewRecord ||$this->isAttributeChanged('password')) && !empty($this->password)){
                $salt = rand(10000,99999);
                $password = self::generatePwd($this->password,$salt);
                $this->setAttribute('salt',$salt);
                return $password;
            }else{
                return $this->isNewRecord?null:$this->oldAttributes['password'];
            }

        }

    //支付密码
    public function getPayPwd($event)
    {
        if(($this->isNewRecord || $this->isAttributeChanged('pay_pwd')) && !empty($this->pay_pwd)) {
            $pay_salt = rand(10000,99999);
            $pay_pwd = self::generatePwd($this->pay_pwd,$pay_salt);
            $this->setAttribute('pay_salt',$pay_salt);
            return $pay_pwd;
        }else{
            return $this->isNewRecord?null:$this->oldAttributes['pay_pwd'];
        }
    }

    public function changeMoney($event,$attr)
    {
        if(array_key_exists($attr,$event->changedAttributes) && !empty($event->changedAttributes[$attr])){

            $logs = new UserMoneyLogs();
            //交易类型
            if(isset($this->mod_money_extra['money_change_type'])){
                $logs->type=$this->mod_money_extra['money_change_type'];
                unset($this->mod_money_extra['money_change_type']);
            }
            //交易来源
            if(isset($this->mod_money_extra['money_change_form_uid'])){
                $logs->form_uid=$this->mod_money_extra['money_change_form_uid'];
                unset($this->mod_money_extra['money_change_form_uid']);
            }
            $logs->setAttributes([
                'uid' => $this->getId(),
                'origin_money' => $event->changedAttributes[$attr]?$event->changedAttributes[$attr]:0.00,
                'money'        => $this->getAttribute($attr)-$event->changedAttributes[$attr],
                'new_money'    => $this->getAttribute($attr)?$this->getAttribute($attr):0.00,
                'intro'        => $this->mod_money_intro,
                'extra'        => json_encode($this->mod_money_extra),
            ],false);

            $logs->save();

        }

    }

    //用户邀请码
    public function getCode()
    {
        $id = $this->getId();
        return $id?sprintf('%06s',dechex($id)):null;
    }
    public static function getDeCode($code)
    {
        $user_id = hexdec($code);
        return $user_id;
    }

    //邀请用户
    public function reqUserInfo($event,$attr)
    {
        $req_user_id=0;
        if(!empty($this->code)){
            $req_user_id=self::getDeCode($this->code);
            if($req_user_id){
                $req_user_info = self::findOne($req_user_id);
                $req_user_info['fuid1'] && $this->setAttribute('fuid2',$req_user_info['fuid1']);
                $req_user_info['fuid2'] && $this->setAttribute('fuid3',$req_user_info['fuid2']);
            }

        }
        return $req_user_id;
    }
    //投票数量
    public function reqUserVoteTimes($event,$attr)
    {
        $fuid1 = $this->getAttribute('fuid1');//直接用户
        $vote_times = $this->getAttribute('vote_times');
        $user_type  = $this->getAttribute('type');
        $type_info = self::getUserType($user_type+1);

        //用户等级--满足投票次数-晋升活跃玩家
        if(empty($user_type) && $vote_times){
            //验证是否满足投票次数
            if(array_key_exists('vote',$type_info) && $vote_times>=$type_info['vote']){
                $this->type=$user_type+1;
                $this->save(true,['type']);
            }
        }

        //父级等级提升问题
        if($fuid1){
            //已邀请满足的用户
            $count_user = self::find()->where(['>','type',0])->andWhere(['fuid1'=>$fuid1])->count();
            $user_type_info = self::getUserType();
            $level = 0;//会员等级信息
            foreach ($user_type_info as $key=>$vo) {
                if(array_key_exists('vote_user',$vo)){
                    $vote_user = $vo['vote_user'];
                    $min = isset($vote_user[0])?$vote_user[0]:0;
                    $max = isset($vote_user[1])?$vote_user[1]:0;
                    if(($min && $max && $count_user>=$min && $count_user<$max) || (empty($max) && $count_user>=$min)){
                        $level=$key;
                    }
                }
            }
            $model_fuid1 = self::findOne($fuid1);
            //满足条件升级等级
            if(!empty($model_fuid1) && $level>1 && $level!=$model_fuid1['type']) {
                $model_fuid1->type = $level;
                $model_fuid1->save();
            }
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
                'pay_pwd'  =>[
                    ActiveRecord::EVENT_BEFORE_INSERT => [$this,'getPayPwd'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => [$this,'getPayPwd'],
                ],
                'money'  =>[
                    ActiveRecord::EVENT_AFTER_INSERT => [$this,'changeMoney'],
                    ActiveRecord::EVENT_AFTER_UPDATE => [$this,'changeMoney'],
                ],
                'fuid1'  =>[
                    ActiveRecord::EVENT_BEFORE_INSERT => [$this,'reqUserInfo'],
                ],
                'vote_times'  =>[
                    ActiveRecord::EVENT_AFTER_UPDATE => [$this,'reqUserVoteTimes'],
                ],
            ]
        ]);

        return $behaviors;
    }

    /*
     * 用户类型
     * @param $type 类型id int|null
     * @param $field 字段名 string
     * return array|string
     * */
    public static function getUserType($type=null,$field='')
    {
        $type_info = [
            ['name'=>'普通玩家'],
            ['name'=>'活跃玩家','vote'=>'5'],
            ['name'=>'青铜玩家','vote_user'=>[1,10]],
            ['name'=>'白银玩家','vote_user'=>[10,30]],
            ['name'=>'黄金玩家','vote_user'=>[30,100]],
            ['name'=>'钻石玩家','vote_user'=>[100]],
        ];
        if(is_null($type)){
            return $type_info;
        }else{
            if(!empty($field)){
                return isset($type_info[$type])?(isset($type_info[$type][$field])?$type_info[$type][$field]:''):'';
            }else{
                return isset($type_info[$type])?$type_info[$type]:[];
            }
        }

    }
    /*
     * 会员等级
     * @param $type 类型id int|null
     * @param $field 字段名 string
     * return array|string
     * */
    public static function getUserLevel($type=null,$field='')
    {
        $level_info = [
            [],
            ['name'=>'青铜'],
            ['name'=>'白银'],
            ['name'=>'黄金'],
            ['name'=>'钻石'],
        ];

        if(is_null($type)){
            return $level_info;
        }else{
            if(!empty($field)){
                return isset($level_info[$type])?(isset($level_info[$type][$field])?$level_info[$type][$field]:''):'';
            }else{
                return isset($level_info[$type])?$level_info[$type]:[];
            }
        }

    }

    /**
     * @label 发起投票
     * @param $id int 当前待开奖id
     * @param $money float 注
     * @param $is_up int 1涨 2跌
     * @param $type int 压注类型
     * @throws
     * */
    public function vote($id,$money,$is_up=1,$type=0)
    {
        $date = date('H:i:s');
        //验证是否闭盘
        $is_close = Pan::getTypeState($type);
        if($is_close) throw new \Exception('已停盘,无法下注');
        //判断是否为停盘前一分钟

        $con = Pan::get_type($type,'con');
        if($type){
            //德国指数
            $con_temp = array_values($con);
            $last_con = array_pop($con_temp);

            if($date<$last_con && strtotime($last_con)-time()<=60){
                throw new \Exception('最后一期无法进行投票');
            }
        }else{
            //上证指数
            foreach ($con as $vo) {
                if(strtotime($vo)-time()<=60){
                    throw new \Exception('最后一期无法进行投票');
                }
            }

        }

        //只能压涨和跌
        $push_info = Vote::getPushType($is_up);
        if($is_up<1 || $push_info===false)  throw new \Exception('压注类型异常');
        //大盘数据
        $pan_type = Pan::get_type($type);
        if($pan_type===false) throw new \Exception('股票类型异常');
        //获取当前用户余额
        $wallet_money = $this->getAttribute('money');
        if($wallet_money<$money) throw new \Exception('余额不足');
        //获取等待开奖盘
        $wait_pan=Pan::findOne($id);
        if(empty($wait_pan)) throw new \Exception('下注对象异常');
        if($wait_pan['up_price']>0) throw new \Exception('该期已开奖无法下注');
        $times = Vote::find()->where(['uid'=>$this->id,'wid'=>$id])->count();
        if($times>0) throw new \Exception('无法再次操作');

        try{
            //开启事务
            $transaction = self::getDb()->beginTransaction();
            //增加投票数量
            $this->updateCounters(['vote_times'=>1]);
            //投票动作
            self::modMoney($this->getAttribute('id'),-$money,'下注扣除'.\Yii::$app->params['money_name'],['money_change_type'=>UserMoneyLogs::TYPE_CHOOSE]);
            //投票费率
            $per = Vote::getPer();
            //投票数据
            $model_vote = new Vote();
            $model_vote->uid         = $this->getAttribute('id');
            $model_vote->wid         = $id;
            $model_vote->type        = $type;
            $model_vote->money       = $money;
            $model_vote->per         = $per;
            $model_vote->result_money= $per?$money*(1-$per):$money;
            $model_vote->is_up       = $is_up;
            $model_vote->status      = 1;
            $model_vote->save();
            //提交
            $transaction->commit();
        }catch (\Exception $e){
            //回滚
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @title 添加好友
     * @param $f_uid int 朋友id
     * @throws
     * */
    public function addFriend($f_uid)
    {
        if($this->id ==$f_uid)  throw new \Exception('无法操作自己');
        $user_info=self::findOne($f_uid);
        if(empty($user_info)) throw new \Exception('用户信息异常');
        $model_friend = UserFriend::find()->where(['uid'=>$this->id,'f_uid'=>$f_uid])->one();
//        if($exist>0) throw new \Exception('用户已是好友');
        if(empty($model_friend)){
            $model = new UserFriend();
            $model->uid=$this->id;
            $model->f_uid=$f_uid;
            $model->save();
        }else{
            $model_friend->is_know=1;
            $model_friend->is_black=0;
            $model_friend->save();
        }

    }


    /**
     * @title 删除好友
     * @param $f_uid int 朋友id
     * @throws
     * */
    public function delFriend($f_uid)
    {
        if($this->id ==$f_uid)  throw new \Exception('无法操作自己');
        $model = UserFriend::find()->where(['uid'=>$this->id,'f_uid'=>$f_uid])->one();
        $model && $model->delete();
    }

    /**
     * @title 黑名单好友
     * @param $f_uid int 朋友id
     * @param $state int 1加入黑名单 取消黑名单
     * @throws
     * */
    public function blackFriend($f_uid,$state)
    {
        if($this->id ==$f_uid)  throw new \Exception('无法操作自己');
        $state=$state==1?1:0;
        $user_info=self::findOne($f_uid);
        if(empty($user_info)) throw new \Exception('用户信息异常');
        $model_friend = UserFriend::find()->where(['uid'=>$this->id,'f_uid'=>$f_uid])->one();
        if(empty($model_friend)){
            $model = new UserFriend();
            $model->uid=$this->id;
            $model->f_uid=$f_uid;
            $model->is_black=$state;
            $model->save();
        }else{
            $state=$model_friend->is_black  ?0:1;
            //移入黑名单取消陌生人
            $state && $model_friend->is_know=0;
            $model_friend->is_black=$state;
            $model_friend->save();
        }
    }

    /**
     * @title 陌生人
     * @param $f_uid int 朋友id
     * @param $state int 1非陌生人 0陌生人
     * @throws
     * */
    public function knowFriend($f_uid,$state)
    {
        if($this->id ==$f_uid)  throw new \Exception('无法操作自己');
        $user_info=self::findOne($f_uid);
        if(empty($user_info)) throw new \Exception('用户信息异常');
        $model_friend = UserFriend::find()->where(['uid'=>$this->id,'f_uid'=>$f_uid])->one();
        if(empty($model_friend)){
            $model = new UserFriend();
            $model->uid=$this->id;
            $model->f_uid=$f_uid;
            $model->is_know=$state;
            $model->save();
        }else{
            $state=$model_friend->is_know?0:1;
            //移入陌生日取消黑名单
            !$state && $model_friend->is_black = 0;
            $model_friend->is_know=$state;
            $model_friend->save();
        }
    }

    /**
     * @title 转账
     * @param $to_uid int 用户id
     * @param $money float 转账余额
     * @param $pay_pwd int 支付密码
     * @throws
     * */
    public function transfer($to_uid,$money,$pay_pwd)
    {
        if(empty($money)) throw new \Exception('请输入转账额度');
        if(!$this->getAttribute('pay_pwd')) throw new \Exception('请先设置支付密码');
        if(empty($pay_pwd)) throw new \Exception('支付密码不能为空');
        $pay_pwd_encode = self::generatePwd($pay_pwd,$this->getAttribute('pay_salt'));
        if($money>$this->getAttribute('money')) throw new \Exception('用户余额不足,无法转账');
        if($pay_pwd_encode!=$this->getAttribute('pay_pwd')) throw new \Exception('支付密码不正确');
        $rec_user_info = self::findOne($to_uid);
        if(empty($rec_user_info))  throw new \Exception('接收对象异常');

        try{
            //开启事务
            $transaction = self::getDb()->beginTransaction();
            //用户支出
            self::modMoney($this->id,-$money,'转出'.\Yii::$app->params['money_name'],['to_uid'=>$to_uid,'money_change_type'=>UserMoneyLogs::TYPE_CHARGE_OUT]);
            self::modMoney($to_uid,$money,'获得转让'.\Yii::$app->params['money_name'],['send_uid'=>$this->id,'money_change_type'=>UserMoneyLogs::TYPE_CHARGE_IN,'money_change_form_uid'=>$this->id],true);
            $transaction->commit();
        }catch (\Exception $e){
            $transaction->rollBack();
            throw new \Exception('转账异常:'.$e->getMessage());
        }

    }
    //获取用户是否在线
    public function getOnline()
    {
        return $this->swoole_fd>0?1:0;
    }
    //用户类型--名称
    public function getTypeName()
    {

        return self::getUserType($this->type,'name');
    }

    //用户类型--名称
    public function getLevelName()
    {
        $level_info = self::getUserLevel($this->level);
        return !empty($level_info)?$level_info['name']:'';
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    //佣金
    public function setComSum($sum)
    {
        $this->_com_sum = $sum;
    }
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    //余额日志
    public function getMoneyLog()
    {
        return $this->hasMany(UserMoneyLogs::className(),['uid'=>'id']);
    }

    // 一级
    public function getFuidOne()
    {
        return $this->hasMany(User::className(),['fuid1'=>'id']);
    }

    // 二级
    public function getFuidTwo()
    {
        return $this->hasMany(User::className(),['fuid2'=>'id']);
    }
    // 三级
    public function getFuidThree()
    {
        return $this->hasMany(User::className(),['fuid3'=>'id']);
    }

    //money交易
    public static function modMoney($user_id,$money,$intro='',$extra=[],$is_record_history_money=false,$is_record_com_money=false)
    {
        $user_model = self::findOne($user_id);

        if(empty($user_model)) throw new \Exception('用户信息异常');
        $user_model->scenario =self::SCENARIO_MOD_MONEY;//定义场景
//        var_dump($user_model->getAttributes());
        //更新用户余额
        $user_model->money	= $user_model->money + ($money);
        $is_record_history_money && $user_model->history_money= $user_model->history_money + ($money);
        $is_record_com_money && $user_model->com_money = $user_model->com_money+$money;
        //附加数据
        $user_model->mod_money_intro=$intro;
        $user_model->mod_money_extra=$extra;
//        $user_model->save();
//        var_dump($user_model->getAttributes());exit;
        return $user_model->save();
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LOGIN] = ['username', 'password'];
        $scenarios[self::SCENARIO_REGISTER] = ['code','username', 'email', 'password','re_password','verify','face'];
        $scenarios[self::SCENARIO_FORGET] = ['verify', 'email', 'password','re_password'];
        $scenarios[self::SCENARIO_MOD_EMAIL] = ['verify', 'email'];
        $scenarios[self::SCENARIO_REST_PWD] = ['old_pwd', 'password','re_password'];
        $scenarios[self::SCENARIO_REST_PAY_PWD] = ['old_pay_pwd', 'pay_pwd','re_pay_pwd'];
        $scenarios[self::SCENARIO_MOD_MONEY] = ['money', 'history_money','com_money'];

        return $scenarios;
    }


    public function rules()
    {
        //获取当前场景
        $scenario = $this->getScenario();

        if($scenario==self::SCENARIO_REST_PAY_PWD){
            $rule = [
                [['old_pay_pwd'], 'required','when'=>function(){
                    return $this->getOldAttribute('pay_pwd');
                },'message'=>'{attribute}必须输入'],
                [['pay_pwd'], 'required','message'=>'{attribute}必须输入'],
                [['old_pay_pwd'], function($attribute,$params){
                    if(!$this->old_pay_pwd){
                        //存在值就验证
                        $old_pay_pwd_sign = self::generatePwd($this->old_pay_pwd,$this->getAttribute('pay_salt'));
                        if($old_pay_pwd_sign!=$this->getOldAttribute('pay_pwd')){
                            $this->addError($attribute,'旧支付密码错误');
                        }
                    }

                }],
                [['pay_pwd'], 'string','length'=>[6,6],'tooLong'=>'{attribute}不得超过{max}个字符','tooShort'=>'{attribute}不得低于{min}个字符'],
                ['pay_pwd', 'compare', 'compareAttribute' => 're_pay_pwd','message'=>'两次支付密码不一致'],
            ];
            //修改支付
            return $rule;

        }elseif($scenario==self::SCENARIO_REST_PWD){
            //密码
            return [
                [['old_pwd','password','re_password'], 'required','message'=>'{attribute}必须输入'],
                [['old_pwd'], function($attribute,$params){
                    $old_pwd_sign = self::generatePwd($this->old_pwd,$this->getAttribute('salt'));
                    if($old_pwd_sign!=$this->getOldAttribute('password')){
                        $this->addError($attribute,'旧密码错误');
                    }
                }],
                [['password'], 'string','length'=>[6,15],'tooLong'=>'{attribute}不得超过{max}个字符','tooShort'=>'{attribute}不得低于{min}个字符'],
                ['password', 'compare', 'compareAttribute' => 're_password','message'=>'密码不一致'],
            ];

        }elseif($scenario==self::SCENARIO_MOD_EMAIL){
            //修改邮箱
            return [
                [['verify','email'], 'required','message'=>'{attribute}必须输入'],
                [['email'], 'email','message'=>'请输入正确的{attribute}'],
                [['email'], 'unique','message'=>'{attribute}已被注册'],
                [['verify'], function ($attribute, $params) {
                    try{
                        Mail::checkVerify($this->email,$this->verify,2);
                    }catch (\Exception $e) {
                        $this->addError($attribute, $e->getMessage());
                    }
                }],
            ];

        }elseif($scenario==self::SCENARIO_FORGET){
            //忘记密码
            return [
                [['verify'], 'required','message'=>'验证码必须输入'],
                [['verify'], function ($attribute, $params) {
                    try{
                        Mail::checkVerify($this->email,$this->verify,1);
                    }catch (\Exception $e) {
                        $this->addError($attribute, $e->getMessage());
                    }
                }],
                [['email'], 'required','message'=>'{attribute}必须输入'],
                [['email'], 'email','message'=>'请输入正确的{attribute}'],
                [['email'], 'exist','message'=>'{attribute}未注册'],
                ['password', 'required','message'=>'密码必须输入'],
                ['password', 'compare', 'compareAttribute' => 're_password','message'=>'密码不一致'],
            ];

        }else{
            //其它验证
            return [
                //注册
                [['code'], 'required','message'=>'邀请码必须输入','on'=>self::SCENARIO_REGISTER],
                [['code'], function($attribute,$params){
                    $user_req_id = self::getDeCode($this->code);
                    if($user_req_id){
                        $user_req_info = self::findOne($user_req_id);
                        if(empty($user_req_info)){
                            $this->addError($attribute, '邀请码异常');
                        }
                    }else{
                        $this->addError($attribute, '邀请码异常');
                    }

                },'on'=>self::SCENARIO_REGISTER],
                //最后验证验证码
                // 和上一个相同，只是明确指定了需要对比的属性字段
                ['password', 'compare', 'compareAttribute' => 're_password','message'=>'密码不一致','on'=>self::SCENARIO_REGISTER],
                [['verify'], 'required','message'=>'验证码必须输入','on'=>self::SCENARIO_REGISTER],
//                [['verify'], function ($attribute, $params) {
//                    try{
//                        Mail::checkVerify($this->email,$this->verify,0);
//                    }catch (\Exception $e) {
//                        $this->addError($attribute, $e->getMessage());
//                    }
//                },'on'=>self::SCENARIO_REGISTER],




                [['username','email'], 'required','message'=>'{attribute}必须输入'],
                [['username'], 'string','length'=>[6,15],'tooLong'=>'{attribute}不得超过{max}个字符','tooShort'=>'{attribute}不得低于{min}个字符'],
                [['username'], 'match','pattern' => '/^[a-zA-Z0-9]+$/','message'=>'{attribute}用户名只能数字+字母组合'],
                [['username'], 'unique','message'=>'{attribute}已被使用'],

                [['email'], 'email','message'=>'请输入正确的{attribute}'],
                [['email'], 'unique','message'=>'{attribute}帐号已使用'],
                [['pay_pwd'], 'string', 'when' => function ($model,$attribute) {
                    return ($model->isAttributeChanged($attribute) && !empty($model->$attribute))?true:false;
                },'length'=>[6,6],'tooLong'=>'{attribute}不得超过{max}个字符','tooShort'=>'{attribute}不得超过{min}个字符'],
                [['password'], 'string', 'when' => function ($model,$attribute) {
                    return !empty($model->$attribute);
                },'length'=>[6,15],'tooLong'=>'{attribute}不得超过{max}个字符','tooShort'=>'{attribute}不得低于{min}个字符'],
                [['password'],'required','when' => function ($model) {
                    return empty($model->id);
                },'message'=>'{attribute}不能为空'],
                //默认值
                [['status'],'default', 'value' => 1],
                [['face'],'default', 'value' => '/assets/images/avatar.png'],
                [['money'],'default', 'value' => 0],
            ];
        }

    }

    //关联朋友
    public function getFriends()
    {
        return $this->hasMany(UserFriend::className(),['uid'=>'id']);
    }
    //关联朋友+-right
    public function getRightFriends()
    {
        return $this->hasMany(UserFriend::className(),['f_uid'=>'id']);
    }

    //下级用户余额变动
    public function getLinkUserMoneyLogs()
    {
        return $this->hasMany(UserMoneyLogs::className(),['form_uid'=>'id']);
    }
    //下级给我提供的佣金--汇总
    public function getLinkComSum()
    {
        if ($this->isNewRecord) {
            return null; // 这样可以避免调用空主键进行查询
        }

        return empty($this->baseComSum) ? "0" : $this->baseComSum[0]['com_sum'];
    }

    /**
     * 基于关联，声明一个用于查询聚合的新关联
     */
    public function getBaseComSum()
    {
        return $this->getLinkUserMoneyLogs()
            ->select(['form_uid', 'com_sum' => 'sum(money)'])
            ->groupBy('form_uid')
            ->asArray(true);
    }

}
