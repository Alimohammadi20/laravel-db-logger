<?php

namespace Alimi7372\DBLogger\Enums;
enum LogType: string
{
    case PROCESS = 'process';
    case SERVICE = 'service';
    case LARAVEL_SERVICES = 'laravel-services';
    case AKAM_SERVICES = 'akam_services';
    case TEFAS_SERVICES = 'tefas_services';
    case IBAR_SERVICES = 'ibar_services';
    case ALOPAYK_SERVICES = 'alopayk_services';
}
