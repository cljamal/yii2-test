<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customers}}`.
 */
class m230924_154106_create_customers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('customers', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string()->null(),
            'last_name' => $this->string()->null(),
            'dob' => $this->date()->null(),
            'email' => $this->string()->notNull()->unique(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('customers');
    }
}
