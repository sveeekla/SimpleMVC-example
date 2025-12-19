<?php
namespace application\models;

/* 
 * class Article
 * Модель для работы со статьями
 */

class Article extends BaseExampleModel {
    
    public string $tableName = "articles";
    
    public string $orderBy = 'publicationDate DESC'; // Изменено на DESC для новых статей первыми
    
    public ?int $id = null;
    
    public ?string $title = null;
    
    public ?string $summary = null; // Добавлено
    
    public ?string $content = null;
    
    public ?string $publicationDate = null;

    public ?int $categoryId = null;

    public ?int $subcategoryId = null;
    
    public ?int $active = 1; // Добавлено
    
    public function insert()
    {
        $sql = "INSERT INTO $this->tableName 
                (title, summary, content, publicationDate, categoryId, subcategoryId, active) 
                VALUES (:title, :summary, :content, :publicationDate, :categoryId, :subcategoryId, :active)"; 
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":publicationDate", $this->publicationDate ?? (new \DateTime('NOW'))->format('Y-m-d'), \PDO::PARAM_STR);
        $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary ?? '', \PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
        $st->bindValue(":categoryId", $this->categoryId ?? 1, \PDO::PARAM_INT);
        $st->bindValue(":subcategoryId", $this->subcategoryId, \PDO::PARAM_INT);
        $st->bindValue(":active", $this->active ?? 1, \PDO::PARAM_INT);
        
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
    }
    
    public function update()
    {
        $sql = "UPDATE $this->tableName SET 
                publicationDate = :publicationDate, 
                title = :title, 
                summary = :summary,
                content = :content,
                categoryId = :categoryId,
                subcategoryId = :subcategoryId,
                active = :active
                WHERE id = :id";  
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":publicationDate", $this->publicationDate ?? (new \DateTime('NOW'))->format('Y-m-d'), \PDO::PARAM_STR);
        $st->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary ?? '', \PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, \PDO::PARAM_STR);
        $st->bindValue(":categoryId", $this->categoryId ?? 1, \PDO::PARAM_INT);
        $st->bindValue(":subcategoryId", $this->subcategoryId, \PDO::PARAM_INT);
        $st->bindValue(":active", $this->active ?? 1, \PDO::PARAM_INT);
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        $st->execute();
    }

    // Добавляем метод для получения названия категории
    public function getCategoryName()
    {
        if ($this->categoryId) {
            $sql = "SELECT name FROM categories WHERE id = :id";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":id", $this->categoryId, \PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch();
            return $row ? $row['name'] : 'Без категории';
        }
        return 'Без категории';
    }

    public function getSubcategoryName()
    {
        if ($this->subcategoryId) {
            $sql = "SELECT name FROM subcategories WHERE id = :id";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":id", $this->subcategoryId, \PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch();
            return $row ? $row['name'] : 'Без подкатегории';
        }
        return 'Без подкатегории';
    }

    public function getCategoryNameForId($categoryId)
    {
        if ($categoryId) {
            $sql = "SELECT name FROM categories WHERE id = :id";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":id", $categoryId, \PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch();
            return $row ? $row['name'] : null;
        }
        return null;
    }

    public function getSubcategoryNameForId($subcategoryId)
    {
        if ($subcategoryId) {
            $sql = "SELECT name FROM subcategories WHERE id = :id";
            $st = $this->pdo->prepare($sql);
            $st->bindValue(":id", $subcategoryId, \PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch();
            return $row ? $row['name'] : null;
        }
        return null;
    }

    // Получить авторов статьи (пользователей) через article_users
    public function getAuthors($articleId = null)
    {
        $articleId = $articleId ?? $this->id;
        if (!$articleId) return [];
        
        $sql = "SELECT u.* FROM users u 
                JOIN article_users au ON u.id = au.user_id 
                WHERE au.article_id = :article_id 
                ORDER BY u.login";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":article_id", $articleId, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_OBJ);
    }

    // Добавить автора (пользователя)
    public function addAuthor($userId)
    {
        $sql = "INSERT IGNORE INTO article_users (article_id, user_id) VALUES (:article_id, :user_id)";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":article_id", $this->id, \PDO::PARAM_INT);
        $st->bindValue(":user_id", $userId, \PDO::PARAM_INT);
        return $st->execute();
    }

    // Удалить автора
    public function removeAuthor($userId)
    {
        $sql = "DELETE FROM article_users WHERE article_id = :article_id AND user_id = :user_id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":article_id", $this->id, \PDO::PARAM_INT);
        $st->bindValue(":user_id", $userId, \PDO::PARAM_INT);
        return $st->execute();
    }

    // Получить авторов статьи (только имена)
    public function getAuthorNames()
    {
        $authors = $this->getAuthors();
        $names = [];
        foreach ($authors as $author) {
            $names[] = $author->login;
        }
        return implode(', ', $names);
    }

    public function getAuthorsForArticle($articleId)
    {
        $sql = "SELECT u.* FROM users u 
                JOIN article_users au ON u.id = au.user_id 
                WHERE au.article_id = :article_id 
                ORDER BY u.login";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":article_id", $articleId, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_OBJ);
    }

    // Метод для получения всех статей с информацией о категориях
    public function getListWithCategories($numRows = 1000000, $order = "publicationDate DESC", $activeOnly = true)
    {
        $whereClause = $activeOnly ? "WHERE a.active = 1" : "";
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.*, 
                       c.name as category_name,
                       s.name as subcategory_name
                FROM articles a
                LEFT JOIN categories c ON a.categoryId = c.id
                LEFT JOIN subcategories s ON a.subcategoryId = s.id
                $whereClause
                ORDER BY $order 
                LIMIT :numRows";
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":numRows", $numRows, \PDO::PARAM_INT);
        $st->execute();
        
        $list = $st->fetchAll(\PDO::FETCH_ASSOC);
        
        // Получаем общее количество записей
        $sql = "SELECT FOUND_ROWS() as totalRows";
        $st = $this->pdo->query($sql);
        $totalRows = $st->fetch()['totalRows'];
        
        return [
            'results' => $list,
            'totalRows' => $totalRows
        ];
    }

    // Метод для получения статей по категории
    public function getByCategory($categoryId, $activeOnly = true)
    {
        $whereClause = $activeOnly ? "AND a.active = 1" : "";
        
        $sql = "SELECT a.*, c.name as category_name
                FROM articles a
                LEFT JOIN categories c ON a.categoryId = c.id
                WHERE a.categoryId = :categoryId $whereClause
                ORDER BY a.publicationDate DESC";
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":categoryId", $categoryId, \PDO::PARAM_INT);
        $st->execute();
        
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Метод для получения статей по подкатегории
    public function getBySubcategory($subcategoryId, $activeOnly = true)
    {
        $whereClause = $activeOnly ? "AND a.active = 1" : "";
        
        $sql = "SELECT a.*, s.name as subcategory_name, c.name as category_name
                FROM articles a
                LEFT JOIN subcategories s ON a.subcategoryId = s.id
                LEFT JOIN categories c ON s.categoryId = c.id
                WHERE a.subcategoryId = :subcategoryId $whereClause
                ORDER BY a.publicationDate DESC";
        
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":subcategoryId", $subcategoryId, \PDO::PARAM_INT);
        $st->execute();
        
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Метод для активации/деактивации статьи
    public function toggleActive()
    {
        if (!$this->id) return false;
        
        $sql = "UPDATE $this->tableName SET active = NOT active WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        $result = $st->execute();
        
        if ($result) {
            $this->active = !$this->active;
        }
        
        return $result;
    }
}