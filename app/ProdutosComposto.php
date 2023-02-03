<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdutosComposto extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_composto', 'id_simples', 'quantidade'];


     /**
     * Get the user that owns the phone.
     */
    public function produto()
    {
       return $this->belongsTo('App\Produto', 'id', 'id_composto');
    }
    public function simples()
    {
        return $this->hasOne('App\Produto', 'id', 'id_simples');
    }
}
