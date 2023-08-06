# Prueba de selección 💻🎯

Queremos hacer una Api que nos ayude a gestionar pokemon(solo primera generación) y para facilitarnos la vida vamos a desarrollar una serie de herramientas que nos ayuden en este trabajo.

Es importante indicar que **NO ES NECESARIO DESARROLLAR TODAS LAS FUNCIONALIDADES** que se muestran a continuación, ya que lo realmente importante en esta prueba es que podamos ver la forma de trabajar y resolver problemas que tienes.

## 📝 Tecnologías necesarias
- Symfony.
- Php(7.4 o 8.0).
- Mysql o MariaDb.
- Api de pokemon(https://pokeapi.co/)

## 📝 LISTA DE TAREAS

Vamos a separar las tareas en PRINCIPALES y EXTRAS, siendo las PRINCIPALES las que consideramos a priori más importantes y las EXTRAS funcionalidades más secundarias.

La prueba se basa en crear un usuario que pueda obtener información de todos los pokemon para luego hacer consultas sobre los mismos.

**Lee todas las tareas antes de empezar para tener una visión global de lo que se pide**

### Tareas PRINCIPALES:
**Instalación y configuración**

1. **Crear una instalación nueva de Symfony:** Se ha desarrollar toda la aplicación en Symfony y PHP.

2. **Crear una base de datos:** Con los datos que se obtienen en los siguientes apartados generar un base de datos donde se guardará toda la información necesaria.

**Usuarios** Queremos tener un usuario admin que es el único que puede acceder a las llamadas del apartado "Gestión de los pokemon".

1. **Crear un usuario con rol Admin** Será el usuario encargado de poder dar de alta nuevos usuarios y gestionar todo lo relativo a los pokemon.

**Gestión de los pokemon** Estas llamadas sirven para alimentar la base de datos que luego explotaremos con el API. Estas llamadas solo pueden ser usadas por un administrador.

1. **Gestionar Pokemon:**

    -    Cargar pokemon: Crear un endpoint que llame a la pokeapi para obtener la lista de los pokemon de primera generación y guardarlo en nuestra base de datos.
         ```json
             // ENDPOINT => https://pokeapi.co/api/v2/generation/1
                  "pokemon_species": [
                            {
                              "name": "bulbasaur",
                              "url": "https://pokeapi.co/api/v2/pokemon-species/1/"
                            },
                            {
                              "name": "charmander",
                              "url": "https://pokeapi.co/api/v2/pokemon-species/4/"
                            },
                            {
                            ..
         ``
    - Cargar tipos de pokemon: Es necesario crear una lista de tipos de pokemon y guardarlo en nuestra base de datos.
      ```json
             // ENDPOINT => https://pokeapi.co/api/v2/type
                  {
                    "count": 20,
                    "next": null,
                    "previous": null,
                    "results": [
               {
                    "name": "normal",
                    "url": "https://pokeapi.co/api/v2/type/1/"
               },
               ...
      ```
    - Unir tipo de pokemon y pokemon: Con el id o el nombre del pokemon obtener sus datos donde podremos obtener el tipo(*Nota algunos pokemon tienen 1 o más tipos)
      ```json
         // ENDPOINT => https://pokeapi.co/api/v2/pokemon/{id or name}
             {
             ...
                "types": [
                     {
                       "slot": 1,
                       "type": {
                         "name": "grass",
                         "url": "https://pokeapi.co/api/v2/type/12/"
                       }
                     },
                     {
                       "slot": 2,
                       "type": {
                         "name": "poison",
                         "url": "https://pokeapi.co/api/v2/type/4/"
                       }
                     }
                   ],
             ...
      ```
     - Guardar las evoluciones de los pokemon: Llamando al api podemos obtener la evolución de cada pokemon para guardarlo en nuestra base de datos,de esta manera podemos vincular un pokemon y sus evoluciones. Esta llamada nos devuelve la cadena de evoluciones, por tanto, de 1 pokemon se pueden sacar todas sus evoluciones en una llamada.(*NOTA: los demás datos del pokemon no nos interesan)
         ```json
                    // ENDPOINT => https://pokeapi.co/api/v2/evolution-chain/{id}
                        {
                        ...
                                    "evolves_to": [],
                                    "is_baby": false,
                                    "species": {
                                      "name": "venusaur",
                                      "url": "https://pokeapi.co/api/v2/pokemon-species/3/"
                                    }
                                  }
                                ],
                                "is_baby": false,
                                "species": {
                                  "name": "ivysaur",
                                  "url": "https://pokeapi.co/api/v2/pokemon-species/2/"
                                }
                              }
                            ],
                            "is_baby": false,
                            "species": {
                              "name": "bulbasaur",
                              "url": "https://pokeapi.co/api/v2/pokemon-species/1/"
                            }
                          },
                          "id": 1
                        }
                        ...
         ```

**Explotación de la data** En este apartado tendremos 3 endpoints que nos permiten explotar la data, estas llamadas son públicas.

1. **Creación de un API para la consulta de datos:** Se crearán varios endpoints para poder obtener los datos que hay en la plataforma a través de un API pública.
    - Obtención de todos los pokemon: Crear un endpoint que permita traer todos los pokemon de la plataforma con sus tipos y sus evoluciones paginadas a X elementos por página.
    - Obtener las evoluciones de un pokemon en concreto por nombre o id.
    - Obtener los 3 pokemon con más evoluciones ordenadas por su índice y con el número de evoluciones que tiene.(pokedex).

### Tareas Extra:

Estas tareas no son necesarias y se pueden abordar por partes, si necesidad de llevar ningún orden.

1. **Crear un usuario con rol User** Solo podrá consultar los pokemons y su tipo que tenga asignado por el administrador.

2. **Asignación de tipos a usuarios para su filtrado:** El administrador podrá asignar que tipo de pokemon puede ver cada usuario. Por ejemplo: El usuario "AAAA" tiene asignados los tipos fuego y planta. Cuando el usuario "AAAA" haga una consulta a la base de datos, solo podrá obtener los pokemon que sean del tipo fuego y planta. El administrador puede ver todos.

3. **Asignación de tipos a usuarios para su filtrado:** Creación de nuevos endpoints de cierta complejidad y utilidad que nos aporten datos sobre los pokemon,evoluciones etc.
