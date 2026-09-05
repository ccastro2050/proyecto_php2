<?php
/**
 * La traducción de los errores de INTEGRIDAD del motor.
 *
 * ======================================================================
 * POR QUÉ ESTO VIVE EN LA CAPA DE DATOS, Y NO MÁS ARRIBA
 * ======================================================================
 *
 * Los números que se leen aquí abajo —1062, 1452, 1451— son de MariaDB.
 * PostgreSQL usa otros, y SQL Server otros. El repositorio es la ÚNICA capa
 * que tiene derecho a conocerlos, porque es la única que sabe qué motor hay
 * detrás. Si esta traducción viviera en el servicio, cambiar de motor
 * obligaría a tocar el negocio — y eso es exactamente lo que la arquitectura
 * por capas viene a evitar.
 *
 * Hacia arriba no sale un número: sale una ConflictoDeIntegridadExcepcion
 * con el problema dicho en español.
 *
 * ======================================================================
 * Y POR QUÉ NO SE PREGUNTA ANTES
 * ======================================================================
 *
 * La alternativa era comprobar primero: «¿existe la persona PE001?», y solo
 * entonces insertar. Se descartó por dos razones:
 *
 *   1. **No es cierto que evite el problema.** Entre la comprobación y la
 *      inserción, otro usuario puede borrar esa persona. La ventana es
 *      pequeña y por eso el error es difícil de encontrar — que es lo que lo
 *      hace peligroso.
 *   2. **Duplica una regla que ya está escrita.** La llave foránea vive en
 *      la base de datos. Comprobarla también en PHP significa mantener la
 *      misma regla en dos sitios, y el día que cambie uno solo, el sistema
 *      miente.
 *
 * Así que se intenta, y se traduce el veredicto. La base decide; la API
 * explica.
 */

// Modo estricto de tipos (ver explicación completa en index.php):
declare(strict_types=1);

require_once __DIR__ . '/../excepciones/ConflictoDeIntegridadExcepcion.php';

/**
 * Convierte una PDOException de integridad en una excepción con mensaje
 * entendible. Si no la reconoce, la deja pasar tal cual — que se vuelva un
 * 500, porque entonces sí es un problema nuestro.
 *
 * @param string $ficha Cómo se llama esto en el idioma del usuario ("cliente").
 */
function traducirErrorDeIntegridad(PDOException $error, string $ficha): never
{
    // errorInfo trae tres cosas; la [1] es el código del MOTOR.
    // (El getCode() de PDO trae el SQLSTATE genérico, '23000' para todas
    // las violaciones de integridad: no alcanza para distinguirlas.)
    $codigo = $error->errorInfo[1] ?? 0;

    // 1062 — llave duplicada: ya hay una fila con esa llave primaria.
    if ($codigo === 1062) {
        throw new ConflictoDeIntegridadExcepcion(
            "Ya existe $ficha con esa llave. Las llaves no se repiten."
        );
    }

    // 1452 — «cannot add or update a child row»: se apuntó a algo que no
    // existe. Ej.: crear un cliente con fkcodpersona = 'PE999'.
    if ($codigo === 1452) {
        throw new ConflictoDeIntegridadExcepcion(
            "Alguno de los códigos a los que apunta $ficha no existe. "
            . "Revise que la persona o la empresa estén creadas."
        );
    }

    // 1451 — «cannot delete or update a parent row»: hay filas que dependen
    // de ésta. Ej.: borrar una persona que ya es cliente.
    if ($codigo === 1451) {
        throw new ConflictoDeIntegridadExcepcion(
            "No se puede eliminar $ficha porque hay otras fichas que dependen "
            . "de ella. Elimine primero las que la usan."
        );
    }

    // No es un problema de integridad conocido: que suba y se vuelva 500.
    throw $error;
}
