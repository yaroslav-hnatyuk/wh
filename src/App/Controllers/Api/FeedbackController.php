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

    public function getOne($id)
    {
        return new JsonResponse($this->feedbackService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->feedbackService->getAll());
    }

    public function save(Request $request)
    {
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

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->feedbackService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->feedbackService->delete($id));
    }
}
