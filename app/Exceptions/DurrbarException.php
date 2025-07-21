<?php

namespace Modules\Core\Exceptions;

use Exception;

class DurrbarException extends Exception
{
    /**
     * @var @string
     */
    protected $reason;

    public function __construct(string $message = '', string $reason = '')
    {
        parent::__construct($message);

        $this->reason = $reason;
    }

    /**
     * Returns true when exception message is safe to be displayed to a client.
     *
     * @api
     */
    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * Returns string describing a category of the error.
     *
     * Value "graphql" is reserved for errors produced by query parsing or validation, do not use it.
     *
     * @api
     */
    public function getCategory(): string
    {
        return 'durrbar';
    }

    /**
     * Return the content that is put in the "extensions" part
     * of the returned error.
     */
    public function extensionsContent(): array
    {
        return [
            'reason' => $this->reason,
        ];
    }
}
