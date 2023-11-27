<?php

namespace Alimi7372\DBLogger\Enums;
enum LogType: string
{
    case PROCESS = 'process';
    case SERVICE = 'service';
    case LARAVEL_SERVICES = 'laravel-services';
}
