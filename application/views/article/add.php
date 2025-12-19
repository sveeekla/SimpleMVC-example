<style> 
    
    textarea{
        height: 200%;
        width: 1110px;
        color: #003300;
    }
   
</style>

<?php include('includes/admin-articles-nav.php'); ?>
<h2><?= $addArticleTitle ?></h2>

<form id="addArticle" method="post" action="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("admin/articles/add")?>">
    <div class="form-group">
        <label for="title">Название новой статьи</label>
        <input type="text" class="form-control" name="title" id="title" placeholder="имя статьи">
    </div>
    <div class="form-group">
        <label for="content">Содержание</label><br>
        <textarea type="description" name="content" placeholder="описание статьи"  value=></textarea>
    </div>
    <input type="submit" class="btn btn-primary" name="saveNewArticle" value="Сохранить">
    <input type="submit" class="btn" name="cancel" value="Назад">
</form>    