<?php

namespace Modules\Common\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Common\Helpers\ErrorHelper as HelpersErrorHelper;

class ErrorHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return HelpersErrorHelper::class;
    }
}
