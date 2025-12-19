<?php
namespace application\controllers;

use application\models\Article;
use ItForFree\SimpleMVC\Config;

class ArticleController extends \ItForFree\SimpleMVC\MVC\Controller
{
    public string $layoutPath = 'main.php';
    
    public function viewAction()
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect(Config::get('core.router.class')::link(''));
        }
        
        $articleModel = new Article();
        $article = $articleModel->getById($id);
        
        if (!$article) {
            // Статья не найдена
            $this->view->addVar('message', 'Статья не найдена');
            $this->view->render('error.php');
            return;
        }
        
        $this->view->addVar('viewArticles', $article);
        $this->view->render('article/view-item.php');
    }
}