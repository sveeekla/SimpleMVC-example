<?php 
use ItForFree\SimpleMVC\Router\WebRouter;
?>

<h1>Подкатегория: <?= htmlspecialchars($subcategory->name) ?></h1>

<?php if (!empty($articles)): ?>
    <ul id="headlines">
    <?php foreach ($articles as $article): ?>
        <li>
            <h2>
                <a href="<?= WebRouter::link('article/view&id=' . $article['id']) ?>">
                    <?= htmlspecialchars($article['title']) ?>
                </a>
            </h2>
            <p class="pubDate"><?= date('j F Y', strtotime($article['publicationDate'])) ?></p>
            <p class="summary"><?= htmlspecialchars(mb_substr($article['content'], 0, 200, 'UTF-8')) ?>...</p>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>В этой подкатегории пока нет статей.</p>
<?php endif; ?>

<p><a href="./">Вернуться на главную</a></p>