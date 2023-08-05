<?php

include 'global/config.php';
include 'global/conexion.php';
include 'carrito.php';
include 'templates/cabecera.php'
?>

<?php

if($_POST){
    $total = 0;

    $SID = session_id();
    $correo = $_POST["email"];

    foreach($_SESSION["CARRITO"] as $indice=>$producto){


        $total += ($producto["PRECIO"] * $producto["CANTIDAD"]); 
    }

    $sentencia = $pdo->prepare("INSERT INTO tblventas(claveTransaccion,paypalDatos,correo,total) VALUES(:claveTransaccion,'',:correo,:total)");
    $sentencia->bindParam(":claveTransaccion",$SID);
    $sentencia->bindParam(":correo",$correo);
    $sentencia->bindParam(":total",$total);
    $sentencia->execute();
    $idVenta=$pdo->lastInsertId();

     foreach($_SESSION["CARRITO"] as $indice=>$producto){

            $sentencia = $pdo->prepare("INSERT INTO tbldetalleventa(idventa,idproducto,preciounitario,cantidad) VALUES(:idventa,:idproducto,:preciounitario,:cantidad)");
     
              $sentencia->bindParam(":idventa",$idVenta);
    $sentencia->bindParam(":idproducto",$producto["ID"]);
    $sentencia->bindParam(":preciounitario",$producto["PRECIO"]);
     $sentencia->bindParam(":cantidad",$producto["CANTIDAD"]);
    $sentencia->execute();


    }



   // echo "<h3>".$total."</h3>";
}

?>

<script src="https://www.paypal.com/sdk/js?client-id=AVo3YO-4mEEOE8wQ66xuhbeZ-EPaq3nMZdNAzt3LbZ6jDjm7gBIy1-7a4zq9xRfUa_EAKMqZCSTqXmF6&currency=MXN"></script>



<div class="jumbotron bg-dark text-white text-center rounded-3 p-3">
    <h1 class="display-4">¡Paso Final!</h1>
    
    <hr class="my-4">
    <p class="lead">Estas a punto de pagar con paypal la cantidad de:
        <h4>S/.  <?php echo (isset($total)) ? number_format($total,2):0?></h4>
    </p>
    <p>Los productos podrán ser descargados una vez que se procese el pago</p><strong>(para aclaraciones: diego@gmail.com)</strong>
    
    <div class="d-flex justify-content-center pt-3">
    <div id="paypal-button-container" ></div>
    </div>

       
<script>
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl)
})
</script>



<script>

  paypal.Buttons({
    style:{
      color:'blue',
      shape: 'pill',
      label: 'pay',
      size: 'responsive'
    },
    createOrder: function(data,actions){
      return actions.order.create({
        purchase_units:[{
          amount:{
            value: <?php echo (isset($total)) ? $total :0?>,
            custom: '<?php echo $SID; ?>#<?php openssl_encrypt($idVenta,COD,KEY); ?>'
            
          },
          description: 'Compra de productos a la tienda por un valor de $ <?php echo number_format($total); ?>',
        reference_id: "<?php echo $SID; ?>#<?php echo openssl_encrypt($idVenta,COD,KEY);?>"
  //paypal en su documentacion nueva no utiliza custom pot que ahora utiliza reference_id, por eso se debe utilizar este capo
        }]
      });
    },
    onApprove: function(data,actions){
          actions.order.capture().then(function(detalles){
                    console.log(detalles);
                    console.log(data);
                    console.log(actions);
                   let paymentid = detalles.purchase_units[0].payments.captures[0].id
                   console.log(paymentid);
                window.location="verificador.php?orderID="+detalles.id+"&paymentID="+paymentid;

            });
          
    },
    onCancel: function(data){
        alert('sigue fundionando?');
        console.log(data.orderID);
    }
  }).render('#paypal-button-container');

  </script>


</div>



<?php include 'templates/pie.php' ?>