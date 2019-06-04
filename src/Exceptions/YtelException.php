<?php


namespace Khatfield\LaravelYtel\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class YtelException extends Exception
{
    public function report()
    {
        $channel = config('ytel.log_channel');
        Log::channel($channel)->error($this->getMessage());
    }
}