<?php

namespace Alimi7372\DBLogger\Enums;
enum ServiceType: string
{
    case AUTH = 'auth';
    case USER_TASKS = 'user_tasks';
    case COMMIT_PROCESS = 'commit_process';
    case NEXT_PROCESS = 'next_process';
    case NEXT_ACTIVE_PROCESS = 'next_active_process';
    case START_PROCESS = 'start_process';
    case START_WITH_RETURN_PROCESS = 'start_with_return_process';
    case PRCS_BY_KEY_PROCESS = 'prcs_by_key_process';
    case GET_CPG_TOKEN = 'get_cpg_token';
    case VERIFY_TRANSACTION = 'verify_transaction';
    case SMS_GET_TOKEN = 'sms_get_token';
    case SEND_SMS = 'send_sms';

}
