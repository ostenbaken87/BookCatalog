<?php

use yii\db\Migration;

class m241020_000004_create_book_author_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-book_author', '{{%book_author}}', ['book_id', 'author_id']);

        $this->addForeignKey(
            'fk-book_author-book_id',
            '{{%book_author}}',
            'book_id',
            '{{%book}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-book_author-author_id',
            '{{%book_author}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-book_author-book_id', '{{%book_author}}', 'book_id');
        $this->createIndex('idx-book_author-author_id', '{{%book_author}}', 'author_id');
        
        // Связываем тестовые книги с авторами
        // Война и мир - Толстой (book_id=1, author_id=1)
        $this->insert('{{%book_author}}', [
            'book_id' => 1,
            'author_id' => 1,
        ]);
        
        // Преступление и наказание - Достоевский (book_id=2, author_id=2)
        $this->insert('{{%book_author}}', [
            'book_id' => 2,
            'author_id' => 2,
        ]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-book_author-author_id', '{{%book_author}}');
        $this->dropForeignKey('fk-book_author-book_id', '{{%book_author}}');
        $this->dropTable('{{%book_author}}');
    }
}

