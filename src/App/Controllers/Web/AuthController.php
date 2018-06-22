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


class AuthController
{
    const ONE_YEAR = 31556926;
    const salt = "GF2!M4K1dDGFizXNoG9Ar7fghjI3bGAna$65e";
    protected $app;
    protected $usersService;

    public function __construct(Application $app, UsersService $usersService) 
    {
        $this->app = $app;
        $this->usersService = $usersService;
    }

    public function index() {
        return $this->app['twig']->render('login/index.twig');
    }

    public function login(Request $request) {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $user = $this->usersService->getByEmail($email);

        if ($user->id === null) {
            return new JsonResponse(
                array(
                    'status' => 'ERROR',
                    'message' => 'Login failed! Please check your email and password.'
                )
            );
        }

        if (!password_verify($password, $user->pass)) {
            return new JsonResponse(
                array(
                    'status' => 'ERROR',
                    'message' => 'Login failed! Please check your email and password.'
                )
            );
        }

        return new JsonResponse(
            array(
                'status' => 'OK',
                'message' => 'Login successful!',
                'token' =>  hash('sha256', $user->email . 'bAziNgA' . $user->role . $user->salt),
                'user' => array(
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name
                )
            )
        );
    }

    public function registration($cid) {
        return $this->app['twig']->render('login/register.twig', array(
            'cid' => $cid,
        ));
    }

    public function register(Request $request) {
        $data = $request->request->all();
        $user = new User($data);

        $office = null;
        if (isset($data['cid']) && !empty($data['cid'])) {
            $office = $this->app['offices.service']->getOneByUid($data['cid']);
        }

        if (!$office) {
            throw new \App\Exception\ApiException("При перевірці даних виникла помилка. Перевірте будь-ласка введені дані та посилання ");
        }

        $user->role = 'user';
        $user->phone = 'n/a';
        $user->pass = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->salt = hash('sha256', md5($data['email'] . uniqid(rand(), TRUE)));
        $user->office_id = $office->id;

        $this->usersService->save($user->getArray());

        return new JsonResponse(
            array(
                'status' => 'OK',
                'message' => 'Login successful!',
                'token' => hash('sha256', $user->email . 'bAziNgA' . $user->role . $user->salt),
                'user' => array(
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name
                )
            )
        );
    }
}
