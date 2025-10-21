<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var app\models\Author[] $authors */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= Html::activeHiddenInput($model, 'cover_image') ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'authorIds')->checkboxList(
        ArrayHelper::map($authors, 'id', 'full_name')
    )->label('Авторы') ?>

    <?= $form->field($model, 'imageFile')->fileInput()->label('Обложка книги') ?>

    <?php if ($model->cover_image): ?>
        <div class="form-group">
            <label>Текущая обложка:</label><br>
            <?= Html::img($model->getCoverImageUrl(), [
                'alt' => $model->title,
                'style' => 'max-width: 200px; max-height: 300px;'
            ]) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

