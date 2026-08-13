-- Comedor Nony's - esquema de base de datos
-- Ejecutar con: mysql -u root < sql/schema.sql

CREATE DATABASE IF NOT EXISTS comedor_nonys
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE comedor_nonys;

DROP TABLE IF EXISTS menu_del_dia;
DROP TABLE IF EXISTS platillos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS configuracion;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE categorias (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(80)  NOT NULL,
  descripcion VARCHAR(200) DEFAULT NULL,
  icono       VARCHAR(16)  DEFAULT NULL,
  orden       INT          NOT NULL DEFAULT 0,
  activa      TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La foto se guarda en la propia base (foto_tipo + foto_datos) para que
-- sobreviva en servidores con disco efímero, como Render. La columna imagen
-- sigue sirviendo para pegar una liga de internet o una ruta del proyecto.
CREATE TABLE platillos (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT          NOT NULL,
  nombre       VARCHAR(120) NOT NULL,
  descripcion  VARCHAR(400) DEFAULT NULL,
  precio       DECIMAL(8,2) NOT NULL DEFAULT 0,
  imagen       VARCHAR(255) DEFAULT NULL,
  foto_tipo    VARCHAR(40)  DEFAULT NULL,
  foto_datos   MEDIUMBLOB   DEFAULT NULL,
  foto_version INT          NOT NULL DEFAULT 0,
  disponible   TINYINT(1)   NOT NULL DEFAULT 1,
  destacado    TINYINT(1)   NOT NULL DEFAULT 0,
  orden        INT          NOT NULL DEFAULT 0,
  activo       TINYINT(1)   NOT NULL DEFAULT 1,
  creado_en    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_categoria (categoria_id),
  CONSTRAINT fk_platillo_categoria FOREIGN KEY (categoria_id)
    REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Un renglón por platillo ofrecido en una fecha. platillo_id enlaza al menú
-- general cuando el guiso del día ya existe en el catálogo; es NULL cuando la
-- cocina improvisa algo que no está en la carta.
CREATE TABLE menu_del_dia (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  fecha       DATE         NOT NULL,
  platillo_id INT          DEFAULT NULL,
  nombre      VARCHAR(120) NOT NULL,
  descripcion VARCHAR(400) DEFAULT NULL,
  precio      DECIMAL(8,2) NOT NULL DEFAULT 0,
  disponible  TINYINT(1)   NOT NULL DEFAULT 1,
  orden       INT          NOT NULL DEFAULT 0,
  creado_en   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_fecha (fecha),
  CONSTRAINT fk_mdd_platillo FOREIGN KEY (platillo_id)
    REFERENCES platillos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE configuracion (
  clave  VARCHAR(60) PRIMARY KEY,
  valor  TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuarios (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  usuario       VARCHAR(60) NOT NULL UNIQUE,
  contrasena    VARCHAR(255) NOT NULL,
  nombre        VARCHAR(120) DEFAULT NULL,
  creado_en     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario inicial: admin / nonys2024 (cambiar desde el panel)
INSERT INTO usuarios (usuario, contrasena, nombre) VALUES
('admin', '$2y$10$gpooOZ2ULV21bPn3AXBw4uf3WDQMeuQrwjhfWdkSuwVbkaMZS2Oh2', 'Administrador');

INSERT INTO configuracion (clave, valor) VALUES
('nombre_negocio', 'Comedor Nony\'s'),
('lema',           'Comida casera mexicana, como en casa de la abuela'),
('whatsapp',       '4831078918'),
('telefono',       '4831078918'),
('direccion',      'Av. Insurgentes 123, Col. Centro, CDMX'),
('maps_url',       'https://maps.google.com/?q=Av.+Insurgentes+123+Centro+CDMX'),
('horario',        'Lunes a Sábado 8:00 - 18:00 hrs · Domingo 9:00 - 16:00 hrs'),
('aviso',          '');

INSERT INTO categorias (id, nombre, descripcion, icono, orden) VALUES
(1, 'Antojitos',       'Lo que se come con la mano y sabe a fiesta',        '🌮', 1),
(2, 'Guisados',        'De la cazuela al plato, recetas de siempre',        '🍲', 2),
(3, 'Sopas y Caldos',  'Caldito caliente para el alma',                     '🥣', 3),
(4, 'Bebidas',         'Aguas frescas del día y clásicos de la casa',       '🥤', 4),
(5, 'Postres',         'El final dulce que se antoja',                      '🍮', 5);

INSERT INTO platillos (categoria_id, nombre, descripcion, precio, disponible, destacado, orden) VALUES
(1, 'Quesadillas de comal',   'Tortilla de maíz hecha a mano con queso Oaxaca, servidas con salsa verde o roja.', 45.00, 1, 1, 1),
(1, 'Sopes surtidos',         'Tres sopes con frijol, lechuga, crema, queso fresco y su guisado a elegir.',       60.00, 1, 0, 2),
(1, 'Tlacoyos de haba',       'Masa azul rellena de haba, con nopalitos, queso y salsa martajada.',               50.00, 1, 0, 3),
(1, 'Huarache de bistec',     'Masa alargada con frijol, bistec asado, cebolla y salsa de molcajete.',            85.00, 1, 0, 4),
(2, 'Mole poblano con pollo', 'Pierna o muslo bañado en mole de la casa, con arroz rojo y ajonjolí.',            110.00, 1, 1, 1),
(2, 'Chiles rellenos',        'Chile poblano relleno de queso, capeado, en caldillo de jitomate.',                95.00, 1, 0, 2),
(2, 'Tinga de res',           'Deshebrada en chipotle con cebolla, servida con arroz y frijoles refritos.',      100.00, 1, 0, 3),
(2, 'Chicharrón en salsa verde','Chicharrón suave en salsa de tomate verde, con arroz y tortillas.',              90.00, 1, 0, 4),
(2, 'Rajas con crema',        'Poblano en rajas con elote, crema y queso. Opción vegetariana.',                   85.00, 1, 0, 5),
(3, 'Caldo de pollo',         'Con verduras, arroz y garbanzo. Se sirve con limón y chile de árbol.',             70.00, 1, 0, 1),
(3, 'Sopa de fideo',          'Fideo seco en caldillo de jitomate, con aguacate y queso fresco.',                 45.00, 1, 0, 2),
(3, 'Pozole rojo',            'Maíz cacahuazintle y maciza de cerdo, con lechuga, rábano y orégano.',            120.00, 1, 1, 3),
(3, 'Menudo',                 'Solo fines de semana. Con su orégano, cebolla y limón.',                          125.00, 0, 0, 4),
(4, 'Agua fresca del día',    'Jamaica, horchata o el sabor de temporada. Vaso o jarra.',                         25.00, 1, 0, 1),
(4, 'Café de olla',           'Con piloncillo y canela, en jarrito de barro.',                                    30.00, 1, 0, 2),
(4, 'Refresco',               'Línea nacional, 355 ml.',                                                         28.00, 1, 0, 3),
(4, 'Champurrado',            'Atole de maíz con chocolate, espeso y calientito.',                                35.00, 1, 0, 4),
(5, 'Flan napolitano',        'Casero, con caramelo tostado.',                                                    40.00, 1, 0, 1),
(5, 'Arroz con leche',        'Con canela y pasas, servido tibio.',                                               35.00, 1, 0, 2),
(5, 'Gelatina de leche',      'De vainilla o rompope, según el día.',                                             25.00, 1, 0, 3);

-- Menú del día de ejemplo (hoy)
INSERT INTO menu_del_dia (fecha, platillo_id, nombre, descripcion, precio, disponible, orden) VALUES
(CURDATE(), NULL, 'Sopa de fideo',            'De entrada, con aguacate y queso fresco.',              0.00, 1, 1),
(CURDATE(), NULL, 'Milanesa de res empanizada','Con arroz, ensalada y frijoles de la olla.',          95.00, 1, 2),
(CURDATE(), NULL, 'Albóndigas al chipotle',   'En caldillo suave, acompañadas de arroz blanco.',      90.00, 1, 3),
(CURDATE(), NULL, 'Agua de jamaica',          'Incluida con la comida corrida.',                       0.00, 0, 4);
