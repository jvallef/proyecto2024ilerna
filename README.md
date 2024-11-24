# Proyecto Ilerna 2024: EduPlazza
Desarrollado por José Valle

## Agenda trabajo

¿Eliminar al final?

- ~~Instalar Laravel con Breeze y Bootstrap 5~~
- ~~Crear un sistema de permisos rudimentario~~
- ~~Definir las migraciones~~
- ~~Crear seeder de usuarios de prueba~~
- ~~Comprobar que funciona con Sqlite~~
- Comprobar que funciona Postgresql
- ~~Concretar los modelos~~
- ~~Definir la estructura del contenido básico en JSON~~
- ~~Crear Seeders de todos los elementos~~
- ~~Crear tests de los modelos~~
- ~~Migrado a spatie/laravel-permission~~
- ~~Ver como integrar BS5 desde CDN personalizándolo y creando estilos propios~~
- Crear el layout principal de administración
- Migrar a Tailwind (me dio muchos problemas con Bootstrap)
- ~~Crear controladores de prueba~~
- Crear un par de vistas de lista, formulario y detalle para ver cómo se comporta todo
- Crear controladores y vistas a la par, ir probando
- Ir creando los tests de todo
- Crear el layout y diseño públicos
- Crear un par de pantallas de la parte pública, como PoC
- Crear un controlador y formulario para gestionar el JSON del Content

## El proyecto

El proyecto consistirá en crear el MVP de una plataforma online llamada EduPlazza.

La diferencia fundamental de EduPlazza no es técnica, aunque espero incorporar algunos elementos novedosos o no demasiado habituales. Su diferencia clave es el propósito y el enfoque.

El propósito básico de EduPlazza es servir como un repositorio eterno de la información. A lo largo de los años en mi experiencia educativa, me he ido cruzando con referencias a una infinidad de recursos que ya no están accesibles. Es una pena que todo esto se pierda, porque estoy seguro de que muchos de ellos habrán supuesto un esfuerzo notable a sus creadores, que por cualquier motivo en algún momento decidieron que ya no les merecía el esfuerzo mantener esos contenidos. Esta plataforma les permitirá que los recursos se queden aquí para siempre sin desaparecer.

### Planteamiento

EduPlazza es una plataforma donde la información está organizada por distritos:

1º. Barrio de la Cultura (Humanidades y Artes)
 
Este distrito explora la expresión artística y el estudio de la condición humana.

- Plaza del Arte
- Plaza de las Lenguas
- Plaza de la Filosofía
- Plaza de la Cultura
- Plaza de la Historia.

2º. Barrio de la Ciencia (Ciencias y Tecnología)

Este distrito se centra en la ciencia y la tecnología. La investigación, la innovación y el desarrollo técnico son inquietudes fundamentales de sus vecinos.

- Plaza de la Ciencia
- Plaza de la Tecnología
- Plaza de las Matemáticas
- Plaza de la Física 

3º. Barrio de la Sociedad (Sociedad y Negocios)

Este distrito aborda la interacción humana, los negocios y la comunicación, explorando cómo las dinámicas sociales influyen en estos campos. 

- Plaza de las Ciencias Sociales
- Plaza de la Economía
- Plaza de los Negocios
- Plaza del Marketing
- Plaza de la Comunicación

4º. Salud y Bienestar (Vida saludable y Felicidad)

Este distrito abarca temas sobre la salud física y mental, el desarrollo personal y la búsqueda de la felicidad.

-Plaza de la Felicidad
-Plaza de la Salud
-Plaza del Bienestar Personal

### Elementos del sistema
- En la versión definitiva de EduPlazza, los cursos podrían desarrollarse en un entorno virtual compuesto por lugares, centros, edificios... En una primera versión solo se trata de la denominación: barrio, distrito, plaza
- Crear estructuras y módulos que puedan formar parte de un sistema automatizado es uno de los objetivos del proyecto. La IA tendrá un papel preponderante en los próximos años, cualquier sistema debe estar preparado para integrarse con ella.
- En esta plataforma los alumnos podrán seguir diferentes rutas formativas, que incluyen una serie de cursos o bien optar por hacer aquellos cursos que les interesen.
- Los cursos estarán hechos por profesores y cada uno podrá tener múltiples cursos.
- Los administradores tendrán acceso a todos los contenidos del sistema.

### Modelos principales
En esta versión del proyecto estos son los modelos principales, que permitirán implementar la operativa básica.
- users
- areas
- paths
- courses
- contents
- messages

## El sistema de Laravel
- He elegido Laravel 11 y trabajaré con Sqlite mientras desarrollo, posteriormente podría hacerlo con PostgreSql, aunque no espero integrar ninguna funcionalidad que requiera usar este último motor de base de datos para el MVP.
- Usaré Breeze para la autenticación y gestión del registro de usuarios.
- Cómo framework CSS elijo Bootstrap 5.

## Requisitos técnicos
Con lo planteado hasta el momento EduPlazza los requisitos básicos son los de cualquier aplicación desarrollada en PHP, y específicamente con Laravel 11 (PHP >= 8.2).
Laravel integra el uso de Sqlite.

### Instalación

##### Instalación local

- git clone [Url Proyecto]
- cd [Nombre Proyecto]
- composer install
- cp .env.example .env (configurar .env si es necesario)
- php artisan key:generate
- npm install
- npm run build (o npm run dev)
- php artisan migrate --seed
- php artisan serve (o desde el servidor que se utilice, usando Herd ir a http://proyecto.test)

#### Para conectar un proyecto local nuevo en un repositorio existente
1. Creo un repositorio en GitHub, por ejemplo proyecto2024ilerna
Siguiendo los pasos de la sección [Instalación local y repositorio en GitHub](#instalación-local-y-repositorio-en-github)

2. Crear un proyecto Laravel
composer create-project laravel/laravel proyecto2024ilerna
cd proyecto2024ilerna

3. Inicializar el repositorio Git y realizar el primer commit
git init
git add .
git commit -m "Initial commit: Fresh Laravel installation"

4. Añadir el repositorio remoto
git remote add origin https://github.com/jvallef/proyecto2024ilerna.git

5. Renombrar la rama de desarrollo y subir
git branch -M main
git push -u origin main

Con estos pasos un repositorio existente en GitHub se sincronizará con el proyecto de Laravel local.

#### Instalación local y repositorio en GitHub
- En el perfil de GitHub y, en la esquina superior derecha, crear New Repository.
- Darle un nombre al nuevo repositorio, como Ilerna2024, seleccionar público o privado.
- No seleccionar ninguna opción de inicialización del repositorio (como README, .gitignore, etc.), se hará un push del repositorio existente.
- Hacer clic en Create Repository.
- Copiar la url del nuevo repositorio, por ejemplo: https://github.com/jvallef/ilerna2024b.git 
- Clonar el repositorio original, por ejemplo: ```git clone https://github.com/jvallef/ilerna2024.git ilerna-2024-b```
- Eliminar el enlace al repositorio remoto actual: ```git remote remove origin```
- Agregar el proyecto al nuevo repositorio como origin: ```git remote add origin https://github.com/jvallef/ilerna2024b.git```
- Confirmar que se está en la rama principal ```git checkout main```
- Si hay cambios actualizar de la forma habitual:
```bash
git add .
git commit -m "Copia inicial del proyecto original para nueva versión"
git push -u origin main
```
- composer install
- cp .env.example .env (configurar .env si es necesario)
- php artisan key:generate
- npm install
- npm run build (o npm run dev)
- php artisan migrate --seed
- php artisan serve (o desde el servidor que se utilice, usando Herd ir a http://proyecto.test)

#### Instalación y configuración de Spatie Laravel-Permission

Esto ya está instalado en la versión actual, lo incluyo como recordatorio de los pasos para quien pueda ser útil y para mí mismo.

- Instalación del paquete
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```
- Modificar el modelo de usuario
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    ...
}
```
- Crear el seeder ```php artisan make:seeder RolesAndPermissionsSeeder``` Ver el archivo en el proyecto para ver cómo se construye.
- Ejemplo de uso en un controlador
```php
    $user = User::find(1); // Buscar un usuario
    $user->assignRole('teacher'); // Asignarle el rol teacher
    $user->givePermissionTo('manage paths'); // Para darle un permiso no comtemplado en el rol
```

### Lista de usuarios disponibles
El seeder creará una serie de usuarios para realizar las pruebas básicas.

|Nombre        |Rol                 |Email                    |Password  |
|--------------|--------------------|-------------------------|----------|
|Admin         |admin, teacher, student|admin@example.com       |password  |
|Profe 1       |teacher             |profe1@example.com        |password  |
|Profe 2       |teacher, student    |profe2@example.com        |password  |
|Estudiante 1  |student             |estudiante1@example.com   |password  |
|Estudiante 2  |student             |estudiante2@example.com   |password  |


## Estructura de datos

Este es el detalle de la estructura de datos que respalda a EduPlazza, así como del propósito de cada elemento del sistema. Esta es solamente una descripción abreviada de cada tabla, la versión definitiva son las propias migraciones del sistema.

### Areas 

Áreas de conocimiento. Son las zonas o lugares donde transcurre la experiencia de los usuarios dentro de EduPlazza. Por ejemplo uno de los cuatro distritos principales se denomina Barrio de la Sociedad y en el encontraremos la Plaza de la Economía. Un Area puede tener hijos, formando una estructura jerarquica, con tantos niveles como se considere necesario. El Area es creada por un User, sería el "propietario", que mantendría los privilegios principales sobre ésta, como la capacidad de añadir o modificar sus datos, por ejemplo incorporar nuevas Areas hijas.

- id (INT, primary key, auto-increment)
- name (VARCHAR)
- slug (VARCHAR, unique) // Slug único para URL
- description (TEXT, nullable)
- user_id (INT, FK users)
- parent_id (INT, FK areas, nullable)
- featured (BOOLEAN, default false)
- status (ENUM('draft', 'published', 'suspended'), default 'draft')

### Paths 

Las Rutas de aprendizaje. Son recorridos formativos que se pueden encontrar en cualquier área y que son los contenedores de los Courses. Un Path puede tener hijos, así en el Path de Ciencias Sociales, podría encontrarse el Path de Antropología, que a su vez podría tener el Antropología física. Aunque en mi planteamiento sería este último el contenedor de los Courses, en una versión definitiva podrían encontrarse en cualquiera de las diferentes Areas o Paths, la estructura será flexible y podrá crecer de acuerdo con las necesidades de los usuarios y de los contenidos que se vayan incorporando. Como en las Areas, aquí también encontraremos un User que tiene los máximos privilegios sobre este elemento.

Existe una tabla pivote Course_Path, donde se establece la relación entre estos dos elementos. El campo Sort de esta tabla indicará el orden que ocupará cada curso en un Path. Un Course puede formar parte de diferentes Paths.

- id (INT, primary key, auto-increment)
- name (VARCHAR)
- slug (VARCHAR, unique) // Slug único para URL
- description (TEXT, nullable)
- user_id (INT, FK users)
- parent_id (INT, FK paths, nullable)
- area_id (INT, FK areas)
- featured (BOOLEAN, default false)
- status (ENUM('draft', 'published', 'suspended'), default 'draft')

### Courses

Los cursos. Son el contenido formativo principal, un curso es una unidad formativa completa en sí mismo, que está formada por Contents. Así en el Path de Antropología Física, mencionado como ejemplo en el punto anterior, podrían encontrarse Courses como Paleoantropología o Trayectorias evolutivas. En el caso de los Courses el User responsable es un Author, como tal se le identifica. En versiones sucesivas se dará la posibilidad de que el Author principal, pueda incorporar a otros que participen en la creación o el mantenimiento del Course y sus Contents.

He incorporado un campo Age_group, que define el rango de edad al que se dirige el Course, puede ser Null. Ahora es meramente orientativo, solo informa, sirve para simular el comportamiento, pero en una versión posterior podría adaptarse, se convertiría en una tabla pivote, permitiendo que un curso sea accesible para diferentes rangos de edad, de todas formas si es null es accesible para todos. No veo demasiado sentido en limitar el acceso por edad a contenidos concretos, EduPlazza debería ser un lugar de aprendizaje abierto y desde mi punto de vista a lo que accedan los menores debería ser responsabilidad de sus tutores o profesores, pero esto es algo que habrá que debatir en el futuro.

- id (INT, primary key, auto-increment)
- title (VARCHAR)
- slug (VARCHAR, unique) // Slug único para URL
- description (TEXT, nullable)
- age_group (ENUM('0-6', '7-12', '13-20', '21+'), nullable)
- author_id (INT, FK users)
- featured (BOOLEAN, default false)
- status (ENUM('draft', 'published', 'suspended'), default 'draft')

### Contents 

Contenidos del curso. Si el Course es el contenido formativo principal, el Content es el elemento formativo básico. Dentro de una estructura definida, será un elemento muy flexible, aceptará algunos tipos predefinido, como content, book, theme, lesson, quiz... Su campo Content es un JSON, para que pueda contener cualquier tipo de información, que estará formada por una serie de datos predefinidos, pero podrá también tener una parte donde se definan nuevos tipos de datos en su estructura. En cualquier caso, la razón principal de almacenar la información en una estructura modificable, es que se pueda alimentar automáticamente y que pueda crearse cualquier tipo de contenido a partir de la información que contenga. Como en otros casos el campo parent_id, permitirá crear contenidos dependientes, y tendrá un author_id.

En teoría un Content podría almacenar cualquier tipo de información, no se limita a los contenidos educativos: una página de la web de la plataforma, un post de un blog, un producto comercializable...

- id (INT, primary key, auto-increment)
- type (ENUM('content', 'book', 'theme', 'lesson', 'quiz', ...), default 'content')
- title (VARCHAR, nullable)
- content (JSON, nullable)
- author_id (INT, FK users)
- parent_id (INT, FK contents, nullable)

#### Estructura JSON de Content

Este es un ejemplo de un Content, bastante detallado, para un primer MVP. En el se muestra la estructura de lo que podría ser una lección, pero recordemos que los Contents son elementos que pueden tener una relación jerárquica entre ellos y que además forman parte de los cursos.

La estructura mínima de un Content para que funcione podría ser esta:
```json
{
    "content": "Este podría ser el contenido completo de un content que no use las Sections en Base (no es obligatorio)"
}
```

Un Content detallado tendrá un formato notablemente más complejo y que probablemente irá creciendo con el paso del tiempo. Nótese que aunque la mayoría de los campos no son obligatorios, bastantes de ellos son recomendables, y alguno depende de otros. Por ejemplo, el id debe ser un identificador único y el mejor id que se me ocurre, siendo descriptivo sería un slug del título. Pero esto puede hacer ids muy largo y difíciles de usar, si el usuario tiene que referenciarlos por sí mismo. Por otra parte hacer slugs del tipo UUID sería poco usable igualmente. Probablemente un id formado por la estructura del contenido podría tener sentido, ser más legible y también entendible. 

Por ejemplo una sección podría tener un id así: PL-aR2i.L1.S2. Que se estructura así:
- Id del curso: PT-aR2i. Uno o más caracteres a la izquierda del guión con las iniciales del curso, por ejemplo en este caso, Python para todos. A continuación del guión, encontraríamos un id generado por el sistema que empezaría con un número de caracteres determinado que podría crecer con el tiempo.
- Id de la lección: L1
- Id del contenido: S2

Por lo tanto en este curso la referencia a una sección desde fuera se haría con el id completo "PL-aR2i.L1.S2" y desde cualquier otro punto del curso con la lección y la sección "L1.S2".

Así un Content más detallado podría tener este formato.
```json
{
    "id": "identificador único",
    "type": "lesson (¿obligatorio si se quiere que el sistema pueda tratarlo?)",
    "title": "Título de la lección (obligatorio)",
    "description": "Breve descripción de la lección (obligatorio)",
    "content": "Este podría ser el contenido completo de un content que no use las Sections en Base (no es obligatorio)",
    "base": {
        "sections": [
            {
                "id": "unique_identier  (no es obligatorio, puede usarse luego en la lección o en el curso)",
                "title": "Sección 1 (obligatorio)",
                "description": "Una introducción o descripción breve de la sección (conveniente)",
                "content": "Contenido de la sección 1 en formato markdown (no es obligatorio)",
                "type": "text",
                "medias": [
                    {
                        "id": "identificador único (este puede ser usado en el content de la sección)",
                        "title": "Imagen 1 (obligatorio si se quiere usar el id, porque genera un slug)",
                        "description": "Una descripción suficiente",
                        "type": "image",
                        "content": "\"Imagen de un patito\" (datos parametrizados de la media)"
                    },
                    {
                        "id": "identificador único",
                        "title": "Imagen 2 (obligatorio si se quiere usar el id, porque genera un slug)",
                        "type": "image",
                        "content": "\"Una tabla de los tipos de patitos\""
                    }
                ]
            },
            {
                "title": "Sección 2",
                "description": "Una introducción o descripción breve de la sección (conveniente)",
                "content": "Contenido de la sección 2 en formato markdown",
                "type": "video",
                "media": "id de la media",
                "url": "url externa, no recomendado, puede ser la anterior o este"
            }
        ],
        "quiz": {
            "questions": [
                {
                    "question": "¿Pregunta 1?",
                    "description": "Más información, no es imprescindible, pero puede ser conveniente",
                    "type": "single-answer",
                    "media": "id_de_la_media (no es obligatorio)",
                    "options": {
                        "correct": [
                            {
                                "text": "Opción A",
                                "explanation": "Respuesta correcta porque... no obligatorio pero conveniente",
                                "media": "id_de_la_media (no es obligatorio)"
                            }
                        ],
                        "incorrect": [
                            {
                                "text": "Opción B",
                                "explanation": "Esta opción es incorrecta porque..."
                            },
                            {
                                "text": "Opción C",
                                "explanation": "Esta opción es incorrecta porque...",
                                "media": "id_de_la_media (no es obligatorio)"
                            }
                        ]
                    },
                    "related_sections": [
                        "id_seccion"
                    ]
                },
                {
                    "question": "¿Pregunta 2?",
                    "type": "multiple-answer",
                    "options": {
                        "correct": [
                            {
                                "text": "Opción A",
                                "explanation": "Esta es una respuesta correcta porque..."
                            },
                            {
                                "text": "Opción B",
                                "explanation": "Esta también es una respuesta correcta porque..."
                            }
                        ],
                        "incorrect": [
                            {
                                "text": "Opción C",
                                "explanation": "Esta opción es incorrecta porque..."
                            },
                            {
                                "text": "Opción D",
                                "explanation": "Esta opción es incorrecta porque..."
                            }
                        ]
                    },
                    "related_sections": [
                        "id_seccion (no obligatorio)", "id_otra_seccion"
                    ]
                }
            ]
        },
        "resources": [
            {
                "title": "Recurso adicional (no obligatorio)",
                "description": "Más información, no es imprescindible, pero puede ser conveniente",
                "type": "pdf",
                "media": "id de la media",
                "url": "url externa, no recomendado, puede ser la anterior o este"
            }
        ]
    },
    "extra": {
        "data": [
            {
                "label": "Libros recomendados",
                "type": "list",
                "data": [
                    {
                        "type": "text",
                        "content": "\"Mi título de libro\" autor: Nombre autor, ISBN: 978-0714898704"
                    },
                    {
                        "label": "Libro recomendado",
                        "type": "text",
                        "content": "\"Otro título\" autor: Otro autor, ISBN: 99"
                    }
                ]
            }
        ],
        "other-extra-field": {
            "label": "Campo personalizado 1",
            "type": "date",
            "content": "Contenido del campo personalizado"
        }
    }
}
```


### Messages

Mensajes. Los mensajes permiten que los usuarios del sistema interactúen. Un mensaje puede formar parte de cualquiera de los elementos del sistema: areas, paths, courses, contents... y también puede enviarse a un usuario concreto, sería privado.

- id (INT, primary key, auto-increment)
- title (VARCHAR, nullable)
- body (TEXT)
- user_id (INT, FK users)
- private (BOOLEAN, default false)
- user_to_id (INT, FK users, nullable)
- area_id (INT, FK areas, nullable)
- path_id (INT, FK paths, nullable)
- course_id (INT, FK courses, nullable)
- content_id (INT, FK contents, nullable)

### Medias

Archivos relacionados. Contiene las imágenes que se utilicen en el sistema, así como cualquier otro tipo de archivo que precise asociarse a cualquier elemento: imágenes, pdfs, videos, etc. Defino este contenido como polimórfico, al modo que facilita Laravel, permitiendo que pueda ser asociado a cualquier modelo de la plataforma. Como en las anteriores tablas se almacena el usuario que ha subido el archivo.

He definido campos url y path, en previsión de que algún archivo no pudiera formar parte del sistema, aunque obviamente no es una buena opción. Si pretendemos crear un repositorio eterno, no puede depender de nada que no esté siempre accesible.

Un campo extra de tipo JSON debería permitir añadir información relativa al contenido que se deba mantener en un formato estructurado. Por ejemplo en las imágenes podría ser interesante contar con información que fuese relevante a propósito del SEO; pero en otros archivos podría haber información o procedimientos para automatizar cualquier tarea relacionada con el archivo.

- id (INT, primary key, auto-increment)
- user_id (INT, FK users)
- mediable_id (INT) // ID del modelo polimórfico
- mediable_type (VARCHAR) // Tipo del modelo polimórfico
- type (ENUM('picture', 'file', 'video', 'audio', ...), default 'picture')
- url (VARCHAR, nullable)
- path (VARCHAR, nullable)
- extra (JSON, nullable)

### Course_path

Tabla pivote de cursos-rutas. Relaciona Courses y Paths. El único campo relevante es sort que indica el orden del Course en el Path.

- id (INT, primary key, auto-increment)
- course_id (INT, FK courses)
- path_id (INT, FK paths)
- sort (INT, nullable) // Orden del curso en la ruta

### Content_course

Tabla pivote contenidos-cursos. Vincula los Contents con los Courses. Como en el caso anterior incluye un campo de orden.

- id (INT, primary key, auto-increment)
- content_id (INT, FK contents)
- course_id (INT, FK courses)
- sort (INT, nullable) // Orden del contenido en el curso

### Course_enrollments

Tabla pivote de Courses con Users. Relaciona a los estudiantes o profesores con los cursos de los que forman parte. En el caso de los estudiantes indica el progreso en el curso y si lo han completado. En el caso de los profesores serviría para incluir a otros profesores en un curso, además del propietario del mismo, aunque esta funcionalidad no estará operativa en el MVP.

- id (INT, primary key, auto-increment)
- user_id (INT, FK users)
- course_id (INT, FK coures)
- role (ENUM('student', 'teacher'), default 'student')
- progress (FLOAT, default 0)
- completed (BOOLEAN, default false)

### Path_enrollments

Tabla pivote de Paths con Users. Es igual a la tabla anterior en el planteamiento. Este planteamiento, al crear índices compuestos únicos ( $table->unique(['user_id', 'path_id', 'role']) ), permitiría que un profesor fuese al mismo tiempo alumno y estudiante de un Path, que no me parece descartable.

- id (INT, primary key, auto-increment)
- user_id (INT, FK users)
- path_id (INT, FK path)
- role (ENUM('student', 'teacher'), default 'student')
- progress (FLOAT, default 0)
- completed (BOOLEAN, default false)

### Relaciones

- areas 1:N paths
- paths N:M courses (a través de course_path)
- courses N:M contents (a través de content_course)
- courses 1:N messages
- medias 1:1 areas, paths, courses, contents (Polimórfica)
- course_enrollments, courses N:M users
- path_enrollments, paths N:M users


### Crear Migraciones

- php artisan make:migration create_areas_table
- php artisan make:migration create_paths_table
- php artisan make:migration create_courses_table
- php artisan make:migration create_contents_table
- php artisan make:migration create_messages_table
- php artisan make:migration create_medias_table
- php artisan make:migration create_course_path_table
- php artisan make:migration create_content_course_table

### Crear Modelos

- php artisan make:model Area
- php artisan make:model Path
- php artisan make:model Course
- php artisan make:model Content
- php artisan make:model Message
- php artisan make:model Media

## Diseño

Al ser un diseño destinado a público de todas las edades debe ser visualmente llamativo, por los más jóvenes, pero al mismo tiempo fácil de leer y usar para los más mayores.

Me inspiraré en https://laravelshift.com. Me gustan la claridad y las fuentes grandes, que permiten hacer un diseño donde cualquier elemento contrastado destaque suficientemente.

Para personalizar Bootstrap 5 he usado https://bootstrap.build/app/project/NrACtlaOLCNQ, después de revisar las opciones disponibles. He optado por uno de los temas ofrecidos, que tiene una paleta de colores apropiada, modificando unos pocos elementos, para que no tenga ese look un poquito antiguo que tienen los diseños de BS.

- El color primario es un naranja, que es un color llamativo
- He usado la fuente Inter de Google, que es uno de los elementos más caracteríscos del diseño en el que me inspiro.
- He incrementado el tamaño del spacer de BS, para que los elementos respiren un poquito más.
- Modificado las esquinas redondeadas, para que el efecto sea muy sutil.
- Los botones son ligeramente más grandes, he modificado el padding vertical y horizontal para que la etiqueta no esté tan constreñida.

### Logo

Haré algo muy simple y funcional, que no sea excesivamente serio, ni demasiado divertido. A menudo uso namecheap.com para inspirarme y coger ideas.

- Fuente: https://fonts.google.com/specimen/Croissant+One Autor: Eduardo Tuni http://www.tipo.net.ar/
- Icono Autor: Alexander Gruzdev https://thenounproject.com/creator/enjoydezign/
- He modificado el icono con Affinity Designer, para tener un logo vectorial, que tenga una apariencia menos formal.

## Seguridad

Describir aquellos aspectos de la seguridad que puedan ser relevantes para la versión actual o futuras.

## Futuro

Describir por dónde podría evolucionar EduPlazza.

## Herramientas

### Elementos del sistema

- Creado el Middleware EnsureRole.php para tener una gestión de roles básica, adaptada a mis necesidades. (Reemplazado por Spatie Permissions)
- Creado el Trait GeneratesSlug.php para crear cualquier slug necesario. De momento lo uso en Areas, Paths y Contents.

### Automatización

- Como estaba todo el tiempo tecleando los mismos comandos he creado ```gpc.bash```, para automatizar la actualización del repositorio en GitHub
- He creado ```getCode.php``` que devuelve el código de los ficheros en aquellos directorios que me interesan, a efectos de documentación o lo que haga falta, para no tener que ir buscando archivo por archivo.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
