<?php

namespace App\Controllers\Web;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use App\Services\UsersService;
use App\Entities\User;


class ErrorsController
{
    
    public function error40x() {
        require_once dirname(ROOT_PATH) . "/views/errors/40x.html";
    }
    
    public function error50x() {
        require_once dirname(ROOT_PATH) . "/views/errors/50x.html";
    }

}
