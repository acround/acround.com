<?php

use yii\db\Migration;

/**
 * Handles the creation of roles: CEO (Руководитель), Admin (Администратор), Accountant (Бухгалтер).
 */
class m251128_100000_init_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // --- Создание разрешений (Permissions) ---
        // Общие
        $viewDashboard = $auth->createPermission('viewDashboard');
        $viewDashboard->description = 'Просмотр главной страницы';
        $auth->add($viewDashboard);

        // Для Бухгалтера и Администратора
        $viewTasks = $auth->createPermission('viewTasks');
        $viewTasks->description = 'Просмотр списка задач';
        $auth->add($viewTasks);

        $manageTasks = $auth->createPermission('manageTasks');
        $manageTasks->description = 'Создание/редактирование задач';
        $auth->add($manageTasks);

        $viewDocuments = $auth->createPermission('viewDocuments');
        $viewDocuments->description = 'Просмотр документов';
        $auth->add($viewDocuments);

        // Для Администратора и Руководителя
        $manageCompanies = $auth->createPermission('manageCompanies');
        $manageCompanies->description = 'Управление компаниями-клиентами';
        $auth->add($manageCompanies);

        $viewReports = $auth->createPermission('viewReports');
        $viewReports->description = 'Просмотр отчетов и аналитики';
        $auth->add($viewReports);

        // Только для Руководителя/Администратора
        $systemSettings = $auth->createPermission('systemSettings');
        $systemSettings->description = 'Управление системными настройками и пользователями';
        $auth->add($systemSettings);


        // --- Создание ролей (Roles) ---

        // 1. Бухгалтер (Accountant)
        $accountant = $auth->createRole('accountant');
        $accountant->description = 'Бухгалтер';
        $auth->add($accountant);
        $auth->addChild($accountant, $viewDashboard);
        $auth->addChild($accountant, $viewTasks);
        $auth->addChild($accountant, $viewDocuments);

        // 2. Администратор (Admin)
        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор системы';
        $auth->add($admin);
        $auth->addChild($admin, $accountant); // Наследует все права Бухгалтера
        $auth->addChild($admin, $manageCompanies);
        $auth->addChild($admin, $manageTasks);
        $auth->addChild($admin, $viewReports);
        $auth->addChild($admin, $systemSettings);

        // 3. Руководитель (CEO)
        $ceo = $auth->createRole('ceo');
        $ceo->description = 'Руководитель агентства';
        $auth->add($ceo);
        $auth->addChild($ceo, $admin); // Наследует все права Администратора

        // Пример: назначение роли пользователю с ID 1 (Руководитель)
        // $auth->assign($ceo, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
    }
}
