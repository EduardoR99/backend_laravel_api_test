<?php

namespace Database\Factories;


use App\Models\Redirect;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Vinkla\Hashids\Facades\Hashids;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Redirect>
 */
class RedirectFactory extends Factory
{
    protected $model = Redirect::class;

    public function definition()
    {
        return [
            'url_destino' => $this->faker->url,
            'ativo' => true,
            'code' => Hashids::encode($this->faker->unique()->randomNumber()),
        ];
    }
}
