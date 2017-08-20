<?php

namespace slavkovrn\logvisitor\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class.
 *
 * @property string $filterIp
 * @property string $filterUri
 * @property string $dateFrom
 * @property string $dateTo
 */
class LogVisitorForm extends Model
{
    public $filterIp;
    public $filterUri;
    public $dateFrom;
    public $dateTo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
            'filterIp' => Yii::t('logvisitor', 'Filter IP'),
            'filterUri' => Yii::t('logvisitor', 'Filter URI'),
            'dateFrom' => Yii::t('logvisitor', 'Date from'),
            'dateTo' => Yii::t('logvisitor', 'Date to'),
        ];
    }
}
