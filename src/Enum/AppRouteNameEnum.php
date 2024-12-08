<?php declare(strict_types=1);

namespace App\Enum;

enum AppRouteNameEnum: string
{
    case ADMIN_CAMUNDA_TASKS_ROUTE = 'admin.camunda_tasks.list';
    case ADMIN_CAMUNDA_TASKS_SUBMISSION_ROUTE = 'admin.camunda_tasks.submission';
    case ADMIN_CAMUNDA_TASKS_SUBMISSION_ADD_NEW_ROUTE = 'admin.camunda_tasks.submission_add_new';
    case ADMIN_CAMUNDA_TASKS_COMPLETE_ROUTE = 'admin.camunda_tasks.complete';
    case ADMIN_CAMUNDA_TASKS_ASSIGN_ME_ROUTE = 'admin.camunda_tasks.assign_me';
    case ADMIN_NATIVE_TASKS_ASSIGN_ME_ROUTE = 'admin.native_tasks.assign_me';
    case TEST_FORMIO = 'test_formio';

    public const INDEX = 'app.account.index';
    public const ACCOUNT_LOGIN = 'app.account.login';
    public const ACCOUNT_INDEX = 'app.account.index';
    public const ACCOUNT_LOGOUT = 'app.account.logout';
    public const OAUTH_CALL_BACK_URL_NAME = 'app.oauth_callback';
}
