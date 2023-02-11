<?php

$inicio = false;
include('./includes/templates/header.php');

// Validar que el id sea un entero
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

// Si no es un entero redireccionar al admin
if (!$id) {
    header('Location: /bienesraices/');
}

// Importar la conexión
require './includes/config/database.php';
$db = conectarDB();

// Consultar para obtener los datos de la propiedad
$query = "SELECT * FROM propiedades WHERE id = $id";

// Obtener los datos de la propiedad
$resultado = mysqli_query($db, $query);

// Validar que la propiedad exista
if (!$resultado -> num_rows) {
    header('Location: /bienesraices/');
}

?>

<main class="contenedor seccion contenido-centrado">

    <?php while ($propiedad = mysqli_fetch_assoc($resultado)) : ?>

        <h1>Casa en Venta frente al bosque</h1>

        <img loading="lazy" src="/bienesraices/imagenes/<?php echo $propiedad['imagen']; ?>" alt="Propiedad">

        <div class="resumen-propiedad">

            <p class="precio">$<?php echo $propiedad['precio']; ?></p>

            <ul class="iconos-caracteristicas">
                <li>
                    <img class="icono" loading="lazy" src="build/img/icono_wc.svg" alt="icono wc">
                    <p><?php echo $propiedad['wc']; ?></p>
                </li>
                <li>
                    <img class="icono" loading="lazy" src="build/img/icono_estacionamiento.svg" alt="icono estacionamiento">
                    <p><?php echo $propiedad['estacionamiento']; ?></p>
                </li>
                <li>
                    <img class="icono" loading="lazy" src="build/img/icono_dormitorio.svg" alt="icono habitaciones">
                    <p><?php echo $propiedad['habitaciones']; ?></p>
                </li>
            </ul>

            <p><?php echo $propiedad['descripcion']; ?></p>

        </div>

    <?php endwhile; ?>

</main>

<?php

// Cerrar la conexión
mysqli_close($db);

include('./includes/templates/footer.php');

?>