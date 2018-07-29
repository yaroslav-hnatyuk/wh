<?php

namespace App;

use Silex\Application;

class RoutesLoader
{
    private $app;
    private $resources;

    public function __construct(Application $app, $resources)
    {
        $this->app = $app;
        $this->resources = $resources;
        $this->instantiateControllers();

    }

    private function instantiateControllers()
    {
        foreach($this->resources as $resource) {
            $this->app["{$resource}.controller"] = function() use($resource) {
                $rc = new \ReflectionClass('App\\Controllers\\Api\\' . ucfirst($resource) . "Controller");
                return $rc->newInstanceArgs(array($this->app, $this->app["{$resource}.service"]));
            };
        }  

        $this->app["settings.controller"] = function() use($resource) {
            $rc = new \ReflectionClass('App\\Controllers\\Api\\SettingsController');
            return $rc->newInstanceArgs(array($this->app));
        };

        $this->instantiateWebControllers();
    }

    public function bindRoutesToControllers()
    {
        $api = $this->app["controllers_factory"];
        $this->bindApiRoutes($api);
        $this->app->mount($this->app["api.endpoint"].'/'.$this->app["api.version"], $api);   

        $web = $this->app["controllers_factory"];
        $this->bindWebRoutes($web);
        $this->app->mount("", $web);   
    }

    private function bindApiRoutes($api) 
    {
        foreach($this->resources as $resource) {
            $api->get("/{$resource}", "{$resource}.controller:getAll");
            $api->get("/{$resource}/{id}", "{$resource}.controller:getOne");
            $api->post("/{$resource}", "{$resource}.controller:save");
            $api->put("/{$resource}/{id}", "{$resource}.controller:update");
            $api->delete("/{$resource}/{id}", "{$resource}.controller:delete");
        }

        $api->post("/users/current", "users.controller:saveCurrent");
        $api->post("/dishes/upload/{id}", "dishes.controller:upload");
        $api->get("/settings", "settings.controller:getAll");
        $api->post("/settings", "settings.controller:save");
        $api->get("/feedback/dish/{id}", "feedback.controller:dish");
    }

    private function bindWebRoutes($web) 
    {
        $web->get("/login", "auth.web.controller:index");
        $web->post("/login", "auth.web.controller:login");
        $web->get("/registration/{cid}", "auth.web.controller:registration");
        $web->post("/register", "auth.web.controller:register");
        $web->get("/restore", "auth.web.controller:restore");
        $web->post("/restore", "auth.web.controller:sendpass");
        $web->get("/logout", "auth.web.controller:logout");
        
        $web->get("/", "orders.web.controller:main");
        $web->get("/order", "orders.web.controller:index");
        $web->get("/export", "orders.web.controller:export");

        $web->get("/users", "users.web.controller:index");
        $web->get("/reports", "reports.web.controller:index");
        $web->get("/menu", "menu.web.controller:index");
        $web->get("/companies", "companies.web.controller:index");
        $web->get("/dishes", "dishes.web.controller:index");
        $web->get("/filters", "filters.web.controller:index");
        $web->get("/settings", "settings.web.controller:index");

        $web->get("/profile", "profile.web.controller:index");
        $web->get("/profile/feedback", "profile.web.controller:feedback");
        $web->get("/profile/reminders", "profile.web.controller:reminders");
        $web->get("/profile/promo", "profile.web.controller:promo");

        $web->get("/errors/40x", "errors.web.controller:error40x");
        $web->get("/errors/50x", "errors.web.controller:error50x");
    }

    private function instantiateWebControllers()
    {
        $this->app['auth.web.controller'] = function() {
            return new Controllers\Web\AuthController($this->app, $this->app["users.service"]);
        };

        $this->app['orders.web.controller'] = function() {
            return new Controllers\Web\OrdersController(
                $this->app, $this->app["orders.service"], $this->app["menudishes.service"]
            );
        };

        $this->app['profile.web.controller'] = function() {return new Controllers\Web\ProfileController($this->app);};
        $this->app['reports.web.controller'] = function() {return new Controllers\Web\ReportsController($this->app);};

        $this->app['menu.web.controller'] = function() {
            return new Controllers\Web\MenuController(
                $this->app, 
                $this->app["menudishes.service"], 
                $this->app["orders.service"],
                $this->app["dishes.service"], 
                $this->app["dishgroups.service"]
            );
        };

        $this->app['companies.web.controller'] = function() {
            return new Controllers\Web\CompaniesController(
                $this->app, $this->app["companies.service"], $this->app["offices.service"]
            );
        };
        
        $this->app['dishes.web.controller'] = function() {
            return new Controllers\Web\DishesController(
                $this->app, $this->app["dishes.service"], $this->app["dishgroups.service"]
            );
        };

        $this->app['users.web.controller'] = function() {
            return new Controllers\Web\UsersController(
                $this->app, $this->app["users.service"]
            );
        };

        $this->app['filters.web.controller'] = function() { return new Controllers\Web\FiltersController($this->app); };
        $this->app['settings.web.controller'] = function() { return new Controllers\Web\SettingsController($this->app); };
        $this->app['errors.web.controller'] = function() { return new Controllers\Web\ErrorsController(); };
    }

}

