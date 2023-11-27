<?php

namespace Alimi7372\DBLogger\Enums;
enum LogLevel: string
{
    case EMERGENCY = 'emergency';
    case ALERT = 'alert';
    case CRITICAL = 'critical';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';
    case DEBUG = 'debug';
    case NOTICE = 'notice';
    case SUCCESS = 'success';
}
