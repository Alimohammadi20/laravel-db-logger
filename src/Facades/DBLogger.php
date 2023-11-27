<?php
namespace Alimi7372\DBLogger\Facades;
class DBLogger extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \Alimi7372\DBLogger\DBLogger::class;
    }
}