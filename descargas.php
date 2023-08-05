<?php

include 'global/config.php';
include 'global/conexion.php';
include 'carrito.php';


if ($_POST) {
    $idventa = openssl_decrypt($_POST["idventa"], COD, KEY);
    $idproducto = openssl_decrypt($_POST["idproducto"], COD, KEY);

    $sentencia = $pdo->prepare("SELECT * FROM tbldetalleventa WHERE idventa=:idventa and idproducto=:idproducto and descargado <".DESCARGASPERMITIDAS);
    $sentencia->bindParam(":idventa", $idventa);
    $sentencia->bindParam(":idproducto", $idproducto);
    $sentencia->execute();

    $listaproductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    if ($sentencia->rowCount() > 0) {


        $rutaArchivos = "archivos/" . $listaproductos[0]["idproducto"] . ".pdf";

        if (file_exists($rutaArchivos)) {

            $nuevoNombre = $_POST["idventa"] . $_POST["idproducto"] . ".pdf";

            header("Content-Transfer-Encoding: binary");
            header("Content-Type: application/force-download");
            header('Content-Disposition: attachment; filename="' . $nuevoNombre . '"');
            readfile("$rutaArchivos");



            $sentencia = $pdo->prepare("UPDATE tbldetalleventa set descargado=descargado+1 WHERE idventa=:idventa and idproducto = :idproducto");
            $sentencia->bindParam(":idventa", $idventa);
            $sentencia->bindParam("idproducto", $idproducto);
            $sentencia->execute();
        } else {
            echo "no existe el archivo";
        }
    } else {
        include 'templates/cabecera.php';
        echo "<br><br><br><h2>Tus descargas se agotaron</h2>";
        include 'templates/pie.php';
    }
}
