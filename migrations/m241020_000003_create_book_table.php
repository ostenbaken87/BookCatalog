<?php

use yii\db\Migration;

class m241020_000003_create_book_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->comment('Название книги'),
            'year' => $this->integer()->notNull()->comment('Год выпуска'),
            'description' => $this->text()->null()->comment('Описание'),
            'isbn' => $this->string(20)->notNull()->unique()->comment('ISBN'),
            'cover_image' => $this->string(255)->null()->comment('Фото главной страницы'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-book-title', '{{%book}}', 'title');
        $this->createIndex('idx-book-year', '{{%book}}', 'year');
        $this->createIndex('idx-book-isbn', '{{%book}}', 'isbn');
        
        // Добавляем тестовые книги
        $this->insert('{{%book}}', [
            'title' => 'Война и мир',
            'year' => 1869,
            'description' => 'Роман-эпопея Льва Николаевича Толстого',
            'isbn' => '978-5-17-098352-1',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        
        $this->insert('{{%book}}', [
            'title' => 'Преступление и наказание',
            'year' => 1866,
            'description' => 'Роман Федора Михайловича Достоевского',
            'isbn' => '978-5-17-098353-2',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%book}}');
    }
}

