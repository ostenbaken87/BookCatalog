<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Вход в систему';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <h1 class="h3 mb-0">🔐 <?= Html::encode($this->title) ?></h1>
                </div>
                <div class="card-body p-4">
                    <p class="text-center text-muted mb-4">Введите ваши учетные данные для входа</p>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                        ],
                    ]); ?>

                    <div class="mb-3">
                        <?= $form->field($model, 'username')->textInput([
                            'autofocus' => true,
                            'placeholder' => 'Введите имя пользователя',
                            'class' => 'form-control form-control-lg'
                        ])->label('👤 Имя пользователя') ?>
                    </div>

                    <div class="mb-3">
                        <?= $form->field($model, 'password')->passwordInput([
                            'placeholder' => 'Введите пароль',
                            'class' => 'form-control form-control-lg'
                        ])->label('🔑 Пароль') ?>
                    </div>

                    <div class="mb-4">
                        <?= $form->field($model, 'rememberMe')->checkbox([
                            'template' => "<div class='form-check'>{input} {label}</div>\n{error}",
                            'class' => 'form-check-input',
                            'labelOptions' => ['class' => 'form-check-label'],
                        ])->label('Запомнить меня') ?>
                    </div>

                    <div class="d-grid mb-3">
                        <?= Html::submitButton('🚀 Войти', [
                            'class' => 'btn btn-primary btn-lg',
                            'name' => 'login-button'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-2">Нет аккаунта?</p>
                        <?= Html::a('📝 Зарегистрироваться', ['signup'], ['class' => 'btn btn-outline-success']) ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <small class="text-muted">
                        <strong>Тестовые аккаунты:</strong><br>
                        admin / admin123 (полный доступ)<br>
                        guest_user / guest123 (просмотр)
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.site-login .card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
}

.site-login .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
}

.site-login .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
}
</style>
