<?php

namespace app\api\modules\Api\models\v1;

use \yii\db\ActiveRecord;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property string|null $description
 * @property string $created_at
 * @property string $updated_at
 * @property int $in_stock
 */
class Product extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'price', 'created_at', 'updated_at'], 'required'],
            [['price'], 'number'],
            [['description'], 'string'],
            [['in_stock'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_at', 'updated_at'], 'default', 'value' => time()],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование продукта',
            'price' => 'Цена',
            'in_stock' => 'На складе',
            'description' => 'Описание',
            'created_at' => 'Создан в',
            'updated_at' => 'Обновлён в',
        ];
    }
}
