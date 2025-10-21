<?php

use yii\db\Migration;

/**
 * Миграция для инициализации ролей и разрешений RBAC
 */
class m241020_000007_init_rbac_data extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        
        // Создаем права доступа
        
        // Просмотр книг (доступно всем)
        $viewBooks = $auth->createPermission('viewBooks');
        $viewBooks->description = 'Просмотр каталога книг';
        $auth->add($viewBooks);
        
        // Управление книгами (только для user)
        $manageBooks = $auth->createPermission('manageBooks');
        $manageBooks->description = 'Добавление, редактирование и удаление книг';
        $auth->add($manageBooks);
        
        // Управление авторами (только для user)
        $manageAuthors = $auth->createPermission('manageAuthors');
        $manageAuthors->description = 'Добавление, редактирование и удаление авторов';
        $auth->add($manageAuthors);
        
        // Подписка на авторов (доступно всем)
        $subscribeToAuthors = $auth->createPermission('subscribeToAuthors');
        $subscribeToAuthors->description = 'Подписка на новые книги авторов';
        $auth->add($subscribeToAuthors);
        
        // Просмотр отчетов (доступно всем)
        $viewReports = $auth->createPermission('viewReports');
        $viewReports->description = 'Просмотр отчетов и статистики';
        $auth->add($viewReports);
        
        // Роль "Гость" - только просмотр и подписка
        $guest = $auth->createRole('guest');
        $guest->description = 'Гость - просмотр и подписка';
        $auth->add($guest);
        $auth->addChild($guest, $viewBooks);
        $auth->addChild($guest, $subscribeToAuthors);
        $auth->addChild($guest, $viewReports);
        
        // Роль "Пользователь" - полный доступ (CRUD)
        $user = $auth->createRole('user');
        $user->description = 'Пользователь - полный доступ';
        $auth->add($user);
        $auth->addChild($user, $viewBooks);
        $auth->addChild($user, $manageBooks);
        $auth->addChild($user, $manageAuthors);
        $auth->addChild($user, $subscribeToAuthors);
        $auth->addChild($user, $viewReports);
        
        $auth->assign($user, 1); // admin
        $auth->assign($guest, 2); // guest_user
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        
        $auth->removeAll();
    }
}

