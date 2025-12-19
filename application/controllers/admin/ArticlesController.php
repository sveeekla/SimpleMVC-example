<?php
namespace application\controllers\admin;
use application\models\Article;
use ItForFree\SimpleMVC\Config;

/* 
 *   Class-controller articles
 * 
 * 
 */

class ArticlesController extends \ItForFree\SimpleMVC\MVC\Controller
{
    
    public string $layoutPath = 'admin-main.php';
    
    
    public function indexAction()
    {
        $Article = new Article();

        $articleId = $_GET['id'] ?? null;
        
        if ($articleId) { // если указан конкретная статья
            $viewArticles = $Article->getById($_GET['id']);
            $this->view->addVar('viewArticles', $viewArticles);
            $this->view->render('article/view-item.php');
        } else { // выводим полный список
            
            $articles = $Article->getList()['results'];
            $this->view->addVar('articles', $articles);
            $this->view->render('article/index.php');
        }
    }
    
    /**
     * Выводит на экран форму для создания новой статьи (только для Администратора)
     */
    public function addAction()
    {
        $Url = Config::get('core.router.class');
        if (!empty($_POST)) {
            if (!empty($_POST['saveNewArticle'])) {
                $Article = new Article();
                $newArticle = $Article->loadFromArray($_POST);
                $newArticle->insert(); 
                $this->redirect($Url::link("admin/articles/index"));
            } 
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/articles/index"));
            }
        }
        else {
            $addArticleTitle = "Добавление новой статьи";
            $this->view->addVar('addArticleTitle', $addArticleTitle);
            
            $this->view->render('article/add.php');
        }
    }
    
    /**
     * Выводит на экран форму для редактирования статьи (только для Администратора)
     */
    public function editAction()
    {
        $id = $_GET['id'];
        $Url = Config::get('core.router.class');
        
        if (!empty($_POST)) { // это выполняется нормально.
            
            if (!empty($_POST['saveChanges'] )) {
                $Article = new Article();
                $newArticle = $Article->loadFromArray($_POST);
                $newArticle->update();
                $this->redirect($Url::link("admin/articles/index&id=$id"));
            } 
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/articles/index&id=$id"));
            }
        }
        else {
            $Article = new Article();
            $viewArticles = $Article->getById($id);
            
            $editArticleTitle = "Редактирование статьи";
            
            $this->view->addVar('viewArticles', $viewArticles);
            $this->view->addVar('editArticleTitle', $editArticleTitle);
            
            $this->view->render('article/edit.php');
        }
        
    }
    
    /**
     * Выводит на экран предупреждение об удалении данных (только для Администратора)
     */
    public function deleteAction()
    {
        $id = $_GET['id'];
        $Url = Config::get('core.router.class');
        
        if (!empty($_POST)) {
            if (!empty($_POST['deleteArticle'])) {
                $Article = new Article();
                $newArticle = $Article->loadFromArray($_POST);
                $newArticle->delete();
                
                $this->redirect($Url::link("admin/articles/index"));
              
            }
            elseif (!empty($_POST['cancel'])) {
                $this->redirect($Url::link("admin/articles/edit&id=$id"));
            }
        }
        else {
            
            $Article = new Article();
            $deletedArticle = $Article->getById($id);
            $deleteArticleTitle = "Удалить статью?";
            
            $this->view->addVar('deleteArticleTitle', $deleteArticleTitle);
            $this->view->addVar('deletedArticle', $deletedArticle);
            
            $this->view->render('article/delete.php');
        }
    }
    
    public function getSubcategoriesAction()
    {
        $categoryId = $_GET['categoryId'] ?? null;
        
        if (!$categoryId) {
            echo json_encode([]);
            return;
        }
        
        $subcategoryModel = new \application\models\Subcategory();
        $sql = "SELECT id, name FROM subcategories WHERE categoryId = :categoryId ORDER BY name";
        $st = $subcategoryModel->pdo->prepare($sql);
        $st->bindValue(":categoryId", $categoryId, \PDO::PARAM_INT);
        $st->execute();
        $subcategories = $st->fetchAll(\PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode($subcategories);
    }

}