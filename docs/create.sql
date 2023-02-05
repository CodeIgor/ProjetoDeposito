CREATE TABLE produtos (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    --UNIQUE,
    preco_custo FLOAT,
    preco_venda FLOAT NOT NULL,
    composto BOOLEAN,
    created_at DATETIME,
    updated_at DATETIME,
    deleted_at DATETIME
);
CREATE TABLE produtos_compostos (
    id_composto INT NOT NULL,
    id_simples INT NOT NULL,
    quantidade INT NOT NULL,
    CONSTRAINT fk_produto_composto FOREIGN KEY (id_composto) REFERENCES produtos(id),
    CONSTRAINT fk_produto_simples FOREIGN KEY (id_simples) REFERENCES produtos(id)
);
CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    remember_tolen VARCHAR(100),
    created_at DATETIME,
    updated_at DATETIME,
);
CREATE TABLE estoques (
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    id_requisicao INT NOT NULL,
    data_operacao DATETIME NOT NULL,
    tipo_operacao int not null,
    CONSTRAINT fk_produto FOREIGN KEY (id_produto) REFERENCES produtos(id),
    CONSTRAINT fk_USER FOREIGN KEY (id_requisicao) REFERENCES requisicoes(id),
    CONSTRAINT fk_operacao FOREIGN KEY (tipo_operacao) REFERENCES operacoes(id)
);
CREATE TABLE requisicoes(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nome_funcionario VARCHAR(255) NOT NULL,
    data_retirada DATETIME,
);
CREATE TABLE operacoes(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    descricao VARCHAR(255) not null
)
