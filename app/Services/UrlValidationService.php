<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UrlValidationService
{
    public function validateUrl(string $url)
    {
        $errors = [];

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = 'A URL de destino é inválida.';
            return $errors; 
        }

        $parsedUrl = parse_url($url);

        $host = $parsedUrl['host'] ?? null;
        if ($host && !checkdnsrr($host, 'A') && !checkdnsrr($host, 'AAAA')) {
            $errors[] = 'O DNS da URL de destino é inválido.';
        }

        if ($parsedUrl && Str::startsWith($url, url('/'))) {
            $errors[] = 'A URL de destino não pode apontar para a própria aplicação.';
        }

        if (!Str::startsWith($url, 'https://')) {
            $errors[] = 'A URL de destino deve começar com HTTPS.';
        }

        $response = Http::get($url);
        if (!$response->successful()) {
            $errors[] = 'A URL de destino não está acessível ou retornou um status diferente de 200 ou 201.';
        }

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            foreach ($queryParams as $key => $value) {
                if ($key === '' || $value === '') {
                    $errors[] = 'A URL de destino contém query params com chave vazia.';
                    break; 
                }
            }
        }

        return $errors;
    }
}
