<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Author $model */
/** @var app\models\SubscriptionForm $subscriptionForm */
/** @var yii\data\ActiveDataProvider $booksDataProvider */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="author-view">

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">✍️ <?= Html::encode($this->title) ?></h1>
            <div>
                <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->can('manageAuthors')): ?>
                    <?= Html::a('✏️ Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-light']) ?>
                    <?= Html::a('🗑️ Удалить', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-outline-light',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="number"><?= $model->getBooksCount() ?></div>
                        <div class="label">Книг опубликовано</div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="stats-card" style="background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);">
                        <div class="number"><?= count($model->subscriptions) ?></div>
                        <div class="label">Подписчиков</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">📚 Книги автора</h3>
                </div>
                <div class="card-body">
                    <?php if ($model->getBooksCount() > 0): ?>
                        <div class="row g-3">
                            <?php foreach ($booksDataProvider->getModels() as $book): ?>
                                <div class="col-md-6">
                                    <div class="book-item p-3 border rounded">
                                        <div class="d-flex">
                                            <img src="<?= $book->getCoverImageUrl() ?>" 
                                                 alt="<?= Html::encode($book->title) ?>" 
                                                 style="width: 80px; height: 120px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                                            <div class="flex-grow-1">
                                                <h5 class="h6 mb-2">
                                                    <?= Html::a(Html::encode($book->title), ['book/view', 'id' => $book->id]) ?>
                                                </h5>
                                                <div class="text-muted small mb-2">
                                                    📅 <?= $book->year ?><br>
                                                    📖 <?= Html::encode($book->isbn) ?>
                                                </div>
                                                <?= Html::a('Подробнее →', ['book/view', 'id' => $book->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            У этого автора пока нет опубликованных книг.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="subscription-card">
                <h3 class="h5 mb-3">📱 Подписка на уведомления</h3>
                <p class="mb-3">Подпишитесь на SMS уведомления о новых книгах этого автора:</p>
                
                <?php $form = ActiveForm::begin(['action' => ['author/view', 'id' => $model->id]]); ?>

                <?= $form->field($subscriptionForm, 'phone')->textInput([
                    'placeholder' => '+79001234567',
                    'maxlength' => true,
                    'class' => 'form-control'
                ])->label('Телефон') ?>

                <?= $form->field($subscriptionForm, 'email')->textInput([
                    'placeholder' => 'your@email.com',
                    'maxlength' => true,
                    'class' => 'form-control'
                ])->label('Email (опционально)') ?>

                <?= Html::activeHiddenInput($subscriptionForm, 'author_id') ?>

                <div class="d-grid">
                    <?= Html::submitButton('🔔 Подписаться', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
                
                <div class="alert alert-info mt-3 mb-0 small">
                    <strong>ℹ️ Как это работает:</strong><br>
                    После подписки вы будете получать SMS уведомления о новых книгах автора на указанный номер телефона.
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.book-item {
    transition: all 0.3s ease;
}

.book-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}
</style>
