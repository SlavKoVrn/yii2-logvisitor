<?php

namespace slavkovrn\logvisitor\controllers;

use Yii;
use yii\web\Controller;
use slavkovrn\logvisitor\models\LogVisitorModel;

/**
 * Default controller for the `LogVisitorModule` module
 */
class DefaultController extends Controller
{
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

    public function actionChart()
    {
        $model = new LogVisitorModel();
        $model->calculateChart();

        return $this->renderPartial('_widget',compact('model'));
    }

    public function actionIndex()
    {
        $model = new LogVisitorModel();
        $model->calculateIndex();

        $mode=Yii::$app->request->get('mode');
        if ($mode == 'demo')
            return $this->render('_widget',compact('model'));
        else
            return $this->render('index',compact('model'));
    }
}
