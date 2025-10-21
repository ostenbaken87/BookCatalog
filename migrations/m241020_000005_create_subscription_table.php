<?php

use yii\db\Migration;

class m241020_000005_create_subscription_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null()->comment('ID пользователя (null для неавторизованных гостей)'),
            'author_id' => $this->integer()->notNull()->comment('ID автора'),
            'phone' => $this->string(20)->notNull()->comment('Телефон для SMS уведомлений'),
            'email' => $this->string(255)->null()->comment('Email для дополнительных уведомлений'),
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1-активна, 0-отключена'),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-subscription-user_id',
            '{{%subscription}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-subscription-author_id',
            '{{%subscription}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-subscription-user_id', '{{%subscription}}', 'user_id');
        $this->createIndex('idx-subscription-author_id', '{{%subscription}}', 'author_id');
        $this->createIndex('idx-subscription-status', '{{%subscription}}', 'status');
        $this->createIndex('idx-subscription-phone', '{{%subscription}}', 'phone');
        
        $this->createIndex(
            'idx-subscription-unique',
            '{{%subscription}}',
            ['author_id', 'phone'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-subscription-author_id', '{{%subscription}}');
        $this->dropForeignKey('fk-subscription-user_id', '{{%subscription}}');
        $this->dropTable('{{%subscription}}');
    }
}

