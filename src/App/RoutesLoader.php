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
                return $rc->newInstanceArgs(array($this->app["{$resource}.service"]));
            };
        }  
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
    }

    private function bindWebRoutes($web) 
    {
        $web->get("/login", "auth.web.controller:index");
        $web->post("/login", "auth.web.controller:login");
        
        $web->get("/order", "orders.web.controller:index");
    }

    private function instantiateWebControllers()
    {
        $this->app['auth.web.controller'] = function() {
            return new Controllers\Web\AuthController($this->app, $this->app["users.service"]);
        };

        $this->app['orders.web.controller'] = function() {
            return new Controllers\Web\OrdersController($this->app);
        };
    }

}

