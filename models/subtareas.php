
class Subtareas {

private $conexion;
private $input;

public function __construct($conexion){
    $this->conexion = $conexion;
    $this->input = json_decode(file_get_contents("php://input"), true);
}

    public function insertar() {

        $input = $this->input;

        $tareaId = $input['tarea_id'];
        $titulo = $input['titulo'];
        $descripcion = $input['descripcion'];
        $completado = false;

        $sql = "INSERT INTO subtareas (tarea_id, titulo, descripcion, completado) VALUES (?, ?, ?, ?)";
        $resultado = $this->conexion->prepare($sql);
        $resultado->bind_param("isss", $tarea_id, $titulo, $descripcion, $completado);

        if ($resultado->execute()) {
            $respuesta = array('status' => 'exito','mensaje' => 'Subtarea creada correctamente');
            echo json_encode($respuesta);
        } else {
            $respuesta = array('status' => 'error', 'mensaje' => 'Error al crear la subtarea');
            echo json_encode($respuesta);
        }
    }

    public function consultarPorId(){

        $sql = "SELECT * FROM subtarea WHERE tarea_id = ?":
        $resultado = $this->$conexion->prepare($sql);
        $resultado = bind_param("i", tarea_id);
        $resultado ->execute();
        $sentencia = $resultado->get_result();

        if ($sentencia->num_rows > 0) {
            $tareas = [];
            while ($fila = $sentencia->fetch_assoc()) {
            $tareas[] = $fila;
            }
            echo json_encode($tareas);
            
        }else{
            $resultado = array('status' => 'error','mensaje' => 'La subtarea no existe');
            echo json_encode($resultado);
        }
    }












}