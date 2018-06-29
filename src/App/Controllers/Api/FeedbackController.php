<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class FeedbackController extends BaseController
{

    protected $feedbackService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->feedbackService = $service;
    }

    public function getAll()
    {
        $this->checkPermissions(array('admin', 'manager'));
        return new JsonResponse($this->feedbackService->getAll());
    }

    public function save(Request $request)
    {
        $this->checkPermissions(array('user'));

        $feedbackList = array();
        $created = date("Y-m-d");
        $feedbacksData = json_decode($request->getContent(), true);
        foreach ($feedbacksData as $feedback) {
            if ($feedback['text'] && $feedback['dish_id']) {
                $feedbackList[] = array(
                    'text' => $feedback['text'],
                    'created' => $created,
                    'user_id' => $this->getUser()->id,
                    'dish_id' => $feedback['dish_id']
                );
            }
        }

        $this->app['users.service']->setFeedbackInactive($this->getUser()->id);

        return new JsonResponse(
            $this->feedbackService->saveFeedback($feedbackList)
        );
    }

    public function dish($id)
    {
        $this->checkPermissions(array('admin', 'manager'));
        return new JsonResponse($this->feedbackService->getAllByDishId($id));
    }

}
