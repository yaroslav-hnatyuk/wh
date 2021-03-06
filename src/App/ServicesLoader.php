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
        
        $this->app["export.service"] = function() {
            $rc = new \ReflectionClass('App\\Services\\ExportService');
            return $rc->newInstanceArgs(array($this->app["db"]));
        }; 

        $this->app["settings.service"] = function() {
            $rc = new \ReflectionClass('App\\Services\\SettingsService');
            return $rc->newInstanceArgs(array($this->app["db"]));
        }; 

        $this->app["reminders.service"] = function() {
            $rc = new \ReflectionClass('App\\Services\\RemindersService');
            return $rc->newInstanceArgs(array($this->app["db"]));
        }; 
    }
}

