<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use app\models\Author;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class BookController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->can('manageBooks');
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Book::find()->with('authors')->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => Yii::$app->params['pagination']['booksPageSize'],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->saveWithAuthors()) {
                Yii::$app->session->setFlash('success', 'Книга успешно добавлена.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при сохранении книги.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => $this->getAuthorsList(),
        ]);
    }

    /**
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->saveWithAuthors()) {
                Yii::$app->session->setFlash('success', 'Книга успешно обновлена.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка при обновлении книги.');
            }
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => $this->getAuthorsList(),
        ]);
    }

    /**
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $bookTitle = $model->title;
        
        if ($model->delete()) {
            Yii::info("Book deleted: ID={$id}, Title=\"{$bookTitle}\"", __METHOD__);
            Yii::$app->session->setFlash('success', 'Книга успешно удалена.');
        } else {
            Yii::error("Failed to delete book: ID={$id}, Title=\"{$bookTitle}\"", __METHOD__);
            Yii::$app->session->setFlash('error', 'Ошибка при удалении книги.');
        }

        return $this->redirect(['index']);
    }

    /**
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
    }

    /**
     * Get list of all authors sorted by name
     * 
     * @return Author[] array of authors
     */
    protected function getAuthorsList()
    {
        return Author::find()->orderBy(['full_name' => SORT_ASC])->all();
    }
}

