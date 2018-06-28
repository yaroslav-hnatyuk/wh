<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Controllers\BaseController;
use Silex\Application;

class SettingsController extends BaseController
{

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getAll()
    {
        $this->checkPermissions(array('admin'));
        return new JsonResponse($this->app['settings.service']->getAll());
    }

    public function save(Request $request)
    {
        $this->checkPermissions(array('admin'));
        $settingsData = array();

        $lunchDiscount = array(
            'name' => 'lunch_discount',
            'value' => (int)$request->request->get('lunch_discount')
        );
        $weeklyDiscount = array(
            'name' => 'weekly_discount',
            'value' => (int)$request->request->get('weekly_discount')
        );
        $orderHourSetting = array(
            'name' => 'order_hour',
            'value' => (int)$request->request->get('order_hour')
        );

        if ($lunchDiscount['value'] >= 0 && $lunchDiscount['value'] < 100) {
            $settingsData[] = $lunchDiscount;
        } else {
            throw new \Exception("Incorrect lunch discount data");
        }

        if ($weeklyDiscount['value'] >= 0 && $weeklyDiscount['value'] < 100) {
            $settingsData[] = $weeklyDiscount;
        } else {
            throw new \Exception("Incorrect weekly discount data");
        }

        if ($orderHourSetting['value'] >= 0 && $orderHourSetting['value'] <= 24) {
            $settingsData[] = $orderHourSetting;
        } else {
            throw new \Exception("Incorrect order hour data");
        }

        return new JsonResponse(
            $this->app['settings.service']->saveSettings($settingsData)
        );
    }

}
