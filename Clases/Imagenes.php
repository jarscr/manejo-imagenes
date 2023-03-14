<?php
/**
 * Description of Imagenes
 *
 * @author José Alfredo Rodriguez Siles <arodriguez@mktechstore.com>
 */
class ImagenesDB {
    protected $mysqli;
    
    /**
     * Constructor de clases
     */
    public function __construct() {
        try{
            //conexión a base de datos
            $this->mysqli = new mysqli(_MYSQL_SERVER, _MYSQL_USER, _MYSQL_PASSWORD, _MYSQL_DATABASE);
            $this->mysqli->query("SET NAMES 'utf8';");
        }catch (mysqli_sql_exception $e){
            //Si no se puede realizar la conexión
            http_response_code(500);
            exit;
        }
    }

	public function base64_to_jpeg($base64_string, $output_file) {
	    // open the output file for writing
	    $ifp = fopen( $output_file, 'wb' ); 

	    // split the string on commas
	    // $data[ 0 ] == "data:image/png;base64"
	    // $data[ 1 ] == <actual base64 string>
	    $data = explode( ',', $base64_string );

	    // we could add validation here with ensuring count( $data ) > 1
	    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

	    // clean up the file resource
	    fclose( $ifp ); 

	    return $output_file; 
	}

	public function jpeg_to_base64($input_file){
		$type = pathinfo($input_file, PATHINFO_EXTENSION);
		$data = file_get_contents($input_file);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}

	public function MakeThumb($thumb_target = '', $width = 60,$height = 60,$SetFileName = false, $quality = 80)
	            {
	                $thumb_img  =   imagecreatefromjpeg($thumb_target);

	                // size from
	                list($w, $h) = getimagesize($thumb_target);

	                if($w > $h) {
	                        $new_height =   $height;
	                        $new_width  =   floor($w * ($new_height / $h));
	                        $crop_x     =   ceil(($w - $h) / 2);
	                        $crop_y     =   0;
	                }
	                else {
	                        $new_width  =   $width;
	                        $new_height =   floor( $h * ( $new_width / $w ));
	                        $crop_x     =   0;
	                        $crop_y     =   ceil(($h - $w) / 2);
	                }

	                // I think this is where you are mainly going wrong
	                $tmp_img = imagecreatetruecolor($width,$height);

	                imagecopyresampled($tmp_img, $thumb_img, 0, 0, $crop_x, $crop_y, $new_width, $new_height, $w, $h);

	                if($SetFileName == false) {
	                        header('Content-Type: image/jpeg');
	                        imagejpeg($tmp_img);
	                }
	                else
	                    imagejpeg($tmp_img,$SetFileName,$quality);

	                imagedestroy($tmp_img);
	            }

	public function resample($jpgFile, $thumbFile, $width, $orientation) {
	    // Get new dimensions
	    list($width_orig, $height_orig) = getimagesize($jpgFile);
	    $height = (int) (($width / $width_orig) * $height_orig);
	    // Resample
	    $image_p = imagecreatetruecolor($width, $height);
	    $image   = imagecreatefromjpeg($jpgFile);
	    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	    // Fix Orientation
	    switch($orientation) {
	        case 1:
	            $image_p = imagerotate($image_p, 180, 0);
	            break;
            case 2:
	            $image_p = imagerotate($image_p, -180, 0);
	            break;
	        case 3:
	            $image_p = imagerotate($image_p, -90, 0);
	            break;
	        case 4:
	            $image_p = imagerotate($image_p, 90, 0);
	            break;
            case 5:
	            $image_p = imagerotate($image_p, 360, 0);
	            break;
            case 6:
	            $image_p = imagerotate($image_p, -360, 0);
	            break;
	    }
	    // Output
	    imagejpeg($image_p, $thumbFile, 90);
	}

	public function muestreo($jpgFile, $thumbFile, $width) {
	    // Get new dimensions
	    list($width_orig, $height_orig) = getimagesize($jpgFile);
	    $height = (int) (($width / $width_orig) * $height_orig);
	    // Resample
	    $image_p = imagecreatetruecolor($width, $height);
	    $image   = imagecreatefromjpeg($jpgFile);
	    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	    // Output
	    imagejpeg($image_p, $thumbFile, 90);
	}

	public function EditarImagen($IdEmpleado,$Rotar){
		$Ruta = realpath(dirname(__FILE__));
		$stmt =$this->mysqli->query("SELECT `Cedula`,`Foto` FROM `Empleado` WHERE `IdEmpleado` = '".$IdEmpleado."'");
		$Empleado = $stmt->fetch_array(MYSQLI_ASSOC);

		$tn_w = 300;
		$Imagen = $Ruta.'/tmp/'.$Empleado['Cedula'].'.jpg';
		$ImagenThumb = $Ruta.'/tmp/'.$Empleado['Cedula'].'-thumb.jpg';
		$thumb_target   =  $Ruta.'/tmp/'.$Empleado['Cedula'].'-150x150.jpg';

		$this->base64_to_jpeg($Empleado['Foto'],$Imagen);
		$this->resample($Imagen,$ImagenThumb,$tn_w,$Rotar);
		$this->MakeThumb($ImagenThumb,150,150,$thumb_target);
		$imagen64 = $this->jpeg_to_base64($thumb_target);
 		$this->mysqli->query("UPDATE Empleado SET `Foto`='".$imagen64."' WHERE `IdEmpleado` = '".$IdEmpleado."'");
 		@unlink($Imagen);
 		@unlink($ImagenThumb);
 		@unlink($thumb_target);
		return  json_encode(array('result'=>'success','Foto'=>$imagen64));
	}

	public function ImagenesCapture($IdEmpleado,$Foto){
		$Ruta = realpath(dirname(__FILE__));


		$tn_w = 300;
		$Imagen = $Ruta.'/tmp/cap-'.$IdEmpleado.'.jpg';
		$ImagenThumb = $Ruta.'/tmp/cap-'.$IdEmpleado.'-thumb.jpg';
		$thumb_target   =  $Ruta.'/tmp/cap-'.$IdEmpleado.'-150x150.jpg';

		$this->base64_to_jpeg($Foto,$Imagen);
		$this->muestreo($Imagen,$ImagenThumb,$tn_w,'0');
		$this->MakeThumb($ImagenThumb,150,150,$thumb_target);
		$imagen64 = $this->jpeg_to_base64($thumb_target);
 		$this->mysqli->query("UPDATE Empleado SET `Foto`='".$imagen64."' WHERE `IdEmpleado` = '".$IdEmpleado."'");
 		@unlink($Imagen);
 		@unlink($ImagenThumb);
 		@unlink($thumb_target);
		return  json_encode(array('result'=>'success','Foto'=>$imagen64));
	}

	public function CropImagen($IdEmpleado){
		$Ruta = realpath(dirname(__FILE__));
		$stmt =$this->mysqli->query("SELECT `Cedula`,`Foto` FROM `Empleado` WHERE `IdEmpleado` = '".$IdEmpleado."'");
		$Empleado = $stmt->fetch_array(MYSQLI_ASSOC);

		$tn_w = 300;
		$Imagen = $Ruta.'/tmp/'.$Empleado['Cedula'].'.jpg';
		$ImagenThumb = $Ruta.'/tmp/'.$Empleado['Cedula'].'-thumb.jpg';
		$thumb_target   =  $Ruta.'/tmp/'.$Empleado['Cedula'].'-150x150.jpg';

		$this->base64_to_jpeg($Empleado['Foto'],$Imagen);
		$this->muestreo($Imagen,$ImagenThumb,$tn_w,'0');
		$this->MakeThumb($ImagenThumb,150,150,$thumb_target);
		$imagen64 = $this->jpeg_to_base64($thumb_target);
 		$this->mysqli->query("UPDATE Empleado SET `Foto`='".$imagen64."' WHERE `IdEmpleado` = '".$IdEmpleado."'");
 		@unlink($Imagen);
 		@unlink($ImagenThumb);
 		@unlink($thumb_target);
		return  json_encode(array('result'=>'success','Foto'=>$imagen64));
	}
	public function RotarImagen($IdEmpleado,$Rotar){
		$Ruta = realpath(dirname(__FILE__));
		$stmt =$this->mysqli->query("SELECT `Cedula`,`Foto` FROM `Empleado` WHERE `IdEmpleado` = '".$IdEmpleado."'");
		$Empleado = $stmt->fetch_array(MYSQLI_ASSOC);

		$tn_w = 300;
		$Imagen = $Ruta.'/tmp/'.$Empleado['Cedula'].'.jpg';
		$ImagenThumb = $Ruta.'/tmp/'.$Empleado['Cedula'].'-thumb.jpg';
		$thumb_target   =  $Ruta.'/tmp/'.$Empleado['Cedula'].'-150x150.jpg';

		$this->base64_to_jpeg($Empleado['Foto'],$Imagen);
		$this->resample($Imagen,$ImagenThumb,$tn_w,$Rotar);
		$this->MakeThumb($ImagenThumb,150,150,$thumb_target);
		$imagen64 = $this->jpeg_to_base64($thumb_target);
 		$this->mysqli->query("UPDATE Empleado SET `Foto`='".$imagen64."' WHERE `IdEmpleado` = '".$IdEmpleado."'");
 		@unlink($Imagen);
 		@unlink($ImagenThumb);
 		@unlink($thumb_target);
		return  json_encode(array('result'=>'success','Foto'=>$imagen64));
	}

}