<?php
// actualizar.php
include("conexion.php"); // Incluye tu archivo de conexión

// Inicializar la variable $mensaje
$mensaje = "";
$tipo_mensaje = "";

// 1. Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Verificar si el ID y otros campos necesarios fueron enviados
    if (isset($_POST['id'], $_POST['nombre'], $_POST['rol'], $_POST['control'])) {
        
        // 3. Obtener y sanear todos los datos
        $id_a_actualizar = intval($_POST['id']); // Debe ser entero
        
        // Sanear datos de texto (usar htmlspecialchars para mostrar seguro en la web)
        $nombre_saneado = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
        $rol_saneado = htmlspecialchars(trim($_POST['rol']), ENT_QUOTES, 'UTF-8');
        $control_saneado = htmlspecialchars(trim($_POST['control']), ENT_QUOTES, 'UTF-8');
        
        // Nota: La subida de foto es más compleja y se omitió aquí para simplicidad.
        // Solo actualizaremos Nombre, Rol y Numero_de_control. 
        // Si necesitas actualizar la foto, la lógica es similar a insertar.php.
        // Si no tienes un campo de foto en el formulario de edición, no es necesario.
        
        // 4. PREPARAR la consulta SQL de ACTUALIZACIÓN
        // UPDATE tabla SET campo1 = ?, campo2 = ? WHERE ID = ?
        $sql = "UPDATE eternals SET Nombre = ?, Rol = ?, Numero_de_control = ? WHERE ID = ?";
        
        // Inicializar la sentencia preparada
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $mensaje = "❌ Error al preparar la consulta: " . $conn->error;
            $tipo_mensaje = "error";
        } else {
            // 5. VINCULAR los parámetros
            // 'sssi' indica 3 strings y 1 integer (ID)
            $stmt->bind_param("sssi", $nombre_saneado, $rol_saneado, $control_saneado, $id_a_actualizar);

            // 6. EJECUTAR la consulta
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0 || $stmt->warning_count === 0) {
                    // Actualización exitosa (affected_rows > 0 si hubo cambios, 
                    // si es 0 significa que los datos eran los mismos, pero la consulta fue OK)
                    $mensaje = "✅ ¡Registro de **{$nombre_saneado}** (ID: **{$id_a_actualizar}**) actualizado exitosamente!";
                    $tipo_mensaje = "exito";
                } else {
                    // No se encontraron cambios o el ID no existía
                    $mensaje = "⚠️ No se realizó ninguna actualización. Los datos son iguales o el ID **{$id_a_actualizar}** no existe.";
                    $tipo_mensaje = "aviso";
                }
            } else {
                // Error en la ejecución
                $mensaje = "❌ Error al intentar actualizar el registro: " . $stmt->error;
                $tipo_mensaje = "error";
            }

            // 7. CERRAR la sentencia
            $stmt->close();
        }
    } else {
        $mensaje = "❌ Error: Faltan datos del formulario (ID, Nombre, Rol o Control).";
        $tipo_mensaje = "error";
    }
} else {
    // Si la página se accede directamente sin POST
    $mensaje = "❌ Acceso no permitido. Usa el formulario de edición.";
    $tipo_mensaje = "error";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de la Actualización</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #f4f7f6; }
        .mensaje { 
            max-width: 400px; margin: 0 auto; padding: 20px; border-radius: 8px;
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
    </style>
</head>
<body>
    <div class="mensaje <?php echo $tipo_mensaje; ?>">
        <h1>Resultado de la Operación</h1>
        <p><?php echo $mensaje; ?></p>
        <a href="index.php" class="btn-volver">Volver a la Lista</a>
        <?php if ($tipo_mensaje === "error" && isset($id_a_actualizar)): ?>
            <a href="formulario_edicion.php?id=<?php echo $id_a_actualizar; ?>" class="btn-volver" style="background-color: #ff9800;">Volver a Editar</a>
        <?php endif; ?>
    </div>
</body>
</html>