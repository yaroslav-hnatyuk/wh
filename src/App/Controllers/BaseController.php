<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\Request;


abstract class BaseController
{
    protected $app;
    protected $user = null;

    protected function getUser()
    {
        if (!$this->user) {
            $token = $this->app['security.token_storage']->getToken();
            if (null !== $token) {
                $user = $token->getUser();
                $this->user = $this->app['users.service']->getByEmail($user->getUsername());
            }
        }

        return $this->user;
    }
}
