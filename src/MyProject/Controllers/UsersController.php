<?php
namespace MyProject\Controllers;

use Vendor\Controllers\ParentController;
use MyProject\Models\Users\User;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\Users\UserActivationService;
use Vendor\Services\EmailSender;
use MyProject\Exceptions\ActivationException;
use MyProject\Models\Users\UserAuthService;

class UsersController extends ParentController 
{
    public function signUp(): void {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('signUp.php', ['error' => $e->getMessage()]);
                return;
            }
            if ($user instanceof User) {
                $code = UserActivationService::createActivationCode($user);
                EmailSender::send($user, 'Активация', 'userActivation.php', [
                    'userId' => $user->getId(),
                    'code' => $code
                ]);
                $this->view->renderHtml('signUpSuccessfull.php');
                return;
            }           
        }
        $this->view->renderHtml('signUp.php');
    }
    public function activate(int $userId, string $activationCode): void {
        try {
            $user = User::activate($userId, $activationCode);
        } catch (ActivationException $e) {
            $this->view->renderHtml('../errors/activationError.php', ['message' => $e->getMessage()]);
            return;
        }
        if ($user instanceof User) {
            UserActivationService::deleteActivationCode($user);
            $this->view->renderHtml('activationSuccessfull.php');
            return;
        }  
    }
    public function login(): void {
        if (!empty($_POST)) {
            try {
                $user = User::login($_POST);
                UserAuthService::createToken($user);
                header('Location: /');
                exit();
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('login.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('login.php');
    }
    public function logOut(): void {
        UserAuthService::deleteToken();
        header('Location: /');
        exit();
    }
}