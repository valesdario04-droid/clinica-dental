<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AppointmentController extends Controller
{
    public function index(Request $request): View
{
    $filters = $request->validate([
        'date' => ['nullable', 'date'],
        'patient_search' => ['nullable', 'string', 'max:100'],
    ]);

    $date = $filters['date'] ?? now()->toDateString();
    $patientSearch = trim($filters['patient_search'] ?? '') ?: null;

    $appointments = $this->filteredAppointments($date, $patientSearch)
        ->paginate(10)
        ->withQueryString();

    return view('appointments.index', compact(
        'appointments',
        'date',
        'patientSearch'
    ));
}

public function findPatientByDni(Request $request)
{
    $data = $request->validate([
        'dni' => ['required', 'digits:8'],
    ]);

    $patient = Patient::where('document', $data['dni'])->first();

    if (! $patient) {
        return response()->json([
            'message' => 'No existe un paciente registrado con ese DNI.',
        ], 404);
    }

    return response()->json([
        'id' => $patient->id,
        'document' => $patient->document,
        'full_name' => $patient->first_name . ' ' . $patient->last_name,
    ]);
}

    public function create(): View
{
    $doctors = User::whereHas('role', function ($query) {
        $query->where('name', 'doctor');
    })
        ->where('active', true)
        ->orderBy('name')
        ->get();

    $selectedPatient = old('patient_id')
        ? Patient::find(old('patient_id'))
        : null;

    return view('appointments.create', compact('doctors', 'selectedPatient'));
}

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAppointment($request);

        if ($this->doctorHasConflict(
            $data['doctor_id'],
            $data['scheduled_at'],
            $data['duration_minutes']
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'scheduled_at' => 'El doctor ya tiene una cita en ese horario.',
                ]);
        }

        $data['created_by'] = auth()->id();
        $data['status'] = $data['appointment_type'] === 'immediate'
            ? 'attended'
            : 'scheduled';

        Appointment::create($data);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Cita registrada correctamente.');
    }

    public function edit(Appointment $appointment): View
{
    $doctors = User::whereHas('role', function ($query) {
        $query->where('name', 'doctor');
    })
        ->where('active', true)
        ->orderBy('name')
        ->get();

    $selectedPatient = $appointment->patient;

    return view('appointments.edit', compact(
        'appointment',
        'doctors',
        'selectedPatient'
    ));
}

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $this->validateAppointment($request, true);

        if ($this->doctorHasConflict(
            $data['doctor_id'],
            $data['scheduled_at'],
            $data['duration_minutes'],
            $appointment->id
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'scheduled_at' => 'El doctor ya tiene otra cita en ese horario.',
                ]);
        }

        $appointment->update($data);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->update([
    'status' => 'cancelled',
    'cancelled_at' => now(),
    'cancelled_by' => auth()->id(),
]);

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Cita cancelada correctamente.');
    }

    public function report(Request $request): View
{
    $filters = $request->validate([
        'date' => ['nullable', 'date'],
        'patient_search' => ['nullable', 'string', 'max:100'],
    ]);

    $date = $filters['date'] ?? now()->toDateString();
    $patientSearch = trim($filters['patient_search'] ?? '') ?: null;

    $appointments = $this->filteredAppointments($date, $patientSearch)->get();

    return view('appointments.report', compact('appointments', 'date'));
}

    public function export(Request $request): StreamedResponse
{
    $filters = $request->validate([
        'date' => ['nullable', 'date'],
        'patient_search' => ['nullable', 'string', 'max:100'],
    ]);

    $date = $filters['date'] ?? now()->toDateString();
    $patientSearch = trim($filters['patient_search'] ?? '') ?: null;

    $appointments = $this->filteredAppointments($date, $patientSearch)->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Citas');

    $sheet->fromArray([
        [
            'Fecha',
            'Hora',
            'Paciente',
            'Doctor',
            'Tipo',
            'Duración (minutos)',
            'Estado',
            'Motivo',
        ],
    ], null, 'A1');

    $row = 2;

    foreach ($appointments as $appointment) {
        $sheet->fromArray([
            [
                $appointment->scheduled_at->format('d/m/Y'),
                $appointment->scheduled_at->format('h:i A'),
                $appointment->patient->last_name . ', ' . $appointment->patient->first_name,
                $appointment->doctor->name,
                $appointment->appointment_type === 'immediate' ? 'Inmediata' : 'Reservada',
                $appointment->duration_minutes,
                $this->statusLabel($appointment->status),
                $appointment->reason ?? '',
            ],
        ], null, "A{$row}");

        $row++;
    }

    $lastRow = max(1, $row - 1);

    $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    $sheet->getStyle('A1:H1')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB('DDEBFF');

    $sheet->freezePane('A2');
    $sheet->setAutoFilter("A1:H{$lastRow}");

    foreach (range('A', 'H') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    return response()->streamDownload(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }, "reporte-citas-{$date}.xlsx", [
        'Content-Type' =>
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}

    private function filteredAppointments(string $date, ?string $patientSearch)
{
    return Appointment::with(['patient', 'doctor', 'creator'])
        ->whereDate('scheduled_at', $date)
        ->when($patientSearch, function ($query) use ($patientSearch) {
            $query->whereHas('patient', function ($patientQuery) use ($patientSearch) {
                $patientQuery
                    ->where('document', 'like', "%{$patientSearch}%")
                    ->orWhere('first_name', 'like', "%{$patientSearch}%")
                    ->orWhere('last_name', 'like', "%{$patientSearch}%");
            });
        })
        ->orderBy('scheduled_at');
}

    private function validateAppointment(Request $request, bool $updating = false): array
    {
        $rules = [
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'appointment_type' => ['required', 'in:reserved,immediate'],
            'scheduled_at' => $updating
                ? ['required', 'date']
                : ['required', 'date', 'after_or_equal:now'],
            'duration_minutes' => ['required', 'integer', 'in:15,30,45,60,90,120'],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];

        if ($updating) {
            $rules['status'] = [
                'required',
                'in:scheduled,attended,cancelled,no_show',
            ];
        }

        return $request->validate($rules);
    }

    private function doctorHasConflict(
        int $doctorId,
        string $scheduledAt,
        int $durationMinutes,
        ?int $ignoreAppointmentId = null
    ): bool {
        $start = Carbon::parse($scheduledAt);
        $end = $start->copy()->addMinutes($durationMinutes);

        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when($ignoreAppointmentId, function ($query, $appointmentId) {
                $query->where('id', '!=', $appointmentId);
            })
            ->where('scheduled_at', '<', $end)
            ->whereRaw(
                'DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?',
                [$start]
            )
            ->exists();
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'scheduled' => 'Reservada',
            'attended' => 'Atendida',
            'cancelled' => 'Cancelada',
            'no_show' => 'No asistió',
        };
    }
}