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
     * Display TOP-10 authors report by year
     * 
     * @return string the rendering result
     */
    public function actionTopAuthors()
    {
        $year = Yii::$app->request->get('year', date('Y'));
        $years = $this->getAvailableYears();

        // Cache key based on year
        $cacheKey = "top_authors_report_{$year}";
        
        // Try to get from cache
        $topAuthors = Yii::$app->cache->get($cacheKey);
        
        if ($topAuthors === false) {
            // Отчет - ТОП 10 авторов, выпустившие больше книг за какой-то год
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
                ->limit(Yii::$app->params['pagination']['topAuthorsLimit'])
                ->all();
            
            // Cache for 1 hour (3600 seconds)
            Yii::$app->cache->set($cacheKey, $topAuthors, 3600);
        }

        return $this->render('top-authors', [
            'topAuthors' => $topAuthors,
            'year' => $year,
            'years' => $years,
        ]);
    }

    /**
     * Get list of available years from books
     * 
     * @return array year list for dropdown
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

