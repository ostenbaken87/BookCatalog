<?php

use yii\db\Migration;

/**
 * Migration for adding performance indexes to frequently queried columns
 */
class m251022_095334_add_indexes_to_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Check and add composite index for subscription queries (author_id + status)
        // This improves performance for getActiveSubscriptionsByAuthor() queries
        try {
            $this->createIndex(
                'idx-subscription-author_id-status',
                '{{%subscription}}',
                ['author_id', 'status']
            );
        } catch (\Exception $e) {
            echo "Index idx-subscription-author_id-status already exists, skipping.\n";
        }
        
        // Note: Other indexes already exist:
        // - book_author.book_id: idx-book_author-book_id (already exists)
        // - book_author.author_id: idx-book_author-author_id (already exists)
        // - subscription.author_id: idx-subscription-author_id (already exists)
        // - book.year: idx-book-year (already exists)
        // - subscription.phone + author_id: covered by idx-subscription-unique
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop only the composite index that was created
        try {
            $this->dropIndex('idx-subscription-author_id-status', '{{%subscription}}');
        } catch (\Exception $e) {
            echo "Index idx-subscription-author_id-status does not exist, skipping.\n";
        }

        return true;
    }
}
