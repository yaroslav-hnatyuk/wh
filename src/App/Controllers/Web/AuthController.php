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

        // if (!password_verify($password, $user->pass)) {
        //     return new JsonResponse(
        //         array(
        //             'status' => 'ERROR',
        //             'message' => 'Login failed! Please check your email and password.'
        //         )
        //     );
        // }

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

    public function registration(Request $request, $cid) {

        $week = $request->query->get('week');
        $year = $request->query->get('year');
        $period = $this->app['orders.service']->getPeriodForYearAndWeek($year, $week, 'week');
        $menu = $this->app['menudishes.service']->getForPeriodForOrders($period);
        $menu = $this->app['menudishes.service']->groupMenuDishes($menu, true);

        return $this->app['twig']->render('login/register.twig', array(
            'period' => $period,
            'menu' => $menu,
            'cid' => $cid,
        ));
    }

    public function restore() {
        return $this->app['twig']->render('login/restore.twig');
    }

    public function agreement() {
        return $this->app['twig']->render('login/agreement.twig');
    }

    public function sendpass(Request $request) {
        $result = false;
        $email = $request->request->get('email');
        
        try {
            $user = $this->usersService->getByEmail($email);
            if ($user->id) {
                $newPass = uniqid();
                $result = $this->usersService->updatePass($user->id, password_hash($newPass, PASSWORD_BCRYPT));

                if ($result) {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('[Walnut House] New password')
                        ->setFrom(array('noreply@walnut.house'))
                        ->setTo(array($email))
                        ->setBody("Ваш новий пароль: {$newPass}");
    
                    $this->app['mailer']->send($message);
                } else {
                    throw new \Exception("DB record was not updated.");
                }
            } else {
                throw new \Exception("Email not found.");
            }
        } catch (\Exception $exc) {
            throw new \Exception("Password was not updated. Something went wrong.");
        }

        return json_encode(array($result));
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
        $user->is_feedback_active = 1;
        $user->is_active = 1;
        $user->reminders = 0;
        $user->feedback_count = 0;
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

    public function logout() {
        if (isset($_COOKIE['X-AUTH-TOKEN'])) {
            unset($_COOKIE['X-AUTH-TOKEN']);
            setcookie('X-AUTH-TOKEN', '', time() - 3600, '/');
        }
        session_destroy();
        return new RedirectResponse('/login');
    }
}
