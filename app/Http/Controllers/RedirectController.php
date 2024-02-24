<?php

namespace App\Http\Controllers;

use App\Services\UrlValidationService;
use Illuminate\Http\Request;
use App\Models\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;

class RedirectController extends Controller
{
    protected $redirectModel;
    protected $urlValidationService;

    public function __construct(Redirect $redirectModel, UrlValidationService $urlValidationService)
    {
        $this->redirectModel = $redirectModel;
        $this->urlValidationService = $urlValidationService;
    }

    public function index()
    {
        $redirects = $this->redirectModel->where('ativo', 1)->get()->map(function ($redirect) {
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

    public function store(Request $request, UrlValidationService $urlValidationService)
    {
        $validator = Validator::make($request->all(), [
            'url_destino' => 'required|url|',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
    
        $url_destino = $request->input('url_destino');
    
        $validationResult = $urlValidationService->validateUrl($url_destino);
        if (!empty($validationResult)) {
            return response()->json(['error' => $validationResult], 400);
        }

        $randomNumber = mt_rand(100000, 999999); 
    
        $hashedCode = Hashids::encode($randomNumber);
    
        $redirect = $this->redirectModel->create([
            'url_destino' => $url_destino,
            'ativo' => true,
            'code' => $hashedCode,
        ]);
    
        return response()->json($redirect, 201);
    }

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
            'url_destino' => $request->input('url_destino'),
            'ativo' => $request->input('ativo'),
        ]);

        return response()->json($redirect, 200);
    }



    
    public function destroy(string $id)
    {
        try {
           
            $redirect = $this->redirectModel->where('code', $id)->firstOrFail();

            $redirect->update(['ativo' => 0]);

            return response()->json(['message' => 'Redirecionamento deletado com sucesso.'], 204);

        } catch (\Exception $e) {
           
            return response()->json(['error' => 'Erro ao deletar redirecionamento.'], 500);
        }
    }
}
