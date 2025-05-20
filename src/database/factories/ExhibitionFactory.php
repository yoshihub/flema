<?php

namespace Database\Factories;

use App\Models\Exhibition;
use App\Models\User;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExhibitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exhibition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word . ' ' . $this->faker->colorName,
            'exhibition_image' => time() . '_' . $this->faker->bothify('?????') . '_item.jpg',
            'brand' => $this->faker->boolean(70) ? $this->faker->company : null,
            'explanation' => $this->faker->sentence(10),
            'price' => $this->faker->numberBetween(1000, 50000),
            'is_sold' => false,
            'condition_id' => Condition::factory(),
            'user_id' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * 販売済み状態の商品を定義
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function sold()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_sold' => true,
            ];
        });
    }
}
