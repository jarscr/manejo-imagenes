<?php
include 'Config.php';
include 'Clases/Imagenes.php';

/**
 * Description of RHApi
 *
 * @author Jose Alfredo Rodriguez <alfredo@jarscr.com>
 * @copyright  JARS Costa Rica
 * */

class RHApi
{


    public function OpcionesImagenActualizar()
    {
        switch ($_REQUEST['action']) {
           
            case 'empleadoFoto':
                $obj    = json_decode(file_get_contents('php://input'));
                $objArr = (array) $obj;
                if (empty($objArr)) {
                    $this->response(422, "error", "Nothing to add. Check json");
                } else if (isset($obj->IdEmpleado)) {
                    $db = new ImagenesDB();
                    echo $db->ImagenesCapture($obj->IdEmpleado, $obj->FotoUpdate);
                } else {
                    $this->response(422, "error", "The property is not defined");
                }
                break;
            case 'imagenperfil':
                $obj    = json_decode(file_get_contents('php://input'));
                $objArr = (array) $obj;
                if (empty($objArr)) {
                    $this->response(422, "error", "Nothing to add. Check json");
                } else if (isset($obj->IdEmpleado)) {
                    $db = new ImagenesDB();
                    echo $db->EditarImagen($obj->IdEmpleado, $obj->Rotacion);
                    //$this->response(200,"success","Se las Vacaciones por Empleado");
                } else {
                    $this->response(422, "error", "The property is not defined");
                }
                break;
            case 'imagenrotar':
                $obj    = json_decode(file_get_contents('php://input'));
                $objArr = (array) $obj;
                if (empty($objArr)) {
                    $this->response(422, "error", "Nothing to add. Check json");
                } else if (isset($obj->IdEmpleado)) {
                    $db = new ImagenesDB();
                    echo $db->RotarImagen($obj->IdEmpleado, $obj->Rotacion);
                    //$this->response(200,"success","Se las Vacaciones por Empleado");
                } else {
                    $this->response(422, "error", "The property is not defined");
                }
                break;
            case 'recortarimagen':
                $obj    = json_decode(file_get_contents('php://input'));
                $objArr = (array) $obj;
                if (empty($objArr)) {
                    $this->response(422, "error", "Nothing to add. Check json");
                } else if (isset($obj->IdEmpleado)) {
                    $db = new ImagenesDB();
                    echo $db->CropImagen($obj->IdEmpleado);
                    //$this->response(200,"success","Se las Vacaciones por Empleado");
                } else {
                    $this->response(422, "error", "The property is not defined");
                }
                break;
            case 'nada':
                //echo 'Multiple resultado de consulta con accion distinta';
                break;
            default:
                $r = $this->response(400);
                break;
        }
    }



    public function API()
    {
        header('Content-Type: application/JSON');
        $method  = $_SERVER['REQUEST_METHOD'];
        $funcion = $_REQUEST['funcion'];
        switch ($method) {
            case 'GET': //consulta
                //$this->ObtenerTodosAccionesRHConsultas();
                break;
            case 'POST': //inserta
              
                if ($funcion == "actualizar") {
                    $this->OpcionesImagenActualizar();
                }

                break;
            case 'PUT': //actualiza
                echo 'METODO NO SOPORTADO';
                $this->response(405);
                break;
            case 'DELETE': //elimina
                echo 'METODO NO SOPORTADO';
                $this->response(405);
                break;
            default: //metodo NO soportado
                echo 'METODO NO SOPORTADO';
                $this->response(405);
                break;
        }
    }

    public function response($code = 200, $status = "", $message = "", $resultado = "")
    {
        http_response_code($code);
        if (!empty($status) && !empty($message)) {
            $response = array("status" => $status, "message" => $message, "resultado" => $resultado);
            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {
            $status   = "error";
            $message  = "You bad request, try again xD!";
            $response = array("status" => $status, "message" => $message, "codigo" => $code);
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }

    public function numberDays($startDate, $endDate)
    {
        $startDate = $this->formatDate($startDate);
        $endDate   = $this->formatDate($endDate);

        $startTimeStamp = strtotime($startDate);
        $endTimeStamp   = strtotime($endDate);
        $timeDiff       = abs($endTimeStamp - $startTimeStamp);
        $numberDays     = $timeDiff / 86400; // 86400 seconds in one day
        $numberDays     = intval($numberDays);
        $numberDays     = $numberDays + 1;
        return $numberDays;
    }

    public function formatDate($str)
    {
        $date = preg_replace('/[^A-Za-z0-9\-]/', '-', $str);
        $date = date("Y-m-d", strtotime($date));
        return $date;
    }

} //end class