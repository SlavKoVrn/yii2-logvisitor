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
 */
class LogVisitorModel extends \yii\db\ActiveRecord
{
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ip' => Yii::t('app', 'Ip'),
            'time' => Yii::t('app', 'Time'),
            'rfc822' => Yii::t('app', 'Rfc822'),
            'uri' => Yii::t('app', 'Uri'),
            'get' => Yii::t('app', 'Get'),
            'post' => Yii::t('app', 'Post'),
            'cookies' => Yii::t('app', 'Cookies'),
            'session' => Yii::t('app', 'Session'),
            'method' => Yii::t('app', 'Method'),
            'scheme' => Yii::t('app', 'Scheme'),
            'protocol' => Yii::t('app', 'Protocol'),
            'port' => Yii::t('app', 'Port'),
            'browser' => Yii::t('app', 'Browser'),
            'language' => Yii::t('app', 'Language'),
        ];
    }
}
