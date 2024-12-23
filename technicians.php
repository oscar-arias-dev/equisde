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
    <title>Resultados</title>
</head>
<body>
    <h1>Fechas seleccionadas</h1>
    <p><strong>Fecha de inicio:</strong> <?php echo htmlspecialchars($fecha_inicio); ?></p>
    <p><strong>Fecha de fin:</strong> <?php echo htmlspecialchars($fecha_fin); ?></p>
    <a href="index.php">Regresar</a>

    <h2>Resultados de la API</h2>
    <?php if (is_array($resultados)): ?>
        <ul>
            <?php foreach ($resultados as $item): ?>
                <li>
                    <?php echo htmlspecialchars($item['tecnico_nombre']); ?> - 
                    <?php echo htmlspecialchars($item['tecnico_sede']); ?>
                    <a href="services.php?fecha_inicio=<?php echo urlencode($fecha_inicio); ?>&fecha_fin=<?php echo urlencode($fecha_fin); ?>&tecnico_nombre=<?php echo urlencode($item['tecnico_nombre']); ?> &id_tecnico=<?php echo urlencode($item['id_tecnico']); ?>">
                        <button>Ver servicios</button>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><?php echo htmlspecialchars($resultados); ?></p>
    <?php endif; ?>
</body>
</html>