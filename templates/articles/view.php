<?php include __DIR__ . '/../header.php'; ?>
    <h4>Автор: <?= $article->getAuthor()->getNickname() ?></h4>
    <h2><?= $article->getName() ?></h2>
    <p><?= $article->getText() ?></p>
<?php include __DIR__ . '/../footer.php'; ?>