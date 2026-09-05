<?php
/**
 * Ensamblador — el ÚNICO lugar del sistema que conoce clases concretas.
 *
 * En la v1 era una función. Ahora son seis, una por recurso, y todas hacen
 * lo mismo: armar la cadena `servicio → repositorio` con la configuración
 * que llega del entorno.
 *
 * ======================================================================
 * ¿SEIS FUNCIONES CASI IGUALES? SÍ, Y ES A PROPÓSITO
 * ======================================================================
 *
 * La tentación es evidente: una sola función
 * `crearServicio(string $recurso)` con un `switch` adentro, o peor, algo que
 * arme el nombre de la clase con texto (`"Repositorio{$recurso}MariaDB"`).
 *
 * Se descartó, y por la misma razón por la que la API es específica y no
 * genérica (Artículo 10 de la constitución):
 *
 *   · con seis funciones, PHP verifica los tipos: si `ServicioCliente` no
 *     acepta un `IRepositorioCliente`, el error sale al llamarla;
 *   · con nombres armados en texto, el error sale **en producción**, cuando
 *     alguien pida el recurso que nadie probó;
 *   · y quien lea este archivo ve el inventario completo del sistema de un
 *     vistazo, sin ejecutar nada.
 *
 * Seis funciones cortas y aburridas se leen mejor que una lista y un
 * `switch`. Cuando llegue la v3 con un segundo motor, **es este archivo —y
 * solo éste— el que se convierte en una fábrica de verdad**: el examen del
 * principio abierto/cerrado.
 */

// Modo estricto de tipos (ver explicación completa en index.php):
declare(strict_types=1);

require_once __DIR__ . '/ServicioProducto.php';
require_once __DIR__ . '/ServicioEmpresa.php';
require_once __DIR__ . '/ServicioPersona.php';
require_once __DIR__ . '/ServicioCliente.php';
require_once __DIR__ . '/ServicioVendedor.php';
require_once __DIR__ . '/ServicioFactura.php';

require_once __DIR__ . '/../repositorios/RepositorioProductoMariaDB.php';
require_once __DIR__ . '/../repositorios/RepositorioEmpresaMariaDB.php';
require_once __DIR__ . '/../repositorios/RepositorioPersonaMariaDB.php';
require_once __DIR__ . '/../repositorios/RepositorioClienteMariaDB.php';
require_once __DIR__ . '/../repositorios/RepositorioVendedorMariaDB.php';
require_once __DIR__ . '/../repositorios/RepositorioFacturaMariaDB.php';

/**
 * Los tres datos de conexión, leídos del entorno.
 *
 * Esto sí se comparte —es configuración, no una decisión de qué clase usar—.
 * Los valores por defecto apuntan al puerto PUBLICADO de la base, para poder
 * correr la API sin Docker mientras la base sí está en Docker.
 *
 * @return array{0: string, 1: string, 2: string}
 */
function datosDeConexion(): array
{
    return [
        getenv('DB_DSN')     ?: 'mysql:host=localhost;port=13327;dbname=bdfacturas_mariadb_local',
        getenv('DB_USUARIO') ?: 'paradigmas',
        getenv('DB_CLAVE')   ?: 'paradigmas123',
    ];
}

// ======================================================================
// Una función por recurso. Fíjese en el tipo de retorno: siempre LA
// INTERFAZ, nunca la clase concreta.
// ======================================================================

function crearServicioProducto(): IServicioProducto
{
    [$dsn, $usuario, $clave] = datosDeConexion();
    return new ServicioProducto(new RepositorioProductoMariaDB($dsn, $usuario, $clave));
}

function crearServicioEmpresa(): IServicioEmpresa
{
    [$dsn, $usuario, $clave] = datosDeConexion();
    return new ServicioEmpresa(new RepositorioEmpresaMariaDB($dsn, $usuario, $clave));
}

function crearServicioPersona(): IServicioPersona
{
    [$dsn, $usuario, $clave] = datosDeConexion();
    return new ServicioPersona(new RepositorioPersonaMariaDB($dsn, $usuario, $clave));
}

function crearServicioCliente(): IServicioCliente
{
    [$dsn, $usuario, $clave] = datosDeConexion();
    return new ServicioCliente(new RepositorioClienteMariaDB($dsn, $usuario, $clave));
}

function crearServicioVendedor(): IServicioVendedor
{
    [$dsn, $usuario, $clave] = datosDeConexion();
    return new ServicioVendedor(new RepositorioVendedorMariaDB($dsn, $usuario, $clave));
}

function crearServicioFactura(): IServicioFactura
{
    [$dsn, $usuario, $clave] = datosDeConexion();
    return new ServicioFactura(new RepositorioFacturaMariaDB($dsn, $usuario, $clave));
}
