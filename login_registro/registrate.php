<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header('location: index.php');
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = filter_var(strtolower($_POST['usuario']), FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    $errores = '';

    if (empty($usuario) or empty($password) or empty($password2)) {
        $errores .= '<li>rellena todos los datos correctamente</li>';
    }else{
        try {
            $conexion = new PDO('mysql:host=localhost;dbname=curso_login', 'root', '');
        } catch (PDOException $e) {
            echo "error: " . $e->getMessage();
        }

        $statement =$conexion -> prepare('select * from usuarios where usuario= :usuario limit 1');
        $statement->execute(array(':usuario'=>$usuario));
        $resultado = $statement->fetch();

        if ($resultado != false) {
            $errores .= '<li>el nombre de usuario ya exisite</li>';
        }

        $password = hash('sha512', $password);
        $password2 = hash('sha512', $password2);

        if ($password != $password2) {
            $errores .= '<li>las contraseñas no son iguales</li>';
        }
    }

    if ($errores == '') {
        $statement = $conexion->prepare('insert into usuarios (id, usuario, pass) values (null, :usuario, :pass)');
        $statement->execute(array(':usuario' => $usuario, ':pass' => $password));
        header('location: login.php');
    }

}

require 'views/registrate_view.php';