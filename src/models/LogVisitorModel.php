<?php

namespace slavkovrn\logvisitor\models;

use Yii;

/**
 * This is the model class for table "{{%logvisitor}}".
 *
 * @property string $id
 * @property string $ip
 * @property string $time
 * @property string $rfc822
 * @property string $uri
 * @property string $get
 * @property string $post
 * @property string $cookies
 * @property string $session
 * @property string $method
 * @property string $scheme
 * @property string $protocol
 * @property string $port
 * @property string $browser
 * @property string $language
 * @property string $filterIp
 * @property string $filterUri
 * @property string $dateFrom
 * @property string $dateTo
 */
class LogVisitorModel extends \yii\db\ActiveRecord
{
    public $filterIp;
    public $filterUri;
    public $dateFrom;
    public $dateTo;

    public $ip_uri = [];
    public $graphic_id = 'graphic';
    public $graphic_name = '';
    public $graphic_width = 1000;
    public $graphic_height = 200;
    public $graphic_chart = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%logvisitor}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip'], 'string', 'max' => 20],
            [['time'], 'integer'],
            [['rfc822'], 'string', 'max' => 50],
            [['uri'], 'string', 'max' => 256],
            [['get', 'post', 'cookies', 'session', 'browser', 'language'], 'string'],
            [['method', 'scheme', 'protocol', 'port'], 'string', 'max' => 10],
            [['filterIp', 'filterUri'], 'string'],
            [['dateFrom', 'dateTo'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('logvisitor', 'ID'),
            'ip' => Yii::t('logvisitor', 'Ip'),
            'time' => Yii::t('logvisitor', 'Time'),
            'rfc822' => Yii::t('logvisitor', 'Rfc822'),
            'uri' => Yii::t('logvisitor', 'Uri'),
            'get' => Yii::t('logvisitor', 'Get'),
            'post' => Yii::t('logvisitor', 'Post'),
            'cookies' => Yii::t('logvisitor', 'Cookies'),
            'session' => Yii::t('logvisitor', 'Session'),
            'method' => Yii::t('logvisitor', 'Method'),
            'scheme' => Yii::t('logvisitor', 'Scheme'),
            'protocol' => Yii::t('logvisitor', 'Protocol'),
            'port' => Yii::t('logvisitor', 'Port'),
            'browser' => Yii::t('logvisitor', 'Browser'),
            'language' => Yii::t('logvisitor', 'Language'),
            'filterIp' => Yii::t('logvisitor', 'Filter IP'),
            'filterUri' => Yii::t('logvisitor', 'Filter URI'),
            'dateFrom' => Yii::t('logvisitor', 'Date from'),
            'dateTo' => Yii::t('logvisitor', 'Date to'),
        ];
    }
    function filter_Ip($ip_uri,$filterIp)
    {
        $arrFilterIp=explode(',',$filterIp);
        foreach ($arrFilterIp as $key=>$val)
            if (empty($val))
                unset($arrFilterIp[$key]);
        $out=[];
        foreach ($ip_uri as $_ip_uri)
        {
            $good = true;
            foreach ($arrFilterIp as $f_ip)
            {
                $f_ip=trim($f_ip);
                if (preg_match('/^'.$f_ip.'/', $_ip_uri['ip']))
                    $good = false;
            }
            if ($good)
                $out[]=$_ip_uri;
        }
        return $out;
    }

    function filter_Uri($ip_uri,$filterUri)
    {
        $arrFilterUri=explode(',',$filterUri);
        foreach ($arrFilterUri as $key=>$val)
            if (empty($val))
                unset($arrFilterUri[$key]);
        $out=[];
        foreach ($ip_uri as $_ip_uri)
        {
            $good = true;
            foreach ($arrFilterUri as $f_uri)
            {
                $f_uri=trim($f_uri);
                if ($f_uri==='/'){
                    if ($_ip_uri['uri']===$f_uri)
                        $good = false;
                }
                elseif (strpos($_ip_uri['uri'],$f_uri)!==false)
                    $good = false;
            }
            if ($good)
                $out[]=$_ip_uri;
        }
        return $out;
    }

    public function calculateChart()
    {
        $this->load(Yii::$app->request->post());
        $ip=$this->ip;
        $uri=$this->uri;

        $dateFrom=$this->dateFrom;
        $dateFrom_year=substr($dateFrom,0,4);
        $dateFrom_month=substr($dateFrom,5,2);
        $dateFrom_day=substr($dateFrom,8,2);

        $dateTo=$this->dateTo;
        $dateTo_year=substr($dateTo,0,4);
        $dateTo_month=substr($dateTo,5,2);
        $dateTo_day=substr($dateTo,8,2);

        $timeFrom = strtotime(date($dateFrom_year.'-'.$dateFrom_month.'-'.$dateFrom_day.' 00:00:00'));
        $timeTo = strtotime(date($dateTo_year.'-'.$dateTo_month.'-'.$dateTo_day.' 23:59:59'));

        $ip_uri = (new \yii\db\Query())
            ->select(['time','ip','uri'])
            ->from('{{%logvisitor}}')
            ->where('time>'.$timeFrom)
            ->andWhere('time<='.$timeTo)
            ->andWhere('ip="'.$ip.'"')
            ->andWhere('uri="'.$uri.'"')
            ->all();

        if (!empty($filterIp))
            $ip_uri = $this->filterIp($ip_uri,$filterIp);
        if (!empty($filterUri))
            $ip_uri = $this->filterUri($ip_uri,$filterUri);

        $delta=($timeTo-$timeFrom)/20;
        $current=$timeFrom;
        $x[$current]=0;
        while ($current < $timeTo)
        {
            $current+=$delta;
            $x[$current]=0;
        }
        $current=$timeFrom;
        while ($current < $timeTo)
        {
            $current+=$delta;
            foreach ($ip_uri as $ipuri)
            {
                if ($ipuri['time']>($current-$delta) and $ipuri['time']<=$current)
                    $x[$current]++;
            }
        }

        foreach ($x as $key=>$val)
        {
            $xx[date('H:i',$key).'<br/>'.date('m-d',$key)]=$val;
        }

        $this->graphic_chart=[
                $this->ip.' '.$this->uri => $xx
            ];
        $this->graphic_name = $this->ip.' '.$this->uri;
    }

    function calculateIndex()
    {
        $timeFrom = strtotime(date('Y-m-d 00:00:00'));
        $timeTo = strtotime(date('Y-m-d 23:59:59'));
        $dateFrom = date('Y-m-d',$timeFrom);
        $this->dateFrom = $dateFrom;
        $dateTo = date('Y-m-d',$timeTo);
        $this->dateTo = $dateTo;
        $filterIp='';
        $filterUri='';

        if ($this->load(Yii::$app->request->post())) {
            $timeFrom = mktime(0,0,0,substr($this->dateFrom,5,2),substr($this->dateFrom,8,2),substr($this->dateFrom,0,4));
            $timeTo = mktime(23,59,59,substr($this->dateTo,5,2),substr($this->dateTo,8,2),substr($this->dateTo,0,4));
            $filterIp = $this->filterIp;
            $filterUri = $this->filterUri;
        }

        $ip_uri = (new \yii\db\Query())
            ->select(['time','ip','uri'])
            ->from('{{%logvisitor}}')
            ->where('time>'.$timeFrom)
            ->andWhere('time<='.$timeTo)
            ->all();

        if (!empty($filterIp))
            $ip_uri = $this->filter_Ip($ip_uri,$filterIp);
        if (!empty($filterUri))
            $ip_uri = $this->filter_Uri($ip_uri,$filterUri);

        $delta=($timeTo-$timeFrom)/20;
        $current=$timeFrom;
        $x[$current]=0;
        while ($current < $timeTo)
        {
            $current+=$delta;
            $x[$current]=0;
        }

        $current=$timeFrom;
        while ($current < $timeTo)
        {
            $current+=$delta;
            foreach ($ip_uri as $ipuri)
            {
                if ($ipuri['time']>($current-$delta) and $ipuri['time']<=$current)
                    $x[$current]++;
            }
        }

        foreach ($x as $key=>$val)
        {
            $xx[date('H:i',$key).'<br/>'.date('m-d',$key)]=$val;
        }

        $this->graphic_chart=[
            Yii::t('logvisitor','All') => $xx,
        ];

        $ip_uri = (new \yii\db\Query())
            ->select(['id','time','ip','uri','count(ip)'])
            ->from('{{%logvisitor}}')
            ->where('time>'.$timeFrom)
            ->andWhere('time<='.$timeTo)
            ->groupBy(['ip','uri'])
            ->orderBy('ip,uri')
            ->all();

        if (!empty($filterIp))
            $ip_uri = $this->filter_Ip($ip_uri,$filterIp);
        if (!empty($filterUri))
            $ip_uri = $this->filter_Uri($ip_uri,$filterUri);
        $this->ip_uri = $ip_uri;
        $this->graphic_name = Yii::t('logvisitor','Chart');
    }
}
