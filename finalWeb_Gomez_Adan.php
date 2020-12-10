<?php

//LoteController 

require_once ('models/lab.model..php');
require_once ('models/ciudad.model..php');
require_once ('models/lote.model..php');
require_once ('helpers/autentication.helper.php');

class LoteController{

private $modelLab;
private $modelCiudad;
private $modelLote;
private $view;

public function __construct() {
    $this->modelLab = new LabModel();
    $this->modelCiudad = new CiudadsModel();
    $this->modelLote = new LoteModel();
    $this->view = new View();
    HelperAutenticacion::checkLogged();
}

public function asignarLote(){
    //supoiendo que los datos vienen cargados de un formulario
    $nro_lote = $_POST['nro_lote'];
    $vencimiento = $_POST['anio_vencimiento'];
    $id_ciudad = $_POST['id_ciudad'];
    $id_laboratorio = $_POST['id_laboratorio'];

    $todasCiudades = $this->modelCiudad->getAllCitys();
    $todosLabs = $this->modelLab->getAllLab();
    $stock_lotes = $this->modelLab->getStock($id_laboratorio);
    
    if(!empty($nro_lote)|| !empty($vencimiento) || !empty($id_ciudad) || !empty($id_laboratorio)){
        $this->view->formAsignarLote("Faltan completar campos");
    }
    else{
        foreach ($todasCiudades as $ciudad){
            if ($id_ciudad == $ciudad->id){
                foreach ($todosLabs as $lab){
                    if ($id_laboratorio == $lab->id){
                        if($stock_lotes > 0){
                            $this->modelLote->newLote($nro_lote, $vencimiento, $id_ciudad, $id_laboratorio);
                            $this->view->formAsignarLote("Se agregó el lote");
                            $stock_lotes--;
                        }
                    }
                }
            } else{
                $this->view->formAsignarLote("no se pudo agregar el lote");
            }
        }
    }
}
}

//Helper
class HelperAutenticacion {

    static public function checkLogged() {
        if(session_status() != PHP_SESSION_ACTIVE){
        session_start();
        }

        if (!isset($_SESSION['logged'])) {
            header('Location: ' . BASE_URL);
            die();
        }
    }
}

//LabModel

class LabModel{

    public function __construct() {
        $this->db = $this->createConection();
    }

    Private function createConection(){
        $host = 'localhost';
        $userName = 'root';
        $password = '';
        $database = 'db_vacunas';
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $userName , $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function getAllLab(){
        $sentencia = $this->db->prepare("SELECT * FROM Laboratorio");
        $sentencia->execute();
        $Laboratorios = $sentencia->fetchAll(PDO::FETCH_OBJ);
        return $Laboratorios;
    }

    public function getStock($id){
        $sentencia = $this->db->prepare("SELECT Laboratorio.stock_lotes FROM Laboratorio WHERE Laboratorio.id = ?");
        $sentencia->execute([$id]);
        $stock = $sentencia->fetch(PDO::FETCH_OBJ);
        return $stock;
    }

}

//CiudadModel

class CiudadModel{

    public function __construct() {
        $this->db = $this->createConection();
    }

    Private function createConection(){
        $host = 'localhost';
        $userName = 'root';
        $password = '';
        $database = 'db_vacunas';
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $userName , $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function getAllCitys(){
        $sentencia = $this->db->prepare("SELECT * FROM Ciudad");
        $sentencia->execute();
        $ciudades = $sentencia->fetchAll(PDO::FETCH_OBJ);
        return $ciudades;
    }
}

//LoteModel

class LoteModel{

    public function __construct() {
        $this->db = $this->createConection();
    }

    Private function createConection(){
        $host = 'localhost';
        $userName = 'root';
        $password = '';
        $database = 'db_vacunas';
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $userName , $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function newLote($nro, $anio, $ciudad, $lab){
        $sentencia = $this->db->prepare("INSERT INTO Lote(Lote(nro_lote, año_vencimiento, id_ciudad, id_laboratorio) ) VALUE(?,?,?,?)");
        $sentencia->execute([$nro, $anio, $ciudad, $lab]);
    }
}

//PUNTO DOS

//PagosController
require_once ('models/lab.model..php');
require_once ('models/ciudad.model..php');
require_once ('models/lote.model..php');

class Loteontroller{

    private $modelLab;
    private $modelLote;
    private $view;

    public function __construct() {
        $this->modelLab = new LabModel();
        $this->modelLote = new LoteModel();
        $this->view = new View();
    }

    public function pagosALab(){
        
        $todosLabs = $this->modelLab->getAllLab();
        $todosLotes = $this->modelLote->getAllLotes();

        echo (<table>);
        echo (<td>'Lab'</td>);
        echo (<td>'U$S'</td>);
        foreach($todosLabs as $lab){
            $cant_lotes=0;
            $total=$cant_lotes*$lab->costo_lote;
            foreach ($todosLotes->id_laboratorio = $todosLabs->id){
                $cant_lotes++;
            }
                echo (<tr>);
                echo (<td>$lab->nombre</td>);
                echo (<td>$$total</td>;
                echo (</tr>);
        }
        echo (/<table>);
        $this->view->listaDePagos($todosLabs, $todosLotes);
    }   
}

//LoteModel

//Agregaría la función getAllLotes()

public function getAllLotes(){
    $sentencia = $this->db->prepare("SELECT * FROM Lote");
    $sentencia->execute();
    $Lotes = $sentencia->fetchAll(PDO::FETCH_OBJ);
    return $Lotes;
}

//PUNTO 3

//A

/**
 * En la base de datos agregaría la tabla cetroSalud(id, nombreCentro) y en la tabla Lote agregaría la columna 
 * id_centro.
 * En ModelCentro agregaría la función getCentros() que este contenga cada centro y lote que pertenecen.
 * En LoteController agregaría la función lotesPorCentro() que con un foreach recorreria todos los centros y 
 * cada uno de sus lotes y de ahi lo mandaría a la vista.
 * 
 * 
 */

 //B

 /**
  * RoterApi
  */

$router = new Router();

// creo la tabla de ruteo

//CENTROS DE SALUD
$router->addRoute('centroSalud', 'GET', 'PublicApiController', 'getAllCenter');  

//LOTES de Centro específico
$router->addRoute('centroSalud/:ID', 'GET', 'PublicApiController', 'getLotesOfCenter'); 

$router->route($_REQUEST['resource'], $_SERVER['REQUEST_METHOD']);

/**
 * PublicApiController
 */
require_once ('models/lab.model..php');
require_once ('models/centro.model..php');
require_once ('models/lote.model..php');
require_once 'api/api.view.php';

class PublicApiController{

    private $modelLote;
    private $modelCentro;
    private $view;

    public function __construct() {
        $this->modelCentro = new CentroModel();
        $this->modelLote = new LoteModel();
        $this->view = new APIView();
        $this->data= file_get_contents("php://input");
    }

        public function getAllCenter($params = []) {
            $centros = $this->modelCentro->getAllCenter();
            if ($centros){
                $this->view->response($centros, 200);
            }else{
                $this->view->response($centros, 204);
            }
        }

        public function getLotesOfCenter($params){
            $idCentro = $params[':ID'];
            $lote = $this->modelLote->getLotesOfCenter($idCentro);
            if ($lote){
                $this->view->response($lote, 200);
            }else{
                $this->view->response("no existe centro con id {$idCentro}", 404);
            }
        }
    
}

//ModelCentro

class CentroModel{

public function __construct() {
    $this->db = $this->createConection();
}

Private function createConection(){
    $host = 'localhost';
    $userName = 'root';
    $password = '';
    $database = 'db_vacunas';
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $userName , $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

public function getLotesOfCenter($idCentro){
        $sentencia = $this->db->prepare("SELECT * FROM Lote WHERE Lote.id_centro = ?");
        $sentencia->execute([$idCentro]);
        $lotes = $sentencia->fetchAll(PDO::FETCH_OBJ);
        return $lotes;
}
}

/**C */

/**Considero que los municipios solicitaron el servicio API REST, para poder agregar, borrar editar u obtener
 * los datos de cada lote, ciudad, centro o laboratorio de una forma más sencilla. Y en el caso de que a la página
 * le surgiera algo se marcará con un mensaje de error
 */

