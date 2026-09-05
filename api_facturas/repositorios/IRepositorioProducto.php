<?php
/**
 * IRepositorioProducto — el CONTRATO de la capa de datos.
 *
 * Una interface nativa de PHP define QUÉ operaciones existen sobre producto,
 * sin decir CÓMO ni CONTRA QUÉ motor. Cualquier clase con
 * `implements IRepositorioProducto` puede ocupar este lugar: el MariaDB real
 * de la v1, el falso en memoria de las pruebas, o el PostgreSQL que llegará
 * en la v3 (polimorfismo).
 *
 * El servicio depende de ESTA interfaz, nunca de una clase concreta
 * (inversión de dependencias — la D de SOLID).
 *
 * Las lecturas devuelven objetos del MODELO (la clase Producto), no arrays:
 * la capa de datos entrega el dato ya tipado y limpio.
 */

// Modo estricto de tipos (ver explicación completa en index.php):
declare(strict_types=1);

require_once __DIR__ . '/../modelos/Producto.php';

// Una interface solo declara FIRMAS de métodos (nombre, parámetros, tipo de
// retorno) — sin cuerpo. Quien la implemente escribirá el cómo.
interface IRepositorioProducto
{
    /**
     * Devuelve hasta $limite productos ordenados por código.
     * @return Producto[] (lista de objetos Producto)
     */
    public function obtenerTodos(int $limite): array;

    /**
     * Devuelve el Producto con ese código, o null si no existe.
     * "?Producto" = puede ser un Producto O null (tipo "nullable").
     */
    public function obtenerPorCodigo(string $codigo): ?Producto;

    /** Inserta el producto (llega como objeto del modelo). true = insertado. */
    public function crear(Producto $producto): bool;

    /**
     * Escribe los campos de $datos (los usan PUT y PATCH). Va como array
     * porque un PATCH puede traer SOLO algunos campos — un Producto completo
     * no puede representar "solo el stock".
     * Devuelve el número de filas afectadas (0 = el código no existe).
     */
    public function actualizar(string $codigo, array $datos): int;

    /** Elimina el producto. Devuelve filas eliminadas (0 = no existía). */
    public function eliminar(string $codigo): int;
}
