<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Book;
use app\models\Subscription;

/**
 * Book Event Handler for sending notifications
 */
class BookEventHandler extends Component
{
    /**
     * Handle book creation event - send SMS notifications to subscribers
     *
     * @param \yii\base\Event $event
     */
    public static function onBookCreated($event)
    {
        /** @var Book $book */
        $book = $event->sender;
        
        Yii::info('New book created: ' . $book->title, __METHOD__);

        // Get all authors of the book
        $authors = $book->authors;
        
        if (empty($authors)) {
            Yii::warning('Book has no authors, skipping notifications', __METHOD__);
            return;
        }

        // Send notifications to subscribers of each author
        foreach ($authors as $author) {
            $subscriptions = Subscription::getActiveSubscriptionsByAuthor($author->id);
            
            Yii::info('Found ' . count($subscriptions) . ' subscriptions for author: ' . $author->full_name, __METHOD__);
            
            foreach ($subscriptions as $subscription) {
                try {
                    $result = Yii::$app->smsPilot->sendNewBookNotification(
                        $subscription->phone,
                        $book->title,
                        $author->full_name
                    );
                    
                    if ($result) {
                        Yii::info('SMS sent to ' . $subscription->phone . ' for book: ' . $book->title, __METHOD__);
                    } else {
                        Yii::error('Failed to send SMS to ' . $subscription->phone, __METHOD__);
                    }
                } catch (\Exception $e) {
                    Yii::error('Exception while sending SMS: ' . $e->getMessage(), __METHOD__);
                }
            }
        }
    }

    public static function register()
    {
        // Attach event handler to Book model
        \yii\base\Event::on(
            Book::class,
            Book::EVENT_AFTER_INSERT_BOOK,
            [self::class, 'onBookCreated']
        );
    }
}

