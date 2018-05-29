<?php

namespace App\Controllers\Web;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use App\Services\UsersService;


class AuthController
{
    const ONE_YEAR = 31556926;
    protected $app;
    protected $usersService;

    public function __construct(Application $app, UsersService $usersService) 
    {
        $this->app = $app;
        $this->usersService = $usersService;
    }

    public function index()
    {
        return $this->app['twig']->render('login/index.twig', array(
            'name' => $name,
        ));
    }

    public function login(Request $request)
    {
        $email = $request->request->get('email');
        $user = $this->usersService->getByEmail($email);

        if ($user->id !== null) {
            $response = new RedirectResponse('/order');
            $response->headers->set('X-AUTH-TOKEN', $user->email . ':secret');
        } else {
            $response = new RedirectResponse('/login');
        }

        return $response;
    }
}
