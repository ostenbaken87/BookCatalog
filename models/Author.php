<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Author model
 *
 * @property integer $id
 * @property string $full_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Book[] $books
 * @property Subscription[] $subscriptions
 */
class Author extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%author}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name'], 'required'],
            [['full_name'], 'string', 'min' => 2, 'max' => 255],
            [['full_name'], 'trim'],
            [['full_name'], 'match', 
                'pattern' => '/^[а-яёА-ЯЁa-zA-Z\s\-\.\']+$/u',
                'message' => 'ФИО может содержать только буквы, пробелы, дефисы, точки и апострофы.'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'full_name' => 'ФИО автора',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('{{%book_author}}', ['author_id' => 'id'])
            ->orderBy(['year' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::class, ['author_id' => 'id'])
            ->where(['status' => Subscription::STATUS_ACTIVE]);
    }

    /**
     * Get count of books published by this author in specific year
     * 
     * @param integer $year the publication year
     * @return integer number of books
     */
    public function getBooksCountByYear($year)
    {
        return $this->getBooks()
            ->where(['year' => $year])
            ->count();
    }

    /**
     * Get total count of books by this author
     * 
     * @return integer total number of books
     */
    public function getBooksCount()
    {
        return $this->getBooks()->count();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Удаляем связи с книгами
                Yii::$app->db->createCommand()
                    ->delete('{{%book_author}}', ['author_id' => $this->id])
                    ->execute();
                
                // Удаляем подписки на этого автора
                Subscription::deleteAll(['author_id' => $this->id]);
                
                $transaction->commit();
                return true;
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error('Error deleting author dependencies: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }
}

