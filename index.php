<?php
date_default_timezone_set('Europe/Madrid');

/**
 * Index.php
 *
 * Archivo principal de la API REST.
 *
 * @package API
 * @author Alba Tolosa Bonora <4lbawork@gmail.com>
 * @author GitHub: [Nusky7](https://github.com/Nusky7)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//Encabezados CORS para permitir solicitudes desde cualquier origen:
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


/**
 * Incluir modelos
 * Bucle que comprueba la existencia de los modelos a incluir
 */

foreach (glob("models/*.php") as $modelo) {
    require_once $modelo;
}

/**
 * Conexión a la base de datos
 *
 * @var object $conexion Conexión a la base de datos.
 */

$usuario = "alba";
$contrasena = "smartimeBD";
$basedatos = "smarTime";
$servidor = "localhost";

$conexion = new mysqli($servidor, $usuario, $contrasena, $basedatos);

if ($conexion->connect_error) {
    die("Error conectando a la base de datos: " . $conexion->connect_error);
}

/**
 * Método HTTP de la solicitud
 *
 * @var string $metodo Método HTTP de la solicitud.
 */

$metodo = $_SERVER['REQUEST_METHOD'];

/**
 * Instancias de las clases principales
 *
 * @var object $usuario Instancia de la clase usuario.
 * @var object $proyecto Instancia de la clase proyecto.
 * @var object $notas Instancia de la clase notas.
 * @var object $tareas Instancia de la clase tareas.
 * @var object $evento Instancia de la clase eventoCalendario.
 */


$usuario = new usuario($conexion);
$proyecto = new proyecto($conexion);
$notas = new notas($conexion);
$tareas = new tareas($conexion);
$evento = new eventoCalendario($conexion);


// Rutas simuladas - probando
//const USER = "/login";
//const PROJECT = "/project";
//const NOTAS = "/notas";
//const TAREAS = "/tareas";
//const EVENTO = "/eventoCalendario";


/**
 * Ruta de la solicitud
 *
 * @var string $ruta Ruta de la solicitud.
 */
$ruta = $_SERVER['REQUEST_URI'];

/**
 * Constante para ruta no válida
 */
const SIN_RUTA = '{"mensaje":"Ruta no válida"}';


/**
 * Procesar la solicitud según el método HTTP
 *
 * @switch $metodo
 */

switch ($metodo) {
    case 'GET':
    //Consulta de registros - GET
        if (preg_match('/\/proyectos\?user_id=(\d+)/', $ruta, $matches)){
            $proyecto->consultarPorId();
        }elseif (preg_match('/\/usuario\?user_id=(\d+)/', $ruta, $matches)){
            $usuario->consultarPorId();
        }elseif (preg_match('/\/notas\?user_id=(\d+)/', $ruta, $matches)){
            $notas->consultarPorId();
        }elseif (preg_match('/\/tareas\?project_id=(\d+)/', $ruta, $matches)){
            $project_id = $matches[1];
            $tareas->consultarPorId($project_id);
        }elseif (preg_match('/\/eventos\?user_id=(\d+)/', $ruta, $matches)){
            $evento->consultarPorId();
        }
        else{
            echo SIN_RUTA;
        }
        break;
        
    case 'POST':
    //Inserción de registros - POST
        if (preg_match('/\/usuario\/login$/', $ruta)){
            $usuario->login();
        } elseif (preg_match('/\/usuario\/registro$/', $ruta)) {
            $usuario->insertar();
        }elseif (preg_match('/\/proyectos$/', $ruta)){
            $proyecto->insertar();
        }elseif (preg_match('/\/notas$/', $ruta)){
            $notas->insertar();
        }elseif (preg_match('/\/tareas$/', $ruta)){
            $tareas->insertar();
        }elseif (preg_match('/\/eventos$/', $ruta)){
            $evento->insertar();
        }
        else{
            echo SIN_RUTA;
        }
        break;
        
    case 'PUT':
    //Modificación de registros - PUT
        if (preg_match('/\/usuario\/(\d+)$/', $ruta, $matches)){
            $usuario->modificar();
        }elseif (preg_match('/\/proyectos\/(\d+)$/', $ruta, $matches)){
            $proyecto->modificar();
        }elseif (preg_match('/\/notas\/(\d+)$/', $ruta, $matches)){
            $notas->modificar();
        }elseif (preg_match('/\/tareas\/(\d+)$/', $ruta, $matches)){
            $id = $matches[0];
            $id = str_replace('/tareas/', '', $id);
            $tareas->modificar($id);
        }elseif (preg_match('/\/eventos\/(\d+)$/', $ruta, $matches)){
            $evento->modificar();
        }
        else{
            echo SIN_RUTA;
        }
        break;
        
    case 'DELETE':
    //Borrado de registros - DELETE
        if (preg_match('/\/usuario\/(\d+)/', $ruta, $matches)){
            $usuario->borrar();
        }elseif (preg_match('/\/proyectos\/(\d+)/', $ruta, $matches)){
            $id = $matches[1];
            $proyecto->borrar($id);
        }elseif (preg_match('/\/notas\/(\d+)/', $ruta, $matches)){
            $id = $matches[1];
            $notas->borrar($id);
        }elseif (preg_match('/\/tareas\/(\d+)/', $ruta, $matches)){
            $id = $matches[1];
            $tareas->borrar($id);
        }elseif (preg_match('/\/eventos\/(\d+)/', $ruta, $matches)){
            $id = $matches[1];
            $evento->borrar($id);
        }
        else{
            echo SIN_RUTA;
        }
        break;
    default:
      echo "Error";
      break;
}


