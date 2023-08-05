<?php

include 'global/config.php';
include 'global/conexion.php';
include 'carrito.php';
include 'templates/cabecera.php'

?>

  <?php if($mensaje != ""){ ?>
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje?>
        <a href="mostrarCarrito.php" class="nav-link badge bg-success text-white">Ver Carrito</a>
    </div>

<?php }?>
  <div class="row">

    <?php 
    $sentencia = $pdo->prepare("SELECT * FROM tblproductos");
    $sentencia->execute();
    $listaProductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    //print_r($listaProductos);
    ?>

    <?php
    
      foreach($listaProductos as $producto){

    ?>

      <div class="col-3">
      <div class="card">
        <img  title="<?php echo $producto["nombre"]?>" data-bs-content="<?php echo $producto["descripcion"]?>" alt="<?php echo $producto["nombre"]?>" data-bs-toggle="popover" data-bs-trigger="hover"class="card-img-top" src="<?php echo $producto["imagen"]?>" height="317px" >
        <div class="card-body">
          <span><?php echo $producto["nombre"]?></span>
          <h5 class="card-title">S/. <?php echo $producto["precio"]?></h5>
          <p class="card-text">Descripción</p>

        <form action="" method="post">

        <input type="hidden" name="id" id="id" value="<?php echo openssl_encrypt($producto["id"],COD,KEY)  ?>">
        <input type="hidden" name="nombre" id="nombre" value="<?php echo openssl_encrypt($producto["nombre"],COD,KEY)?>">
        <input type="hidden" name="precio" id="precio" value="<?php echo openssl_encrypt($producto["precio"],COD,KEY)?>">
        <input type="hidden" name="cantidad" id="cantidad" value="<?php echo openssl_encrypt(1,COD,KEY)?>">

        <button name="btnAccion" value="Agregar" type="submit" class="btn btn-primary">Agregar al Carrito</button>
          
      </form>
        </div>
      </div>

    </div>

    <?php } ?>

   
  </div>

 


<?php include 'templates/pie.php' ?>