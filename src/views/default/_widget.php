<?php
use slavkovrn\visualize\VisualizeWidget;
$this->registerCss("
.visualize-labels-x li span.label, .visualize-labels-y li span.label
{
    font-size: 0.9em;
}
");
?>
<?= VisualizeWidget::widget([
    'id' => $graphic_id,      // Id of visualize widget should be unique at page
    'class' => 'graphic',   // Class to define stile
    'name' => Yii::t('logvisitor','Chart'),  // Name of visualize widget
    'style' => 'light',     // Style of widget (only 'dark' or 'light' option)
    'width' => $graphic_width, // Width of widget in pixels
    'height' => $graphic_height, // Height of widget in pixels
    'graphic' => $graphic_chart,
]) ?>
