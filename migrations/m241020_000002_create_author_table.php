<?php

use yii\db\Migration;

class m241020_000002_create_author_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(255)->notNull()->comment('ФИО автора'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-author-full_name', '{{%author}}', 'full_name');

        $authors = [
            'Лев Николаевич Толстой',
            'Федор Михайлович Достоевский',
            'Антон Павлович Чехов',
            'Александр Сергеевич Пушкин',
            'Михаил Юрьевич Лермонтов',
        ];
        
        foreach ($authors as $author) {
            $this->insert('{{%author}}', [
                'full_name' => $author,
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }

    public function safeDown()
    {
        $this->dropTable('{{%author}}');
    }
}

