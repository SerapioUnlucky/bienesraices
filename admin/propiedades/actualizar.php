<?php

session_start();

// Validar la sesion
if(!$_SESSION['login']){

    header('Location: /bienesraices/login.php');
    
}

// Validar que el id sea un entero
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

// Si no es un entero redireccionar al admin
if (!$id) {
    header('Location: /bienesraices/admin/');
}

// Importar la conexión
require '../../includes/config/database.php';
$db = conectarDB();

// Obtener los datos de la propiedad
$consultaPropiedad = "SELECT * FROM propiedades WHERE id = $id";
$resultadoPropiedad = mysqli_query($db, $consultaPropiedad);
$propiedad = mysqli_fetch_assoc($resultadoPropiedad);

//Consultar sobre tabla vendedores
$consulta = "SELECT * FROM vendedores";
$resultado = mysqli_query($db, $consulta);

//Arreglo con mensajes de errores
$errores = [];

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedorId = $propiedad['vendedores_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = mysqli_real_escape_string($db, $_POST['titulo']);
    $precio = mysqli_real_escape_string($db, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
    $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones']);
    $wc = mysqli_real_escape_string($db, $_POST['wc']);
    $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
    $vendedorId = mysqli_real_escape_string($db, $_POST['vendedor']);
    $creado = date('Y/m/d');

    // Asignar files hacia una variable
    $imagen = $_FILES['imagen'];


    if (!$titulo) {
        $errores[] = "Debes añadir un título";
    }

    if (!$precio) {
        $errores[] = "Debes añadir un precio";
    }

    if (strlen($descripcion) < 50) {
        $errores[] = "La descripción es obligatoria y debe tener al menos 50 caracteres";
    }

    if (!$habitaciones) {
        $errores[] = "El número de habitaciones es obligatorio";
    }

    if (!$wc) {
        $errores[] = "El número de baños es obligatorio";
    }

    if (!$estacionamiento) {
        $errores[] = "El número de estacionamientos es obligatorio";
    }

    if (!$vendedorId) {
        $errores[] = "Elige un vendedor";
    }

    // Validar por tamaño (1mb máximo)
    $medida = 1000 * 1000;

    if ($imagen['size'] > $medida) {
        $errores[] = "La imagen es muy pesada";
    }

    if (empty($errores)) {

        // Crear carpeta
        $carpetaImagenes = '../../imagenes/';

        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        $nombreImagen = '';

        if ($imagen['name']) {

            unlink($carpetaImagenes . $propiedad['imagen']);

            // Generar un nombre único
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            // Subida de imagen
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);

        }else{
            $nombreImagen = $propiedad['imagen'];
        }

        // Insertar en la base de datos
        $query = "UPDATE propiedades SET titulo = '$titulo', precio = $precio, imagen = '$nombreImagen', descripcion = '$descripcion', habitaciones = $habitaciones, wc = $wc, estacionamiento = $estacionamiento, vendedores_id =  $vendedorId WHERE id = $id";
        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            header('location: /bienesraices/admin?resultado=2');
        }
    }
}

$inicio = false;
include('../../includes/templates/header.php');

?>

<main class="contenedor seccion">
    <h1>Actualizar propiedad</h1>

    <a href="/bienesraices/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST" enctype="multipart/form-data">

        <fieldset>

            <legend>Información general</legend>

            <label for="titulo">Título:</label>
            <input type="text" id="titulo" placeholder="Título de la propiedad" name="titulo" value=<?php echo $titulo ?>>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" placeholder="Precio de la propiedad" name="precio" value=<?php echo $precio ?>>

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion"><?php echo $descripcion ?></textarea>

        </fieldset>

        <fieldset>

            <legend>Información de la propiedad</legend>

            <label for="habitaciones">Habitaciones:</label>
            <input type="number" id="habitaciones" placeholder="Ej: 3" min="1" max="9" name="habitaciones" value=<?php echo $habitaciones ?>>

            <label for="wc">Baños:</label>
            <input type="number" id="wc" placeholder="Ej: 3" min="1" max="9" name="wc" value=<?php echo $wc ?>>

            <label for="estacionamiento">Estacionamiento:</label>
            <input type="number" id="estacionamiento" placeholder="Ej: 3" min="1" max="9" name="estacionamiento" value=<?php echo $estacionamiento ?>>

        </fieldset>

        <fieldset>

            <legend>Vendedor</legend>

            <select name="vendedor">
                <option value="">-- Seleccione --</option>
                <?php while ($vendedor = mysqli_fetch_assoc($resultado)) : ?>

                    <option <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']; ?>"><?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?></option>

                <?php endwhile; ?>
            </select>

        </fieldset>

        <input type="submit" value="Actualizar propiedad" class="boton boton-verde">

    </form>

</main>

<?php

mysqli_close($db);

include('../../includes/templates/footer.php');

?>