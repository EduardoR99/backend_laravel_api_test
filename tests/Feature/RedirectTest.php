<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_criacao_redirect_com_url_valida()
    {
        $response = $this->postJson('/api/redirects', [
            'url_destino' => 'https://example.com',
        ]);

        $response->assertStatus(201);
    }
    public function test_criacao_redirect_com_dns_invalido()
    {
        $response = $this->postJson('/api/redirects', [
            'url_destino' => 'https://invalid-domain.example',
        ]);

        $response->assertStatus(400);
    }

    public function test_criacao_redirect_com_url_invalida()
    {
        $response = $this->postJson('/api/redirects', [
            'url_destino' => 'not-a-url',
        ]);

        $response->assertStatus(400);
    }

    public function test_criacao_redirect_com_url_apontando_para_aplicacao()
    {
        $response = $this->postJson('/api/redirects', [
            'url_destino' => '/',
        ]);

        $response->assertStatus(400);
    }

    public function test_criacao_redirect_com_url_sem_https()
    {
        $response = $this->postJson('/api/redirects', [
            'url_destino' => 'http://example.com',
        ]);

        $response->assertStatus(400);
    }

    public function test_criacao_redirect_com_status_diferente_de_200_ou_201()
    {
        $response = $this->postJson('/api/redirects', [
            'url_destino' => 'https://httpstat.us/404',
        ]);

        $response->assertStatus(400);
    }

    public function test_criacao_redirect_com_query_params_com_chave_vazia()
    {
        $response = $this->postJson('/api/redirects', [
            'url_destino' => 'https://example.com?param1=value1&param2=',
        ]);

        $response->assertStatus(400);
    }
}
