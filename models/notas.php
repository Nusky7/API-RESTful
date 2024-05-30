<?php

/**
 * Clase Notas
 *
 * Esta clase maneja las operaciones relacionadas con las notas, como consultar, insertar, modificar y eliminar.
 *
 * @package API
 * @author Alba Tolosa Bonora <4lbawork@gmail.com>
 * @author GitHub: [Nusky7](https://github.com/Nusky7)
 */

class Notas {

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
     * Constructor de la clase Notas
     *
     * @param object $conexion Conexión a la base de datos.
     */

    public function __construct($conexion){
        $this->conexion = $conexion;
        $this->input = json_decode(file_get_contents("php://input"), true);
    }


     /**
     * Consultar todas las notas
     *
     * Método para consultar todas las notas en la base de datos.
     *
     * @return void
     */

    public function consultar() {

        $sql = "SELECT * FROM notas";
        $resultado = $this->conexion->query($sql);

        if ($resultado) {
            $datos = [];
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
            echo json_encode($datos);
        } else {
            echo "No se pudieron recuperar los resultados";
        }
    }


    /**
     * Consultar notas por el ID de usuario
     *
     * Método para consultar las notas de un usuario específico en la base de datos.
     *
     * @return void
     */

    public function consultarPorId() {

        $user_id = $_GET["user_id"];

        $sql = "SELECT * FROM notas WHERE user_id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $user_id);
        $resultado->execute();
        $sentencia = $resultado->get_result();

        if ($sentencia->num_rows > 0){
            $notas = [];
            while ($fila = $sentencia->fetch_assoc()){
                $notas[] = $fila;
        }
            echo json_encode($notas);
        }  else {
            $resultado = array('status' => 'error','mensaje' => 'No se encontraron notas para el usuario');
            echo json_encode($resultado);
        }
    }


    /**
     * Consultar la información de una nota por su ID
     *
     * Método para consultar toda la información de una nota específica en la base de datos.
     *
     * @return void
     */
    public function consultarTodoPorId() {

        $input = $this->input;

        $id = $input['id'];

        $sql = "SELECT * FROM notas WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $id);
        $resultado->execute();
        $sentencia = $resultado->get_result();

        if ($sentencia->num_rows === 0) {
            $resultado = array('status' => 'error','mensaje' => 'La nota no existe');
            echo json_encode($resultado);
        }else{
            $notas = [];
            while ($fila = $sentencia->fetch_assoc()){
                $notas[] = $fila;
            }echo json_encode($notas);
        }
    }

    /**
     * Insertar una nueva nota
     *
     * Método para insertar una nueva nota en la base de datos.
     *
     * @return void
     */

    public function insertar() {

        $input = $this->input;

        $user_id = $input['user_id'];
        $titulo = $input['titulo'];
        $descripcion = $input['descripcion'];
        $fechaCreacion = date('Y-m-d H:i:s');

        $sql = "INSERT INTO notas (user_id, titulo, descripcion, fechaCreacion)VALUES (?, ?, ?, ?)";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("isss", $user_id, $titulo, $descripcion, $fechaCreacion);
        
        if ($resultado->execute() && $resultado->affected_rows > 0) {
            $resultado = array('mensaje' => 'Nota añadida correctamente');
            echo json_encode($resultado);
        }else {
            $resultado = array('mensaje' => 'Error al insertar la nota');
            echo json_encode($resultado);
        }

    }


     /**
     * Modificar una nota existente
     *
     * Método para modificar una nota existente en la base de datos.
     *
     * @return void
     */

    public function modificar(){
        $input = $this->input;
        $id = $input['id'];
        $titulo = $input['titulo'];
        $descripcion = $input['descripcion'];

        $sql = "UPDATE notas SET titulo = ?, descripcion = ? WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("ssi", $titulo, $descripcion, $id);

        if ($resultado->execute() && $resultado->affected_rows >0) {
            $resultado = array('mensaje' => 'Nota modificada correctamente');
            echo json_encode($resultado);
        }else{
            $resultado = array('mensaje' => 'Error al modificar la nota');
            echo json_encode($resultado);
        }
    }


    /**
     * Eliminar una nota existente
     *
     * Método para eliminar una nota existente en la base de datos.
     *
     * @param int $id Identificador de la nota a eliminar.
     * @return void
     */

    public function borrar($id) {
     
        $sql = "SELECT * FROM notas WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $id);
        $resultado->execute();
        $sentencia = $resultado->get_result();

        if ($sentencia->num_rows > 0) {
        $sql_delete = "DELETE FROM notas WHERE id = ?";
        $resultado = $this->conexion->prepare($sql_delete);
        $resultado->bind_param("i", $id);

        if ($resultado->execute() && $resultado->affected_rows > 0) {
            $resultado = array('mensaje' => 'La nota se eliminó correctamente');
            echo json_encode($resultado);
        } else {
            $resultado = array('mensaje' => 'Error al eliminar');
            echo json_encode($resultado);
        }
        }else {
            $resultado = array('mensaje' => 'La nota no existe');
            echo json_encode($resultado);
        }
    }
    

}