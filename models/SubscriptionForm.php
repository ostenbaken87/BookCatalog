<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Subscription form for guests (non-authenticated users)
 */
class SubscriptionForm extends Model
{
    public $author_id;
    public $phone;
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'phone'], 'required'],
            ['author_id', 'integer'],
            ['author_id', 'exist', 'targetClass' => Author::class, 'targetAttribute' => 'id'],
            
            ['phone', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^\+?[0-9]{10,15}$/', 'message' => 'Некорректный формат телефона. Введите номер в формате +79001234567'],
            
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            
            [['phone', 'author_id'], 'checkSubscription'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'author_id' => 'Автор',
            'phone' => 'Телефон для SMS уведомлений',
            'email' => 'Email (опционально)',
        ];
    }

    /**
     */
    public function checkSubscription($attribute, $params)
    {
        if (Subscription::isSubscribed($this->author_id, $this->phone)) {
            $this->addError('phone', 'Вы уже подписаны на этого автора с данным номером телефона.');
        }
    }

    /**
     * @return Subscription|null
     */
    public function subscribe()
    {
        if (!$this->validate()) {
            return null;
        }

        $subscription = new Subscription();
        $subscription->author_id = $this->author_id;
        $subscription->phone = $this->phone;
        $subscription->email = $this->email;
        $subscription->status = Subscription::STATUS_ACTIVE;
        
        // If user is authenticated, save user_id
        if (!Yii::$app->user->isGuest) {
            $subscription->user_id = Yii::$app->user->id;
        }

        if ($subscription->save()) {
            return $subscription;
        }

        return null;
    }
}

