<?php
namespace app\models;

use yii\behaviors\AttributeBehavior;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Pan extends BaseModel
{
    const DEFAULT_FLASH_SECOND=60;
    protected $_watch;
    public $y_count=0;
    public $up_money_total=0;
    public $down_money_total=0;
    /*
     * 查看观察时间
     * */
    public function getWatch()
    {
        return $this->_watch?$this->_watch:date('Y-m-d');
    }
    /*
     * 查看观察时间
     * */
    public function setWatch($value)
    {
        $this->_watch=$value;
    }

    public static function tableName()
    {
        return '{{%pan}}';
    }
    //获取当前
    public static function getCachePanData($type)
    {
        $open_data = \Yii::$app->cache->get('open_pan_data_'.$type);
        $close_data = \Yii::$app->cache->get('close_pan_data_'.$type);
        return [$open_data?$open_data:[],$close_data?$close_data:[]];
    }
    //获取当前
    public static function getLastOpenSecond($type)
    {
        //当前小时分
        $c_h_i_s = date('H:i:s');
        $c_h_i = substr($c_h_i_s,0,5);
        $last_open_time = \Yii::$app->cache->get('last_open_time'.$type);
        $last_open_time_m = substr($last_open_time,0,5);
        if($last_open_time_m!=$c_h_i){
            //下一分钟时间戳
            $last_open_time_m = substr($c_h_i_s,0,5);
            $last_open_time=$c_h_i_s;
        }else{
            //下一分钟时间戳
            $last_open_time_m = date('H:i',strtotime('+1 minute',strtotime(date('Y-m-d ').$last_open_time_m)));
            $last_open_time=$c_h_i_s;
        }

//        var_dump($last_open_time_m);
        $last_next_time_m = strtotime(date('Y-m-d').' '.$last_open_time_m);
//        var_dump(date('Y-m-d H:i:s',$last_next_time_m));
        //下一次开奖剩余时间
//        var_dump(date('Y-m-d H:i:s',strtotime(date('Y-m-d ').$last_open_time)));exit;
        $open_next_second = $last_next_time_m - strtotime(date('Y-m-d ').$last_open_time);
        return ($open_next_second<=0||$open_next_second>60)?self::DEFAULT_FLASH_SECOND:$open_next_second;
    }

    //缓存数据
    public function recordCacheData($event)
    {
        //缓存数据
        \Yii::$app->cache->set('open_pan_data_'.$this->type,[$this->time,$this->current_price]);
        \Yii::$app->cache->set('close_pan_data_'.$this->type,[$this->up_time,$this->up_price]);
        //最后一次开盘时间
        \Yii::$app->cache->set('last_open_time'.$this->type,$this->up_time);
    }

    //处理交易数据
    public function handleVote($event)
    {
        //是否开奖
        if($this->getAttribute('up_date')){
//            var_dump($this->getAttributes());
            //当前开奖id
            $id = $this->getAttribute('id');
            //开奖结果 1涨 2跌
            $compare = $this->getAttribute('compare');
            //查询投票信息--待开奖的数据
            $model = Vote::find()->where(['wid'=>$id,'status'=>1]);
            //查询投票汇总数据
            $vote_result = (new \yii\db\Query())
                ->select(['total_money'=>'sum(money)','total_result_money'=>'sum(result_money)','total_num'=>'count(*)','is_up'])
                ->from(Vote::tableName())
                ->where(['wid'=>$id,'status'=>1])
                ->groupBy(['is_up'])
                ->all();
            //统计 金额/人数
            $win_money = $win_num = $win_result_money = $lose_money = $lose_result_money = $lose_num = 0;
            foreach ($vote_result as $vo){
                if($vo['is_up']==1){
                    //win
                    $win_money = $vo['total_money'];//下压额度
                    $win_result_money = $vo['total_result_money']; //扣除手续费的额度
                    $win_num = $vo['total_num'];
                }elseif($vo['is_up']==2){
                    $lose_money = $vo['total_money'];
                    $lose_result_money = $vo['total_result_money'];
                    $lose_num = $vo['total_num'];
                }
            }
            //分的钱 涨win拿压跌的钱 跌win拿压涨的钱
            $award_money = $compare==1?$lose_result_money:$win_result_money;
            //分钱的人数 涨win就那压涨的所有人数 跌win拿压跌的总人数
//        $award_num   =  $compare==1?$win_num:$lose_num;
            //统计每个用户产生了多了费用--平台手续费
            //开启事务
            $transaction = self::getDb()->beginTransaction();
            try{

                $user_vote_info = [];
                foreach ($model->each() as $item){
                    $get_money = 0;

                    if($compare==3){
                        //平
                        User::modMoney($item['uid'],$item['money'],'返还',['id'=>$item['id'],'money_change_type'=>UserMoneyLogs::TYPE_BACK],true);
                        $item->award_state==4; //默认 4返还
                    }else{
                        $award_state = $compare==1 ? 1 : 2; //1涨 2跌
                        $is_win = $award_state==$item['is_up']?1:2;
                        $item->is_win = $is_win;
                        //开奖
                        $item->award_state = $award_state;  //1涨 2跌
                        if(empty($win_num) || empty($lose_num)){
                            if($is_win){ //获胜返回
                                $item->award_state = 4;  //默认 4返还
                                User::modMoney($item['uid'],$item['money'],'返还',['id'=>$item['id'],'money_change_type'=>UserMoneyLogs::TYPE_BACK],true);
                            }
                        }else{

                            $charge = $item['money']-$item['result_money']; //平台手续费
                            if(array_key_exists($item['uid'],$user_vote_info)){
                                $user_vote_info[$item['uid']] += $charge;
                            }else{
                                $user_vote_info[$item['uid']] = $charge;
                            }



                            if($item->is_up==1){
                                //涨赢了
                                if($item->is_win==1 && $win_result_money>0){
                                    $get_money = intval(($item->result_money/$win_result_money*$award_money)*100)/100;
                                }
                            }elseif ($item->is_up==2){
                                //跌
                                if($item->is_win==1 && $lose_result_money>0){
                                    $get_money = intval(($item->result_money/$lose_result_money*$award_money)*100)/100;
                                }
                            }
                        }
                    }
                    $item->get_money = $get_money;//奖金
                    $item->status = 2;//已开奖状态
                    $item->open_time = date('Y-m-d H:i:s');
                    $item->save();
                    //获胜 获得 压注金额(扣手续费)+奖励金额
                    $get_money > 0 && User::modMoney($item->uid,($item->result_money+$get_money),'下注获胜',['money_change_type'=>UserMoneyLogs::TYPE_CHOOSE_WIN],true);

                }

                //统计手续费问题
                $commission_money = [];
                foreach ($user_vote_info as $key=>$vo) {
                    //查询用户信息
                    $f_user_model = User::findOne($key);
                    if($f_user_model['fuid1']){
                        $per = self::commissionMoney(0);
                        $money = $vo*$per;//获得比例
                        $u_l_f_key = $f_user_model['fuid1'].'_'.$key;
                        $commission_money[$u_l_f_key] = ['money'=>$money,'extra'=>['com'=>$vo,'per'=>$per]];


                    }
                    if($f_user_model['fuid2']){
                        $per = self::commissionMoney(1);
                        $money = $vo*$per;//获得比例
                        $u_l_f_key = $f_user_model['fuid2'].'_'.$key;
                        $commission_money[$u_l_f_key] = ['money'=>$money,'extra'=>['com'=>$vo,'per'=>$per]];

                    }
                    if($f_user_model['fuid3']){
                        $per = self::commissionMoney(2);
                        $money = $vo*$per;//获得比例
                        $u_l_f_key = $f_user_model['fuid2'].'_'.$key;
                        $commission_money[$u_l_f_key] = ['money'=>$money,'extra'=>['com'=>$vo,'per'=>$per]];
                    }
                }

                foreach ($commission_money as $uid=>$info){
                    $arr = explode('_',$uid);
                    $rec_uid  = isset($arr[0])?$arr[0]:0;  //接收则
                    $form_uid = isset($arr[1])?$arr[1]:0;  //来源者
                    $money = isset($info['money'])?$info['money']:0;
                    $extra = isset($info['extra'])?$info['extra']:[];
                    $extra = array_merge($extra,['money_change_type'=>UserMoneyLogs::TYPE_COMMISSION,'money_change_form_uid'=>$form_uid]);
                    $money && User::modMoney($rec_uid,$money,'佣金获取',$extra,true,true);
                }
                $transaction->commit();
            }catch (\Exception $e){
                $transaction->rollBack();
                \Yii::info('分佣异常'.$e->getMessage());
//                throw new \Exception('操作异常'.$e->getMessage());
            }

        }

    }
    public static function commissionMoney($index=null)
    {
        $per = [0.5,0.15,0.05];
        return is_null($index)?$per:(isset($per[$index])?$per[$index]:0);
    }
    /**
     * 自动添加时间戳，序列化参数
     * @return array
     */
    public function behaviors()
    {
        $behaviors[]=[
            'class' => AttributesBehavior::className(),
            'attributes' =>  [
                'up_price'  =>[
                    ActiveRecord::EVENT_AFTER_UPDATE => [$this,'recordCacheData'],
                ],
                'up_date'  =>[
                    ActiveRecord::EVENT_AFTER_UPDATE => [$this,'handleVote'],
                ],
            ],
        ];


        return $behaviors;
    }
    /**
     * @description  获取是否还在开盘状态
     *
     * */
    public static function getTypeState($type=0)
    {
        $is_close = 1; //1bi 0open
        $current_date_time = date('H:i:s');
        $con = self::get_type($type,'con');
        foreach ($con as $key=>$vo) {
            if($current_date_time<$vo && $current_date_time>$key) {
                $is_close=0;
                break;
            }
        }
        return $is_close;

    }

    /**
     * 获取类型
     * */
    public static function get_type($type=null,$fields='')
    {
        $data = [
            ['name'=>'上证指数','url'=>'http://hq.sinajs.cn/list=sh000001','con'=>['09:00:00'=>'11:30:00','14:00:00'=>'15:00:00']],
            ['name'=>'德国','url'=>'','con'=>['16:00:00'=>'23:59:59','00:00:00'=>'00:30:00']],
        ];
        if(is_null($type)){
            return $data;
        }else{
            if(isset($data[$type])){
                if(isset($data[$type][$fields])){
                    return $data[$type][$fields];
                }else{
                    return $data[$type];
                }
            }
            return false;
        }
    }

    /**
     * 获取盘数据
     * 初始数据/最新数据
     * @param $type int 查询类型
     * @param $next_data bool
     * @return array
     * */
    public function getPanData($type=0,$next_data = true)
    {
//        $fnc = ['getSHData'];
        $pool=[];
        $need_field = ['time'=>'','today_price'=>'','current_price'=>'','down_price'=>'','top_price'=>'','compare'=>0];
        if(!$next_data) {
            //初始化页面数据
            $data = self::find()->asArray()->where(['type'=>$type,'date'=>$this->watch])->orderBy('id desc')->limit(15)->all();
            foreach ($data as $vo){
                array_unshift($pool,self::handleNeedData($need_field,$vo));
            }
        }else{
            $data = self::find()->asArray()->where(['type'=>$type,'date'=>$this->watch])->orderBy('id desc')->limit(1)->one();
            $pool = $data?self::handleNeedData($need_field,$data):[];
        }

//        if(isset($fnc[$type])){
//            $fnc = $fnc[$type];
//            $data = $this->$fnc($need_field);
//            if($next_data) return $data?$data:[];
//        }

        return $pool;



}


    /*
     * 获取对应接口数据
     * 0：”大秦铁路”，股票名字；
        1：”27.55″，今日开盘价；
        2：”27.25″，昨日收盘价；
        3：”26.91″，当前价格；
        4：”27.55″，今日最高价；
        5：”26.20″，今日最低价；
        6：”26.91″，竞买价，即“买一”报价；
        7：”26.92″，竞卖价，即“卖一”报价；
        8：”22114263″，成交的股票数，由于股票交易以一百股为基本单位，所以在使用时，通常把该值除以一百；
        9：”589824680″，成交金额，单位为“元”，为了一目了然，通常以“万元”为成交金额的单位，所以通常把该值除以一万；
        10：”4695″，“买一”申请4695股，即47手；
        11：”26.91″，“买一”报价；
        12：”57590″，“买二”
        13：”26.90″，“买二”
        14：”14700″，“买三”
        15：”26.89″，“买三”
        16：”14300″，“买四”
        17：”26.88″，“买四”
        18：”15100″，“买五”
        19：”26.87″，“买五”
        20：”3100″，“卖一”申报3100股，即31手；
        21：”26.92″，“卖一”报价
        (22, 23), (24, 25), (26,27), (28, 29)分别为“卖二”至“卖四的情况”
        30：”2008-01-11″，日期；
        31：”15:05:32″，时间；
     * 上证指数
     * */
    public  function getSHData(array $need_field)
    {
        $url = self::get_type(0,'url');

        $content = file_get_contents($url);
        $data = $content?explode(',',$content):'';
        $state=false;
        if($data){
            //当前对象记录时间
            $old_time = $this->getAttribute('time');
            if($this->getAttribute('id') && substr($old_time,0,5)!=substr($data[31],0,5)){

                //说明有数据--更新此次同步数据
                $this->compare=$data[3]>$this->current_price?1:0;//当前价格
                $this->up_date=$data[30];
                $this->up_time=$data[31];
                $this->up_price=$data[3];//当前价格
                $this->save();

            }
            if(substr($old_time,0,5) != substr($data[31],0,5)){
                $model = new self();
                $model->attributes = [
                    'type' => 0,
                    'today_price' => $data[1],
                    'yesterday_price' => $data[2],
                    'current_price' => $data[3],
                    'top_price' => $data[4],
                    'down_price' => $data[5],
                    'tran_num' => $data[8],
                    'tran_money' => $data[9],
                    'date' => $data[30],
                    'content' => iconv('GB2312', 'UTF-8', substr($content,0,-2)),
                    'time' => $data[31],
                ];
                $model->save();
                return self::handleNeedData($need_field,$model->getAttributes());
            }

        }


        return $state;
    }


    /*
     * 状态
     * */
    public static function getCompareInfo($type=null)
    {
        $data = ['待开奖','涨','跌','平'];
        if(is_null($type)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }


    }

//    public function rules()
//    {
//        return [[['type','today_price','yesterday_price','current_price','top_price','down_price','tran_num','tran_money','date','time','content','compare'],'safe']];
//    }

    public static function handleNeedData($need_fields,$arr)
    {
        $result=[];
        foreach($need_fields as $key=>$vo){
            $result[] = isset($arr[$key])?$arr[$key]:$vo;
        }
        return $result;
    }

    /*
     * 数据关联
     * */
    public function getLinkVote()
    {
        return $this->hasMany(Vote::className(),['wid'=>'id']);
    }

}
