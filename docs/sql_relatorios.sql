-- Relatorio de entrada de estoque
SELECT p.nome,
    sum(e.quantidade) as qtde_requisitada,
    sum(e.quantidade) * p.preco_custo as preco_custo_total,
    sum(e.quantidade) * p.preco_venda as preco_venda_total
from estoques e
    inner join produtos p on e.id_produto = p.id
where tipo_operacao = 1
    and e.data_operacao >= :data_inicial
    and e.data_operacao <= :data_final
group by p.id;

-- Relatorio de saida de estoque
select t.nome,
    sum(qtde_retirada) as qtde_retirada,
    sum(preco_custo_total) as preco_custo_total
from (
        SELECT p.nome,
            sum(e.quantidade) as qtde_retirada,
            sum(e.quantidade) * p.preco_custo as preco_custo_total
        from estoques e
            inner join produtos p on e.id_produto = p.id
            left join produtos_compostos pc on p.id = pc.id_composto
            left join produtos ps on pc.id_simples = ps.id
        where tipo_operacao = 2
            and p.composto = false
            and e.data_operacao >= :data_inicial
            and e.data_operacao <= :data_final
        group by p.id
        union
        SELECT ps.nome,
            sum(e.quantidade) * pc.quantidade as qtde_retirada,
            sum(e.quantidade) * pc.quantidade * ps.preco_custo as preco_custo_total
        from estoques e
            inner join produtos p on e.id_produto = p.id
            inner join produtos_compostos pc on p.id = pc.id_composto
            inner join produtos ps on pc.id_simples = ps.id
        where tipo_operacao = 2
            and p.composto = true
            and e.data_operacao >= :data_inicial
            and e.data_operacao <= :data_final
        group by p.id,
            ps.id
    ) t
group by t.nome;

-- Relatorio requisicao de produtos
select nome_funcionario,
    data_retirada,
    p.nome as nome_produto,
    quantidade,
    e.quantidade * p.preco_custo as preco_custo_total,
    e.quantidade * p.preco_venda as preco_venda_total
from requisicoes r
    inner join estoques e on r.id = e.id_requisicao
    inner join produtos p on e.id_produto = p.id
where r.data_retirada >= :data_inicial
    and r.data_retirada <= :data_final