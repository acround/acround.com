<?php

namespace app\services;

use Yii;
use app\models\Accountant;

class AuthService
{

    const PERMISSION_MANAGE_COMPANIES = 'manageCompanies';
    const PERMISSION_MANAGE_TASKS = 'manageTasks';
    const PERMISSION_SYSTEM_SETTINGS = 'systemSettings';
    const PERMISSION_VIEW_ACCOUNTANTS = 'viewAccountants';
    const PERMISSION_VIEW_DASHBOARD = 'viewDashboard';
    const PERMISSION_VIEW_DOCUMENTS = 'viewDocuments';
    const PERMISSION_VIEW_REPORTS = 'viewReports';
    const PERMISSION_VIEW_TASKS = 'viewTasks';

    public const ACCESS_TOKEN_NAME = 'access_token';
    /**
     *
     * @return string
     */
    public static function encodePassword($password): string
    {
        return md5($password);
    }

    public static function getPermissions(Accountant $user)
    {
        return Yii::$app->authManager->getPermissionsByRole($user->rule);
    }

    public static function hasPermission(Accountant $user, $permissionName)
    {
        $permissions = self::getPermissions($user);
        return array_key_exists($permissionName, $permissions);
    }

    public static function generateAccessToken()
    {
        return Yii::$app->security->generateRandomString(32);
    }
}
