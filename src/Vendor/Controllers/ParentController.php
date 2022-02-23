<?php
namespace Vendor\Controllers;

use MyProject\Models\Users\User;
use Vendor\View\View;
use MyProject\Models\Users\UserAuthService;

abstract class ParentController
{   
    /** @var View */
    protected $view;

    /** @var User|null */
    protected $user;

    public function __construct() {
        $this->user = UserAuthService::getUserByToken();

        $className = static::class;
        $pieces = explode('\\', $className);
        $shortName = array_pop($pieces);
        $viewFolder = lcfirst(substr($shortName,0,-10));
        $this->view = new View(__DIR__ . '/../../../templates/' . $viewFolder);

        // get shortName of static Class using \ReflectionClass
        // return;
        // $reflector = new \ReflectionClass($className);
        // $viewFolder = lcfirst(substr($reflector->getShortName(),0,-10));
        // $this->view = new View(__DIR__ . '/../../../templates/' . $viewFolder);
        
        $this->view->setVar('user', $this->user);
    }
}