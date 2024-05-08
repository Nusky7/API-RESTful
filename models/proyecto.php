<?php

/**
 * Clase Proyecto
 *
 * Clase para consultar, insertar, modificar y borrar proyectos en la base de datos.
 *
 * @package API
 * @author Alba Tolosa Bonora <4lbawork@gmail.com>
 * @author GitHub: [Nusky7](https://github.com/Nusky7)
 */

class Proyecto {

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
     * Constructor de la clase Proyecto
     *
     * @param object $conexion Conexión a la base de datos.
     */

    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->input = json_decode(file_get_contents("php://input"), true);
    }


    /**
     * Consultar todos los proyectos
     *
     * Método para consultar todos los proyectos en la base de datos.
     *
     * @return void
     */

    public function consultar() {
        $sql = "SELECT * FROM proyecto";
        $resultado = $this->conexion->query($sql);

        if ($resultado) {
            $datos = array();
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
            echo json_encode($datos);
        } else {
            return null;
        }
    }


    /**
     * Consultar un proyecto por su ID
     *
     * Método para consultar un proyecto en la base de datos por su ID.
     *
     * @return void
     */

    public function consultarPorId() {

        $id = $this->input['id'];

        $sql = "SELECT * FROM proyecto WHERE id = ?";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("i", $id);
        $sentencia->execute();
        $resultado = $sentencia->get_result();

        if ($resultado->num_rows === 0) {
            $resultado = array('status' => 'error','mensaje' => 'El proyecto no existe');
            echo json_encode($resultado);
        }else{
             $proyecto = $resultado->fetch_assoc();
             echo json_encode($proyecto);
    }
}

    /**
     * Insertar un nuevo proyecto
     *
     * Método para insertar un nuevo proyecto en la base de datos.
     *
     * @return void
     * @throws Exception Si faltan campos requeridos en la solicitud.
     */

    public function insertar() {
  
        if (isset($this->input['titulo'], $this->input['descripcion'], $this->input['user_id'])) {

            $titulo = $this->input['titulo'];
            $descripcion = $this->input['descripcion'];
            $user_id = $this->input['user_id'];
            $fechaCreacion = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO proyecto (titulo, descripcion, fechaCreacion, user_id) VALUES (?, ?, ?, ?)";
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bind_param("sssi", $titulo, $descripcion, $fechaCreacion, $user_id);
        
        if ($sentencia->execute() && $sentencia->affected_rows> 0) {
            $resultado = array('status' => 'exito','mensaje' => 'Proyecto creado');
            echo json_encode($resultado);
            return $this->conexion->insert_id;
        }
     else {
            $resultado = array('status' => 'error','mensaje' => 'Error al insertar el proyecto');
            echo json_encode($resultado);
        }
    } else {
        throw new Exception("Faltan campos requeridos en la solicitud");
    }
}


    /**
     * Modificar un proyecto existente
     *
     * Método para modificar un proyecto existente en la base de datos.
     *
     * @return void
     * @throws Exception Si ocurre un error al modificar el proyecto.
     */

    public function modificar() {

        $id = $this->input['id'];
        $titulo = $this->input['titulo'];
        $descripcion = $this->input['descripcion'];

        if (isset($this->input['id'])) {
        $sql = "UPDATE proyecto SET titulo = ?, descripcion = ? WHERE id = ?";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("ssi", $titulo, $descripcion, $id);

        if ($resultado->execute() && $resultado->affected_rows > 0){
            $resultado = array('mensaje' => 'Modificado correctamente');
            echo json_encode($resultado);
        } else {
            $resultado = array('status' => 'error','mensaje' => 'Error al modificar el registro');
            echo json_encode($resultado);
    
        }}else {
            throw new Exception("Error al modificar el proyecto");
        }
    }


    /**
     * Eliminar un proyecto existente
     *
     * Método para eliminar un proyecto existente en la base de datos.
     *
     * @return void
     */

    public function borrar() {
        
        $id = $this->input['id'];

        $sql = "SELECT * FROM proyecto WHERE id = ?";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("i", $id);
        $sentencia->execute();
        $resultado = $sentencia->get_result();

        if ($resultado->num_rows > 0) {
            $sql_borrar = "DELETE FROM proyecto WHERE id = ?";
            $borrar = $this->conexion->prepare($sql_borrar);
            $borrar->bind_param("i", $id);

            if ($borrar->execute() && $borrar->affected_rows > 0) {
                $resultado = array('mensaje:' => 'Eliminado correctamente');
                echo json_encode($resultado);
            }
        }else {
            $resultado = array('mensaje' => 'error', 'mensaje' => 'El proyecto no existe o fue eliminado');
            echo json_encode($resultado);
        }
    }
}
    