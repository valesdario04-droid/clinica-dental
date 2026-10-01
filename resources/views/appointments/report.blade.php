<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de citas</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            margin: 40px;
        }

        h1, h2, p {
            margin: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .clinic-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }

        .report-title {
            font-size: 20px;
            margin-top: 8px;
        }

        .date {
            color: #4b5563;
            margin-top: 8px;
        }

        .print-button {
            background: #1d4ed8;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        th {
            background: #eff6ff;
            color: #1e3a8a;
            text-align: left;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 9px;
            font-size: 13px;
        }

        .empty {
            text-align: center;
            color: #6b7280;
            padding: 24px;
        }

        .footer {
            margin-top: 28px;
            color: #6b7280;
            font-size: 12px;
        }

        @media print {
            body {
                margin: 20px;
            }

            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <p class="clinic-name">Clínica Dental</p>
            <h1 class="report-title">Reporte diario de citas</h1>
            <p class="date">
                Fecha: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
            </p>
        </div>

        <button class="print-button" onclick="window.print()">
            Imprimir / Guardar PDF
        </button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Doctor</th>
                <th>Duración</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Motivo</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->scheduled_at->format('h:i A') }}</td>
                    <td>
                        {{ $appointment->patient->last_name }},
                        {{ $appointment->patient->first_name }}
                    </td>
                    <td>{{ $appointment->doctor->name }}</td>
                    <td>{{ $appointment->duration_minutes }} min</td>
                    <td>
                        {{ $appointment->appointment_type === 'immediate' ? 'Inmediata' : 'Reservada' }}
                    </td>
                    <td>
                        @switch($appointment->status)
                            @case('scheduled') Reservada @break
                            @case('attended') Atendida @break
                            @case('cancelled') Cancelada @break
                            @case('no_show') No asistió @break
                        @endswitch
                    </td>
                    <td>{{ $appointment->reason ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">
                        No hay citas registradas para esta fecha.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        Reporte generado el {{ now()->format('d/m/Y h:i A') }}.
    </p>
</body>
</html>