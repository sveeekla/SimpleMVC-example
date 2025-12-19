<?php
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');
?>

<h2><?= $viewArticles->title ?>
    <span>
        <?php if ($User->isAllowed("admin/articles/edit") && strpos($_SERVER['REQUEST_URI'], 'admin/') !== false): ?>
        <?= $User->returnIfAllowed("admin/articles/edit", 
            "<a href=" . \ItForFree\SimpleMVC\Router\WebRouter::link("admin/articles/edit&id=". $viewArticles->id) 
            . ">[Редактировать]</a>");?>
        
        <?= $User->returnIfAllowed("admin/articles/delete",
                "<a href=" . \ItForFree\SimpleMVC\Router\WebRouter::link("admin/articles/delete&id=". $viewArticles->id)
            .    ">[Удалить]</a>"); ?>
        <?php endif; ?>
    </span>
    
</h2> 

<p>Контент: <?= $viewArticles->content ?></p>
<p>Зарегестрирована: <?= $viewArticles->publicationDate ?></p>