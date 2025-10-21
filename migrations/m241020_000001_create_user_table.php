<?php

use yii\db\Migration;

class m241020_000001_create_user_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull()->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'phone' => $this->string(20)->null()->comment('Телефон для SMS уведомлений'),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'role' => $this->string(20)->notNull()->defaultValue('user')->comment('user или guest'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-user-status', '{{%user}}', 'status');
        $this->createIndex('idx-user-email', '{{%user}}', 'email');
        $this->createIndex('idx-user-role', '{{%user}}', 'role');
        
        // Тестовые пользователи
        // admin (полные права) - password: admin123
        $this->insert('{{%user}}', [
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '+79001234567',
            'auth_key' => \Yii::$app->security->generateRandomString(),
            'password_hash' => \Yii::$app->security->generatePasswordHash('admin123'),
            'status' => 10,
            'role' => 'user',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        
        // guest (только просмотр и подписки) - password: guest123
        $this->insert('{{%user}}', [
            'username' => 'guest_user',
            'email' => 'guest@example.com',
            'phone' => '+79009876543',
            'auth_key' => \Yii::$app->security->generateRandomString(),
            'password_hash' => \Yii::$app->security->generatePasswordHash('guest123'),
            'status' => 10,
            'role' => 'guest',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}

