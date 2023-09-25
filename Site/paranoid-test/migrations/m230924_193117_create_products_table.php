<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products}}`.
 */
class m230924_193117_create_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('products', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'price' => $this->float()->notNull(),
            'description' => $this->text()->null(),
            'in_stock' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('products');
    }
}
