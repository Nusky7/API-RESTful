<?php

/**
 * Clase EventoCalendario
 *
 * Clase para manejar los eventos de calendario en la base de datos.
 *
 * @package API
 * @author Alba Tolosa Bonora <4lbawork@gmail.com>
 * @author GitHub: [Nusky7](https://github.com/Nusky7)
 */

class EventoCalendario {
    
     /**
     * Conexión a la base de datos
     *
     * @var object $conexion Conexión a la base de datos.
     */
    private $conexion;

    /**
     * Input JSON decodificado
     *
     * @var array $input Input JSON decodificado.
     */
    private $input;

    /**
     * Constructor de la clase EventoCalendario
     *
     * @param object $conexion Conexión a la base de datos.
     */

    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->input = json_decode(file_get_contents("php://input"), true);
    }


    /**
     * Consultar todos los eventos de calendario
     *
     * Método para consultar todos los eventos de calendario en la base de datos.
     *
     * @return void
     */

    public function consultar(){

        $sql = "SELECT * FROM eventoCalendario";
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
     * Consultar un evento de calendario por su ID
     *
     * Método para consultar un evento de calendario en la base de datos por su ID.
     *
     * @return void
     */

    public function consultarPorId() {

        $user_id = $_GET['user_id'];

        $sql = "SELECT * FROM eventoCalendario WHERE user_id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $user_id);
        $resultado->execute();
        $sentencia = $resultado->get_result();

        if ($sentencia->num_rows === 0) {
            $resultado = array('status' => 'error','mensaje' => 'El evento no existe');
            echo json_encode($resultado);
            }else{
                $eventos = [];
                while ($evento = $sentencia->fetch_assoc()) { 
                $eventos[] = $evento;
            }
            echo json_encode($eventos);
        }

    }


    /**
     * Insertar un nuevo evento de calendario
     *
     * Método para insertar un nuevo evento de calendario en la base de datos.
     *
     * @return void
     */

    public function insertar(){

    if (!isset($this->input['user_id']) || !isset($this->input['titulo']) || !isset($this->input['descripcion']) || !isset($this->input['fechaInicio']) || !isset($this->input['fechaFin'])) {
        $resultado = array('status' => 'error','mensaje' => 'Error: faltan datos necesarios para insertar el evento en el calendario');
        echo json_encode($resultado);
        return;
    }

    $user_id = $this->input['user_id'];
    $titulo = $this->input['titulo'];
    $descripcion = $this->input['descripcion'];
    $fechaInicio = date('Y-m-d H:i:s', strtotime($this->input['fechaInicio'] && $this->input['horaInicio']));
    $fechaFin = date('Y-m-d H:i:s', strtotime($this->input['fechaFin'] && $this->input['horaFin']));

    $sql = "INSERT INTO eventocalendario (titulo, descripcion, fechaInicio, fechaFin, user_id)
            VALUES (?, ?, ?, ?, ?)";
    $resultado = $this->conexion->prepare($sql);
    $resultado->bind_param("ssssi", $titulo, $descripcion, $fechaInicio, $fechaFin, $user_id);

    if ($resultado->execute() && $resultado->affected_rows> 0) {
        $resultado = array('status' => 'exito','mensaje' => 'Evento creado');
        echo json_encode($resultado);
        return $this->conexion->insert_id;
    } else {
        $resultado = array('status' => 'error','mensaje' => 'Error al insertar el evento en el calendario');
        echo json_encode($resultado);
    }
}

    /**
     * Modificar un evento de calendario existente
     *
     * Método para modificar un evento de calendario existente en la base de datos.
     *
     * @return void
     */

    public function modificar() {

        $id = $this->input['id'];
        $titulo = $this->input['titulo'];
        $descripcion = $this->input['descripcion'];
        $fechaInicio = $this->input['fechaInicio'];
        $fechaFin = $this->input['fechaFin'];

        $sql = "UPDATE eventocalendario SET titulo = ?, descripcion = ?, fechaInicio = ?, fechaFin = ?
             WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("ssssi", $titulo, $descripcion, $fechaInicio, $fechaFin, $id);

        if ($resultado->execute() && $resultado->affected_rows > 0){
            $resultado = array('Mensaje' => 'Modificado correctamente');
            echo json_encode($resultado);
        }else{
            $resultado = array('Mensaje' => 'Error al modificar el evento');
            echo json_encode($resultado);
        }
    }


    /**
     * Eliminar un evento de calendario existente
     *
     * Método para eliminar un evento de calendario existente en la base de datos.
     *
     * @return void
     */

    public function borrar($id){

        $sql = "SELECT * FROM eventocalendario WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $id);
        $resultado->execute();
        $sentencia = $resultado->get_result();

        if ($sentencia->num_rows > 0){
            $sql_borrar = "DELETE FROM eventocalendario WHERE id = ?";
            $borrar = $this->conexion->prepare($sql_borrar);
            $borrar->bind_param("i", $id);
            
            if ($borrar->execute() && $borrar->affected_rows > 0){
                $resultado = array('Mensaje' => 'Evento eliminado');
                echo json_encode($resultado);
            }else{
                $resultado = array('Mensaje' => 'Error al eliminar el evento');
                echo json_encode($resultado);
            }
        }
}
}