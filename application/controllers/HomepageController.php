<?php

namespace application\controllers;

/**
 * Контроллер для домашней страницы
 */
class HomepageController extends \ItForFree\SimpleMVC\MVC\Controller
{
    /**
     * @var string Название страницы
     */
    public $homepageTitle = "Домашняя страница";
    
    /**
     * @var string Пусть к файлу макета 
     */
    public string $layoutPath = 'main.php';
      
    /**
     * Выводит на экран страницу "Домашняя страница"
     */
    public function indexAction()
    {
        $Article = new \application\models\Article();
        $articlesData = $Article->getListWithCategories();
        $articles = $articlesData['results'];
        
        // Загружаем информацию об авторах для каждой статьи
        foreach ($articles as &$article) {
            $articleObj = new \application\models\Article();
            $authors = $articleObj->getAuthorsForArticle($article['id']);
            $article['authors'] = $authors;
            
            // Добавляем названия категорий и подкатегорий
            $article['categoryName'] = $article['category_name'] ?? null;
            $article['categoryId'] = $article['categoryId'] ?? null;
            $article['subcategoryName'] = $article['subcategory_name'] ?? null;
            $article['subcategoryId'] = $article['subcategoryId'] ?? null;
        }
        
        // Получаем список подкатегорий
        $Subcategory = new \application\models\Subcategory();
        $subcategoriesData = $Subcategory->getList(100);
        $subcategories = $subcategoriesData['results'];
        
        // Получаем список категорий
        $Category = new \application\models\Category();
        $categoriesData = $Category->getList(100);
        $categories = $categoriesData['results'];
        
        $this->view->addVar('homepageTitle', $this->homepageTitle);
        $this->view->addVar('articles', $articles);
        $this->view->addVar('subcategories', $subcategories);
        $this->view->addVar('categories', $categories);
        $this->view->render('homepage/index.php');
    }
}

