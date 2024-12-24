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
</head>
<body>

    <h1>Servicios de <?php echo htmlspecialchars($tecnico_nombre); ?></h1>
    <h2>ID del técnico: <?php echo htmlspecialchars($id_tecnico); ?></h2>
    <p><strong>Fecha de inicio:</strong> <?php echo htmlspecialchars($fecha_inicio); ?></p>
    <p><strong>Fecha de fin:</strong> <?php echo htmlspecialchars($fecha_fin); ?></p>
    <h2>Lista de servicios</h2>
    <p>Aquí se mostrarían los servicios del técnico <?php echo htmlspecialchars($tecnico_nombre); ?> entre las fechas seleccionadas.</p>
    <a href="index.php">Regresar</a>

    <h2>Resultados de la API</h2>
    <h4>Total generado del técnico: <?php echo htmlspecialchars($totalDeTotales); ?></h4>
    <?php if (is_array($resultados)): ?>
        <ul>
            <?php foreach ($resultados as $item): ?>
                <li>
                    <?php echo htmlspecialchars($item['id']); ?> - 
                    <?php echo htmlspecialchars($item['localforaneo']); ?> - 
                    <?php echo htmlspecialchars($item['tipo']); ?> -
                    <?php echo htmlspecialchars($item['descripcion']); ?> -
                    <?php echo htmlspecialchars($item['fecha']); ?> -
                    <?php echo htmlspecialchars($item['idmodelo']); ?> -
                    <?php echo htmlspecialchars($item['modeloclave']) #en Tablulador actualizado excel viene como clave; ?> -
                    <?php echo htmlspecialchars($item['modelotipo']); ?> -
                    <?php echo htmlspecialchars($item['modelonombre']); ?> -
                    <?php echo htmlspecialchars($item['taco']['item_total']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><?php echo htmlspecialchars($resultados); ?></p>
    <?php endif; ?>
</body>
</html>