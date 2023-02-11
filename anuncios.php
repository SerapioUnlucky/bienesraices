<?php

$inicio = false;
include ('./includes/templates/header.php');

?>

<main class="contenedor seccion">

    <h2>Casas y Depas en Venta</h2>

    <?php 
    
    $limite = 10;
    include ('./includes/templates/anuncios.php'); 
    
    ?>

</main>

<?php include ('./includes/templates/footer.php'); ?>