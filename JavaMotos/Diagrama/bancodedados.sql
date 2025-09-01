DROP DATABASE IF EXISTS javamotos;
CREATE DATABASE javamotos;
USE javamotos;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_usuario VARCHAR(30),
    email VARCHAR(50),
    senha VARCHAR(14),
    foto BLOB
);

CREATE TABLE publicacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    foto BLOB,
    titulo TINYTEXT,
    legenda TEXT,
    likes INT DEFAULT 0,
    deslikes INT DEFAULT 0,
    CONSTRAINT fk_publicacoes_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    publicacao_id INT,
    texto TEXT,
    CONSTRAINT fk_comentarios_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_comentarios_publicacao FOREIGN KEY (publicacao_id) REFERENCES publicacoes(id)
);

CREATE TABLE interacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    like_deslike TINYINT(1),
    publicacao_id INT,
    CONSTRAINT fk_interacoes_publicacao FOREIGN KEY (publicacao_id) REFERENCES publicacoes(id),
    CONSTRAINT fk_interacoes_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT unique_usuario_publicacao UNIQUE (usuario_id, publicacao_id)
);

-- Usuários
INSERT INTO usuarios (nome_usuario, email, senha, foto)
VALUES ('javamotos', 'contato@javamotos.com', 'senha123', NULL),
       ('João Paulo', 'teste@gmail.com', 'senha123', NULL),
       ('Maria', NULL, NULL, NULL),
       ('Carlos', NULL, NULL, NULL),
       ('Ana', NULL, NULL, NULL),
       ('Pedro', NULL, NULL, NULL),
       ('Lucas', NULL, NULL, NULL),
       ('Fernanda', NULL, NULL, NULL),
       ('Julia', NULL, NULL, NULL),
       ('Rafael', NULL, NULL, NULL),
       ('Bianca', NULL, NULL, NULL);

-- Publicações (3 Motos)
INSERT INTO publicacoes (usuario_id, foto, titulo, legenda)
VALUES 
(1, NULL, 'Honda CB 500F', 'Versátil, econômica e perfeita para uso urbano e rodoviário.'),
(1, NULL, 'Yamaha MT-07', 'Design agressivo e torque impressionante para quem ama adrenalina.'),
(1, NULL, 'Kawasaki Ninja 400', 'Esportiva de entrada com ótimo desempenho e estilo arrojado.');

-- Interações (exemplo de likes/deslikes nas 3 motos)
INSERT INTO interacoes (usuario_id, like_deslike, publicacao_id) VALUES
(1, 1, 1), (3, 1, 1), (4, 0, 1), (5, 1, 1), (6, 1, 1),
(1, 0, 2), (3, 1, 2), (4, 0, 2), (5, 1, 2), (6, 1, 2),
(1, 1, 3), (3, 0, 3), (4, 1, 3), (5, 0, 3), (6, 1, 3);
