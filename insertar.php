<?php
// insertar.php
include("conexion.php"); // Incluye tu archivo de conexión

$mensaje = "";
$tipo_mensaje = "";

// 1. Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Recibir y sanear los datos del formulario
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $rol = isset($_POST['rol']) ? trim($_POST['rol']) : '';
    $control = isset($_POST['control']) ? trim($_POST['control']) : '';

    // Sanear para seguridad (evitar HTML y posibles ataques XSS)
    $nombre_saneado = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $rol_saneado = htmlspecialchars($rol, ENT_QUOTES, 'UTF-8');
    $control_saneado = htmlspecialchars($control, ENT_QUOTES, 'UTF-8');

    // 3. Procesar la subida de la foto
    $nombre_foto_db = ""; // Valor por defecto si no hay foto

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Nuevo nombre único para la imagen
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        
        // Directorio de subida (debe existir)
        $uploadFileDir = './fotos/';
        $dest_path = $uploadFileDir . $newFileName;

        // Validar tipo de archivo (puedes añadir más si quieres)
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Mover el archivo subido a la carpeta 'fotos/'
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $nombre_foto_db = $newFileName; // Solo el nombre del archivo se guarda en la DB
            } else {
                $mensaje = "❌ Error al mover el archivo de la foto.";
                $tipo_mensaje = "error";
            }
        } else {
            $mensaje = "⚠️ Tipo de archivo no permitido. Solo se permiten JPG, PNG o GIF. El registro se guardó SIN foto.";
            $tipo_mensaje = "aviso";
            // Continuar con la inserción sin foto
        }
    }

    // Solo si no hubo un error crítico en la subida que detenga la operación
    if ($tipo_mensaje !== "error") {
        
        // 4. PREPARAR la consulta SQL de inserción
        $sql = "INSERT INTO eternals (Nombre, Rol, Numero_de_control, Foto) VALUES (?, ?, ?, ?)";
        
        // Inicializar la sentencia preparada
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $mensaje = "❌ Error al preparar la consulta SQL: " . $conn->error;
            $tipo_mensaje = "error";
        } else {
            // 5. VINCULAR los parámetros
            // 'ssss' indica que todos los parámetros son strings (cadenas).
            $stmt->bind_param("ssss", $nombre_saneado, $rol_saneado, $control_saneado, $nombre_foto_db);

            // 6. EJECUTAR la consulta
            if ($stmt->execute()) {
                // Inserción exitosa
                if ($tipo_mensaje !== "aviso") {
                    $mensaje = "✅ ¡Eternals **{$nombre_saneado}** registrado exitosamente!";
                    $tipo_mensaje = "exito";
                } else {
                    $mensaje .= "<br>✅ Registro de **{$nombre_saneado}** completado (sin la foto por el error anterior).";
                }
            } else {
                // Error en la ejecución
                $mensaje = "❌ Error al registrar al hommie: " . $stmt->error;
                $tipo_mensaje = "error";
            }

            // 7. CERRAR la sentencia
            $stmt->close();
        }
    }
} else {
    // Si la página se accede directamente sin POST
    $mensaje = "❌ Acceso no permitido. Por favor, usa el formulario.";
    $tipo_mensaje = "error";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de la Inserción</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #f4f7f6; }
        .mensaje { 
            max-width: 500px; margin: 0 auto; padding: 25px; border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); background-color: white;
        }
        .exito { border: 2px solid green; color: green; }
        .error { border: 2px solid red; color: red; }
        .aviso { border: 2px solid orange; color: orange; }
        .btn-volver {
            display: inline-block; margin-top: 20px; padding: 10px 15px;
            background-color: #3f51b5; color: white; text-decoration: none;
            border-radius: 5px; transition: background-color 0.3s;
        }
        .btn-volver:hover { background-color: #1a237e; }
        strong { font-weight: bold; }
    </style>
</head>
<body>
    <div class="mensaje <?php echo $tipo_mensaje; ?>">
        <h1>Resultado del Registro</h1>
        <p><?php echo $mensaje; ?></p>
        <a href="index.php" class="btn-volver">Volver a la Lista Principal</a>
        <a href="formulario_insercion.html" class="btn-volver" style="background-color: #4CAF50;">Registrar Otro</a>
    </div>
</body>
</html>