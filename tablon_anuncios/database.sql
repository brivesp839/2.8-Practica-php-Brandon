USE tablon_anuncios
CREATE TABLE usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,  -- Identificador único
    nombre_usuario      VARCHAR(50)  NOT NULL UNIQUE,             -- UNIQUE: no puede repetirse
    email         VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL                    -- NUNCA la contraseña en claro
);

CREATE TABLE categorias (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE articulos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario     INT UNSIGNED NOT NULL,   -- Autor del anuncio (FK a users)
    id_categoria INT UNSIGNED NOT NULL,   -- Categoría (FK a categories)
    titulo       VARCHAR(150) NOT NULL,
    body        TEXT         NOT NULL,   -- TEXT admite textos largos
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Si se borra un usuario, se borran sus anuncios
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    -- No se puede borrar una categoría que tenga anuncios
    FOREIGN KEY (id_categoria) REFERENCES categorias(id) ON DELETE RESTRICT
);

CREATE TABLE comentarios (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_articulo INT UNSIGNED NOT NULL,    -- Anuncio comentado (FK a articles)
    id_usuario   INT UNSIGNED NOT NULL,    -- Autor del comentario (FK a users)
    body       TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Si se borra el anuncio, desaparecen sus comentarios
    FOREIGN KEY (id_articulo) REFERENCES articulos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario)    REFERENCES usuarios(id)    ON DELETE CASCADE
);

INSERT INTO categorias (nombre) VALUES
    ('Compra-venta'),
    ('Proyectos'),
    ('Objetos perdidos'),
    ('Otros');
