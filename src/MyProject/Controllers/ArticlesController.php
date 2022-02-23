<?php
namespace MyProject\Controllers;

use Vendor\Controllers\ParentController;
use MyProject\Models\Articles\Article;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UnAuthorizedException;
use MyProject\Exceptions\Forbidden;
use MyProject\Exceptions\InvalidArgumentException;

class ArticlesController extends ParentController
{
    public function view(int $articleId): void {
        $article = Article::getById($articleId);
        if ($article === null) {
            throw new NotFoundException;
        }
        $this->view->renderHtml('view.php', ['article' => $article]);
    }
    public function edit(int $articleId): void {
        $article = Article::getById($articleId);
        if ($article === null) {
            throw new NotFoundException;
        }
        $article->setName('Новое название статьи');
        $article->setText('Новый текст статьи');
        $article->save();
    }
    public function add(): void {
        if ($this->user === null) {
            throw new UnAuthorizedException;
        }
        if ($this->user->isAdmin() === false) {
            throw new Forbidden('Для добавления статьи нужно обладать правами администратора');
        }
        if (!empty($_POST)) {
            try {
                $article = Article::createFromArray($_POST, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('add.php', ['error' => $e->getMessage()]);
                return;
            }
            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }
        $this->view->renderHtml('add.php');
    }
    public function delete(int $articleId): void {
        $article = Article::getById($articleId);
        if ($article === null) {
            throw new NotFoundException;
        }
        $article->delete();
        $this->view->renderHtml('deletionSuccessfull.php', ['article' => $article]);
    }
}