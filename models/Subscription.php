<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Subscription model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $author_id
 * @property string $phone
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 *
 * @property User $user
 * @property Author $author
 */
class Subscription extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%subscription}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['user_id', 'author_id', 'status', 'created_at'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['phone'], 'match', 'pattern' => '/^\+?[0-9]{10,15}$/', 'message' => 'Некорректный формат телефона'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            ['created_at', 'default', 'value' => time()],
            
            [['phone', 'author_id'], 'unique', 'targetAttribute' => ['phone', 'author_id'], 
                'message' => 'Вы уже подписаны на этого автора.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'author_id' => 'Автор',
            'phone' => 'Телефон',
            'email' => 'Email',
            'status' => 'Статус',
            'created_at' => 'Дата подписки',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * Check if phone is already subscribed to author
     * 
     * @param integer $authorId the author ID
     * @param string $phone the phone number
     * @return bool whether subscription exists
     */
    public static function isSubscribed($authorId, $phone)
    {
        return self::find()
            ->where(['author_id' => $authorId, 'phone' => $phone, 'status' => self::STATUS_ACTIVE])
            ->exists();
    }

    /**
     * Get all active subscriptions for specific author
     * 
     * @param integer $authorId the author ID
     * @return Subscription[] array of subscription models
     */
    public static function getActiveSubscriptionsByAuthor($authorId)
    {
        return self::find()
            ->where(['author_id' => $authorId, 'status' => self::STATUS_ACTIVE])
            ->all();
    }
}

