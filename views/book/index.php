<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Каталог книг';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6 mb-0">📚 <?= Html::encode($this->title) ?></h1>
        
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->can('manageBooks')): ?>
            <?= Html::a('➕ Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php foreach ($dataProvider->getModels() as $model): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="book-card">
                    <a href="<?= Url::to(['view', 'id' => $model->id]) ?>">
                        <img src="<?= $model->getCoverImageUrl() ?>" 
                             alt="<?= Html::encode($model->title) ?>" 
                             class="book-card-img">
                    </a>
                    <div class="book-card-body">
                        <h5 class="book-card-title">
                            <?= Html::a(Html::encode($model->title), ['view', 'id' => $model->id]) ?>
                        </h5>
                        <div class="book-card-authors">
                            ✍️ <?= Html::encode($model->getAuthorsNames()) ?>
                        </div>
                        <div class="book-card-meta mb-3">
                            📅 <?= $model->year ?> | 📖 ISBN: <?= Html::encode($model->isbn) ?>
                        </div>
                        <?php if ($model->description): ?>
                            <p class="text-muted small mb-3">
                                <?= Html::encode(mb_substr($model->description, 0, 100)) . (mb_strlen($model->description) > 100 ? '...' : '') ?>
                            </p>
                        <?php endif; ?>
                        <div class="d-flex gap-2">
                            <?= Html::a('Подробнее', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary flex-grow-1']) ?>
                            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->can('manageBooks')): ?>
                                <?= Html::a('✏️', ['update', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'title' => 'Редактировать'
                                ]) ?>
                                <?= Html::a('🗑️', ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'title' => 'Удалить',
                                    'data' => [
                                        'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4">
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pagination justify-content-center'],
        ]) ?>
    </div>

</div>
