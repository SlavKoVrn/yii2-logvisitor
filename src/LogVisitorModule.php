<?php

namespace slavkovrn\logvisitor;

use Yii;
use yii\i18n\PhpMessageSource;

/**
 * LogVisitorModule module definition class
 */
class LogVisitorModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'slavkovrn\logvisitor\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->registerTranslations();
        // custom initialization code goes here


    }
    protected function registerTranslations()
    {
        Yii::$app->get('i18n')->translations['logvisitor'] = [
            'class' => PhpMessageSource::class,
            'basePath' => __DIR__ . '/messages',
            'sourceLanguage' => (isset(Yii::$app->language))?Yii::$app->language:'en',
            'forceTranslation' => true,
        ];
    }
}
