CREATE TABLE produtos (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL, --UNIQUE,
    preco_custo FLOAT,
    preco_venda FLOAT NOT NULL,
    composto BOOLEAN,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME
);

CREATE TABLE produtos_compostos (
    id_composto INT NOT NULL,
    id_simples INT NOT NULL,
    quantidade INT NOT NULL,
    CONSTRAINT fk_produto_composto FOREIGN KEY (id_composto) REFERENCES produtos(id),
    CONSTRAINT fk_produto_simples FOREIGN KEY (id_simples) REFERENCES produtos(id)
);