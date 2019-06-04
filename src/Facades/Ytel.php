<?php


namespace Khatfield\LaravelYtel\Facades;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Ytel
 *
 * @package Khatfield\LaravelYtel\Facades
 *
 * @method static array getSmsDetails(string $sms_id)
 * @method static Collection getInboundSms(Carbon|\DateTime|null $date = null)
 */
class Ytel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ytel';
    }
}