<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .header {
            text-align: center;
            border-bottom: 4px solid {{ $setting->primary_color ?? '#0F766E' }};
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .clinic-name {
            font-size: 22px;
            font-weight: bold;
            color: {{ $setting->secondary_color ?? '#111827' }};
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            color: {{ $setting->primary_color ?? '#0F766E' }};
        }

        .section {
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            background: {{ $setting->primary_color ?? '#0F766E' }};
            color: white;
            padding: 8px;
        }

        td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }

        .footer {
            margin-top: 35px;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
            font-size: 10px;
            text-align: center;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="clinic-name">
            {{ $setting->business_name }}
        </div>

        @if ($setting->ruc)
            <div>RUC: {{ $setting->ruc }}</div>
        @endif

        @if ($setting->address)
            <div>{{ $setting->address }}</div>
        @endif

        @if ($setting->phone)
            <div>Tel: {{ $setting->phone }}</div>
        @endif

        @if ($setting->email)
            <div>{{ $setting->email }}</div>
        @endif
    </div>

    <div class="document-title">
        RECETA MÉDICA
    </div>

    <div class="section">
        <p><strong>Código:</strong> {{ $prescription->prescription_code }}</p>
        <p><strong>Paciente:</strong> {{ $prescription->patient->user->name }}</p>
        <p><strong>Médico:</strong> {{ $prescription->doctor->user->name }}</p>

        @if ($setting->show_cmp && $prescription->doctor->cmp_number)
            <p><strong>CMP:</strong> {{ $prescription->doctor->cmp_number }}</p>
        @endif

        <p><strong>Fecha:</strong> {{ $prescription->issued_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th>Dosis</th>
                    <th>Frecuencia</th>
                    <th>Duración</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($prescription->items as $item)
                    <tr>
                        <td>{{ $item->medicine_name }}</td>
                        <td>{{ $item->dosage }}</td>
                        <td>{{ $item->frequency }}</td>
                        <td>{{ $item->duration }}</td>
                    </tr>

                    @if ($item->instructions)
                        <tr>
                            <td colspan="4">
                                <strong>Indicaciones:</strong> {{ $item->instructions }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($prescription->general_indications)
        <div class="section">
            <strong>Indicaciones generales:</strong>
            <p>{{ $prescription->general_indications }}</p>
        </div>
    @endif

    @if ($setting->show_signature)
        <div class="signature">
            _______________________________<br>
            Firma del médico
        </div>
    @endif

    @if ($setting->footer_text)
        <div class="footer">
            {{ $setting->footer_text }}
        </div>
    @endif

</body>

</html>
