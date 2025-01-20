
# ➡️ Examen Sec. de Modernización - Backend Laravel - Pamich Gabriel


## ℹ️ Descripción de la app
Rest API para el sistema gestor de tareas colaborativas.


## 📄Documentación de la API
[Documentación](https://documenter.getpostman.com/view/15080099/2sAYQcFATG)


## 🚀 Instalación y requisitos
Es necesario contar con un server MySQL funcional, PHP 8.2 o superior. Tambien se puede cambiar el SGBD desde el .env a un sqlite, mariadb, etc..
Pasos para la instalación y ejecución en Windows:
1) Clonar repositorio y  ejecutar `composer install`
2) Configurar `.env` a partir del `.env.example`
3) Ejecutar `php artisan migrate` para crear las migraciones en la db y `php artisan db:seed` para cargar los datos de prueba.
4) Ejecutar `php artisan jwt:secret` para setear una secret key para los tokens.
5) Chequear con los tests que este todo funcionando correctamente, ejecutando `php artisan test`
6) Si los tests pasan correctamente,  ejecutar `php artisan serve` y la API deberia estar funcionando en localhost:8000

## Aclaraciones y datos relevantes

- Users: La API permite registrarse como user, asi como también gestionar los mismos siendo administrador a traves de los endpoints de /users. Se pueden realizar ABMs de usuarios, asi como gestionar sus roles.

### Aclaraciones sobre el RF4
![image](https://github.com/user-attachments/assets/294690f1-046b-46eb-9551-54befb32822d)
El endpoint de completar tarea `/tasks/{id}/complete`  **exclusivamente completa la parte de un usuario asociado a esa tarea**, es decir setea en true la variable is_finished en la tabla intermedia entre usuarios y tareas. Una tarea solo puede pasar a estado COMPLETADA a traves de la edición de la misma en el endpoint `/tasks/id` usando el metodo PUT (Enviando objeto completo) o PATCH (enviando solo esa parte a actualizar).

## 🔐Credenciales de prueba


| Usuario | Pass | Rol | * | 
|--|--|--|--|
| admin@example.com | password |Admin| **Se setea en .env, si copias el .env.example revisar el apartado ADMIN_EMAIL Y ADMIN_PASSWORD**
| example@example.com | password | User |
