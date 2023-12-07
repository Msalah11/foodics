<?php

namespace App\Exceptions;

use Exception;
class NotEnoughStockException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int $code
     * @param  Exception|null  $previous
     * @return void
     */
    public function __construct(string $message = 'Not enough stock available.', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
