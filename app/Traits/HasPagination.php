<?php

namespace Modules\Core\Traits;

trait HasPagination
{
    /**
     * Get pagination metadata.
     *
     * @return array
     */
    public function pagination()
    {
        return [
            'total'         => $this->total(),
            'perPage'       => $this->perPage(),
            'currentPage'   => $this->currentPage(),
            'lastPage'      => $this->lastPage(),
            'firstPageUrl'  => $this->url(1),
            'lastPageUrl'   => $this->url($this->lastPage()),
            'nextPageUrl'   => $this->nextPageUrl(),
            'prevPageUrl'   => $this->previousPageUrl(),
        ];
    }
}
