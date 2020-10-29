<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\Numeric;


class UserController extends ControllerBase
{
    public function loginAction()
    {
        $form = new Form();
        $form->add(
            new Email('email')
        );
        $form->add(
            new Password('password')
        );
        $this->view->form = $form;

    }

    public function registerAction()
    {
        $form = new Form();
        $form->add(
            new Text('name')
        );
        $form->add(
            new Numeric('phone')
        );
        $form->add(
            new Email('email')
        );
        $form->add(
            new Password('password')
        );
        $this->view->form = $form;
    }

    public function createAction()
    {
        $user = new Users();
        $user->name = $this->request->getPost('name');
        $user->email = $this->request->getPost('email');
        $user->phone = $this->request->getPost('phone');
        $user->password = $this->security->hash($this->request->getPost("password"));
        $user->save();
        return $this->response->redirect('index/index');
    }

    public function authorizeAction()
    {

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user = Users::findFirstByEmail($email);
        if($user)
        {

            if($this->security->checkHash($password, $user->password))
            {
                $this->session->set('auth',['userName' => $user->name]);
                $this->session->set('user',$user);
                $this->flash->success("Welcome back " . $user->name);

                return $this->dispatcher->forward(["controller" => "index","action" => "index"]);
            }
            else
            {
                $this->flash->error("Your password is incorrect - try again");

                return $this->dispatcher->forward(["controller" => "index","action" => "index"]);
            }
        }
        else
        {
            $this->flash->error("That username was not found - try again");
            return $this->dispatcher->forward(["controller" => "user","action" => "login"]);
        }
        var_dump('not you');
        die();
    }

    public function logoutAction()
    {
        $this->session->destroy();
        return $this->dispatcher->forward(["controller" => "index","action" => "index"]);
    }

}