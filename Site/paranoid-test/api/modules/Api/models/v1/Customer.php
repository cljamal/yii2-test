<?php

namespace app\api\modules\Api\models\v1;

use app\api\modules\Api\Repositories\v1\ProductsRepository;
use Yii;
use \yii\db\ActiveRecord;
/**
 * This is the model class for table "customers".
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $dob
 * @property string $email
 */
class Customer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dob'], 'safe'],
            [['email'], 'required'],
            [['first_name', 'last_name', 'email'], 'string', 'max' => 255],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'dob' => 'Dob',
            'email' => 'Email',
            'orders' => 'Заказы',
        ];
    }

    public function getOrders()
    {
        return $this->hasMany(Order::class,  ['customer_id' => 'id']);
    }

    public function getProducts()
    {
        $products = [];
        foreach ($this->orders as $order)
            foreach ($order->order_info as $info)
            $products[$info['product_id']] = [
                'id' => $info['product_id'],
                'qty' => ($products[$info['product_id']]['qty'] ?? 0) + $info['qty']
            ];

        return (new ProductsRepository)->getManyById($products);
    }
}
