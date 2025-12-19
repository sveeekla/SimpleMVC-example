<?php
namespace application\models;

class Category extends BaseExampleModel 
{
    public string $tableName = "categories";
    public string $orderBy = 'name ASC';
    
    public ?int $id = null;
    public $name = null;
    public $description = null;
    
    public function insert()
    {
        $sql = "INSERT INTO $this->tableName (name, description) VALUES (:name, :description)"; 
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":name", $this->name, \PDO::PARAM_STR);
        $st->bindValue(":description", $this->description, \PDO::PARAM_STR);
        $st->execute();
        $this->id = $this->pdo->lastInsertId();
    }
    
    public function update()
    {
        $sql = "UPDATE $this->tableName SET name = :name, description = :description WHERE id = :id";  
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":name", $this->name, \PDO::PARAM_STR);
        $st->bindValue(":description", $this->description, \PDO::PARAM_STR);
        $st->bindValue(":id", $this->id, \PDO::PARAM_INT);
        $st->execute();
    }  
    
        /**
     * Получить подкатегорию по ID
     */
    public function getSubcategoryById($id)
    {
        $sql = "SELECT s.*, c.name as category_name 
                FROM subcategories s 
                LEFT JOIN categories c ON s.categoryId = c.id 
                WHERE s.id = :id";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(":id", $id, \PDO::PARAM_INT);
        $st->execute();
        return $st->fetch(\PDO::FETCH_ASSOC);
    }
}