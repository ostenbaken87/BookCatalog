<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Каталог книг', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">📖 <?= Html::encode($this->title) ?></h1>
            <div>
                <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->can('manageBooks')): ?>
                    <?= Html::a('✏️ Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-light']) ?>
                    <?= Html::a('🗑️ Удалить', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-outline-light',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <div class="book-cover-wrapper">
                        <?php if ($model->cover_image): ?>
                            <?= Html::img($model->getCoverImageUrl(), [
                                'alt' => $model->title,
                                'class' => 'img-thumbnail',
                                'style' => 'width: 100%; max-width: 350px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);'
                            ]) ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-image"></i> Обложка не загружена
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="book-details">
                        <h2 class="h4 text-primary mb-3"><?= Html::encode($model->title) ?></h2>
                        
                        <div class="mb-3">
                            <strong class="text-muted">✍️ Авторы:</strong>
                            <div class="mt-2">
                                <?php foreach ($model->authors as $author): ?>
                                    <?= Html::a(
                                        Html::encode($author->full_name), 
                                        ['author/view', 'id' => $author->id],
                                        ['class' => 'badge bg-primary me-2 mb-2', 'style' => 'font-size: 0.95rem; text-decoration: none;']
                                    ) ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="info-box p-3 bg-light rounded">
                                    <small class="text-muted d-block">📅 Год выпуска</small>
                                    <strong class="h5 mb-0"><?= $model->year ?></strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-box p-3 bg-light rounded">
                                    <small class="text-muted d-block">📖 ISBN</small>
                                    <strong><?= Html::encode($model->isbn) ?></strong>
                                </div>
                            </div>
                        </div>

                        <?php if ($model->description): ?>
                            <div class="mb-3">
                                <strong class="text-muted d-block mb-2">📝 Описание:</strong>
                                <div class="description-text p-3 bg-light rounded">
                                    <?= nl2br(Html::encode($model->description)) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row g-2 text-muted small">
                            <div class="col-sm-6">
                                <i class="bi bi-clock"></i> Создано: <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                            </div>
                            <div class="col-sm-6">
                                <i class="bi bi-clock-history"></i> Обновлено: <?= Yii::$app->formatter->asDatetime($model->updated_at) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mb-4">
        <?= Html::a('← Вернуться к каталогу', ['index'], ['class' => 'btn btn-outline-primary']) ?>
    </div>

</div>

<style>
.book-cover-wrapper {
    position: relative;
}

.book-cover-wrapper img {
    transition: transform 0.3s ease;
}

.book-cover-wrapper img:hover {
    transform: scale(1.05);
}

.info-box {
    border-left: 4px solid var(--primary-color);
}

.description-text {
    line-height: 1.8;
    font-size: 1.05rem;
}
</style>
