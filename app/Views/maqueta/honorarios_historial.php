<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Honorarios >= 1500 - <?= $ruc ?></title>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            margin-bottom: 4px;
        }

        p.subtitulo {
            margin: 0 0 12px;
            font-size: 13px;
            color: #555;
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
            text-align: left;
        }

        td.monto {
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>

</head>

<body>

    <div class="no-print">
        <button onclick="window.print()">Imprimir / Descargar PDF</button>
    </div>

    <h2>Recibos por Honorarios &gt;= S/ 1,500</h2>
    <p class="subtitulo">Cliente RUC: <?= $ruc ?> &nbsp;&mdash;&nbsp; Total registros: <?= count($honorarios) ?></p>

    <?php if (empty($honorarios)): ?>
        <p>No se encontraron registros.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha Emisión</th>
                    <th>Serie - Número</th>
                    <th>RUC</th>
                    <th>Nombre</th>
                    <th>Monto</th>
                    <th>Período</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($honorarios as $h): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $h['fecha'] ?></td>
                        <td><?= $h['numero_documento'] ?></td>
                        <td><?= $h['ruc'] ?></td>
                        <td><?= $h['razon_social'] ?></td>
                        <td class="monto">S/ <?= number_format($h['total'], 2) ?></td>
                        <td><?= $h['periodo'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>

</html>
