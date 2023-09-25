<?php

namespace app\api\modules\Api\models\v1;

use \yii\db\ActiveRecord;
/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $order_info
 * @property float $total_price
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 */
class Order extends ActiveRecord
{
    /**
     * {}
     */
    public static function tableName(): string
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['customer_id', 'order_info', 'total_price'], 'required'],
            [['customer_id'], 'integer'],
            [['order_info', 'created_at', 'updated_at'], 'safe'],
            [['total_price'], 'number'],
            [['status'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Покупатель',
            'order_info' => 'Содержание заказа',
            'total_price' => 'Общая сумма',
            'status' => 'Статус заказа',
            'created_at' => 'Заказано в',
            'updated_at' => 'Обновлено в',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
