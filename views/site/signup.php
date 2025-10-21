<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\SignupForm $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <h1 class="h3 mb-0">📝 <?= Html::encode($this->title) ?></h1>
                </div>
                <div class="card-body p-4">
                    <p class="text-center text-muted mb-4">Создайте новый аккаунт для доступа к системе</p>

                    <?php $form = ActiveForm::begin([
                        'id' => 'form-signup',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                        ],
                    ]); ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'username')->textInput([
                                'autofocus' => true,
                                'placeholder' => 'Введите имя пользователя',
                                'class' => 'form-control'
                            ])->label('👤 Имя пользователя') ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'email')->textInput([
                                'placeholder' => 'your@email.com',
                                'class' => 'form-control'
                            ])->label('📧 Email') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <?= $form->field($model, 'phone')->textInput([
                            'placeholder' => '+79001234567',
                            'class' => 'form-control'
                        ])->label('📱 Телефон') ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'password')->passwordInput([
                                'placeholder' => 'Минимум 6 символов',
                                'class' => 'form-control'
                            ])->label('🔑 Пароль') ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'password_repeat')->passwordInput([
                                'placeholder' => 'Повторите пароль',
                                'class' => 'form-control'
                            ])->label('🔐 Подтверждение пароля') ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'role')->dropDownList([
                            \app\models\User::ROLE_USER => '⚡ Пользователь (полный доступ к CRUD)',
                            \app\models\User::ROLE_GUEST => '👤 Гость (только просмотр и подписки)',
                        ], [
                            'class' => 'form-select'
                        ])->label('🎭 Тип аккаунта') ?>
                    </div>

                    <div class="alert alert-info mb-4">
                        <strong>ℹ️ Выбор роли:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Пользователь</strong> - может добавлять, редактировать и удалять книги/авторов</li>
                            <li><strong>Гость</strong> - может только просматривать каталог и подписываться на авторов</li>
                        </ul>
                    </div>

                    <div class="d-grid mb-3">
                        <?= Html::submitButton('🚀 Зарегистрироваться', [
                            'class' => 'btn btn-success btn-lg',
                            'name' => 'signup-button'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-2">Уже есть аккаунт?</p>
                        <?= Html::a('🔐 Войти', ['login'], ['class' => 'btn btn-outline-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.site-signup .card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
}

.site-signup .card-header {
    background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
    color: white;
    padding: 2rem;
}

.site-signup .form-control:focus,
.site-signup .form-select:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.15);
}
</style>
