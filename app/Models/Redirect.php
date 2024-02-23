<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;

class Redirect extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['url_destino', 'ativo'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($redirect) {
            // Não precisa definir o ID aqui
        });

        static::retrieved(function ($redirect) {
            // Nada a ser feito aqui
        });
    }

    // Sobrescrevendo o método getKey para decodificar o ID
    public function getKey()
    {
        $decodedId = Hashids::decode(parent::getKey())[0];
        return $decodedId ? $decodedId : parent::getKey();
    }

    // Definindo o ID como string para evitar cast para int
    protected $keyType = 'string';

    // Desativando incremento automático do ID
    public $incrementing = false;

    public function logs()
    {
        return $this->hasMany(RedirectLog::class);
    }
}
