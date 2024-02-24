<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Redirect;
use App\Models\RedirectLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatisticTest extends TestCase
{
    use RefreshDatabase;

    

    public function test_acessos_ultimos_10_dias_quando_nao_ha_acessos()
    {
        $this->assertEquals(0, RedirectLog::where('created_at', '>=', now()->subDays(10))->count());
    }

   
}
