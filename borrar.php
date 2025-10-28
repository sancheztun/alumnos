<?php
// borrar.php
include("conexion.php"); // Incluye tu archivo de conexión

// 1. Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Verificar si el ID a borrar fue enviado
    if (isset($_POST['id_a_borrar'])) {
        
        // 3. Obtener y sanear el ID
        // Usamos intval() para asegurar que el valor sea un entero.
        $id_a_borrar = intval($_POST['id_a_borrar']); 

        // 4. PREPARAR la consulta SQL para evitar Inyección SQL
        // Usamos '?' como marcador de posición.
        $sql = "DELETE FROM eternals WHERE ID = ?";
        
        // Inicializar la sentencia preparada
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Manejar error en la preparación (ej: error de sintaxis SQL)
            die("Error al preparar la consulta: " . $conn->error);
        }

        // 5. VINCULAR el parámetro
        // 'i' indica que el tipo de dato es un entero (integer).
        $stmt->bind_param("i", $id_a_borrar);

        // 6. EJECUTAR la consulta
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Borrado exitoso
                $mensaje = "✅ ¡Registro con ID **{$id_a_borrar}** borrado exitosamente!";
            } else {
                // No se encontró el ID
                $mensaje = "⚠️ No se encontró ningún registro con ID **{$id_a_borrar}** para borrar.";
            }
        } else {
            // Error en la ejecución
            $mensaje = "❌ Error al intentar borrar el registro: " . $stmt->error;
        }

        // 7. CERRAR la sentencia
        $stmt->close();
    } else {
        $mensaje = "❌ Error: ID de registro no especificado.";
    }
} else {
    // Si la página se accede directamente sin POST
    $mensaje = "❌ Acceso no permitido. Usa el formulario de la página principal.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Borrado</title>
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
    <div class="mensaje 
        <?php 
        if (strpos($mensaje, '✅') !== false) echo 'exito'; 
        else if (strpos($mensaje, '❌') !== false) echo 'error';
        else echo 'aviso';
        ?>
    ">
        <h1>Resultado de la Operación</h1>
        <p><?php echo $mensaje; ?></p>
        <a href="index.php" class="btn-volver">Volver a la Lista</a>
    </div>
</body>
</html>