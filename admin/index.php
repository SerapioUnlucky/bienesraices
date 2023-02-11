<?php

session_start();

// Validar la sesion
if(!$_SESSION['login']){

    header('Location: /bienesraices/login.php');
    
}

require ('../includes/config/database.php');
$db = conectarDB();

$query = "SELECT * FROM propiedades";
$resultadoConsulta = mysqli_query($db, $query);

$resultado = $_GET['resultado'] ?? null;

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if($id){

        // Eliminar archivo
        $query = "SELECT imagen FROM propiedades WHERE id = $id";
        $resultado = mysqli_query($db, $query);
        $propiedad = mysqli_fetch_assoc($resultado);

        unlink('../imagenes/' . $propiedad['imagen']);

        // Eliminar la propiedad
        $query = "DELETE FROM propiedades WHERE id = $id";
        $resultado = mysqli_query($db, $query);

        if($resultado){

            header('Location: /bienesraices/admin?resultado=3');

        }

    }

}


$inicio = false;
include ('../includes/templates/header.php');

?>

<main class="contenedor seccion">
    <h1>Administrador de bienes raices</h1>

    <?php if (intval($resultado) === 1) : ?>
        <p class="alerta exito">Anuncio creado correctamente</p>
    <?php elseif (intval($resultado) === 2) : ?>
        <p class="alerta exito">Anuncio actualizado correctamente</p>
    <?php elseif (intval($resultado) === 3) : ?>
        <p class="alerta exito">Anuncio eliminado correctamente</p>
    <?php endif; ?>

    <a href="./propiedades/crear.php" class="boton boton-verde">Registrar propiedad</a>

    <table class="propiedades">

        <thead>

            <tr>

                <th>ID</th>
                <th>Titulo</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Acciones</th>

            </tr> 

        </thead>

        <tbody>

            <?php while($propiedad = mysqli_fetch_assoc($resultadoConsulta)): ?>

                <tr>

                    <td><?php echo $propiedad['id']; ?></td>
                    <td><?php echo $propiedad['titulo']; ?></td>
                    <td><img src="/bienesraices/imagenes/<?php echo $propiedad['imagen']; ?>" alt="Propiedad" class="imagen-tabla"/></td>
                    <td>$<?php echo $propiedad['precio']; ?></td>
                    <td>
                        <a href="./propiedades/actualizar.php?id=<?php echo $propiedad['id']; ?>" class="boton-amarillo-block">Actualizar</a>
                        
                        <form method="POST" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">
                            <input type="submit" value="Eliminar" class="boton-rojo-block">

                        </form>

                    </td>

                </tr>

            <?php endwhile; ?>

        </tbody>

    </table>

</main>

<?php

mysqli_close($db);

include ('../includes/templates/footer.php');

?>