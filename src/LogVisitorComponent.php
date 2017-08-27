<?php

namespace slavkovrn\logvisitor;

use Yii;
use yii\base\Component;
use slavkovrn\logvisitor\models\LogVisitorModel;

class LogVisitorComponent extends Component
{
	public $table = 'logvisitor';
	public $filterIp = '';
	public $filterUri = '';

	public function init()
	{
		$this->checkTable();
        if (!$this->filter()) $this->insert();
	}

	/**
	 * Checks if necessary table exist and if not, create it.
	 */
	protected function checkTable()
	{
        if (isset(Yii::$app->db->schema->db->tablePrefix))
            $this->table = Yii::$app->db->schema->db->tablePrefix.$this->table;
        
		if (Yii::$app->db->schema->getTableSchema($this->table, true) === null) {
			Yii::$app->db->createCommand()
                ->createTable(
                    $this->table,
    				array(
    					'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
    					'ip' => 'varchar(20) DEFAULT NULL',
    					'time' => 'int(10) unsigned DEFAULT NULL',
    					'rfc822' => 'varchar(50) DEFAULT NULL',
    					'uri' => 'varchar(255) DEFAULT NULL',
    					'get' => 'text',
    					'post' => 'text',
    					'cookies' => 'text',
    					'session' => 'text',
    					'method' => 'varchar(10) DEFAULT NULL',
    					'scheme' => 'varchar(10) DEFAULT NULL',
    					'protocol' => 'varchar(10) DEFAULT NULL',
    					'port' => 'varchar(10) DEFAULT NULL',
    					'browser' => 'text',
    					'language' => 'text',
    				),
                    'ENGINE=InnoDB DEFAULT CHARSET=utf8'
                )
                ->execute();
			Yii::$app->db->createCommand()
                ->createIndex(
                    'idx_ip',
                    $this->table,
    				'ip',
                    false
                )
                ->execute();
			Yii::$app->db->createCommand()
                ->createIndex(
                    'idx_time',
                    $this->table,
    				'time',
                    false
                )
                ->execute();
			Yii::$app->db->createCommand()
                ->createIndex(
                    'idx_uri',
                    $this->table,
    				'uri',
                    false
                )
                ->execute();
		}
	}

    protected function getIp(){
    	switch(true){
    	case (!empty($_SERVER['HTTP_X_REAL_IP'])) : 
    		return $_SERVER['HTTP_X_REAL_IP'];
    	case (!empty($_SERVER['HTTP_CLIENT_IP'])) : 
    		return $_SERVER['HTTP_CLIENT_IP'];
    	case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : 
    		return $_SERVER['HTTP_X_FORWARDED_FOR'];
    	default : 
    		return $_SERVER['REMOTE_ADDR'];
    	}
    } 
    protected function filter()
    {
        if (!empty($this->filterIp))
        {
            $arrFilterIp=explode(',',$this->filterIp);
            foreach ($arrFilterIp as $key=>$val)
                if (empty($val))
                    unset($arrFilterIp[$key]);
            foreach ($arrFilterIp as $f_ip)
            {
                $f_ip=trim($f_ip);
                if (preg_match('/^'.$f_ip.'/', $this->ip))
                    return true;
            }
        }
        if (!empty($this->filterUri))
        {
            $uri=(isset($_SERVER["REQUEST_URI"]))?$_SERVER["REQUEST_URI"]:'';

            $arrFilterUri=explode(',',$this->filterUri);
            foreach ($arrFilterUri as $key=>$val)
                if (empty($val))
                    unset($arrFilterUri[$key]);
            foreach ($arrFilterUri as $f_uri)
            {
                $f_uri=trim($f_uri);
                if ($f_uri==='/'){
                    if ($uri===$f_uri)
                       return true;
                }
                elseif (strpos($uri,$f_uri)!==false)
                    return true;
            }
        }
        return false;
    }

	protected function insert()
	{
        $model = new LogVisitorModel();
        $model->ip = $this->ip;
        $model->time = time();
        $model->rfc822 = date(DATE_RFC822,$model->time);
        $model->uri = (isset($_SERVER["REQUEST_URI"]))?$_SERVER["REQUEST_URI"]:'';
        $model->get = json_encode(Yii::$app->request->get());
        $model->post = json_encode(Yii::$app->request->post());
        $model->cookies = json_encode(Yii::$app->request->cookies);
        $model->session = json_encode(Yii::$app->session);
        $model->method = (isset($_SERVER["REQUEST_METHOD"]))?$_SERVER["REQUEST_METHOD"]:'';
        $model->protocol = (isset($_SERVER["SERVER_PROTOCOL"]))?$_SERVER["SERVER_PROTOCOL"]:'';
        $model->scheme = (isset($_SERVER["REQUEST_SCHEME"]))?$_SERVER["REQUEST_SCHEME"]:'';
        $model->port = (isset($_SERVER["SERVER_PORT"]))?$_SERVER["SERVER_PORT"]:'';
        $model->browser = (isset($_SERVER["HTTP_USER_AGENT"]))?$_SERVER["HTTP_USER_AGENT"]:'';
        $model->language = (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))?$_SERVER["HTTP_ACCEPT_LANGUAGE"]:'';
        $model->save(false);
	}

	/**
	 * Refreshes the counters and updates database. The updated values are stored in
	 * class variables.
	 */

	protected function truncate()
	{
		return Yii::$app->db->createCommand()
            ->truncateTable($this->table)
            ->execute();
	}

	/**
	 * Total number of records.
	 * @return int
	 */
	public function getTotal()
	{
        $rows = (new \yii\db\Query())
            ->select('*')
            ->from($this->table)
            ->all();
		return count($rows);
	}

}
