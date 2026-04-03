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

        $requestedIncludes = array_values(array_filter(
            array_map(static fn (string $value): string => trim($value), explode(',', $include)),
            static fn (string $value): bool => $value !== ''
        ));

        return in_array($key, $requestedIncludes, true);
    }
}
