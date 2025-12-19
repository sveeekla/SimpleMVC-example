<?php 
use ItForFree\SimpleMVC\Config;
use application\models\Category;
use application\models\Subcategory;
use ItForFree\SimpleMVC\Router\WebRouter;

$User = Config::getObject('core.user.class');
?>
<?php include('includes/admin-articles-nav.php'); ?>

<h2>List articles</h2>

<?php if (!empty($articles)): ?>
<table class="table">
    <thead>
    <tr>
      <th scope="col">Оглавление</th>
      <th scope="col">Посвящается</th>
      <th scope="col">Дата</th>
      <th scope="col">Категория</th>
      <th scope="col">Подкатегория</th>
      <th scope="col"></th>
    </tr>
     </thead>
    <tbody>
    <?php foreach($articles as $article): ?>
    <tr>
        <td> <?= "<a href=" . WebRouter::link('admin/articles/index&id=' 
		. $article->id . ">{$article->title}</a>" ) ?> </td>
        <td><?= htmlspecialchars(mb_substr($article->content, 0, 30, 'UTF-8')) ?>...</td>
        <td> <?= $article->publicationDate ?> </td>
        
        <td>
            <?php if ($article->categoryId): ?>
                <?php 
                $categoryModel = new Category();
                $category = $categoryModel->getById($article->categoryId);
                if ($category): ?>
                    <a href="<?= WebRouter::link('admin/adminCategories/edit&id=' . $category->id) ?>">
                        <?= htmlspecialchars($category->name) ?>
                    </a>
                <?php else: ?>
                    <span class="text-muted">Категория удалена</span>
                <?php endif; ?>
            <?php else: ?>
                <span class="text-muted">Без категории</span>
            <?php endif; ?>
        </td>

        <td>
            <?php if ($article->subcategoryId): ?>
                <?php 
                $subcategoryModel = new Subcategory();
                $subcategory = $subcategoryModel->getById($article->subcategoryId);
                if ($subcategory): ?>
                    <span class="text-muted">
                        <?= htmlspecialchars($subcategory->name) ?>
                    </span>
                <?php else: ?>
                    <span class="text-muted">Подкатегория неопределена</span>
                <?php endif; ?>
            <?php else: ?>
                <span class="text-muted">Без подкатегории</span>
            <?php endif; ?>
        </td>

    </tr>
    <?php endforeach; ?>

    </tbody>
</table>

<?php else:?>
    <p> Список статей пуст</p>
<?php endif; ?>