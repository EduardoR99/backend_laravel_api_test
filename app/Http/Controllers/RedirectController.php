<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Redirect;
use Illuminate\Support\Str;

class RedirectController extends Controller
{
    protected $redirectModel;
    /**
     * Display a listing of the resource.
     */
    public function __construct(Redirect $redirectModel)
    {
        $this->redirectModel = $redirectModel;
    }

    public function index()
    {
        $redirects = $this->redirectModel->all()->map(function ($redirect) {
            return [
                'id' => $redirect->code,
                'url_destino' => $redirect->url_destino,
                'ativo' => $redirect->ativo,
                'ultimo_acesso' => $redirect->ultimo_acesso,
                'created_at' => $redirect->created_at,
                'updated_at' => $redirect->updated_at,
            ];
        });

        return response()->json($redirects);
    }

    public function redirect($redirectTo)
{
    // Obtém os parâmetros da query string da request
    $requestParams = request()->query();

    // Verifica se o redirecionamento existe
    $redirect = $this->redirectModel->where('code', $redirectTo)->first();

    if ($redirect) {
        info('Redirecionamento Encontrado: ' . $redirect);
        $redirect->ultimo_acesso = now();
        $redirect->save();

        // Obtém os parâmetros da query string do redirect
        $redirectParams = [];
        parse_str(parse_url($redirect->url_destino, PHP_URL_QUERY), $redirectParams);

        // Funde os parâmetros da request com os do redirect, dando prioridade à request
        $queryParams = array_merge($redirectParams, $requestParams);

        // Remove os parâmetros vazios da request
        $queryParams = array_filter($queryParams, function ($value) {
            return $value !== '';
        });

        // Constrói a URL destino com os parâmetros da query string
        $url_destino = strtok($redirect->url_destino, '?'); // Remove os parâmetros existentes na URL destino
        if (!empty($queryParams)) {
            $url_destino .= '?' . http_build_query($queryParams);
        }

        // Verifica o protocolo da URL destino e realiza o redirecionamento
        if (Str::startsWith($url_destino, 'https://')) {
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


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url_destino' => 'required|url|starts_with:https',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $redirect = $this->redirectModel->create([
            'url_destino' => $request->url_destino,
            'ativo' => true,
        ]);

        return response()->json($redirect, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $redirect = $this->redirectModel->where('code', $id)->firstOrFail();
        return response()->json($redirect);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'url_destino' => 'required|url|starts_with:https',
            'ativo' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $redirect = $this->redirectModel->where('code', $id)->firstOrFail();
        $redirect->update([
            'url_destino' => $request->url_destino,
            'ativo' => $request->ativo,
        ]);

        return response()->json($redirect, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $redirect = $this->redirectModel->where('code', $id)->firstOrFail();
        $redirect->delete();

        return response()->json(null, 204);
    }
}