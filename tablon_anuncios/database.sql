USE tablon_anuncios
-- ============================================================
-- USUARIOS
-- ============================================================
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,  -- Identificador único
    username      VARCHAR(50)  NOT NULL UNIQUE,             -- UNIQUE: no puede repetirse
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,                    -- NUNCA la contraseña en claro
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP       -- Se rellena sola al insertar
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- CATEGORÍAS
-- ============================================================
CREATE TABLE categories (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- ANUNCIOS (publicaciones)
-- ============================================================
CREATE TABLE articles (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL,   -- Autor del anuncio (FK a users)
    category_id INT UNSIGNED NOT NULL,   -- Categoría (FK a categories)
    title       VARCHAR(150) NOT NULL,
    body        TEXT         NOT NULL,   -- TEXT admite textos largos
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Si se borra un usuario, se borran sus anuncios
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    -- No se puede borrar una categoría que tenga anuncios
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- COMENTARIOS
-- ============================================================
CREATE TABLE comments (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id INT UNSIGNED NOT NULL,    -- Anuncio comentado (FK a articles)
    user_id    INT UNSIGNED NOT NULL,    -- Autor del comentario (FK a users)
    body       TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Si se borra el anuncio, desaparecen sus comentarios
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATOS INICIALES: categorías de ejemplo
-- ============================================================
INSERT INTO categories (name) VALUES
    ('Compra-venta'),
    ('Proyectos'),
    ('Objetos perdidos'),
    ('Otros');