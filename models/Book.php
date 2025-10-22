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
     * Get comma-separated list of author names
     * 
     * @return string authors names separated by comma
     */
    public function getAuthorsNames()
    {
        return implode(', ', array_map(function($author) {
            return $author->full_name;
        }, $this->authors));
    }

    /**
     * Delete image file from filesystem
     * 
     * @param string|null $imagePath relative path to image
     * @return bool whether the file was deleted
     */
    protected function deleteImageFile($imagePath = null)
    {
        if ($imagePath === null) {
            $imagePath = $this->cover_image;
        }
        
        if ($imagePath) {
            $file = Yii::getAlias('@webroot') . $imagePath;
            if (file_exists($file) && is_writable($file)) {
                return unlink($file);
            }
        }
        return false;
    }

    /**
     * Upload and process book cover image
     * 
     * @return bool whether the upload was successful
     */
    public function upload()
    {
        if ($this->validate()) {
            if ($this->imageFile) {
                $uploadDir = Yii::$app->params['uploadPaths']['books'];
                $uploadPath = Yii::getAlias('@webroot') . $uploadDir;
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Удаляем старое изображение
                $this->deleteImageFile();

                $fileName = uniqid() . '.' . $this->imageFile->extension;
                $filePath = $uploadPath . '/' . $fileName;
                
                if ($this->imageFile->saveAs($filePath)) {

                    try {
                        Image::thumbnail($filePath, 800, 1200)
                            ->save($filePath, ['quality' => 90]);
                    } catch (\Exception $e) {
                        Yii::error("Failed to resize image: " . $e->getMessage());
                    }
                    
                    $this->cover_image = $uploadDir . '/' . $fileName;
                    return true;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Handle image upload for the book
     * 
     * @return bool whether the upload was successful
     */
    protected function handleImageUpload()
    {
        $oldCoverImage = $this->getOldAttribute('cover_image');
        
        if ($this->imageFile) {
            if (!$this->upload()) {
                $this->addError('imageFile', 'Не удалось загрузить изображение.');
                return false;
            }
        } else {
            // Preserve old image if no new image uploaded
            $this->cover_image = $oldCoverImage;
        }
        
        return true;
    }

    /**
     * Sync book authors relationships
     * 
     * @return bool whether the sync was successful
     */
    protected function syncAuthors()
    {
        // Remove old author relationships
        BookAuthor::deleteAll(['book_id' => $this->id]);

        // Create new author relationships
        if (!empty($this->authorIds)) {
            foreach ($this->authorIds as $authorId) {
                $bookAuthor = new BookAuthor();
                $bookAuthor->book_id = $this->id;
                $bookAuthor->author_id = $authorId;
                
                if (!$bookAuthor->save()) {
                    $this->addError('authorIds', 'Не удалось связать книгу с автором ID: ' . $authorId);
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Save book with authors relationships
     * 
     * @return bool whether the save was successful
     */
    public function saveWithAuthors()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $isNewRecord = $this->isNewRecord;

            // Handle image upload
            if (!$this->handleImageUpload()) {
                $transaction->rollBack();
                return false;
            }

            // Save book data
            if (!$this->save(false)) {
                $this->addError('title', 'Не удалось сохранить данные книги.');
                $transaction->rollBack();
                return false;
            }

            // Sync author relationships
            if (!$this->syncAuthors()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            
            // Trigger event for new books
            if ($isNewRecord) {
                $this->trigger(self::EVENT_AFTER_INSERT_BOOK);
            }
            
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Error saving book with authors: ' . $e->getMessage());
            $this->addError('title', 'Произошла ошибка при сохранении: ' . $e->getMessage());
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
            // Удаляем изображение обложки
            $this->deleteImageFile();

            // Удаляем связи с авторами
            BookAuthor::deleteAll(['book_id' => $this->id]);
            
            return true;
        }
        return false;
    }

    /**
     * Get URL of book cover image or default placeholder
     * 
     * @return string full URL to cover image
     */
    public function getCoverImageUrl()
    {
        return $this->cover_image 
            ? Yii::getAlias('@web') . $this->cover_image 
            : Yii::getAlias('@web') . '/images/no-cover.png';
    }
}

