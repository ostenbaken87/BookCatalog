<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $topAuthors */
/** @var int $year */
/** @var array $years */

$this->title = 'ТОП-10 авторов за ' . $year . ' год';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-top-authors">

    <div class="report-container">
        <h1 class="text-center mb-4">🏆 <?= Html::encode($this->title) ?></h1>

        <div class="text-center mb-4">
            <div class="d-inline-block" style="min-width: 250px;">
                <label class="form-label fw-bold">Выберите год:</label>
                <select class="form-select form-select-lg" onchange="location.href='<?= Url::to(['report/top-authors']) ?>?year=' + this.value">
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php if (empty($topAuthors)): ?>
            <div class="alert alert-info text-center">
                <h4>📚 За <?= $year ?> год не найдено книг.</h4>
                <p class="mb-0">Попробуйте выбрать другой год</p>
            </div>
        <?php else: ?>
            <div class="row g-4 mb-4">
                <?php foreach ($topAuthors as $index => $author): ?>
                    <div class="col-md-6">
                        <div class="card h-100" style="border-left: 5px solid <?= $index < 3 ? '#f59e0b' : '#667eea' ?>;">
                            <div class="card-body d-flex align-items-center">
                                <div class="position-number me-3" style="font-size: 2.5rem; font-weight: 800; color: <?= $index < 3 ? '#f59e0b' : '#667eea' ?>; min-width: 60px; text-align: center;">
                                    <?php if ($index === 0): ?>
                                        🥇
                                    <?php elseif ($index === 1): ?>
                                        🥈
                                    <?php elseif ($index === 2): ?>
                                        🥉
                                    <?php else: ?>
                                        #<?= $index + 1 ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-2">
                                        <?= Html::a(Html::encode($author['full_name']), ['author/view', 'id' => $author['id']], [
                                            'class' => 'text-decoration-none'
                                        ]) ?>
                                    </h5>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary" style="font-size: 1rem; padding: 8px 16px;">
                                            📚 <?= $author['books_count'] ?> <?= $author['books_count'] == 1 ? 'книга' : ($author['books_count'] < 5 ? 'книги' : 'книг') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-3">📊 Статистика</h5>
                    <p class="mb-0">
                        <strong>Отчет показывает авторов, выпустивших наибольшее количество книг в <?= $year ?> году.</strong><br>
                        <small class="text-muted">Всего в рейтинге: <?= count($topAuthors) ?> <?= count($topAuthors) == 1 ? 'автор' : (count($topAuthors) < 5 ? 'автора' : 'авторов') ?></small>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<style>
.position-number {
    line-height: 1;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}
</style>
