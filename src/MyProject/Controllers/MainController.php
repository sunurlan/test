<?php
namespace MyProject\Controllers;

use MyProject\Models\Articles\Article;
use Vendor\Controllers\ParentController;

class MainController extends ParentController
{   
    public function main()
    {
        $articles = Article::findAll();
        $this->view->renderHtml('main.php', ['articles' => $articles]);
    }
    public function sayHello(string $name)
    {
        $this->view->renderHtml('hello.php', ['name' => $name]);
    }
    public function sayBye(string $name)
    {
        $this->view->renderHtml('bye.php', ['name' => $name]);
    }
}