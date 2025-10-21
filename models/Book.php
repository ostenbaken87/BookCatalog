<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * Book model
 *
 * @property integer $id
 * @property string $title
 * @property integer $year
 * @property string $description
 * @property string $isbn
 * @property string $cover_image
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Author[] $authors
 * @property UploadedFile $imageFile
 */
class Book extends ActiveRecord
{
    public $imageFile;
    public $authorIds = [];

    const EVENT_AFTER_INSERT_BOOK = 'afterInsertBook';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%book}}';
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
            [['title', 'year', 'isbn'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => date('Y') + 1],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'unique'],
            [['isbn'], 'match', 'pattern' => '/^(?:ISBN(?:-1[03])?:? )?(?=[0-9X]{10}$|(?=(?:[0-9]+[- ]){3})[- 0-9X]{13}$|97[89][0-9]{10}$|(?=(?:[0-9]+[- ]){4})[- 0-9]{17}$)(?:97[89][- ]?)?[0-9]{1,5}[- ]?[0-9]+[- ]?[0-9]+[- ]?[0-9X]$/i'],
            [['cover_image'], 'string', 'max' => 255],
            [['cover_image'], 'safe'], // Allow cover_image to be safely assigned
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024 * 5], // 5MB
            [['authorIds'], 'each', 'rule' => ['integer']],
            [['authorIds'], 'required', 'message' => 'Необходимо выбрать хотя бы одного автора.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название книги',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_image' => 'Фото обложки',
            'imageFile' => 'Загрузить обложку',
            'authorIds' => 'Авторы',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }

    /**
     * @return string
     */
    public function getAuthorsNames()
    {
        return implode(', ', array_map(function($author) {
            return $author->full_name;
        }, $this->authors));
    }

    /**
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            if ($this->imageFile) {
                $uploadPath = Yii::getAlias('@webroot/uploads/books');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Удаляем старое изображение
                if ($this->cover_image) {
                    $oldFile = Yii::getAlias('@webroot') . $this->cover_image;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $fileName = uniqid() . '.' . $this->imageFile->extension;
                $filePath = $uploadPath . '/' . $fileName;
                
                if ($this->imageFile->saveAs($filePath)) {

                    try {
                        Image::thumbnail($filePath, 800, 1200)
                            ->save($filePath, ['quality' => 90]);
                    } catch (\Exception $e) {
                        Yii::error("Failed to resize image: " . $e->getMessage());
                    }
                    
                    $this->cover_image = '/uploads/books/' . $fileName;
                    return true;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function saveWithAuthors()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $isNewRecord = $this->isNewRecord;

            $oldCoverImage = $this->getOldAttribute('cover_image');
            
            if ($this->imageFile) {
                if (!$this->upload()) {
                    $transaction->rollBack();
                    return false;
                }

            } else {

                $this->cover_image = $oldCoverImage;
            }

            if (!$this->save(false)) {
                $transaction->rollBack();
                return false;
            }

            BookAuthor::deleteAll(['book_id' => $this->id]);

            if (!empty($this->authorIds)) {
                foreach ($this->authorIds as $authorId) {
                    $bookAuthor = new BookAuthor();
                    $bookAuthor->book_id = $this->id;
                    $bookAuthor->author_id = $authorId;
                    if (!$bookAuthor->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                }
            }

            $transaction->commit();
            
            if ($isNewRecord) {
                $this->trigger(self::EVENT_AFTER_INSERT_BOOK);
            }
            
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->authorIds = array_map(function($author) {
            return $author->id;
        }, $this->authors);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {

            if ($this->cover_image) {
                $file = Yii::getAlias('@webroot') . $this->cover_image;
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            BookAuthor::deleteAll(['book_id' => $this->id]);
            
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCoverImageUrl()
    {
        return $this->cover_image 
            ? Yii::getAlias('@web') . $this->cover_image 
            : Yii::getAlias('@web') . '/images/no-cover.png';
    }
}

