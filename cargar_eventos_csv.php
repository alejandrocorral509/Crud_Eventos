<?php
include './conexion.php';

// Configuración
$archivoCsv = 'ruta/a/tu/archivo.csv'; // Cambia esta ruta por la ruta de tu archivo CSV
$delimitador = ','; // Ajusta el delimitador si es necesario, generalmente ',' o ';'

// Validación de la existencia del archivo
if (!file_exists($archivoCsv)) {
    die("El archivo no se encuentra en la ruta especificada.");
}

// Abrir el archivo CSV
if (($archivo = fopen($archivoCsv, 'r')) !== FALSE) {
    // Saltar la primera fila si contiene cabeceras
    fgetcsv($archivo, 1000, $delimitador);

    // Inicializar contadores
    $registrosProcesados = 0;
    $registrosInsertados = 0;
    $registrosRechazados = 0;
    $errores = [];

    // Leer el archivo línea por línea
    while (($datos = fgetcsv($archivo, 1000, $delimitador)) !== FALSE) {
        // Incrementar contador de registros procesados
        $registrosProcesados++;

        // Validar los datos (ajustar según la estructura de tu base de datos)
        $nombreEvento = trim($datos[0]);
        $tipoDeporte = trim($datos[1]);
        $fecha = trim($datos[2]);
        $hora = trim($datos[3]);
        $ubicacion = trim($datos[4]);
        $idOrganizador = trim($datos[5]);

        $erroresRegistro = [];

        // Validar datos (ejemplo de validación básica)
        if (empty($nombreEvento)) {
            $erroresRegistro[] = "El nombre del evento es obligatorio.";
        }
        if (empty($tipoDeporte)) {
            $erroresRegistro[] = "El tipo de deporte es obligatorio.";
        }
        if (!preg_match('/\d{4}-\d{2}-\d{2}/', $fecha)) { // Validación simple para formato de fecha YYYY-MM-DD
            $erroresRegistro[] = "El formato de la fecha no es válido.";
        }
        if (!preg_match('/\d{2}:\d{2}/', $hora)) { // Validación simple para formato de hora HH:MM
            $erroresRegistro[] = "El formato de la hora no es válido.";
        }
        if (empty($ubicacion)) {
            $erroresRegistro[] = "La ubicación es obligatoria.";
        }
        if (empty($idOrganizador) || !is_numeric($idOrganizador)) {
            $erroresRegistro[] = "El organizador es inválido.";
        }

        // Si hay errores, rechazamos el registro
        if (count($erroresRegistro) > 0) {
            $errores[] = [
                'registro' => $datos,
                'errores' => $erroresRegistro
            ];
            $registrosRechazados++;
            continue;
        }

        // Insertar los datos en la base de datos si no hay errores
        $sql = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador)
                VALUES ('$nombreEvento', '$tipoDeporte', '$fecha', '$hora', '$ubicacion', '$idOrganizador')";
        if ($conexion->query($sql)) {
            $registrosInsertados++;
        } else {
            $errores[] = [
                'registro' => $datos,
                'errores' => ["Error en la inserción de datos."]
            ];
            $registrosRechazados++;
        }
    }

    // Cerrar el archivo CSV
    fclose($archivo);

    // Resumen del proceso
    echo "<h3>Resumen de la carga masiva de eventos</h3>";
    echo "<p>Total de registros procesados: $registrosProcesados</p>";
    echo "<p>Total de registros insertados correctamente: $registrosInsertados</p>";
    echo "<p>Total de registros rechazados: $registrosRechazados</p>";

    if ($registrosRechazados > 0) {
        echo "<h4>Registros rechazados:</h4>";
        echo "<ul>";
        foreach ($errores as $error) {
            echo "<li><b>Evento:</b> " . implode(", ", $error['registro']) . "<br><b>Errores:</b> " . implode(", ", $error['errores']) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "No se pudo abrir el archivo CSV.";
}

?>
