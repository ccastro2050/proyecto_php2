# Mapa de versiones — Proyecto PHP

> **Cómo se trabaja este proyecto: por versiones (desarrollo incremental guiado
> por especificaciones).** Así maneja SDD el crecimiento de un sistema: la
> **constitución es permanente** ([../1_constitution.md](../1_constitution.md))
> y cada versión tiene **su propia especificación, plan y tareas** en una
> carpeta `vN_nombre/`. La spec de una versión es EL documento que se le
> entrega a la IA (o al estudiante) para construir ESA versión — ni más ni menos.
>
> Regla de avance: una versión está TERMINADA cuando pasa todos los criterios
> de aceptación de su `2_spec.md`. Solo entonces se escribe la spec de la
> siguiente.

---

## Cada versión vive en su propio repositorio

| Versión | Repositorio | Qué EXISTE al terminarla | Qué concepto nuevo enseña |
|---|---|---|---|
| **v1** | [`proyecto_php1`](https://github.com/ccastro2050/proyecto_php1) | El CRUD de **producto** de punta a punta: API (PHP puro + PDO) contra **MariaDB** **y su front**. Una tabla, un motor. | Arquitectura en capas con `interface` de PHP desde el día 1, y la separación front/API a nivel de sistema |
| **v2** | **`proyecto_php2`** ← **USTED ESTÁ AQUÍ** | Seis recursos: producto, **empresa, persona, cliente, vendedor y factura**, con sus pantallas. Sigue siendo solo MariaDB. | **Llaves foráneas e integridad**; **maestro-detalle** con triggers y procedimientos almacenados; el 409 |
| **v3** | [`proyecto_php3`](https://github.com/ccastro2050/proyecto_php3) | Lo mismo, ahora también contra **PostgreSQL**, escogido por configuración | Nace la **fábrica** — abierto/cerrado en acción: cero cambios en controladores, servicios ni pantallas |
| **v4** | [`proyecto_php4`](https://github.com/ccastro2050/proyecto_php4) | Tercer motor (**SQL Server**), los tres a la vez, compose completo | Liskov entre repositorios; contenedores, volúmenes y healthchecks |

**Un repositorio por versión, y no una rama por versión.** La razón es
práctica: así se pueden tener dos versiones **encendidas al mismo tiempo** y
compararlas lado a lado — por eso cada una usa puertos propios. Y así la v1
sigue siendo un ejemplo completo y ejecutable aunque la v2 ya exista.

Cada repositorio **acumula** las carpetas de especificación de las versiones
anteriores, para poder leer cómo se llegó hasta aquí. El **código**, en
cambio, es el de la versión que le da nombre.

## Qué cambia de la v1 a esta versión

| | v1 | v2 |
|---|---|---|
| Recursos | 1 (`producto`) | 6 |
| Tablas con llave foránea | ninguna | 3 (`cliente`, `vendedor`, `factura`) |
| Llaves | las escribe el cliente | **también las genera la base** (`id`, `numero`) |
| Códigos de error | 400 · 404 · 422 · 500 | **+ 409**, cuando la base dice que no |
| Lógica en la base | ninguna que la API tocara | **triggers y 6 procedimientos almacenados** |
| Recursos que son un CRUD | el único que había | 5 de 6 — **la factura no lo es** |

Ese último renglón es el que más enseña. Empresa, persona, cliente, vendedor y
producto se listan, se leen, se crean, se corrigen y se borran. Una factura
**se anula**, y eso no es ninguna de las cinco.

## Reglas del trabajo por versiones

1. **La constitución no se toca entre versiones.** Si una versión exige cambiar
   una regla, eso es una decisión mayor que se discute aparte. (La v2 le
   *agregó* el Artículo 11, que no contradice ninguno: describe algo que la v1
   no tenía enfrente.)
2. **Cada carpeta de versión es autocontenida**: con la constitución + esa
   carpeta se puede construir la versión desde el estado que dejó la anterior,
   sin leer nada más.
3. **Una versión incluye su front.** No está terminada cuando la API responde:
   está terminada cuando la pantalla de esa versión muestra lo que la API
   devuelve, y sigue en pie —con su aviso— cuando la API no responde
   (Artículo 1.1 de la [constitución](../1_constitution.md)).
4. **El código de una versión no anticipa a la siguiente**: en v2 NO se escribe
   la fábrica multi-motor "por si acaso" — se escribe la interfaz, y la
   fábrica llegará cuando un segundo motor la justifique (v3). **YAGNI con
   dirección**: *You Aren't Gonna Need It* ("no lo vas a necesitar") — no se
   escribe hoy lo que solo hará falta mañana, pero se sabe hacia dónde va.
5. **Cada versión termina en verde**: criterios de aceptación verificables,
   commit (y tag `v1`, `v2`, …) al cerrarla.
6. La spec de la versión siguiente **parte del estado real** dejado por la
   anterior — si el código divergió de la spec, primero se reconcilia
   (la spec siempre refleja el estado actual: deuda de especificación).

## Lo que la v3 va a poner a prueba

Vale la pena dejarlo escrito ahora, porque es lo que le da sentido a cómo está
organizada esta versión:

- El código de la v2 que habla el dialecto de MariaDB está **todo** en las
  clases `Repositorio*MariaDB`. Ni un `SELECT` fuera de ahí.
- Los números de error del motor (1062, 1452, 1451) están en **un solo
  archivo**, `errores_de_integridad.php`.
- Y `ensamblador.php` es el único sitio del sistema que hace `new` de una
  clase concreta.

Si eso es cierto, agregar PostgreSQL en la v3 no debería tocar ni un
controlador, ni un servicio, ni una pantalla. Si resulta que sí hay que
tocarlos, la v2 quedó mal cortada — y ése será el examen.
