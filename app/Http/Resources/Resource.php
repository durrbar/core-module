<?php

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
        return in_array($key, explode(',', $request->get('include')));
    }
}
