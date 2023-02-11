<?php

session_start();

// Validar la sesion
if($_SESSION){

    header('Location: /bienesraices/admin/');
    
}

// Importar la conexion
require './includes/config/database.php';
$db = conectarDB();

$errores = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $email = mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));
    $password = mysqli_real_escape_string($db, $_POST['password']);

    if(!$email){
        $errores[] = "El email es obligatorio o no es válido";
    }

    if(!$password){
        $errores[] = "El password es obligatorio";
    }

    if(empty($errores)){

        // Revisar si el usuario existe
        $query = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query($db, $query);

        if($resultado->num_rows){
            
            $usuario = mysqli_fetch_assoc($resultado);

            // Verificar si el password es correcto o no
            $auth = password_verify($password, $usuario['password']);

            if($auth){

                // El usuario esta autenticado
                session_start();

                // Llenar el arreglo de la sesion
                $_SESSION['usuario'] = $usuario['email'];
                $_SESSION['login'] = true;

                header('Location: /bienesraices/admin/');

            } else {

                $errores[] = "El password es incorrecto";

            }

        } else {

            $errores[] = "El usuario no existe";

        }

    }

}

$inicio = false;
include('./includes/templates/header.php');

?>

<main class="contenedor seccion contenido-centrado">

    <h1>Iniciar sesión</h1>

    <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
    <?php endforeach; ?>

    <form method="POST" class="formulario">   

        <fieldset>

            <legend>Email y contraseña</legend>

            <label for="email">Email</label>
            <input type="email" placeholder="example@gmail.com" name="email" id="email">

            <label for="password">Contraseña</label>
            <input type="password" placeholder="********" name="password" id="password">

        </fieldset>

        <input type="submit" value="Iniciar sesión" class="boton boton-verde">

    </form>

</main>

<?php

mysqli_close($db);
include('./includes/templates/footer.php');

?>