<?php
require_once 'enums.php';
function getValueByCodeAndType(string $clave, string $localforaneo, string $tipo) {
    if ($clave === "" || $localforaneo === "") return json_encode(array('item_total' => 0, 'service_type' => $tipo, 'total' => (0)));
    try {
        $itemTotal = null;
        $type = null;
        if ($localforaneo === "FORANEO") {
            $itemTotal = ClaveCostoForaneo::from($clave);
            $type = ClaveCostoForaneo::type($tipo ?? "");
        } else if ($localforaneo === "LOCAL") {
            $itemTotal = ClaveCostoLocal::from($clave);
            $type = ClaveCostoLocal::type($tipo ?? "");
        }
        if ($itemTotal === null) {
            throw new ValueError("Enum case not found for the given input.");
        }
        return json_encode(array('item_total' => $itemTotal, 'service_type' => $type, 'total' => ($itemTotal + $type)));
    } catch (ValueError $e) {
        return 0;
    }
}
?>
<?php
if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && isset($_GET['tecnico_nombre']) && isset($_GET['id_tecnico'])) {
    $fecha_inicio = $_GET['fecha_inicio'];
    $fecha_fin = $_GET['fecha_fin'];
    $tecnico_nombre = $_GET['tecnico_nombre'];
    $id_tecnico = $_GET['id_tecnico'];

    $fecha_inicio_codificada = urlencode($fecha_inicio);
    $fecha_fin_codificada = urlencode($fecha_fin);
    $api_url = "https://secure.tecnomotum.com/srmotum/Apis/GetServiciosST?DateIni=$fecha_inicio_codificada&DateFin=$fecha_fin_codificada&idTec=$id_tecnico&param=";
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if(curl_errno($ch)) {
        echo "Error de cURL: " . curl_error($ch);
    }
    curl_close($ch);
    $data = json_decode($response, true);
    $totalDeTotales = 0;
    if ($data) {
        foreach ($data as &$item) {
            $jsoncito = json_decode(getValueByCodeAndType($item['modeloclave'] ?? "", $item['localforaneo'] ?? "", $item['tipo'] ?? ""), true);
            $item['taco'] = $jsoncito;
            $totalDeTotales +=$jsoncito['total'];
        }
        $resultados = $data;
    } else {
        $resultados = "No se obtuvo información de la API.";
    }
} else {
    die("Faltan parámetros en la URL.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios del Técnico</title>
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
            max-width: 1100px;
            text-align: left;
            overflow: hidden;
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
        <h1>Servicios de <?php echo htmlspecialchars($tecnico_nombre); ?></h1>
        <p><strong>Fecha de inicio:</strong> <?php echo htmlspecialchars($fecha_inicio); ?></p>
        <p><strong>Fecha de fin:</strong> <?php echo htmlspecialchars($fecha_fin); ?></p>
        <a href="index.php" class="back-link">Regresar</a>
        <h4>Total generado del técnico: <?php echo "$ "  . number_format($totalDeTotales); ?></h4>
        <?php if (is_array($resultados)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Local/Foráneo</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>ID Modelo</th>
                        <th>Clave Modelo</th>
                        <th>Tipo Modelo</th>
                        <th>Nombre Modelo</th>
                        <th>Item Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                            <td><?php echo htmlspecialchars($item['localforaneo']); ?></td>
                            <td><?php echo htmlspecialchars($item['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($item['descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($item['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($item['idmodelo']); ?></td>
                            <td><?php echo htmlspecialchars($item['modeloclave']); ?></td>
                            <td><?php echo htmlspecialchars($item['modelotipo']); ?></td>
                            <td><?php echo htmlspecialchars($item['modelonombre']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['taco']['item_total']); ?></td>
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
