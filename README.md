## Desarrolla JARS Costa Rica

# Información del Proyecto
API de conexión App Movil

## Configuración
### Config.php
```
define('_MYSQL_SERVER', 'localhost');
define('_MYSQL_USER', 'DBUSER');
define('_MYSQL_PASSWORD', 'DBPASS');
define('_MYSQL_DATABASE', 'DBNAME');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
```
### RHApi.php
El archivo contiene las rutas que se utilizarán para el llamado de funciones

La mayor parte de los llamados requieren POST


## API EndPoints y Request

#### Cargar Foto al capturar
https://appm.jarscr.com/actualizar/empleadoFoto

Al resultado de la captura se le agregar "image/jpeg;base64," seguido de la cadena de Base64, el conector de la camara devuelve en base64 las imagenes.

```
Request:
{
    "FotoUpdate": "image/jpeg;base64,bG9nby1wZXJmaWwuanBn",
    "IdEmpleado":1882
}

Result:
{
    "result": "success",
    "Foto": "data:image/jpg;base64,bG9nby1wZXJmaWwuanBn"
}

```


#### Rotar Foto
https://appm.jarscr.com/actualizar/imagenrotar

```
Request: 
{
    "IdEmpleado": 1882,
    "Rotacion": 3
}

Result:
{
    "result": "success",
    "Foto": "data:image/jpg;base64,bG9nby1wZXJmaWwuanBn"
}
```



## Créditos

- [Alfredo Rodriguez] @jarscr
