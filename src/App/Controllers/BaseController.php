<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


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

    protected function checkPermissions($allowedRoles)
    {
        if (!in_array($this->getUser()->role, $allowedRoles, true)) {
            throw new NotFoundHttpException("Access not allowed");
        }
    }
}
