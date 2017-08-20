<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
?>
<?php
Modal::begin([
        'options'=>[
            'id'=>"ip-popup",
            'style'=>"display:none"
        ]]);
Modal::end();
?>
<div class="LogVisitorModule-default-index">
    <div class="content">
        <div class="row">
            <div class="col-sm-10">

                <div class="form-group">
                    <?php $form = ActiveForm::begin([
                        'id' => 'activeform',
                        'layout' => 'horizontal',
                        'action' => '/logvisitor',
                        'fieldConfig' => [
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-4',
                                'offset' => 'col-sm-offset-2',
                                'wrapper' => 'col-sm-8',
                            ],
                        ],
                    ]); 
                    ?>
                    <?= $form->field($model, 'dateFrom')->widget(DatePicker::classname(),[
                        'language' => Yii::$app->language,
                        'dateFormat' => 'yyyy-MM-dd'
                    ]) ?>
                    <?= $form->field($model, 'dateTo')->widget(DatePicker::classname(),[
                        'language' => Yii::$app->language,
                        'dateFormat' => 'yyyy-MM-dd'
                    ]) ?>
                    <?= $form->field($model, 'filterIp') ?>
                    <div class="form-group">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-8">
                            <?= Yii::t('logvisitor','Comma separated substrings of IP to be filtered, begining from first position preg_match(/^needle/,haystack)') ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'filterUri') ?>
                    <div class="form-group">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-8">
                            <?= Yii::t('logvisitor','Comma separated substrings of URI to be filtered') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4"></div>
                        <?= Html::submitButton(Yii::t('logvisitor', 'Show'), ['id'=>'order','class'=>'btn btn-success']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive"> 
                <table class="table table table-striped table-bordered table-hover table-condensed">
                    <caption><h3>Unique IP,URI,count()</h3></caption>
                    <thead>
                        <tr class="success">
                            <th>IP</th>
                            <th>Uri</th>
                            <th>Count()</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ip_uri as $key=>$val) : ?>
                            <tr>
                                <td><?= $val['ip'] ?></td>
                                <td><?= $val['uri'] ?></td>
                                <td><?= $val['count(ip)'] ?></td>
                                <td><a data-id="<?= $val['id'] ?>" class="btn btn-success whois">Whois IP</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
    $('.whois').click(function(){
        var id=$(this).data('id');
		$.ajax({
			url:'/logvisitor/default/whois',
			type: 'post',
            dataType:'json',
            data:{'id':id},
			success: function(data) {
                console.log(data);
                $('#ip-popup .modal-body').html(data.info);
                $('#ip-popup').modal('toggle'); 			}
		});
    });
",$this::POS_READY);
?>