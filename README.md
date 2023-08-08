# 👾 Poke Manager 👾

## 📝 [Enunciado inicial](./doc/enunciado_inicial.md)

## 📝 Tecnologías utilizadas

- **PHP 8.0**
- **Symfony 5.4**
  - He tenido que utilizar la versión 5.4 de Symfony en lugar de una más reciente porque ésta requiere de PHP 8.1.
- **MySQL**
- **Api de Pokemon** (https://pokeapi.co/)

## 🔧 Instalación
1. Instalar las dependencias mediante el siguiente comando:
    ``` 
   composer install
    ```
2. Será necesario servir la página mediante un servidor web. Existen dos alternativas: 
   - Utilizar el servidor integrado en Symfony. Para ello será necesario ejecutar el siguiente comando:
     ```
     symfony server:start
     ```
   - Crear un virtual host en el servidor web local. En mi caso he utilizado el servidor local apache y adjunto un 
     ejemplo de configuración:
     ```
     <VirtualHost *:80>
        ServerName gestionet-test.localhost
        ServerAdmin webmaster@localhost
        DocumentRoot {project_path}/public
        DirectoryIndex /index.php

        <Directory {project_path}/public>
                Require all granted
                AllowOverride All
                Allow from All
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/gestionet-test_error.log
        CustomLog ${APACHE_LOG_DIR}/gestionet-test_access.log combined
     </VirtualHost>
     ```
3. Crear una base de datos específica para este proyecto.
4. Crear un fichero `.env.local` en el proyecto Symfony y cambiar las credenciales de la BBDD de forma similar a como se 
   muestra a continuación:
   ```
   DATABASE_URL="mysql://{nombre_usuario}:{contraseña}@127.0.0.1:3306/{nombre_bbdd}"
   ```
5. Ejecutar migraciones mediante:
   ```
   php bin/console doctrine:migrations:migrate
   ```
6. Ejecutar fixtures para la carga inicial de usuarios:
   ```
   php bin/console doctrine:fixtures:load
   ```

## 📈 Carga de datos
- Se ha creado **un mismo endpoint para cargar todos los pokemon** de primera generación, sus **tipos** y sus 
  **evoluciones**.
  A continuación un ejemplo:
    ```
    {dominio_local}/api/admin/load-pokemons
    ```
- He utilizado el **SecurityBundle** de Symfony para filtrar el acceso a los endpoints mediante roles de usuario.
  Concretamente, una configuración **HTTP Basic** mediante esa librería.
- Los **datos de acceso del usuario Admin** son:
  - Usuario: `admin@gmail.com`
  - Contraseña: `admin`


## 📉 Consulta de datos
- **Obtención de todos los Pokemon:** Para consultar todos los Pokemon que hay en la base de datos, he hecho una 
  paginación para obtener los elementos de 20 en 20. El número de página que se desee consultar se pasará mediante un 
  **parámetro en la URL**, siguiendo este ejemplo: 
  ```
  {dominio_local}/api/pokemons?page=2
  ```


- **Obtención de las evoluciones de un pokemon concreto por nombre**:  
  ```
  {dominio_local}/api/pokemon/charmander/evolutions
  ```


- **Obtención de los 3 pokemon con más evoluciones** ordenadas por su índice (id) y con el número de evoluciones que 
  tiene:
  ```
  {dominio_local}/api/pokemon/max-evolutions
  ```