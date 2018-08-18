<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class RatingsController extends BaseController
{

    protected $ratingsService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->ratingsService = $service;
    }

    public function save(Request $request)
    {
        $this->checkPermissions(array('user'));
        
        $rating = array(
            'user_id' => $this->getUser()->id,
            'dish_id' => $request->request->get('dish_id'),
            'mark' => $request->request->get('mark')
        );
        
        return new JsonResponse(
            $this->ratingsService->save($rating)
        );
    }

    public function dish($id)
    {
        $this->checkPermissions(array('admin', 'manager'));
        return new JsonResponse($this->ratingsService->getGroupedByDishId($id));
    }
}
