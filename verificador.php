<?php

include 'global/config.php';
include 'global/conexion.php';
include 'carrito.php';
include 'templates/cabecera.php'
?>

<?php


//print_r($_GET);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, LINKAPI."/v1/oauth2/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_USERPWD, CLIENTID.":".SECRET);
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$err = curl_error($ch);

//print_r($result);

$objRespuesta = json_decode($result);

$AccessToken = $objRespuesta->access_token;

//print_r($AccessToken);

$venta = curl_init(LINKAPI."/v2/checkout/orders/".$_GET["orderID"]);
curl_setopt($venta,CURLOPT_HTTPHEADER,array("Content-Type: application/json","Authorization: Bearer ".$AccessToken));

curl_setopt($venta,CURLOPT_RETURNTRANSFER, TRUE);

curl_setopt($venta,CURLOPT_POST, FALSE);          

curl_setopt($venta,CURLOPT_SSL_VERIFYPEER, FALSE);

echo "<br>";
echo "<br>";

echo "<br>";

$RespuestaVenta = curl_exec($venta);

$objDatosTransaccion=json_decode($RespuestaVenta);

//print_r($objDatosTransaccion->status); // imprimimos los datos del objeto que recibe los detalles de la venta

$status=$objDatosTransaccion->status;
$email=$objDatosTransaccion->payer->email_address;
$total=$objDatosTransaccion->purchase_units[0]->amount->value;
$currency=$objDatosTransaccion->purchase_units[0]->amount->currency_code;
$reference_id=$objDatosTransaccion->purchase_units[0]->reference_id;

/* ---- aqui imprimimos los datos recuperados del objeto que paypal nos retorna -----
echo $status."<br>";
echo $email."<br>";
echo $total."<br>";
echo $currency."<br>";
echo $reference_id."<br>";*/

$clave = explode("#",$reference_id);
$SID=$clave[0];
$claveVenta=openssl_decrypt($clave[1],COD,KEY);

//print_r($claveVenta);

curl_close($venta);
curl_close($ch);

if($status == "COMPLETED"){
        $mensajePaypal= "<h3>Pago aprobado</h3>";
        $sentencia = $pdo->prepare("UPDATE tblventas set paypalDatos = :paypalDatos, status = 'aprobado' WHERE id=:id");
        $sentencia->bindParam(":id",$claveVenta);
        $sentencia->bindParam(":paypalDatos",$RespuestaVenta);
        $sentencia->execute();

        $sentencia = $pdo->prepare("UPDATE tblventas set status = 'completo' WHERE claveTransaccion=:claveTransaccion and total=:total and id=:id");
        $sentencia->bindParam(":claveTransaccion",$SID);
        $sentencia->bindParam(":total",$total);
        $sentencia->bindParam(":id",$claveVenta);
       
        $sentencia->execute();

        $completado = $sentencia->rowCount();
        session_destroy();

}else{
    $mensajePaypal= "<h3>Hay un problema con el pago</h3>";
}


//echo $mensajePaypal;
?>
<div class="jumbotron bg-dark text-white text-center rounded-3 p-3">
    <h1 class="display-4">¡Listo!</h1>
    
    <hr class="my-4">
    <p class="lead">
        <?php echo $mensajePaypal?>
    </p>
 
        <?php
        
            if($completado>=1){


                $sentencia = $pdo->prepare("SELECT * FROM tbldetalleventa,tblproductos WHERE tbldetalleventa.idproducto=tblproductos.id AND tbldetalleventa.idventa = :id");
               
                $sentencia->bindParam(":id",$claveVenta);
               
                $sentencia->execute();

                $listaProductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
               // print_r($listaProductos);


                

            }

        ?>

    <div class="row">

            <?php foreach($listaProductos as $producto){ ?>

                <div class="col-3">
                            <div class="card">
                                <img class="card-img-top" src="<?php echo $producto["imagen"]?>" height="317px" alt="<?php echo $producto["nombre"]?>">
                                <div class="card-body">
                                    <p class="card-text text-dark"><?php echo $producto["nombre"]?></p>


                                    <?php if($producto["descargado"]<DESCARGASPERMITIDAS){ ?>
                                    <form action="descargas.php" method="post">


                                    <input type="hidden" name="idventa" id="" value="<?php echo  openssl_encrypt($claveVenta,COD,KEY)?>">
                                    <input type="hidden" name="idproducto" id="" value="<?php echo  openssl_encrypt($producto["idproducto"],COD,KEY) ?>">


                                    <button class="btn btn-success" type="submit">Descargar</button>



                                    </form>

                                    <?php }else{?>
                                        
                                        <button class="btn btn-success" type="button" disabled >Descargar</button>

                                        <?php }
                                        ?>
                                </div>
                            </div>
                </div>

                <?php } ?>
    </div> 

</div>     



<?php
//print_r($objDatosTransaccion->purchase_units[0]->reference_id); //forma de imprimir el detalle de una variable que esta dentro de un array del objeto
//print_r("<br> aea");


//$venta2 = curl_init(LINKAPI."/v2/payments/captures/".$_GET["paymentID"]);
//curl_setopt($venta2, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Authorization: Bearer '.$AccessToken));

//$result2a = curl_exec($venta2);

//print_r($result2a);









