<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\Request;


abstract class BaseController
{
    abstract protected function getDataFromRequest(Request $request);
}
