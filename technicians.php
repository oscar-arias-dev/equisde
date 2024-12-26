<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        die("Las fechas son obligatorias.");
    };
    $fecha_inicio_codificada = urlencode($fecha_inicio);
    $fecha_fin_codificada = urlencode($fecha_fin);
    $api_url = "https://secure.tecnomotum.com/srmotum/Apis/GetTecnicos?DateIni=$fecha_inicio_codificada&DateFin=$fecha_fin_codificada";
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if(curl_errno($ch)) {
        echo "Error de cURL: " . curl_error($ch);
    }
    curl_close($ch);
    $data = json_decode($response, true);
    if ($data) {
        $resultados = $data;
    } else {
        $resultados = "No se obtuvo información de la API.";
    }
} else {
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Técnicos por fecha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100%;
            flex-direction: column;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
            text-align: left;
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }
        a {
            text-decoration: none;
        }
        .back-link {
            margin-top: 20px;
            display: inline-block;
            color: #007BFF;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            table {
                width: 100%;
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            th, td {
                padding: 10px;
                font-size: 14px;
            }

            button {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Técnicos</h2>
        <p><strong>Fecha de inicio:</strong> <?php echo htmlspecialchars($fecha_inicio); ?></p>
        <p><strong>Fecha de fin:</strong> <?php echo htmlspecialchars($fecha_fin); ?></p>
        
        <b><a href="index.php" class="back-link">Regresar</a></b>

        <?php if (is_array($resultados)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Técnico</th>
                        <th>Ciudad</th>
                        <th>Sede</th>
                        <th>Distribuidor</th>
                        <th>Proveedor</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['tecnico_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($item['tecnico_ciudad']); ?></td>
                            <td><?php echo htmlspecialchars($item['tecnico_sede']); ?></td>
                            <td><?php echo htmlspecialchars($item['tecnico_distribuidor']); ?></td>
                            <td><?php echo htmlspecialchars($item['tecnico_proveedor']); ?></td>
                            <td><?php echo htmlspecialchars($item['tecnico_estatus']); ?></td>
                            <td>
                                <a href="services.php?fecha_inicio=<?php echo urlencode($fecha_inicio); ?>&fecha_fin=<?php echo urlencode($fecha_fin); ?>&tecnico_nombre=<?php echo urlencode($item['tecnico_nombre']); ?>&id_tecnico=<?php echo urlencode($item['id_tecnico']); ?>">
                                    <button>Ver servicios</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php echo htmlspecialchars($resultados); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
