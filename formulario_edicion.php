<?php
// formulario_edicion.php
include("conexion.php"); // Incluye tu archivo de conexión

// 1. Obtener el ID del registro a editar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Usar header para redireccionar a la lista principal si no hay ID
    header("Location: index.php"); 
    exit("❌ Error: ID de registro no especificado para la edición.");
}

$id_a_editar = intval($_GET['id']);
$eternals = null; // Variable para almacenar los datos del registro

// 2. Consultar los datos actuales del hommie usando sentencia preparada
$sql = "SELECT ID, Nombre, Rol, Numero_de_control, Foto FROM eternals WHERE ID = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id_a_editar);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $eternals = $result->fetch_assoc();
    } else {
        // Mejor redireccionar o mostrar un mensaje menos drástico que die()
        $conn->close();
        header("Location: index.php");
        exit("❌ Error: No se encontró ningún eternals con el ID: {$id_a_editar}");
    }
    $stmt->close();
} else {
    $conn->close();
    die("❌ Error al preparar la consulta de selección: " . $conn->error);
}

// Es buena práctica cerrar la conexión si ya no se necesita más en el script PHP.
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar eternals: <?php echo htmlspecialchars($eternals['Nombre'] ?? 'Desconocido'); ?></title>
    <style>
        /* Reutilizamos los estilos del formulario_insercion.html */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            padding: 30px;
        }

        h2 {
            color: #1a237e;
            text-align: center;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #3f51b5;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        .btn-submit {
            background-color: #ff9800; /* Naranja para Actualizar */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #f57c00;
        }

        .btn-volver {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #3f51b5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            align-self: flex-start;
        }
        .btn-volver:hover { background-color: #1a237e; }

        .current-foto {
            text-align: center;
            margin-bottom: 15px;
        }
        .current-foto img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #3f51b5;
        }
    </style>
</head>
<body>

    <a href="index.php" class="btn-volver">← Volver a la Lista</a>

    <div class="container">
        <h2>Editar Integrante: <?php echo htmlspecialchars($eternals['Nombre']); ?></h2>
        
        <form action="actualizar.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($eternals['ID']); ?>">
            
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" 
                       value="<?php echo htmlspecialchars($eternals['Nombre']); ?>" required>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <?php $currentRol = htmlspecialchars($eternals['Rol']); ?>
                <option value="Alumno" <?php if ($currentRol == 'Alumno') echo 'selected'; ?>>Alumno</option>
                <option value="Profesor" <?php if ($currentRol == 'Profesor') echo 'selected'; ?>>Profesor</option>
                <option value="Administrativo" <?php if ($currentRol == 'Administrativo') echo 'selected'; ?>>Administrativo</option>
            </select>

            <label for="control">Número de Control:</label>
            <input type="text" id="control" name="control" 
                       value="<?php echo htmlspecialchars($eternals['Numero_de_control']); ?>" 
                       required pattern="[0-9]{1,30}" title="Solo números, máximo 30 dígitos">
            
            <div class="current-foto">
                <label>Foto Actual:</label><br>
                <?php 
                    // Se usa `rawurlencode` para el URL para manejar espacios o caracteres especiales en el nombre de archivo
                    $fotoFile = basename($eternals['Foto']);
                    $fotoPath = __DIR__ . "/fotos/" . $fotoFile;
                    $fotoWeb = "fotos/" . rawurlencode($fotoFile);
                    if (!empty($fotoFile) && file_exists($fotoPath) && is_file($fotoPath)):
                ?>
                    <img src="<?php echo $fotoWeb; ?>" alt="Foto Actual">
                <?php else: ?>
                    <p style="color:#757575;">Sin foto registrada.</p>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-submit">🔄 Actualizar Eternals</button>
        </form>
    </div>

</body>
</html>