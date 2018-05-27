<?php

namespace App;

use Silex\Application;

class ServicesLoader
{
    protected $app;
    protected $resources;

    public function __construct(Application $app, $resources)
    {
        $this->app = $app;
        $this->resources = $resources;
    }

    public function bindServicesIntoContainer()
    {
        foreach($this->resources as $resource) {
            $this->app["{$resource}.service"] = function() use ($resource) {
                $rc = new \ReflectionClass('App\\Services\\' . ucfirst($resource) . "Service");
                return $rc->newInstanceArgs(array($this->app["db"]));
            }; 
        }
    }
}

