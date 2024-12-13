<!-- Daniel Márquez, Alejandro Corral y Guilermo Mateos -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Eventos y Organizadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <!-- Alerta para eliminar -->
    <script>
        function eliminar(){
            let respuesta=confirm("¿Estas seguro que quieres eliminarlo?");
            return respuesta;
        }
    </script>

    <div class="container my-5">
        <h1 class="text-center mb-4">Gestión de Eventos Deportivos</h1>

        <h2>Organizadores</h2>
        <table class="table table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Gmail</th>
                    <th>Teléfono</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- Ver tabla de organizadores -->
                <?php
                include "./conexion.php";
                $sql = $conexion->query(" SELECT * FROM organizadores");
                while ($datos = $sql->fetch_object()) { ?>
                    <tr>
                        <td><?= $datos->id ?></td>
                        <td><?= $datos->nombre ?></td>
                        <td><?= $datos->email ?></td>
                        <td><?= $datos->telefono ?></td>
                        <td>
                            <a href="procesar.php?eliminar_organizador=<?= $datos->id ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este organizador?')">
                                <i class="fas fa-trash-alt" style="color: white;"></i> 
                            </a>                    
                        </td>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
        <form class="my-4" method="POST" action="procesar.php" id="formOrganizador">
        <h4>Añadir Nuevo Organizador</h4>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombreOrganizador" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombreOrganizador" name="nombre" required>
            </div>
            <div class="col-md-6">
                <label for="gmailOrganizador" class="form-label">Gmail</label>
                <input type="email" class="form-control" id="gmailOrganizador" name="email" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="telefonoOrganizador" class="form-label">Teléfono</label>
                <input type="number" class="form-control" id="telefonoOrganizador" name="telefono" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" name="registrarOrganizador" value="botonOrganizador">Añadir Organizador</button>
        </form>

        <h2>Eventos</h2>
        <form class="mb-3" method="GET" action="index.php">
        <div class="row">
            <div class="col-md-6">
                <label for="filtro_nombre_evento" class="form-label">Filtrar por Nombre del Evento</label>
                <input type="text" class="form-control" id="filtro_nombre_evento" name="filtro_nombre_evento" 
                    value="<?= isset($_GET['filtro_nombre_evento']) ? htmlspecialchars($_GET['filtro_nombre_evento']) : '' ?>" 
                    placeholder="Introduce el nombre del evento">
            </div>
            <div class="col-md-6 align-self-end">
                <button type="submit" class="btn btn-primary">Buscar</button>
                <a href="index.php" class="btn btn-secondary">Resetear</a>
            </div>
        </div>
        </form>

        <?php
        // Obtener parámetros de ordenación
        $columna_orden = isset($_GET['columna']) ? $_GET['columna'] : 'nombre_evento';
        $orden = isset($_GET['orden']) && $_GET['orden'] == 'desc' ? 'desc' : 'asc';

        // Construir la consulta SQL con ordenación
        $sql = "SELECT eventos.*, organizadores.nombre AS nombre_organizador 
                FROM eventos 
                LEFT JOIN organizadores ON eventos.id_organizador = organizadores.id";
        
        // Añadir filtro de nombre de evento, si existe
        if (isset($_GET['filtro_nombre_evento']) && !empty($_GET['filtro_nombre_evento'])) {
            $filtro = $conexion->real_escape_string($_GET['filtro_nombre_evento']);
            $sql .= " WHERE eventos.nombre_evento LIKE '%$filtro%'";
        }

        $sql .= " ORDER BY $columna_orden $orden";

        $resultado = $conexion->query($sql);
        ?>

        <table class="table table-bordered">
        <thead class="table-success">
            <tr>
                <th><a href="?columna=id&orden=<?= $orden == 'asc' ? 'desc' : 'asc' ?>&filtro_nombre_evento=<?= htmlspecialchars($_GET['filtro_nombre_evento'] ?? '') ?>">ID <?= $columna_orden == 'id' ? ($orden == 'asc' ? '↑' : '↓') : '' ?></a></th>
                <th><a href="?columna=nombre_evento&orden=<?= $orden == 'asc' ? 'desc' : 'asc' ?>&filtro_nombre_evento=<?= htmlspecialchars($_GET['filtro_nombre_evento'] ?? '') ?>">Nombre del Evento <?= $columna_orden == 'nombre_evento' ? ($orden == 'asc' ? '↑' : '↓') : '' ?></a></th>
                <th><a href="?columna=tipo_deporte&orden=<?= $orden == 'asc' ? 'desc' : 'asc' ?>&filtro_nombre_evento=<?= htmlspecialchars($_GET['filtro_nombre_evento'] ?? '') ?>">Tipo de Deporte <?= $columna_orden == 'tipo_deporte' ? ($orden == 'asc' ? '↑' : '↓') : '' ?></a></th>
                <th><a href="?columna=fecha&orden=<?= $orden == 'asc' ? 'desc' : 'asc' ?>&filtro_nombre_evento=<?= htmlspecialchars($_GET['filtro_nombre_evento'] ?? '') ?>">Fecha <?= $columna_orden == 'fecha' ? ($orden == 'asc' ? '↑' : '↓') : '' ?></a></th>
                <th><a href="?columna=hora&orden=<?= $orden == 'asc' ? 'desc' : 'asc' ?>&filtro_nombre_evento=<?= htmlspecialchars($_GET['filtro_nombre_evento'] ?? '') ?>">Hora <?= $columna_orden == 'hora' ? ($orden == 'asc' ? '↑' : '↓') : '' ?></a></th>
                <th><a href="?columna=ubicacion&orden=<?= $orden == 'asc' ? 'desc' : 'asc' ?>&filtro_nombre_evento=<?= htmlspecialchars($_GET['filtro_nombre_evento'] ?? '') ?>">Ubicación <?= $columna_orden == 'ubicacion' ? ($orden == 'asc' ? '↑' : '↓') : '' ?></a></th>
                <th><a href="?columna=nombre_organizador&orden=<?= $orden == 'asc' ? 'desc' : 'asc' ?>&filtro_nombre_evento=<?= htmlspecialchars($_GET['filtro_nombre_evento'] ?? '') ?>">Organizador <?= $columna_orden == 'nombre_organizador' ? ($orden == 'asc' ? '↑' : '↓') : '' ?></a></th>
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($datos = $resultado->fetch_object()) { ?>
                <tr>
                    <td><?= $datos->id ?></td>
                    <td><?= $datos->nombre_evento ?></td>
                    <td><?= $datos->tipo_deporte ?></td>
                    <td><?= $datos->fecha ?></td>
                    <td><?= $datos->hora ?></td>
                    <td><?= $datos->ubicacion ?></td>
                    <td><?= $datos->nombre_organizador ?></td>
                    <td>
                        <a href="./procesar.php?id=<?= $datos->id ?>" class="btn btn-success">
                            <i class="fas fa-edit" style="color: white;"></i> 
                        </a>
                    </td>
                    <td>
                        <a onclick="return eliminar()" href="procesar.php?eliminarEvento=1&id=<?= $datos->id ?>" class="btn btn-danger">
                            <i class="fas fa-trash-alt" style="color: white;"></i> 
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
        </table>

        <form class="my-4" method="POST" action="procesar.php" id="formEventos">
            <h4>Añadir Nuevo Evento</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nombre_evento" class="form-label">Nombre del Evento</label>
                    <input type="text" class="form-control" id="nombre_evento" name="nombre_evento" required>
                </div>
                <div class="col-md-6">
                    <label for="tipo_deporte" class="form-label">Tipo de Deporte</label>
                    <input type="text" class="form-control" id="tipo_deporte" name="tipo_deporte" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <div class="col-md-6">
                    <label for="hora" class="form-label">Hora</label>
                    <input type="time" class="form-control" id="hora" name="hora" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
                </div>
                <div class="col-md-6">
                    <label for="id_organizador" class="form-label">Organizador</label>
                    <select name="id_organizador" id="id_organizador" class="form-control" required>
                        <option value="">Seleccione un Organizador</option>
                        <?php
                        $sqlOrganizadores = $conexion->query(
                            "SELECT id, nombre FROM organizadores"
                        );
                        while ($organizador = $sqlOrganizadores->fetch_object()) { ?>
                        <option value="<?= $organizador->id ?>"><?= $organizador->nombre ?></option>
                    <?php }
                    ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-success mb-5" name="btnAñadirEvento" value="ok">Añadir Evento</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</body>
</html>
