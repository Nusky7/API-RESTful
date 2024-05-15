<?php

// Importar la librería Ramsey Uuid para generar tokens únicos
use Ramsey\Uuid\Uuid;

// Incluir el archivo autoload de Composer para cargar las dependencias
require_once __DIR__ . '/../vendor/autoload.php';

// Iniciar una sesión para almacenar la información del usuario
session_start();


/**
* Clase Usuario
*
* Esta clase maneja las operaciones relacionadas con los usuarios, como iniciar sesión, registrarse,
* modificar y eliminar usuarios.
*
* Utiliza la librería Ramsey Uuid para generar identificadores únicos durante el proceso de inicio de sesión.
*
* @package API
* @author Alba Tolosa Bonora <4lbawork@gmail.com>
* @author GitHub: [Nusky7](https://github.com/Nusky7)
*/

class Usuario {

    /**
    * Conexión a la base de datos
    *
    * @var object $conexion Conexión a la base de datos.
    */
    private $conexion;

    /**
    * Datos de entrada
    *
    * @var array $input Datos de entrada en formato JSON.
    */
    private $input;

    /**
    * Constructor de la clase Usuario
    *
    * @param object $conexion Conexión a la base de datos.
    */
    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->input = json_decode(file_get_contents("php://input"), true);
    }


    /**
     * Consultar usuarios
     *
     * Método para consultar todos los usuarios.
     *
     * @return array Datos de los usuarios en formato JSON.
     */

    public function consultar() {

        $sql = "SELECT * FROM  usuario";
        $resultado = $this->conexion->query($sql);

        if ($resultado){
            $datos = [];
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
            echo json_encode($datos);
            return $datos;
        }
    }
    
    /**
     * Iniciar sesión - Login
     *
     * Método para iniciar sesión con un correo electrónico y contraseña.
     * Utiliza Ramsey Uuid para generar un token de autenticación único.
     *
     * @return void
     */

    public function login() {

        $input = $this->input;
        $correo = $input['correo'];
        $contrasena = $input['contrasena'];

    $sql = "SELECT * FROM usuario WHERE correo = ? AND contrasena = ?";
    $resultado = $this->conexion->prepare($sql);
    $resultado->bind_param("ss", $correo, $contrasena);
    $resultado->execute();
    $respuesta = $resultado->get_result();

    if ($respuesta->num_rows === 1) {
        $fila = $respuesta->fetch_assoc();
        $usuario = array(
            'id' => $fila['id'],
            'nombre' => $fila['nombre'],
            'correo' => $fila['correo'],
            'fechaRegistro' => $fila['fechaRegistro']
        );
        $token = str_replace('-', '', (string) Uuid::uuid4());
        $sql = "UPDATE usuario SET token = ? WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("si", $token, $fila['id']);
        $resultado->execute();
        $_SESSION['user_id'] = $usuario['id'];
        header('Content-Type: application/json');
        $respuesta = array('status' => 'exito', 'usuario' => $usuario, 'token' => $token, 'user_id' => $usuario['id']);
        
        echo json_encode($respuesta);
    } else {
        $respuesta = array('status' => 'error', 'mensaje' => 'Correo electrónico o contraseña incorrectos');
        echo json_encode($respuesta);
    }
}

    /**
     * Insertar usuario - Registro
     *
     * Método para insertar un nuevo usuario.
     *
     * @return void
     */

    public function insertar() {

        $input = $this->input;

        $nombre = $input['nombre'];
        $correo = $input['correo'];
        $contrasena = $input['contrasena'];
        $fechaRegistro = date('Y-m-d H:i:s');

        $sql = "INSERT INTO usuario (nombre, contrasena, correo, fechaRegistro) VALUES (?, ?, ?, ?)";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("ssss", $nombre, $contrasena, $correo, $fechaRegistro);

        if ($resultado->execute()) {
            $respuesta = array('status' => 'exito','mensaje' => 'Registro creado correctamente');
            echo json_encode($respuesta);
        } else {
            $respuesta = array('status' => 'error', 'mensaje' => 'Error al crear el registro');
            echo json_encode($respuesta);
        }
    }


    /**
    * Modificar usuario
    *
    * Método para modificar los datos de un usuario en la base de datos.
    *
    * @return void
    */

    public function modificar() {
    
        $input = $this->input;

        $id = $input['id'];
        $nombre = $input['nombre'];
        $correo = $input['correo'];
  
        $sql = "SELECT * FROM usuario WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $id);
        $resultado->execute();
        $respuesta = $resultado->get_result();

        if ($respuesta->num_rows === 0) {
            echo "No se encontró ningún registro con el ID proporcionado ";
        }

        $sql = "UPDATE usuario SET nombre = ?, correo = ? WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("ssi", $nombre, $correo, $id);

        if ($resultado->execute() && $resultado->affected_rows > 0){
            $respuesta = array('status' => 'exito', 'mensaje' => 'Registro actualizado correctamente');
            echo json_encode($respuesta);
        }else {
            $respuesta = array('status' => 'error','mensaje' => 'Error al actualizar el registro'
            );
            echo json_encode($respuesta);
        }
    }


    /**
    * Borrar usuario
    *
    * Método para eliminar un usuario de la base de datos.
    *
    * @return void
    */

    public function borrar() {
  
        //$input = $this->input;

        $id = $_GET['id'];

        $sql = "DELETE FROM usuario WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $id);

        if ($resultado->execute() && $resultado->affected_rows > 0) {
            $respuesta = array('status' => 'exito','mensaje' => 'Registro eliminado correctamente');
            echo json_encode($respuesta);
        } else {
            $respuesta = array('status' => 'error',
            'mensaje' => 'Error al eliminar, el registro no existe o fue borrado');
            echo json_encode($respuesta);
        }
    }
}
