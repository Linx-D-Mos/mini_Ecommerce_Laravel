📘 LEARNING LOG - Proyecto 1: Gestión de Biblioteca
Fecha: 17 Enero 2026 Estado: Configuración de BD y Seeding completado.

1. Diseño de Base de Datos (Schema)
Aprendí que el diseño inicial es crítico. Un error aquí (como una mala relación) causa deuda técnica inmediata.

Relación Muchos a Muchos (N:M):

Caso: Libros <-> Autores.

Solución: Se requiere una Tabla Pivote.

Convención Laravel: Orden alfabético de los modelos en singular (author_book).

Migración: Usar foreignId()->constrained()->onDelete('cascade') para evitar registros huérfanos.

Integridad de Datos:

Usar unsignedInteger para stocks (no existen stocks negativos).

Usar timestamp nullable (returned_at) en lugar de un campo de estado string (status). Si es null, está prestado; si tiene fecha, se devolvió.

2. Eloquent ORM & Modelos
Naming Conventions:

Si la relación devuelve uno: singular (ej. book()).

Si la relación devuelve colección: plural (ej. books(), loans()).

Configuración de Relaciones:

belongsToMany: Usado en Book y Author (gracias a la tabla pivote).

hasMany / belongsTo: Usado para Préstamos.

3. Factories & Faker
Errores corregidos al generar datos falsos:

Magnitud: randomNumber(20) genera 20 dígitos. Para rangos (0-20) se usa numberBetween(0, 20).

Tipos de Datos: No mezclar objetos DateTime en campos definidos como integer (años). Usar $this->faker->year().

Nombres: Usar firstName() en lugar de name() para evitar prefijos como "Mr." o "Dr.".

4. Seeding Avanzado (Lógica de Negocio)
Aprendí a no depender siempre de la "magia" de los factories, sino a escribir lógica PHP en el DatabaseSeeder para casos complejos.

Seed de Relación N:M:

PHP
// Crear libros y adjuntar autores aleatorios al vuelo
$books = Book::factory(15)->create()->each(function ($book) use ($authors) {
    $book->authors()->attach($authors->random(rand(1, 3)));
});
Seed Condicional (Préstamos):

Iteramos sobre estudiantes creados.

Usamos rand() para decidir si crear préstamos o no.

Controlamos manualmente returned_at para simular libros pendientes vs. devueltos.

5. Herramientas
Git: La interfaz gráfica de VS Code muestra el Staging Area, no el historial. Para ver el historial real: git log --oneline o extensión "Git Graph".

Comando de Reinicio: php artisan migrate:fresh --seed (Borra todo, migra y siembra).

## 📅 [19-01-2026] - Finalización del CRUD de Libros y Testing Automatizado

### 1. 🛠️ Configuración y Corrección del Entorno de Testing
- **Instalación de Pest PHP:** Configuración inicial y resolución de conflictos de dependencias con PHPUnit y Collision en el `composer.json`.
- **Corrección de `Pest.php`:** Se habilitó la carga del entorno de Laravel (App) en los tests unitarios (`Unit`), ya que por defecto solo estaba habilitado para `Feature`. Esto solucionó el error `Call to member function connection() on null`.
- **Faker en Factories:** Se estandarizó el uso de `$this->faker->name()` para evitar errores de `InvalidArgumentException` por configuraciones de idioma (Locale) faltantes en el entorno de testing.

### 2. ✅ TDD: Tests Unitarios de Modelos
Se crearon pruebas para asegurar la integridad de la base de datos antes de construir la API:
- **`BookTest`:** Verificación de la relación "Muchos a Muchos" (N:M) con Autores usando `hasAttached`.
- **`StudentTest`:** Validación de la restricción `unique` en el email, asegurando que se lance una `QueryException` al intentar duplicados.
- **`LoanTest`:** Verificación del *Casting* de fechas (`loaned_at` como instancia de `Carbon`) y la relación `belongsTo` con estudiantes.

### 3. 🚀 Desarrollo API RESTful (Módulo Libros)
Implementación completa del controlador `BookController` con arquitectura profesional:

#### A. Creación (Store)
- **Validación (`StoreBookRequest`):** Reglas para ISBN único, año como entero de 4 dígitos y validación de array de autores existentes (`exists:authors,id`).
- **Transacciones:** Uso de `DB::transaction` para asegurar que el libro y sus relaciones se guarden atómicamente.
- **Relaciones:** Uso de `sync()` para vincular autores en la tabla pivote.

#### B. Lectura (Index & Show)
- **Optimización:** Solución del problema **N+1** usando *Eager Loading* (`with('authors')`).
- **Paginación:** Implementación de `paginate(10)` en lugar de `all()` para proteger la memoria del servidor.
- **Recursos (`BookResource`):** Transformación de datos y anidación de `AuthorResource` para respuestas JSON limpias.

#### C. Actualización (Update)
- **Validación Condicional (`UpdateBookRequest`):** Implementación de `Rule::unique(...)->ignore($this->book)` para permitir guardar el mismo ISBN si pertenece al libro que se está editando.

#### D. Eliminación (Destroy)
- **Limpieza:** Desvinculación previa de relaciones con `detach()` dentro de una transacción.
- **Estándar HTTP:** Retorno de código **204 No Content** al eliminar exitosamente.

### 4. 🐛 Debugging y Herramientas
- **Postman:** Solución de error `ECONNREFUSED` ajustando el puerto (8001 vs 80) y configuración del Header `Accept: application/json` para ver errores de validación en lugar de HTML.
- **DBeaver:** Corrección de la conexión a la base de datos correcta (`sisgesbiblioteca` en lugar de `postgres`) para visualizar las tablas migradas.

## 📅 [20-01-2026] - Feature Testing y CRUD de Estudiantes

### 1. Testing de API (Feature Tests)
Aprendí a probar endpoints HTTP completos en lugar de solo clases aisladas.
- **Simulación de Peticiones:** Usar `postJson`, `putJson`, `deleteJson` para asegurar que Laravel maneje las cabeceras `Accept: application/json` correctamente.
- **Asserts Clave:**
  - `assertCreated()` (201) para creaciones.
  - `assertNoContent()` (204) para eliminaciones.
  - `assertJsonCount(10, 'data')` para verificar que la paginación realmente corta los resultados.
- **RefreshDatabase:** Fundamental usar `uses(RefreshDatabase::class)` para limpiar la BD entre tests y evitar datos basura.

### 2. Errores Comunes y Soluciones
- **Validación en Controlador:** Diferencia crítica entre `$request->validate()` (ejecuta validación, retorna void/redirección) y `$request->validated()` (retorna el array de datos limpios).
- **Rutas de Update:** Siempre requieren el ID en la URL (`/api/students/{id}`).
- **Modelos en Tests:** Los modelos en memoria no se actualizan solos. Si cambio algo en la BD, debo usar `$student->refresh()` para ver los cambios en la variable PHP.

### 3. Estándares REST
- **Delete:** No se devuelve JSON de confirmación, se devuelve un status 204 (No Content).

## 📅 [20-01-2026] - Lógica de Negocio Avanzada y Servicios

### 1. 🏗️ Patrón de Servicios (Service Layer)
Aprendí a desacoplar la lógica de negocio de los Controladores.
- **Cuándo usarlos:** Cuando hay lógica compleja, validaciones de negocio múltiples o transacciones que tocan varias tablas.
- **Beneficio:** El Controlador solo "orquesta" (recibe petición -> llama servicio -> devuelve respuesta), manteniéndose limpio ("Skinny Controller").
- **Inyección:** Se inyectan en el constructor del controlador (`__construct(LoanService $service)`).

### 2. 📦 Optimización de API Resources
- **Solución N+1:** Evitar hacer consultas (`Book::find`) dentro de un `JsonResource`.
- **Eager Loading:** Cargar las relaciones previamente en el Servicio (`$loan->load('book')`) y acceder a ellas en el recurso (`$this->book->title`).

### 3. 🧪 Estrategias de Testing
- **Test After:** Escribir la lógica primero y los tests después para validar flujos críticos (como stock 0).
- **Factories Avanzados:** Uso de `configure()` y `afterCreating` para manejar relaciones complejas en factories.
- **Unit vs Feature:** Testear la clase Servicio aislada (Unit) para reglas de negocio y el Controlador (Feature) para códigos HTTP (409 vs 200).
- 
## 📅 [21-01-2026] - Debugging, Namespaces y Route Model Binding

### 1. 📂 Refactorización y Namespaces
Aprendí que mover archivos físicamente no basta. PHP requiere que el `namespace` dentro del archivo coincida con la estructura de carpetas.
- **Error:** `Class not found` al mover un Request.
- **Solución:**
  1. Actualizar `namespace App\Http\Requests\Book;` en el archivo.
  2. Actualizar el `use` en el Controlador.
  3. Ejecutar `sail composer dump-autoload` si persiste.

### 2. 🤖 Route Model Binding y Errores 404
- Descubrí que al inyectar el modelo en el método (`show(Book $book)`), Laravel busca el registro automáticamente **antes** de entrar al método.
- **No hace falta try-catch:** Si no existe, Laravel lanza `ModelNotFoundException` y devuelve 404 automáticamente.
- **Mantener controladores limpios:** Delegar el manejo de errores estándar al Framework.

### 3. 🌐 Headers HTTP
- **Accept: application/json**: Obligatorio en Postman/Clientes API.
  - Sin esto, Laravel cree que es un navegador y devuelve HTML (o redirecciona) cuando hay errores (404, 422).
  - Con esto, Laravel devuelve errores en formato JSON.
---
**PROYECTO 1 COMPLETADO: Sistema de Biblioteca**

**Proyecto 2 COMENZADO : E-COMMERCE**
[22-01-2026] - Inicio Proyecto 2: Mini E-commerce (Digital Products)
1. 🏗️ Diseño de Base de Datos y Tipos de Datos
Aprendí que las decisiones de tipos de datos afectan la lógica de negocio futura.

Precios: Abandoné float/decimal. Usamos unsignedInteger para guardar precios en centavos (evita errores de redondeo financiero).

Fechas: Cambié date por timestamp en bought_at. Si necesito calcular expiraciones en minutos (ej: links de descarga), date no sirve.

Soft Deletes: Implementado en Productos para mantener la integridad histórica de las compras de los usuarios, incluso si el producto se deja de vender.

2. 🔗 Relaciones Avanzadas y Datos en Pivote (CRÍTICO)
Este fue el concepto más importante de la fase de modelado.

El Problema: Si un producto cambia de precio, las órdenes viejas no pueden cambiar su valor.

La Solución: Guardar el price_at_purchase en la tabla intermedia (order_item).

Implementación:

Forzar nombre de tabla: belongsToMany(..., 'order_item') cuando no seguimos la convención alfabética (order_product).

Recuperar datos: Usar withPivot('price_at_purchase'). Sin esto, Eloquent descarta los datos de la tabla intermedia y solo devuelve los modelos relacionados.

3. 🧠 Lógica de Seeding (Desafío de Lógica)
Me enfrenté a problemas de lógica al intentar crear órdenes y calcular totales dentro de bucles.

Error Inicial: Intentar crear la orden dentro del bucle de productos o intentar leer el precio de la pivote ($order->pivot) inmediatamente después de guardarlo.

Aprendizaje:

Crear la instancia de la Orden antes del bucle.

Iterar para adjuntar productos (attach).

Sumar los precios usando las variables en memoria ($product->price), no consultando la BD repetidamente.

Hacer un update final al total de la orden.

Conclusión: A veces la solución "compleja" en mi cabeza se resuelve simplificando el flujo paso a paso.

4. 🧪 TDD con Archivos y Storage
Aprendí a probar subidas de archivos sin ensuciar el disco duro local.

Herramientas: Storage::fake('public') y UploadedFile::fake()->image(...).

Flujo: El test intercepta la llamada al disco y valida que el controlador intente guardar el archivo, sin necesidad de verificar su existencia física real.

5. 🛡️ Seguridad y UX (Middleware & Services)
Middleware Personalizado: Creé IsAdmin para proteger rutas críticas. Aprendí a registrar su alias en bootstrap/app.php (Laravel 11).

Refactorización de Servicio: Mejoré el SlugService. En lugar de lanzar una Excepción (Error 500) cuando un nombre está duplicado, implementé un while que agrega un contador incremental (slug-1, slug-2). Esto mejora la experiencia de usuario (UX) automáticamente.

Transacciones: Uso de DB::transaction al crear productos para asegurar que o se guarda todo (BD + Archivos) o no se guarda nada.

## 📅 [23-01-2026] - Archivos, Seguridad y Debugging

### 1. 📂 Subida y Descarga Segura de Archivos
- **Arquitectura de Controladores:** Aprendí a separar responsabilidades.
  - `ProductController` (API): Gestiona la lógica de negocio y genera permisos (JSON).
  - `SignedStorageController` (Web/Invokable): Se encarga exclusivamente de servir el archivo binario (`Storage::download`).
- **Signed URLs:** Implementé `URL::temporarySignedRoute`.
  - Permite crear enlaces con fecha de caducidad y firma criptográfica.
  - No requiere autenticación de usuario en la ruta final, ya que la seguridad va incrustada en la firma del link.
- **Rutas con Regex:** Aprendí a usar `->where('path', '.*')` en rutas web para permitir que los parámetros incluyan barras inclinadas (`/`) sin romper el ruteo de Laravel.

### 2. 🐛 Debugging y Herramientas (Postman & Tinker)
- **Error de Puertos:** Entendí la diferencia entre el puerto de la App (80/8000) y el de la BD (5432). Enviar peticiones HTTP al puerto de Postgres causa `socket hang up`.
- **Form-Data:** Para subir archivos en Postman, el Body debe ser `form-data`, las keys deben ser tipo `File` y **no** se deben usar comillas en los strings.
- **Tinker Trait:** Si `User::createToken` falla, es porque falta el trait `HasApiTokens` en el modelo. Tinker requiere reiniciarse (`exit`) para detectar cambios en el código.

### 3. 🧪 Testing de Integración
- **Simulación de Compra:** Para probar la descarga, el test debe crear primero una `Order` en base de datos.
- **Validación de JSON:** Usar `assertJsonStructure(['url'])` para verificar respuestas dinámicas sin conocer el valor exacto del hash.