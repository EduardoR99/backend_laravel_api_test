<?php

namespace App\Services;

use App\Models\RedirectLog;
use App\Models\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Request;

class RedirectService
{
    protected $redirectModel;
    protected $redirectLogModel;

    public function __construct(Redirect $redirectModel, RedirectLog $redirectLogModel)
    {
        $this->redirectModel = $redirectModel;
        $this->redirectLogModel = $redirectLogModel;
    }

    public function redirect($redirectTo)
    {
        $requestParams = request()->query();

        $redirect = $this->redirectModel->where('code', $redirectTo)->first();

        if ($redirect) {
            info('Redirecionamento Encontrado: ' . $redirect);
            $redirect->ultimo_acesso = now();
            $redirect->save();

            $redirectParams = [];
            parse_str(parse_url($redirect->url_destino, PHP_URL_QUERY), $redirectParams);

            $queryParams = array_merge($redirectParams, $requestParams);

            $queryParams = array_filter($queryParams, function ($value) {
                return $value !== '';
            });

            $url_destino = strtok($redirect->url_destino, '?'); 
            if (!empty($queryParams)) {
                $url_destino .= '?' . http_build_query($queryParams);
            }

            if (Str::startsWith($url_destino, 'https://')) {
                $this->createRedirectLog($redirect); 
                return redirect()->away($url_destino);
            } elseif (Str::startsWith($url_destino, 'http://')) {
                echo 'Não é possível acessar rotas http';
            } else {
                return response()->json(['error' => 'URL de destino inválida'], 400);
            }
        } else {
            echo('Redirecionamento Não existe');
        }
    }

    protected function createRedirectLog(Redirect $redirect)
    {
        $queryParams = Request::query();

        $queryParamsJson = json_encode($queryParams);

        $logData = [
            'redirect_id' => $redirect->id,
            'redirect_id_code' => $redirect->code,
            'ip' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'header_referer' => Request::header('Referer'),
            'query_params' => $queryParamsJson, 
        ];

        $this->redirectLogModel->create($logData);
    }
}
