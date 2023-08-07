# 💻🎯 Poke Manager

## 📝 [Enunciado inicial](./doc/enunciado_inicial.md)

## 📝 Tecnologías utilizadas

- **PHP 8.0**
- **Symfony 5.4**
  - He tenido que utilizar la versión 5.4 de Symfony en lugar de la más reciente (6.3) porque ésta requiere de PHP 8.1.
- **MySQL**
- **Api de Pokemon** (https://pokeapi.co/)

## Instalación
- Composer install.
- Virtual host ó arrancar el proyecto con el servidor de Symfony.
- Cambiar credenciales de la BBDD.

## Consulta de los datos
- **Obtención de todos los Pokemon:** Para consultar todos los Pokemon que hay en la base de datos, he hecho una paginación para obtener los elementos de 20 en 20. El número de página que se desee consultar se pasará mediante un parámetro en la url, siguiendo este ejemplo: http://gestionet-test.localhost/api/pokemons?page=2