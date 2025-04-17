Jose Pablo Duran Herrera 
Práctica Programada 4
Instrucciones:

1. Configurar base de datos:

   - En `http://localhost/phpmyadmin`, importe `C:\xampp\htdocs\practica-programada-4\database.sql`.
   - Se crea `app_tareas` con tablas `users`, `tasks`, `comments` y datos de prueba.

2. Verificar servidor que diga: Ruta no encontrada.

Postman

Cree un entorno "Localhost" con el url = http://localhost/practica-programada-4.
Seleccione el entorno.

- Probar los endpoints:

Login (POST {{base_url}}/api/login)**:

     ```json
     {"username":"testuser","password":"testpass"}
     ```

     Respuesta: `{"message":"Sesión iniciada"}` (200).

   - Crear comentario (POST {{base_url}}/api/comments):

     ```json
     {"task_id":1,"content":"Comentario de prueba"}
     ```

     Respuesta: `{id:1,...}` (201).

   - Listar comentarios (GET {{base_url}}/api/comments?task_id=1)**: Respuesta: `[{id:1,...}]` (200).

   - Actualizar comentario (PUT {{base_url}}/api/comments/1):

     ```json
     {"content":"Comentario actualizado"}
     ```

     Respuesta: `{"message":"Comentario actualizado"}` (200).

   - Borrar comentario (DELETE {{base_url}}/api/comments/1)**: Respuesta: `{"message":"Comentario eliminado"}` (200).

   - Sin sesión:

     - En ventana incógnito, envíe `GET {{base_url}}/api/comments?task_id=1`.
     - Respuesta: `{"error":"No hay sesión activa. Inicie sesión."}` (401).

3. Validaciones:

   - Campos vacíos: `POST` con `{}` → `{"error":"Faltan campos requeridos"}` (400).
   - Comentario corto: `{"task_id":1,"content":"abc"}` → `{"error":"El comentario debe tener al menos 5 caracteres"}` (400).
   - Tarea inválida: `{"task_id":999,"content":"Test"}` → `{"error":"La tarea no existe"}` (400).