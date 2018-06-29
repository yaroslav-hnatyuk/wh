<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class UsersController extends BaseController
{

    protected $usersService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->usersService = $service;
    }

    public function saveCurrent(Request $request)
    {
        $this->checkPermissions(array('user'));

        $userData = array(
            'id' => $this->getUser()->id,
            'first_name' => $request->request->get('first_name'),
            'last_name' => $request->request->get('last_name'),
            'email' => $request->request->get('email')
        );
        
        $token = null;
        $user = $this->usersService->findOne($this->getUser()->id);
        if ($user && $this->usersService->updatePersonalData($userData)) {
            $token = hash('sha256', $userData['email'] . 'bAziNgA' . $user['role'] . $user['salt']);
        }

        return new JsonResponse(
            array('token' => $token)
        );
    }

    public function update($id, Request $request)
    {
        $this->checkPermissions(array('admin'));
        $data = json_decode($request->getContent(), true);

        return new JsonResponse(
            $this->usersService->update($id, $data['email'])
        );
    }

    public function delete($id)
    {
        $this->checkPermissions(array('admin'));
        return new JsonResponse($this->usersService->changeActive($id));
    }
}
