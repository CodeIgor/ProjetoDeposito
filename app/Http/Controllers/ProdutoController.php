<?php

namespace App\Http\Controllers;

use App\Produto;
use App\ProdutosComposto;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables; // Server side datatables


class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('produtos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $valid = (object) $this->validate($request, [
            'nome' => 'required|unique:produtos',
            'preco_custo' => 'required',
            'preco_venda' => 'required',
            'produtos' => 'required_with:composto'
        ]);

        $produto = new Produto();
        $produto->nome = $valid->nome;
        if (!empty($request->composto)) $produto->preco_custo = str_replace(['.', ','], ['', '.'], $valid->preco_custo);
        $produto->preco_venda = str_replace(['.', ','], ['', '.'], $valid->preco_venda);
        $produto->composto = !empty($request->composto) ? true : false;
        $produto->save();

        if (!empty($request->composto)) {
            foreach ($request->produtos as $produto_simples => $quantidade) {
                $prod = new ProdutosComposto();
                $prod->id_composto = $produto->id;
                $prod->id_simples = $produto_simples;
                $prod->quantidade = $quantidade;
                $prod->save();
            }
        }

        return redirect()->route('produtos.index')->with('msg', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function show(Produto $produto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Produto $produto)
    {
        $valid = (object) $this->validate($request, [
            'nome' => 'required|unique:produtos',
            'preco_custo' => 'required',
            'preco_venda' => 'required',
            'produtos' => 'required_with:composto'
        ]);

        $produto->nome = $valid->nome;
        $produto->preco_custo = (!empty($request->composto)) ? str_replace(['.', ','], ['', '.'], $valid->preco_custo) : null;
        $produto->preco_venda = str_replace(['.', ','], ['', '.'], $valid->preco_venda);
        $produto->composto = !empty($request->composto) ? true : false;
        $produto->save();

        ProdutosComposto::where('id_composto', $produto->id)->delete();

        if (!empty($request->composto)) {
            foreach ($request->produtos as $produto_simples => $quantidade) {
                $prod = new ProdutosComposto();
                $prod->id_composto = $produto->id;
                $prod->id_simples = $produto_simples;
                $prod->quantidade = $quantidade;
                $prod->save();
            }
        }


        return redirect()->route('produtos.index')->with('msg', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Produto  $produto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produto $produto)
    {
        ProdutosComposto::where('id_simples', $produto->id)->delete();

        $produto->delete();

        return redirect()->route('produtos.index')->with('msg', 'Produto apagado com sucesso!');
    }

    /**
     * Metodo de carregamento server side do datatable
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function dataTable(Request $request)
    {
        return Datatables::of(Produto::query())
            // Traducao bool de composto
            ->addColumn('tipo', function ($produto) {
                return $produto->composto ? 'COMPOSTO' : 'SIMPLES';
            })
            // Botoes de acao
            ->addColumn('action', function ($produto) {
                return '<form action="' . route('produtos.destroy', $produto->id) . '"
                class="d-flex align-items-end excluir" method="POST">
                ' . csrf_field() . method_field('DELETE') . '
                <div class="btn-group ml-auto shadow-sm">
                    <a href="' . route('produtos.edit', $produto->id) . '" class="btn btn-outline-info">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button class="btn btn-outline-danger">
                        <i class="fas fa-trash-alt"></i> Apagar
                    </button>
                </div>
            </form>';
            })->make(true);
    }

    /**
     * Metodo para pesquisa de produtos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function search(Request $request)
    {
        if (!empty($request->id)) {
            // Datdos para a tabela
            return Produto::find($request->id);
        } else {
            // Consulta de produtos simples
            $search = Produto::where('composto', false)->where('nome', 'LIKE', '%' . $request->search . '%')
                ->select('id', 'nome as text');

            if (!empty($request->not)) $search->whereNotIn('id', $request->not);

            $search = $search->get();
            return ["results" => $search]; // Retorno na forma da biblioteca select2
        }
    }
}
