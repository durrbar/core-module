<?php

declare(strict_types=1);

namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Core\Helpers\ErrorHelper as HelpersErrorHelper;

class ErrorHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return HelpersErrorHelper::class;
    }
}
