<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;

class FiltersController
{
    public function __construct(Application $app) 
    {
        $this->app = $app;
    }

    public function index(Request $request)
    {
        $company = $request->query->get('company');
        $office = $request->query->get('office');
        $user = $request->query->get('user');

        $sCompany = $this->app['session']->get('filter_company');
        $sOffice = $this->app['session']->get('filter_office');
        $sUser = $this->app['session']->get('filter_user');

        if ($company && ($company === 'none' || $company != $sCompany)) {
            $this->app['session']->remove('filter_office');
            $this->app['session']->remove('filter_user');
            if ($company === 'none') {
                $this->app['session']->remove('filter_company');
            } elseif ($company != $sCompany) {
                $this->app['session']->set('filter_company', $company);
            }
            return new RedirectResponse('/order');
        }

        if ($office && ($office === 'none' || $office != $sOffice)) {
            $this->app['session']->remove('filter_user');
            if ($office === 'none') {
                $this->app['session']->remove('filter_office');
            } else if ($office != $sOffice) {
                $this->app['session']->set('filter_office', $office);
            }
            return new RedirectResponse('/order');
        }

        if ($user && ($user === 'none' || $user != $sUser)) {
            if ($user === 'none') {
                $this->app['session']->remove('filter_user');
            } else if ($user != $sUser) {
                $this->app['session']->set('filter_user', $user);
            }
        }

        return new RedirectResponse('/order');
    }

}
