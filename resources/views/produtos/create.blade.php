@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-auto"><a href="{{ route('produtos.index') }}" class="btn btn-dark">
                    <div class="fas fa-arrow-left"></div> Voltar
                </a></div>
            <div class="col">
                <h1 class="mb-0">Novo produto</h1>
            </div>
        </div>
        <hr>
        @if (count($errors) > 0)
            <div class="alert alert-danger shadow-sm">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul class="m-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('produtos.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="nome"><b>Nome</b></label>
                    <input type="text" id="nome" name="nome" class="form-control shadow-sm"
                        value="{{ old('nome') }}">
                </div>
                <div class="col-md-3 form-group">
                    <label for="preco_custo"><b>
                            @if (!old('composto'))
                                Preço de custo
                            @else
                                Preço total de custo
                            @endif
                        </b></label>
                    <input type="text" id="preco_custo" name="preco_custo" class="form-control shadow-sm"
                        value="{{ old('preco_custo') }}" @if (old('composto')) readonly @endif>
                </div>
                <div class="col-md-3 form-group">
                    <label for="preco_venda"><b>Preço de venda</b></label>
                    <input type="text" id="preco_venda" name="preco_venda" class="form-control shadow-sm"
                        value="{{ old('preco_venda') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="composto" name="composto"
                            @if (old('composto')) checked @endif>
                        <label class="custom-control-label" for="composto">Produto composto</label>
                    </div>
                </div>
            </div>
            <div id="div_composto" style="@if (!old('composto')) display: none @endif">
                <div class="row">
                    <div class="col-12 mb-3">
                        <input type="text" id="produto_search" class="form-control">
                    </div>
                </div>
                <div class="row" id="composto">
                    <div class="col-12">
                        <table id="produtos_table" class="table table-striped table-hover shadow-sm w-100">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Preço custo</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-right">Subtotal</th>
                                    <th class="text-right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (old('produtos_info') ?? [] as $k => $produto)
                                    <tr>
                                        <td><input type="hidden" name="produtos_info[{{ $k }}]"
                                                value="{{ $produto }}">
                                            @php($produto = json_decode($produto))

                                            {{ $produto->nome }}</td>
                                        <td class="text-right">{{ $produto->preco_custo }}</td>
                                        <td><input type="number" class="form-control m-auto shadow-sm"
                                                name="produtos[{{ $k }}]" value="{{ old('produtos')[$k] }}"
                                                min="1" style="max-width:10rem"> </td>
                                        <td class="text-right">
                                            {{ (float) str_replace(['.', ','], ['', '.'], $produto->preco_custo) * (float) old('produtos')[$k] }}
                                        </td>
                                        <td><button type="button" class="btn btn-danger float-right shadow-sm remove"><i
                                                    class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('js')
    <script>
        var produtos = [
            @foreach (old('produtos_info') ?? [] as $k => $produto)
                {!! $produto !!},
            @endforeach
        ]
        var total = 0

        $("#produtos_table tbody tr td:eq(3)")
            .mask("000.000.000,00", {
                reverse: true
            }).trigger('input')
        $('.remove').click(function() {

            let index = $(this).closest('table').find(
                    'tbody tr')
                .index($(this).closest('tr'))
            produtos.splice(index, 1)

            $('#produtos_table tbody tr:eq(' + index + ')').remove()


        })

        function calculaTotalCusto() {
            total = 0
            $("#produtos_table tbody tr").each(function(index) {
                let subtotal = $(this).find('td:eq(3)').html().replace('.', '').replace(',', '.')
                total += parseFloat(subtotal)
                $('#preco_custo').val(parseFloat(total).toFixed(2)).trigger('input')
            })

        }

        $(document).ready(function() {
            $('#preco_custo,#preco_venda').mask("000.000.000,00", {
                reverse: true
            });
            $('#composto').change(function() {
                if ($(this).prop('checked')) {
                    $('#div_composto').show()
                    $('#preco_custo').prop('readonly', true)
                        .parent().find('label b').html('Preço total de custo')
                } else {
                    $('#div_composto').hide()
                    $('#preco_custo').prop('readonly', false)
                        .parent().find('label b').html('Preço de custo')
                }

            })
            search = $('#produto_search').select2({
                language: "pt-BR",
                theme: 'bootstrap4',
                placeholder: "Adicionar produto simples",
                width: "100%",
                ajax: {
                    url: '{{ route('produtos.search') }}',
                    dataType: 'json',
                    data: function(params) {
                        var query = {
                            search: params.term,
                            not: produtos.map(i => i[`id`])
                        }
                        return query;
                    }
                }
            });

            $('#produtos_table input[type=number]').change(function() {
                // Alteracao do subtotal
                let index = $(this).closest('table').find(
                        'tbody tr')
                    .index($(this).closest('tr'))

                let subtotal = parseFloat(produtos[index]
                    .preco_custo * $(this).val()).toFixed(2)

                $(this).closest('tr').find('td:eq(3)').html(
                    subtotal).mask("000.000.000,00", {
                    reverse: true
                }).trigger('input')

                calculaTotalCusto()
            })

            search.on('select2:select', function() {
                id = $(this).val()
                $.get('{{ route('produtos.search') }}?id=' + id, function(data) {
                    produtos.push(data)

                    $("#produtos_table tbody").append(
                        $('<tr>', ).append([
                            $('<td>').append([
                                $('<input>', {
                                    type: 'hidden',
                                    name: 'produtos_info[' + data.id + ']',
                                    value: JSON.stringify(data)
                                }), data.nome
                            ]),
                            $('<td>', {
                                class: 'text-right'
                            })
                            .html(data.preco_custo)
                            .mask("000.000.000,00", {
                                reverse: true
                            }).trigger('input'),
                            $('<td>').html($('<input>', {
                                type: 'number',
                                name: 'produtos[' + data.id + ']',
                                class: 'form-control m-auto shadow-sm',
                                style: 'max-width:10rem',
                                value: 1,
                                min: 1
                            }).change(function() {
                                // Alteracao do subtotal
                                let index = $(this).closest('table').find(
                                        'tbody tr')
                                    .index($(this).closest('tr'))

                                let subtotal = parseFloat(produtos[index]
                                    .preco_custo * $(this).val()).toFixed(2)

                                $(this).closest('tr').find('td:eq(3)').html(
                                    subtotal).mask("000.000.000,00", {
                                    reverse: true
                                }).trigger('input')

                                calculaTotalCusto()
                            })),
                            $('<td>', {
                                class: 'text-right'
                            }).html(data.preco_custo).mask("000.000.000,00", {
                                reverse: true
                            }).trigger('input'),
                            $('<td>').append($('<button>', {
                                type: 'button',
                                class: 'btn btn-danger float-right shadow-sm'
                            }).html($('<i>', {
                                class: 'fas fa-times'
                            })).click(function() {

                                let index = $(this).closest('table').find(
                                        'tbody tr')
                                    .index($(this).closest('tr'))
                                produtos.splice(index, 1)

                                $('#produtos_table tbody tr:eq(' + index + ')')
                                    .remove()


                            }))


                        ]))
                    calculaTotalCusto()

                })
            })

        })
    </script>
@endpush
