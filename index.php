<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Fechas</title>
</head>
<body>
    <h1>Selecciona las fechas</h1>
    <form action="technicians.php" method="POST">
        <label for="fecha_inicio">Fecha de inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required><br><br>
        
        <label for="fecha_fin">Fecha de fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" required><br><br>
        
        <button type="submit">Buscar</button>
    </form>

</body>
</html>