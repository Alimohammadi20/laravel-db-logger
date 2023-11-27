<?php
namespace Alimi7372\DBLogger\Fecades;
class Logger extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \Alimi7372\DBLogger\Logger::class;
    }
}