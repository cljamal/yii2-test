<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orders}}`.
 */
class m230924_193126_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $this->createTable('orders', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->unsigned()->notNull(),
            'order_info' => $this->json()->notNull(),
            'total_price' => $this->float()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'status' => $this->string(255)->notNull()->defaultValue('new'),
            'updated_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('orders');
    }
}
