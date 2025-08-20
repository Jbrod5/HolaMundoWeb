-- Creacion de la base ded atos
CREATE DATABASE IF NOT EXISTS holamundo 
USE holamundo;

-- Tabla de usuarios
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de comentarios, imagen, audio, video
CREATE TABLE mensajes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  mensaje TEXT NOT NULL,
  imagen VARCHAR(255) NULL,
  audio VARCHAR(255) NULL,
  video VARCHAR(255) NULL,
  creado_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
