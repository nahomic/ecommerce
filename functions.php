<?php
require "admin/conexion.php";
function mostrarMensajes($rta){
    switch($rta){
        case "0x001":
            $mensaje="<span style=color:red>Nombre inválido</span>";
        break;
        case "0x002":
            $mensaje="<span style=color:red>Email inválido</span>";
        break;
        case "0x003":
            $mensaje="<span style=color:red>Mensaje inválido</span>";
        break;
        case "0x004":
            $mensaje="<span style=color:green>Consulta enviada correctamente</span>";
        break;
        case "0x005":
            $mensaje="<span style=color:red>Correo no enviado</span>";
        break;
        case "0x006":
            $mensaje="<span style=color:green>Producto eliminado correctamente</span>";
        break;
        case "0x007":
            $mensaje="<span style=color:red>No puedo borrar el producto</span>";
        break;
        case "0x008":
            $mensaje="<span style=color:green>Producto agregado correctamente</span>";
        break;
        case "0x009":
            $mensaje="<span style=color:red>No puedo agregar el producto</span>";
        break;
        case "0x010":
            $mensaje="<span style=color:green>Producto editado correctamente</span>";
        break;
        case "0x011":
            $mensaje="<span style=color:red>No puedo editar el producto</span>";
        break;
        case "0x012":
            $mensaje="<span style=color:green>Producto cambia de estado</span>";
        break;
        case "0x013":
            $mensaje="<span style=color:red>No puedo modificar el estado</span>";
        break;
        case "0x014":
            $mensaje="<span style=color:red>No puede tener claves diferentes</span>";
        break;
        case "0x015":
            $mensaje="<span style=color:green>Usuario registrado correctamente</span>";
        break;
        case "0x016":
            $mensaje="<span style=color:red>No puedo registrar el usuario</span>";
        break;
        case "0x017":
            $mensaje="<span style=color:red>Error usuario inactivo, ingrese al correo blabla</span>";
        break;
        case "0x018":
            $mensaje="<span style=color:red>Error datos de acceso incorrecto</span>";
        break;
        case "0x019":
            $mensaje="<span style=color:red>Error No existe el usuario en el sistema</span>";
        break;
        case "0x020":
            $mensaje="<span style=color:red>Error no puedo ejecutar su solicitud</span>";
        break;
        case "0x021":
            $mensaje="<span style=color:red>Error primero debes acceder al sistema</span>";
        break;
    }
    return $mensaje;
}

function CargarPagina​​($page){
    try{
        $page=$page.".php";
        if(file_exists($page)){
        include $page; 
        }else{
            include "404.php";
        }
        
    }catch(PDOException $e){
        echo "ERROR ".$e->getMessage();
    }  
    
}

function mostrarProductos(){
    $archivo="listadoProductos.csv";
		if($file=fopen($archivo,'r')){
			while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
			?>
	<div class="product-grid">
			<div class="content_box">
				<a href="./?page=producto">
					<div class="left-grid-view grid-view-left">
						<img src="images/productos/<?php echo $data[0]; ?>.jpg" class="img-responsive watch-right" alt=""/>
					</div>
				</a>
				<h4><a href="#"><?php echo $data[1]; ?></a></h4>
				<p><?php echo $data[5]; ?></p>
				<span>$<?php echo $data[2]; ?></span>
			</div>
		</div>
			<?php	
			}
			fclose($file);
		}else{
			echo "error";
		}
}


function borrarProducto($idProducto,$imagen){
    
        global $conexion;
    /*con : variable
    $sql="delete from productos where idProducto=:idProducto";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(":idProducto",$idProducto,PDO::PARAM_INT);*/

    //con ? variable
    unlink("../images/productos/".$imagen);
    $sql="delete from productos where idProducto=?";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$idProducto,PDO::PARAM_INT);
    if($producto->execute()){
        $rta="0x006";
    }else{
        $rta="0x007";
    }
    return $rta;
    
}


function crearProducto($nombre,$precio,$marca,$categoria,$presentacion,$stock,$imagen){
    global $conexion;
    $imagenName=$imagen["name"];
    $imagenTmp=$imagen['tmp_name'];
    $uploads_dir="../images/productos/";
    //guardar la imagen al directorio
    move_uploaded_file($imagenTmp, "$uploads_dir/$imagenName");
    $sql="INSERT INTO productos (Nombre,Precio,Marca,Categoria,Presentacion,Stock,Imagen) VALUES (?,?,?,?,?,?,?)";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$nombre,PDO::PARAM_STR);
    $producto->bindParam(2,$precio,PDO::PARAM_STR);
    $producto->bindParam(3,$marca,PDO::PARAM_INT);
    $producto->bindParam(4,$categoria,PDO::PARAM_INT);
    $producto->bindParam(5,$presentacion,PDO::PARAM_STR);
    $producto->bindParam(6,$stock,PDO::PARAM_INT);
    $producto->bindParam(7,$imagenName,PDO::PARAM_STR);
    if($producto->execute()){
        $rta="0x008";
    }else{
        $rta="0x009";
    }
    return $rta;
}


function obtenerProducto($idProducto){
    global $conexion;
    $sql="SELECT *FROM productos WHERE idProducto=?";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$idProducto,PDO::PARAM_INT);
    if($producto->execute()){
        $producto=$producto->fetch();
    }
    return $producto;
}

function obtenerMarcaProducto($idMarca){
    global $conexion;
    $sql="SELECT *FROM marcas WHERE idMarca=?";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$idMarca,PDO::PARAM_INT);
    if($producto->execute()){
        $producto=$producto->fetch();
    }
    return $producto;
}

function obtenerCategoriaProducto($idCategoria){
    global $conexion;
    $sql="SELECT *FROM categorias WHERE idCategoria=?";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$idCategoria,PDO::PARAM_INT);
    if($producto->execute()){
        $producto=$producto->fetch();
    }
    return $producto;
}

function actualizarProducto($idProducto,$nombre,$precio,$marca,$categoria,$presentacion,$stock,$imagen,$imagenActual){
    global $conexion;
    $imagenName=$imagen["name"];
    if($imagenName==""){
        $imagenName=$imagenActual;
    }else{
        $imagenTmp=$imagen['tmp_name'];
        $uploads_dir="../images/productos/";
        //guardar la imagen al directorio
        move_uploaded_file($imagenTmp, "$uploads_dir/$imagenName");
        $imagenActual=$uploads_dir.$imagenActual;
        unlink($imagenActual);
    }
    
    $sql="UPDATE productos SET Nombre=?,Precio=?,Marca=?,Categoria=?,Presentacion=?,Stock=?,Imagen=? WHERE idProducto=?";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$nombre,PDO::PARAM_STR);
    $producto->bindParam(2,$precio,PDO::PARAM_STR);
    $producto->bindParam(3,$marca,PDO::PARAM_INT);
    $producto->bindParam(4,$categoria,PDO::PARAM_INT);
    $producto->bindParam(5,$presentacion,PDO::PARAM_STR);
    $producto->bindParam(6,$stock,PDO::PARAM_INT);
    $producto->bindParam(7,$imagenName,PDO::PARAM_STR);
    $producto->bindParam(8,$idProducto,PDO::PARAM_INT);
    if($producto->execute()){
        $rta="0x010";
    }else{
        $rta="0x011";
    }
    return $rta;
    
}

function actualizarEstado($idProducto,$estado){
    global $conexion;
    $sql="UPDATE productos SET Estado=? WHERE idProducto=?";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$estado,PDO::PARAM_INT);
    $producto->bindParam(2,$idProducto,PDO::PARAM_INT);
    if($producto->execute()){
        $rta="0x012";
    }else{
        $rta="0x013";
    }
    return $rta;
}



function crearUsuario($nombre,$apellido,$email,$usuario,$clave){
    global $conexion;
    // $opciones = [
    //     'cost' => 12,
    // ];
    // password_hash($clave,PASSWORD_BCRYPT, $opciones);
    $clave=password_hash($clave,PASSWORD_DEFAULT);
    $codigo="abcdefghi*jklmnopqrstuvwz%yzABCDEFGHIJKLMN+OPQRSTUVWXYZ0123456789";
    $codigo=md5(str_shuffle($codigo));
    //ENVIAR CORREO
    $para="castilloaustry@gmail.com";
    $asunto="Activar cuenta blabla";
    $cabecera = "From:" . $para . "\r\n";
	$cabecera.= "MIME-Version: 1.0\r\n";
	$cabecera.= "Content-Type: text/html; charset=UTF-8\r\n";
    $cuerpo="<img src=https://es.freelogodesign.org/Content/img/logo-samples/flooop.png style=width:30%>";
    $cuerpo.="<h1 style=color:pink>Activación de cuenta</h1>";
    $cuerpo.="<b>Click en el siguiente enlace para activar su cuenta</b><br>";
    $cuerpo.="<a style='background-color:lightpink;color:tomato;padding:20px;text-decoration:none;' href=https://miweb.com/activacionDeUsuario.php?correo".$email."&codigo=".$codigo."&estado=1>Activar mi cuenta</a><br><br>Chaito blabla...";    
    mail($para,$asunto,$cuerpo,$cabecera);
    $sql="INSERT INTO usuarios (Nombre,Apellido,Email, Usuario,Clave, Codigo) VALUES (?,?,?,?,?,?)";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$nombre,PDO::PARAM_STR);
    $producto->bindParam(2,$apellido,PDO::PARAM_STR);
    $producto->bindParam(3,$email,PDO::PARAM_STR);
    $producto->bindParam(4,$usuario,PDO::PARAM_STR);
    $producto->bindParam(5,$clave,PDO::PARAM_STR);
    $producto->bindParam(6,$codigo,PDO::PARAM_STR);
    if($producto->execute()){
        $rta="0x015";
    }else{
        $rta="0x016";
    }
    return $rta;
}


function accederUsuario($usuario,$clave){
    global $conexion;
    $sql="SELECT *FROM usuarios WHERE Usuario=?";
    $producto=$conexion->prepare($sql);
    $producto->bindParam(1,$usuario,PDO::PARAM_STR);
    if($producto->execute()){
        $dato=$producto->fetch();
        if($dato){
            if($dato['Estado']==0){
                        header("location:./?page=ingreso&rta=0x017");
                    }else{
                    $claveC=$dato['Clave'];
                    $usuarioC=$dato['Usuario'];
                    if(password_verify($clave,$claveC)){
                        session_start();//comentarios
                        $_SESSION['usuario']=$usuarioC;
                        $_SESSION['nombre']=$dato['Nombre'];
                        header("location:./admin/?page=listado");
                        }else{
                            header("location:./?page=ingreso&rta=0x018");
                        }
                    }
        }else{
            header("location:./?page=ingreso&rta=0x019");
        }
        
    }else{
        header("location:./?page=ingreso&rta=0x020");
    }
}