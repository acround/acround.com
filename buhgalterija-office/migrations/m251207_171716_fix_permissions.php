<?php

use yii\db\Migration;

class m251207_171716_fix_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $viewAccountants = $auth->createPermission('viewAccountants');
        $viewAccountants->description = 'Просмотр бухгалтеров';
        $auth->add($viewAccountants);

        $ceo = $auth->getRole('ceo');
        $auth->addChild($ceo, $viewAccountants);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $ceo = $auth->getRole('ceo');
        $viewAccountants = $auth->createPermission('viewAccountants');
        $auth->removeChild($ceo, $viewAccountants);
        $auth->remove($viewAccountants);
    }
}
