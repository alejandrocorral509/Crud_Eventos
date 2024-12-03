<?php

include "conexion.php";

//Añadir organizadores
function AñadirOrganizador()
{
    global $conexion;

    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];

    $sql = $conexion->query(
        "INSERT INTO organizadores(nombre, email, telefono) VALUES('$nombre', '$email', '$telefono')"
    );

    if ($sql) {
        Header("Location: index.php");
    } else {
        echo "Error al añadir organizador: " . $conexion->error;
    }
}

// Verifica si se ha enviado el formulario
if (isset($_POST["registrarOrganizador"])) {
    AñadirOrganizador();
}

//Eliminar organizadores
// Función para eliminar organizador
function eliminarOrganizador($id)
{
    global $conexion;

    // Verificar si el organizador tiene eventos asignados
    $consultaEventos = $conexion->query(
        "SELECT COUNT(*) AS count FROM eventos WHERE id_organizador = $id"
    );
    $resultado = $consultaEventos->fetch_object();

    if ($resultado->count > 0) {
        // No permitir eliminar si hay eventos asignados
        header(
            "Location: index.php?mensaje=No se puede eliminar el organizador porque tiene eventos asignados."
        );
        exit();
    } else {
        $sql = $conexion->query("DELETE FROM organizadores WHERE id = $id");

        if ($sql) {
            header(
                "Location: index.php?mensaje=Organizador eliminado correctamente."
            );
            exit();
        } else {
            header(
                "Location: index.php?mensaje=Error al eliminar el organizador: " .
                    $conexion->error
            );
            exit();
        }
    }
}

// Llamar a la función si se recibe el parámetro para eliminar
if (isset($_GET["eliminar_organizador"])) {
    $id = intval($_GET["eliminar_organizador"]);
    eliminarOrganizador($id);
}

//Añadir eventos
function AñadirEvento()
{
    global $conexion;
    $nombre_evento = $_POST["nombre_evento"];
    $tipo_deporte = $_POST["tipo_deporte"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $ubicacion = $_POST["ubicacion"];
    $id_organizador = $_POST["id_organizador"];

    $sql = $conexion->query(
        " INSERT INTO eventos(nombre_evento,tipo_deporte,fecha,hora,ubicacion,id_organizador)VALUES('$nombre_evento','$tipo_deporte','$fecha','$hora','$ubicacion','$id_organizador') "
    );

    if ($sql) {
        Header("Location: index.php");
    } else {
        echo "Error al añadir evento: " . $conexion->error;
    }
}
if (isset($_POST["btnAñadirEvento"])) {
    AñadirEvento();
}

//Eliminar evento
function eliminarEvento($id)
{
    global $conexion;
    $sql = $conexion->query("DELETE FROM eventos WHERE id = '$id'");
    if ($sql) {
        echo "<div class='alert alert-success'>Evento eliminado</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar el evento: " .
            $conexion->error .
            "</div>";
    }
}

if (isset($_GET["eliminarEvento"]) && isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $eliminar = $conexion->query("DELETE FROM eventos WHERE id = $id");

    if ($eliminar) {
        header("Location: index.php?status=success");
    } else {
        header("Location: index.php?status=error");
    }
    exit();
}

//Editar evento

function editarEvento()
{
    global $conexion;
    if (isset($_GET["id"])) {
        $id = intval($_GET["id"]);
        if ($id > 0) {
            $sql = $conexion->query("SELECT * FROM eventos WHERE id = $id");
            return $sql->fetch_object();
        }
    }
    return null;
}

function actualizarEvento()
{
    global $conexion;
    if (isset($_POST['btnModificarEvento'])) {
        $id = intval($_POST['id']);
        $nombre_evento = $conexion->real_escape_string($_POST['nombre_evento']);
        $tipo_deporte = $conexion->real_escape_string($_POST['tipo_deporte']);
        $fecha = $conexion->real_escape_string($_POST['fecha']);
        $hora = $conexion->real_escape_string($_POST['hora']);
        $ubicacion = $conexion->real_escape_string($_POST['ubicacion']);
        $id_organizador = intval($_POST['id_organizador']);

        $sql = "UPDATE eventos SET 
                nombre_evento = '$nombre_evento', 
                tipo_deporte = '$tipo_deporte', 
                fecha = '$fecha', 
                hora = '$hora', 
                ubicacion = '$ubicacion', 
                id_organizador = $id_organizador 
                WHERE id = $id";

        if ($conexion->query($sql)) {
            header("Location: index.php?mensaje=Evento actualizado correctamente");
            exit();
        } else {
            header("Location: index.php?mensaje=Error al actualizar el evento: " . $conexion->error);
            exit();
        }
    }
}

// Llamar a la función de actualización
actualizarEvento();

// Obtener el evento solo si estamos en modo de edición
$evento = null;
if (isset($_GET['id'])) {
    $evento = editarEvento();
}

// HTML para el formulario de edición de eventos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <?php if ($evento): ?>
        <form class="my-4" method="POST" action="procesar.php" id="formEventos">
            <h4 class="mb-4">Modificar Evento</h4>
            <input type="hidden" name="id" value="<?= $evento->id ?>">
            <div class="mb-3">
                <label for="nombre_evento" class="form-label">Nombre del Evento</label>
                <input type="text" class="form-control" id="nombre_evento" name="nombre_evento" value="<?= ($evento->nombre_evento) ?>" required>
            </div>
            <div class="mb-3">
                <label for="tipo_deporte" class="form-label">Tipo de Deporte</label>
                <input type="text" class="form-control" id="tipo_deporte" name="tipo_deporte" value="<?= ($evento->tipo_deporte) ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= ($evento->fecha) ?>" required>
            </div>
            <div class="mb-3">
                <label for="hora" class="form-label">Hora</label>
                <input type="time" class="form-control" id="hora" name="hora" value="<?= ($evento->hora) ?>" required>
            </div>
            <div class="mb-3">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?= ($evento->ubicacion) ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_organizador" class="form-label">Organizador</label>
                <select name="id_organizador" id="id_organizador" class="form-control" required>
                    <option value="">Seleccione un Organizador</option>
                    <?php
                    $sqlOrganizadores = $conexion->query("SELECT id, nombre FROM organizadores");
                    while ($organizador = $sqlOrganizadores->fetch_object()) {
                        $selected = $organizador->id == $evento->id_organizador ? "selected" : "";
                        echo "<option value='{$organizador->id}' $selected>{$organizador->nombre}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success" name="btnModificarEvento" value="ok">Guardar Cambios</button>
        </form>
    <?php else: ?>
        <div class="alert alert-danger mt-4">
            <p>No se encontró el evento o no se proporcionó un ID válido.</p>
            <a href="index.php" class="btn btn-primary mt-2">Volver a la lista de eventos</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

