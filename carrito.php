<?php
session_start();

$mensaje = "";

if(isset($_POST["btnAccion"])){

    switch($_POST["btnAccion"]){

        case 'Agregar':

                if(is_numeric( openssl_decrypt($_POST["id"],COD,KEY))){

                    $ID = openssl_decrypt($_POST["id"],COD,KEY);

                    $mensaje .= "OK ID correcto".$ID."</br> ";
                }else{
                    $mensaje .= "UPS... ID incorrecto </br>";break;
                }

                if(is_string( openssl_decrypt($_POST["nombre"],COD,KEY))){

                    $NOMBRE = openssl_decrypt($_POST["nombre"],COD,KEY);

                    $mensaje .= "OK nombre correcto ".$NOMBRE."</br>";
                }else{
                    $mensaje .= "UPS... ID incorrecto </br>";break;
                }

                if(is_numeric( openssl_decrypt($_POST["precio"],COD,KEY))){

                    $PRECIO = openssl_decrypt($_POST["precio"],COD,KEY);

                    $mensaje .= "OK precio correcto".$PRECIO."</br>";
                }else{
                    $mensaje .= "UPS... precio incorrecto </br>";break;
                }

                if(is_numeric( openssl_decrypt($_POST["cantidad"],COD,KEY))){

                    $CANTIDAD = openssl_decrypt($_POST["cantidad"],COD,KEY);

                    $mensaje .= "OK cantidad correcta ".$CANTIDAD."</br>";
                }else{
                    $mensaje .= "UPS... cantidad incorrecta </br>";
                    break;
                }

                if(!isset($_SESSION["CARRITO"])){
                    $producto = array(
                            'ID'=>$ID,
                            'NOMBRE'=>$NOMBRE,
                            'CANTIDAD'=>$CANTIDAD,
                            'PRECIO'=>$PRECIO
                    );

                    /* otro codigo 

                    $_SESSION["CARRITO"][$ID]=$producto;*/

                    //codigo

                    $_SESSION["CARRITO"][0] = $producto;
                    $mensaje = "Producto agregado al carrito";

                }else{

                  /*  if(array_key_exists($ID,$_SESSION["CARRITO"])){
                        $_SESSION["CARRITO"][$ID]["CANTIDAD"] += 1; 
                    }else{
                        
                        $producto = array(
                            'ID'=>$ID,
                            'NOMBRE'=>$NOMBRE,
                            'CANTIDAD'=>$CANTIDAD,
                            'PRECIO'=>$PRECIO
                    );
                    $_SESSION["CARRITO"][$ID]=$producto;

                    }*/

                    //otro codigo

                    $idProductos = array_column($_SESSION["CARRITO"],"ID");
                    

                    if(in_array($ID,$idProductos)){
                            echo "<script>alert('El producto ya ha sido seleccionado')</script>";
                            $mensaje = "";
                    }else{


                    $numeroProductos= count($_SESSION["CARRITO"]);

                    $producto = array(
                        'ID'=>$ID,
                        'NOMBRE'=>$NOMBRE,
                        'CANTIDAD'=>$CANTIDAD,
                        'PRECIO'=>$PRECIO
                );

                $_SESSION["CARRITO"][$numeroProductos]=$producto;

                $mensaje = "Producto agregado al carrito";
            }
                }

              //  $mensaje = print_r($_SESSION["CARRITO"],true);
           

        break;

        case "Eliminar":

            if(is_numeric( openssl_decrypt($_POST["id"],COD,KEY))){

                $ID = openssl_decrypt($_POST["id"],COD,KEY);

                
                foreach($_SESSION["CARRITO"] as $indice=>$producto){

                    if($producto["ID"]==$ID){
                        unset($_SESSION["CARRITO"][$indice]);
                        $_SESSION['CARRITO']=array_values($_SESSION["CARRITO"]); 
                        echo "<script>alert('Elemento borrado')</script>";
                    }

                }

            }else{
                $mensaje .= "UPS... ID incorrecto </br>";break;
            }

        break;


    }

}