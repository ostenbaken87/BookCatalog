<?php

namespace app\controllers;

use Yii;
use app\models\Author;
use app\models\Book;
use yii\web\Controller;
use yii\db\Query;

class ReportController extends Controller
{
    /**
     * @return string
     */
    public function actionTopAuthors()
    {
        $year = Yii::$app->request->get('year', date('Y'));
        $years = $this->getAvailableYears();

        // Отчет - ТОП 10 авторов, выпустившие больше книг за какой-то год (доступен всем пользователям)
        $query = new Query();
        $topAuthors = $query
            ->select([
                'author.id',
                'author.full_name',
                'COUNT(book.id) as books_count'
            ])
            ->from('author')
            ->innerJoin('book_author', 'book_author.author_id = author.id')
            ->innerJoin('book', 'book.id = book_author.book_id AND book.year = :year', [':year' => $year])
            ->groupBy('author.id')
            ->orderBy('books_count DESC')
            ->limit(10)
            ->all();

        return $this->render('top-authors', [
            'topAuthors' => $topAuthors,
            'year' => $year,
            'years' => $years,
        ]);
    }

    /**
     * @return array
     */
    protected function getAvailableYears()
    {
        $years = Book::find()
            ->select('year')
            ->distinct()
            ->orderBy(['year' => SORT_DESC])
            ->column();

        return array_combine($years, $years);
    }
}

