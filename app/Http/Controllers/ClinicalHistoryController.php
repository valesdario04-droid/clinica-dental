<?php

namespace App\Http\Controllers;

use App\Models\ClinicalHistory;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClinicalHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $histories = $this->accessibleHistories()
            ->when($search, function ($query, $search) {
                $query->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery
                        ->where('document', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('clinical_histories.index', compact('histories', 'search'));
    }

    public function create(): View
    {
        $patients = $this->availablePatients()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('clinical_histories.create', compact('patients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => [
                'required',
                'exists:patients,id',
                'unique:clinical_histories,patient_id',
            ],
            'medical_background' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'general_notes' => ['nullable', 'string'],
        ]);

        $this->ensurePatientAccess($data['patient_id']);

        $data['created_by'] = auth()->id();

        $history = ClinicalHistory::create($data);

        return redirect()
            ->route('clinical-histories.show', $history)
            ->with('success', 'Historia clínica creada correctamente.');
    }

    public function show(ClinicalHistory $clinicalHistory): View
    {
        $this->ensureHistoryAccess($clinicalHistory);

        $clinicalHistory->load(['patient', 'creator']);

        return view('clinical_histories.show', compact('clinicalHistory'));
    }

    public function edit(ClinicalHistory $clinicalHistory): View
    {
        $this->ensureHistoryAccess($clinicalHistory);

        return view('clinical_histories.edit', compact('clinicalHistory'));
    }

    public function update(Request $request, ClinicalHistory $clinicalHistory): RedirectResponse
    {
        $this->ensureHistoryAccess($clinicalHistory);

        $data = $request->validate([
            'medical_background' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'general_notes' => ['nullable', 'string'],
        ]);

        $clinicalHistory->update($data);

        return redirect()
            ->route('clinical-histories.show', $clinicalHistory)
            ->with('success', 'Historia clínica actualizada correctamente.');
    }

    private function accessibleHistories()
    {
        return ClinicalHistory::with(['patient', 'creator'])
            ->when($this->isDoctor(), function ($query) {
                $query->whereHas('patient.appointments', function ($appointmentQuery) {
                    $appointmentQuery->where('doctor_id', auth()->id());
                });
            });
    }

    private function availablePatients()
    {
        return Patient::query()
            ->doesntHave('clinicalHistory')
            ->when($this->isDoctor(), function ($query) {
                $query->whereHas('appointments', function ($appointmentQuery) {
                    $appointmentQuery->where('doctor_id', auth()->id());
                });
            });
    }

    private function ensurePatientAccess(int $patientId): void
    {
        if ($this->isDoctor()) {
            $hasAppointment = Patient::whereKey($patientId)
                ->whereHas('appointments', function ($query) {
                    $query->where('doctor_id', auth()->id());
                })
                ->exists();

            abort_unless($hasAppointment, 403, 'No tienes acceso a este paciente.');
        }
    }

    private function ensureHistoryAccess(ClinicalHistory $clinicalHistory): void
    {
        if ($this->isDoctor()) {
            $hasAppointment = $clinicalHistory->patient
                ->appointments()
                ->where('doctor_id', auth()->id())
                ->exists();

            abort_unless(
                $hasAppointment,
                403,
                'No tienes acceso a esta historia clínica.'
            );
        }
    }

    private function isDoctor(): bool
    {
        return auth()->user()->role?->name === 'doctor';
    }
}