<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;

class FiltersController
{
    const FILTER_PERIOD_WEEK = 'week';
    const FILTER_PERIOD_2WEEKS = '2weeks';

    public function __construct(Application $app) 
    {
        $this->app = $app;
    }

    public function index(Request $request)
    {
        $company = $request->query->get('company');
        $office = $request->query->get('office');
        $user = $request->query->get('user');
        
        $params = $request->query->all();
        if (isset($params['filter_period_week'])) {
            $this->app['session']->set('filter_period', self::FILTER_PERIOD_WEEK);
        } elseif (isset($params['filter_period_2weeks'])) {
            $this->app['session']->set('filter_period', self::FILTER_PERIOD_2WEEKS);
        }

        $sCompany = $this->app['session']->get('filter_company');
        $sOffice = $this->app['session']->get('filter_office');
        $sUser = $this->app['session']->get('filter_user');
        $sPeriod = $this->app['session']->get('filter_period');

        $queryParams = "";
        $year = $request->query->get('selected_year');
        $week = $request->query->get('selected_week');
        if ($year || $week) {
            $queryParams = "?year={$year}&week={$week}";
        }

        if ($company && ($company === 'none' || $company != $sCompany)) {
            $this->app['session']->remove('filter_office');
            $this->app['session']->remove('filter_user');
            if ($company === 'none') {
                $this->app['session']->remove('filter_company');
            } elseif ($company != $sCompany) {
                $this->app['session']->set('filter_company', $company);
            }
            return new RedirectResponse('/order' . $queryParams);
        }

        if ($office && ($office === 'none' || $office != $sOffice)) {
            $this->app['session']->remove('filter_user');
            if ($office === 'none') {
                $this->app['session']->remove('filter_office');
            } else if ($office != $sOffice) {
                $this->app['session']->set('filter_office', $office);
            }
            return new RedirectResponse('/order' . $queryParams);
        }

        if ($user && ($user === 'none' || $user != $sUser)) {
            if ($user === 'none') {
                $this->app['session']->remove('filter_user');
            } else if ($user != $sUser) {
                $this->app['session']->set('filter_user', $user);
            }
        }
        
        return new RedirectResponse('/order' . $queryParams);
    }

}
