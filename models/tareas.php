<?php

/**
 * Clase Tareas
 *
 * Clase para consultar, insertar, modificar y borrar las tareas de los proyectos en la base de datos.
 *
 * @package API
 * @author Alba Tolosa Bonora <4lbawork@gmail.com>
 * @author GitHub: [Nusky7](https://github.com/Nusky7)
 */

class Tareas {

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
     * Constructor de la clase Tareas
     *
     * @param object $conexion Conexión a la base de datos.
     */

    public function __construct($conexion){
        $this->conexion = $conexion;
        $this->input = json_decode(file_get_contents("php://input"), true);
    }


    /**
     * Consultar todas las tareas
     *
     * Método para consultar todas las tareas en la base de datos.
     *
     * @return void
     */

    public function consultar(){
        $sql = "SELECT * FROM tareas";
        $resultado = $this->conexion->query($sql);
        
        if($resultado){
            $datos = [];
            while($fila = $resultado->fetch_assoc()){
                $datos[] = $fila;
            }
            echo json_encode($datos);
        }else{
            echo "No se pudieron recuperar los resultados";
        }
    }


    /**
     * Consultar una tarea por su ID
     *
     * Método para consultar una tarea en la base de datos por su ID.
     *
     * @return void
     */

    public function consultarPorId($project_id) {

        $sql = "SELECT * FROM tareas WHERE project_id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $project_id);
        $resultado->execute();
        $sentencia = $resultado->get_result();

        if ($sentencia->num_rows > 0) {
            $tareas = [];
            while ($fila = $sentencia->fetch_assoc()) {
            $tareas[] = $fila;
            }
            echo json_encode($tareas);
        }else{
            $resultado = array('status' => 'error','mensaje' => 'La tarea no existe');
            echo json_encode($resultado);
        }
    }

     /**
     * Insertar una nueva tarea
     *
     * Método para insertar una nueva tarea en la base de datos.
     *
     * @return void
     */

    public function insertar() {

        if (isset($this->input['proyecto_id'])) {
            $project_id = $this->input['proyecto_id'];
        }


        $titulo = $this->input['titulo'];
        $descripcion = $this->input['descripcion'];
        $fechaVencimiento = date('Y-m-d', strtotime($this->input['fechaVencimiento']));
        $prioridad = $this->input['prioridad'];
        $estado = $this->input['estado'];
        $completado = isset($this->input['completado']) && $this->input['completado'] ? 1 : 0;

        $sql = "INSERT INTO tareas (project_id, titulo, descripcion, prioridad, estado, fechaVencimiento, completado)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("ississi", $project_id, $titulo, $descripcion, $prioridad, $estado, $fechaVencimiento, $completado);
        
        if ($resultado->execute() && $resultado->affected_rows > 0) {
            $resultado = array('mensaje' => 'Tarea añadida correctamente');
            echo json_encode($resultado);
        }else {
            $resultado = array('mensaje' => 'Error al insertar la tarea');
            echo json_encode($resultado);
        }

    }

    
    /**
     * Modificar una tarea existente
     *
     * Método para modificar una tarea existente en la base de datos.
     *
     * @return void
     */
 
    public function modificar($id){
        
        $titulo = $this->input['titulo'];
        $descripcion = $this->input['descripcion'];
        $prioridad = $this->input['prioridad'];
        $estado = $this->input['estado'];
        $completado = $this->input['boolean'] ? 1 : 0;


        $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, prioridad = ?, estado = ?, completado = ?
            WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("ssisis", $titulo, $descripcion, $prioridad, $estado, $completado, $id);

        if ($resultado->execute() && $resultado->affected_rows > 0) {
            $resultado = array('mensaje' => 'Tarea modificada correctamente');
            echo json_encode($resultado);
        }else{
            $resultado = array('mensaje' => 'Error al modificar la tarea');
            echo json_encode($resultado);
        }
    }


     /**
     * Eliminar una tarea existente
     *
     * Método para eliminar una tarea existente en la base de datos.
     *
     * @return void
     */

     public function borrar($id) {

        $sql = "SELECT * FROM tareas WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("i", $id);
        $resultado->execute();
        $sentencia = $resultado->get_result();

        if ($sentencia->num_rows > 0) {
        $sql_delete = "DELETE FROM tareas WHERE id = ?";
        $resultado = $this->conexion->prepare($sql_delete);
        $resultado->bind_param("i", $id);

        if ($resultado->execute() && $resultado->affected_rows > 0) {
            $resultado = array('mensaje' => 'La tarea se eliminó correctamente');
            echo json_encode($resultado);
        } else {
            $resultado = array('mensaje' => 'Error al eliminar la tarea');
            echo json_encode($resultado);
        }
        }else {
            echo "No se ha encontrado la tarea.";
        }
    }


    


}



