@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>Produtos</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('produtos.create') }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i> Novo
                    produto</a>
            </div>
            <div class="col-md-5">
                <div class="input-group mb-3 shadow-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" id="search" placeholder="Pesquisar">
                </div>
            </div>
        </div>
        <hr>
        @if (!empty(session('msg')))
            <div class="alert alert-success shadow-sm" role="alert">
                {{ session('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <table class="table table-striped table-hover" id="produtos" style="width:100%;margin:0px !important">
                    <thead>
                        <tr>
                            <th class="col-5">Nome</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Preco de custo (R$)</th>
                            <th class="text-center">Preco de venda (R$)</th>
                            <th class="col-2"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            var table = $('#produtos').DataTable({
                processing: true,
                serverSide: true,
                dom: "l<'card't><'row'<'col-sm-5'i><'col-sm-7'p>>",
                ajax: "{{ route('produtos.table') }}",
                columns: [{
                        data: 'nome'
                    },
                    {
                        data: 'tipo',
                        searchable: false
                    },
                    {
                        data: 'preco_custo'
                    },
                    {
                        data: 'preco_venda'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function(settings) {
                    $('tr').find('td:eq(1)').addClass('text-center')
                    $('tr').find('td:eq(2),td:eq(3)').mask("#.##0,00", {
                        reverse: true
                    }).addClass('text-right')

                    $('.excluir').submit(function() {
                        Swal.fire({
                            title: 'Atenção!',
                            text: 'Excluir o produto também o removerá de todos os produtos compostos, deseja continuar?',
                            icon: 'warning',
                            confirm: true,
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: 'Excluir',
                            cancelButtonText: 'Cancelar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $(this).unbind().submit()
                            }
                        })
                        return false
                    })
                },


                language: {
                    "processing": "A processar...",
                    "lengthMenu": "Exibindo _MENU_ registros",
                    "zeroRecords": "Não foram encontrados resultados",
                    "info": "Mostrando _START_ até _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando de 0 até 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros no total)",
                    "search": "<b>Procurar:</b>",
                    "paginate": {
                        "first": "Primeiro",
                        "previous": "Anterior",
                        "next": "Seguinte",
                        "last": "Último"
                    }
                },

            });
            $('#search').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endpush
