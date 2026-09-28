<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte Usuarios</title>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
        }

        th {
            background: #f0f0f0;
        }

        /* OCULTAR BOTÓN AL IMPRIMIR */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>

</head>

<body>

    <div class="no-print">
        <button onclick="window.print()">Descargar / Imprimir PDF</button>
    </div>

    <h2>Reporte de Honorarios - <?= $razon_social ?></h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha Emisión</th>
                <th>Serie - Número</th>
                <th>RUC</th>
                <th>Nombre</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($data as $u): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $u['fecha'] ?></td>
                    <td><?= $u['numero_documento'] ?></td>
                    <td><?= $u['ruc'] ?></td>
                    <td><?= $u['razon_social'] ?></td>
                    <td>S/ <?= number_format($u['total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>