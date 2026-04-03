<?php

declare(strict_types=1);

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    /**
     * check it needs include an optional value
     */
    public function needToInclude(Request $request, string $key): bool
    {
        $include = $request->get('include');
        if (! is_string($include) || $include === '') {
            return false;
        }

        return in_array($key, explode(',', $include), true);
    }
}
