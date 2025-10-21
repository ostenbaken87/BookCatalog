<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Каталог книг';
?>
<div class="site-index">

    <div class="hero-section text-center fade-in">
        <div class="container">
            <h1 class="display-4">📚 Каталог книг</h1>
            <p class="lead">Современная система управления каталогом книг с возможностью подписки на любимых авторов</p>
            <p class="mt-4">
                <?= Html::a('Открыть каталог', ['book/index'], ['class' => 'btn btn-light btn-lg me-2']) ?>
                <?= Html::a('Посмотреть авторов', ['author/index'], ['class' => 'btn btn-outline-light btn-lg']) ?>
            </p>
        </div>
    </div>

    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-box text-center">
                    <div class="icon">📖</div>
                    <h2>Каталог книг</h2>
                    <p>Просматривайте полный каталог книг с информацией об авторах, годе издания, описании и ISBN. Удобный поиск и фильтрация помогут найти нужную книгу.</p>
                    <?= Html::a('Перейти к каталогу →', ['book/index'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 fade-in" style="animation-delay: 0.1s;">
                <div class="feature-box text-center">
                    <div class="icon">✍️</div>
                    <h2>Авторы</h2>
                    <p>Информация обо всех авторах представленных в каталоге книг. Подпишитесь на уведомления о новых книгах ваших любимых авторов!</p>
                    <?= Html::a('Список авторов →', ['author/index'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 fade-in" style="animation-delay: 0.2s;">
                <div class="feature-box text-center">
                    <div class="icon">📊</div>
                    <h2>Отчеты</h2>
                    <p>ТОП-10 авторов, выпустивших больше всего книг за выбранный год. Статистика и аналитика доступны для всех пользователей.</p>
                    <?= Html::a('Посмотреть отчет →', ['report/top-authors'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="card-body">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <div class="text-center mt-4">
                            <p class="lead mb-3">Присоединяйтесь к нашему сообществу!</p>
                            <?= Html::a('Зарегистрироваться', ['site/signup'], ['class' => 'btn btn-success btn-lg me-2']) ?>
                            <?= Html::a('Войти', ['site/login'], ['class' => 'btn btn-outline-primary btn-lg']) ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success mt-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <strong>👋 Добро пожаловать, <?= Html::encode(Yii::$app->user->identity->username) ?>!</strong><br>
                                    <small>Роль: <?= Yii::$app->user->identity->role === 'user' ? '⚡ Пользователь (полный доступ)' : '👤 Гость (просмотр и подписки)' ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fade-in {
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>