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

        if ($company) {
            if ($company === 'none') {
                $this->app['session']->remove('filter_company');
            } else {
                $this->app['session']->set('filter_company', $company);
            }
            $this->app['session']->remove('filter_office');
            $this->app['session']->remove('filter_user');
        }

        if ($office) {
            if ($office === 'none') {
                $this->app['session']->remove('filter_office');
            } else {
                $this->app['session']->set('filter_office', $office);
            }
            $this->app['session']->remove('filter_user');
        }

        if ($user) {
            if ($user === 'none') {
                $this->app['session']->remove('filter_user');
            } else {
                $this->app['session']->set('filter_user', $user);
            }
        }

        return new RedirectResponse('/order');
    }

}
