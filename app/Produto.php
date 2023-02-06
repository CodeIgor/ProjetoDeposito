<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nome', 'preco_custo', 'preco_venda', 'composto'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_composto' => 'boolean',
    ];

    /**
     * Mutator para calculo de custo dos produtos compostos
     *
     * @return string
     */
    public function getPrecoCustoAttribute($value)
    {
        $preco_custo = 0;

        if ($this->attributes['composto']) {
            foreach ($this->produtos as $k => $produto) {
                $preco_custo += (float)$produto->simples->preco_custo;
            }
        } else {
            $preco_custo = $value;
        }

        return number_format((float)$preco_custo, 2, '.', ''); // duas casas decimais
    }
    public function getPrecoVendaAttribute($value)
    {
        return number_format((float)$value, 2, '.', ''); // duas casas decimais
    }
    public function produtos()
    {
        return $this->hasMany('App\ProdutosComposto', 'id_composto', 'id');
    }
    public function produtosPai()
    {
        return $this->belongsToMany('App\ProdutosComposto', 'id_simples', 'id');
    }
}
