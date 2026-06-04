DROP TABLE IF EXISTS historial CASCADE;
DROP TABLE IF EXISTS tickets CASCADE;
DROP TABLE IF EXISTS usuarios CASCADE;

DROP TYPE IF EXISTS rol CASCADE;
DROP TYPE IF EXISTS estado CASCADE;
DROP TYPE IF EXISTS prioridad CASCADE;

DROP FUNCTION IF EXISTS actualizar_timestamp CASCADE;

CREATE TYPE rol AS ENUM ('usuario', 'tecnico', 'admin');
CREATE TYPE estado AS ENUM ('abierto', 'en_curso', 'cerrado');
CREATE TYPE prioridad AS ENUM ('baja', 'media', 'alta', 'critica');

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol rol NOT NULL DEFAULT 'usuario',
    creado_en TIMESTAMP DEFAULT NOW()
);

CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    estado estado NOT NULL DEFAULT 'abierto',
    prioridad prioridad NOT NULL DEFAULT 'media',
    creado_por INT NOT NULL,
    asignado_a INT,
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW(),
    CONSTRAINT fk_tickets_creado_por 
		FOREIGN KEY (creado_por) REFERENCES usuarios(id),
    CONSTRAINT fk_tickets_asignado_a 
		FOREIGN KEY (asignado_a) REFERENCES usuarios(id)
);

CREATE TABLE historial (
    id SERIAL PRIMARY KEY,
    ticket_id INT NOT NULL,
    usuario_id INT NOT NULL,
    campo_modificado VARCHAR(50),
    valor_anterior TEXT,
    valor_nuevo TEXT,
    fecha TIMESTAMP DEFAULT NOW(),
    CONSTRAINT fk_historial_ticket 
		FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    CONSTRAINT fk_historial_usuario 
		FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE OR REPLACE FUNCTION actualizar_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.actualizado_en = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_actualizar_ticket
BEFORE UPDATE ON tickets
FOR EACH ROW EXECUTE FUNCTION actualizar_timestamp();

INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES
('Admin', 'admin@incidencias.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Técnico Demo', 'tecnico@incidencias.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tecnico'),
('Usuario Demo', 'usuario@incidencias.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario');