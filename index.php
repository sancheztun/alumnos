<?php
// index.php
include("conexion.php");

// Consulta de la tabla eternals
$sql = "SELECT ID, Nombre, Rol, Numero_de_control, Foto FROM eternals";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Eternals</title>
    <style>
        /* Estilos Generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            padding: 20px;
            display: flex; 
            flex-direction: column;
            align-items: center;
        }

        /* Estilo para el botón de nuevo registro */
        .btn-nuevo {
            display: inline-block;
            padding: 12px 20px;
            margin-bottom: 20px;
            background-color: #4CAF50; /* Verde */
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }
        .btn-nuevo:hover {
            background-color: #45a049;
            text-decoration: none;
        }


        /* Estilos de la Tabla */
        table {
            width: 95%; 
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #ffffff;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15); 
            border-radius: 10px;
            overflow: hidden;
        }

        /* Estilo para el Título dentro de la tabla */
        tr.title-row td {
            background-color: #1a237e; /* Azul oscuro */
            text-align: center;
            padding: 0;
            border-bottom: none;
        }
        
        /* Estilo para el h2 dentro de la celda del título */
        tr.title-row h2 {
            margin: 0;
            padding: 15px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Encabezado de la tabla (Nombre de las columnas) */
        th {
            background-color: #3f51b5; /* Azul medio para el encabezado */
            color: #ffffff;
            padding: 15px 10px;
            text-align: center; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Celdas de datos */
        td {
            padding: 15px 10px;
            text-align: center; 
            border-bottom: 1px solid #e0e0e0;
        }

        /* Filas de la tabla (Estilo Zebra) */
        tr:nth-child(even) {
            background-color: #fafafa; /* Color para filas pares (zebra) */
        }
        tr:hover {
            background-color: #e8eaf6; /* Resaltar fila al pasar el mouse */
        }
        /* No aplicar hover a la fila del título */
        tr.title-row:hover {
            background-color: #1a237e;
        }


        /* Imagen y Placeholder */
        img {
            width: 80px; 
            height: 80px;
            object-fit: cover;
            border-radius: 8px; 
            border: 3px solid #3f51b5;
            transition: transform 0.3s ease;
        }
        img:hover {
            transform: scale(1.05); /* Efecto al pasar el mouse */
        }

        .placeholder {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #9e9e9e;
            color: #fff;
            font-size: 0.8em;
            border-radius: 8px;
            margin: 0 auto;
            border: 3px dashed #757575;
        }

        /* Enlaces (Nombres) */
        a {
            color: #0b5ed7;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            color: #1a237e;
            text-decoration: underline;
        }

        /* Fila para "No hay datos" */
        tr:last-child td {
            border-bottom: none;
            font-style: italic;
            color: #757575;
            padding: 20px;
        }

        /* Estilo para el botón de borrar */
        .btn-borrar {
            background-color: #f44336; /* Rojo */
            color: white;
            border: none;
            padding: 8px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-borrar:hover {
            background-color: #d32f2f;
        }

        /* Estilo para el botón de editar (NUEVO) */
        .btn-editar {
            background-color: #ff9800; /* Naranja */
            color: white;
            border: none;
            padding: 8px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-editar:hover {
            background-color: #f57c00;
        }

    </style>
</head>
<body>

<a href="formulario_insercion.html" class="btn-nuevo">➕ Añadir Nuevo Eternals</a>

<div class="table-container">
    <table>
        <tr class="title-row">
            <td colspan="6">
                <h2>Tabla de Eternals</h2>
            </td>
        </tr>
        
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Rol</th>
            <th>Número de Control</th>
            <th>Foto</th>
            <th>Acción</th> </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $id = (int)$row['ID'];
                $nombre = htmlspecialchars($row['Nombre'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $rol = htmlspecialchars($row['Rol'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $control = htmlspecialchars($row['Numero_de_control'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
                $fotoFile = basename($row['Foto']);
                $fotoPath = __DIR__ . "/fotos/" . $fotoFile;
                $fotoWeb = "fotos/" . rawurlencode($fotoFile);

                echo "<tr>";
                echo "<td>{$id}</td>";
                echo "<td><a href='https://www.google.com/search?q=" . rawurlencode($row['Nombre']) . "' target='_blank'>{$nombre}</a></td>";
                echo "<td>{$rol}</td>";
                echo "<td>{$control}</td>";

                echo "<td>"; 
                if (!empty($fotoFile) && file_exists($fotoPath) && is_file($fotoPath)) {
                    echo "<img src='{$fotoWeb}' alt='Foto de {$nombre}'>";
                } else {
                    echo "<div class='placeholder'>Sin foto</div>";
                }
                echo "</td>";

                // Celda con ACCIONES (Editar y Borrar)
                echo "<td>";
                
                // Botón/Enlace de EDITAR
                echo "<a href='formulario_edicion.php?id={$id}' class='btn-editar' style='margin-right: 10px;'>✏️ Editar</a>";

                // Formulario de Borrado
                echo "<form action='borrar.php' method='POST' onsubmit=\"return confirm('¿Estás seguro de que quieres borrar a {$nombre} (ID: {$id})?');\" style='display:inline-block;'>";
                echo "<input type='hidden' name='id_a_borrar' value='{$id}'>";
                echo "<button type='submit' class='btn-borrar'>🗑️ Borrar</button>";
                echo "</form>";
                echo "</td>";

                echo "</tr>";
            }
        } else {
            // colspan ahora es 6
            echo "<tr><td colspan='6'>No hay datos en la tabla</td></tr>";
        }
        ?>

    </table>
</div>

</body>
</html>

<?php
// Cerrar la conexión al final del archivo
$conn->close();
?>