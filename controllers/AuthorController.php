<?php

namespace app\controllers;

use Yii;
use app\models\Author;
use app\models\SubscriptionForm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * AuthorController implements the CRUD actions for Author model.
 */
class AuthorController extends Controller
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
                            return Yii::$app->user->identity->can('manageAuthors');
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
     * Lists all Author models.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Author::find()->orderBy(['full_name' => SORT_ASC]),
            'pagination' => [
                'pageSize' => Yii::$app->params['pagination']['authorsPageSize'],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Author model.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $subscriptionForm = new SubscriptionForm();
        $subscriptionForm->author_id = $id;

        if ($subscriptionForm->load(Yii::$app->request->post()) && $subscriptionForm->subscribe()) {
            Yii::$app->session->setFlash('success', 'Вы успешно подписались на уведомления о новых книгах автора ' . $model->full_name);
            return $this->refresh();
        }

        // Books by this author
        $booksDataProvider = new ActiveDataProvider([
            'query' => $model->getBooks(),
            'pagination' => [
                'pageSize' => Yii::$app->params['pagination']['authorBooksPageSize'],
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'subscriptionForm' => $subscriptionForm,
            'booksDataProvider' => $booksDataProvider,
        ]);
    }

    /**
     * Creates a new Author model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Author();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Автор успешно добавлен.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Author model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Автор успешно обновлен.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Author model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $authorName = $model->full_name;
        
        if ($model->delete()) {
            Yii::info("Author deleted: ID={$id}, Name=\"{$authorName}\"", __METHOD__);
            Yii::$app->session->setFlash('success', 'Автор успешно удален.');
        } else {
            Yii::error("Failed to delete author: ID={$id}, Name=\"{$authorName}\"", __METHOD__);
            Yii::$app->session->setFlash('error', 'Ошибка при удалении автора.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Author model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Author the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Author::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
    }
}

