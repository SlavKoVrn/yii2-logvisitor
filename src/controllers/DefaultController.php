<?php

namespace slavkovrn\logvisitor\controllers;

use Yii;
use yii\web\Controller;
use slavkovrn\logvisitor\models\LogVisitorModel;
use slavkovrn\logvisitor\models\LogVisitorForm;

/**
 * Default controller for the `LogVisitorModule` module
 */
class DefaultController extends Controller
{
    public $graphic_id = 'graphic';
    public $graphic_width = 1000;
    public $graphic_height = 200;

    public function actionChart()
    {
        $ip=Yii::$app->request->post('ip');
        $uri=Yii::$app->request->post('uri');
        $dateFrom=Yii::$app->request->post('dateFrom');
        $dateFrom_year=substr($dateFrom,0,4);
        $dateFrom_month=substr($dateFrom,5,2);
        $dateFrom_day=substr($dateFrom,8,2);

        $dateTo=Yii::$app->request->post('dateTo');
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
            $xx[date('H:i',$key)]=$val;
        }

        $graphic_id=$this->graphic_id;
        $graphic_width=$this->graphic_width;
        $graphic_height=$this->graphic_height;
        $graphic_chart=[
            $ip.' '.$uri => $xx,
        ];
        return $this->renderPartial('_widget',compact('graphic_id','graphic_width','graphic_height','graphic_chart'));
    }

    public function actionWhois()
    {
        $ip=Yii::$app->request->post('ip');
        $whois_server = "whois.ripe.net";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $whois_server.":43"); // Whois Server
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "$ip\r\n"); // Query

        $whois = curl_exec ($ch);  
        $error = curl_errno($ch);
        $whois = nl2br($whois);

        $info = $this->renderAjax('whois',compact('ip','whois'));
        return json_encode(compact('info','error'));
    }

    function filterIp($ip_uri,$filterIp)
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

    function filterUri($ip_uri,$filterUri)
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

    public function actionIndex()
    {
        $model = new LogVisitorForm();

        $timeFrom = strtotime(date('Y-m-d 00:00:00'));
        $timeTo = strtotime(date('Y-m-d 23:59:59'));
        $model->dateFrom = date('Y-m-d',$timeFrom);
        $model->dateTo = date('Y-m-d',$timeTo);
        $filterIp='';
        $filterUri='';

        if ($model->load(Yii::$app->request->post())) {
            $timeFrom = mktime(0,0,0,substr($model->dateFrom,5,2),substr($model->dateFrom,8,2),substr($model->dateFrom,0,4));
            $timeTo = mktime(23,59,59,substr($model->dateTo,5,2),substr($model->dateTo,8,2),substr($model->dateTo,0,4));
            $filterIp = $model->filterIp;
            $filterUri = $model->filterUri;
        }

        $ip_uri = (new \yii\db\Query())
            ->select(['time','ip','uri'])
            ->from('{{%logvisitor}}')
            ->where('time>'.$timeFrom)
            ->andWhere('time<='.$timeTo)
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
            $xx[date('H:i',$key)]=$val;
        }

        $graphic_id=$this->graphic_id;
        $graphic_width=$this->graphic_width;
        $graphic_height=$this->graphic_height;
        $graphic_chart=[
            Yii::t('logvisitor','All') => $xx,
        ];

        $ip_uri = (new \yii\db\Query())
            ->select(['time','ip','uri','count(ip)'])
            ->from('{{%logvisitor}}')
            ->where('time>'.$timeFrom)
            ->andWhere('time<='.$timeTo)
            ->groupBy(['ip','uri'])
            ->orderBy('ip,uri')
            ->all();

        if (!empty($filterIp))
            $ip_uri = $this->filterIp($ip_uri,$filterIp);
        if (!empty($filterUri))
            $ip_uri = $this->filterUri($ip_uri,$filterUri);

        return $this->render('index',compact('ip_uri','model','graphic_id','graphic_width','graphic_height','graphic_chart'));
    }
}
