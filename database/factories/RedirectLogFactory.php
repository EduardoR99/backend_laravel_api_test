<?php

namespace Database\Factories;

use App\Models\RedirectLog;
use App\Models\Redirect;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RedirectLog>
 */
class RedirectLogFactory extends Factory
{
    protected $model = RedirectLog::class;

    public function definition()
    {
        $redirect = Redirect::factory()->create();
        return [
            'redirect_id' => 2,
            'ip' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'header_referer' => $this->faker->url,
            'query_params' => json_encode(['param1' => 'value1', 'param2' => 'value2']),
        ];
    }
}
