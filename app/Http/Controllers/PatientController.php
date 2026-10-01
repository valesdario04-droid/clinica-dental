<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class PatientController extends Controller
{
    public function index(Request $request): View
{
    $filters = $request->validate([
        'search' => ['nullable', 'string', 'max:100'],
        'registered_date' => ['nullable', 'date'],
    ]);

    $search = $filters['search'] ?? null;
    $registeredDate = $filters['registered_date'] ?? null;

    $patients = Patient::query()
        ->when($search, function ($query, $search) {
            $query->where('document', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
        })
        ->when($registeredDate, function ($query, $registeredDate) {
            $query->whereDate('created_at', $registeredDate);
        })
        ->orderByDesc('created_at')
        ->paginate(10)
        ->withQueryString();

    return view('patients.index', compact('patients', 'search', 'registeredDate'));
}

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'document' => ['required', 'digits:8', 'unique:patients,document'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'sex' => ['required', 'in:Femenino,Masculino'],
            'phone' => ['required', 'digits:9'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'digits:9'],
        ]);

        Patient::create($data);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Paciente registrado correctamente.');
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'document' => ['required', 'digits:8', 'unique:patients,document,' . $patient->id],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'sex' => ['required', 'in:Femenino,Masculino'],
            'phone' => ['required', 'digits:9'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'digits:9'],
        ]);

        $patient->update($data);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Datos del paciente actualizados.');
    }

    public function show(Patient $patient): View
{
    $patient->load([
        'appointments' => function ($query) {
            $query->with('doctor')
                ->orderByDesc('scheduled_at');
        },
        'clinicalHistory',
    ]);

    return view('patients.show', compact('patient'));
}

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }

    public function lookupDni(Request $request)
{
    $data = $request->validate([
        'dni' => ['required', 'digits:8'],
    ]);

    if (blank(config('services.apiperu.token'))) {
        return response()->json([
            'message' => 'No se configuró el token de consulta DNI.',
        ], 500);
    }

    try {
        $response = Http::acceptJson()
            ->withToken(config('services.apiperu.token'))
            ->timeout(10)
            ->post(config('services.apiperu.base_url') . '/dni', [
                'dni' => $data['dni'],
            ]);
    } catch (ConnectionException $exception) {
        return response()->json([
            'message' => 'No se pudo conectar con el servicio de consulta DNI.',
        ], 503);
    }

    if (! $response->successful() || ! $response->json('success')) {
        return response()->json([
            'message' => $response->json('message', 'No se encontraron datos para este DNI.'),
        ], $response->status() ?: 422);
    }

    $person = $response->json('data');

    return response()->json([
        'document' => $person['numero'],
        'first_name' => $person['nombres'],
        'last_name' => trim(
            $person['apellido_paterno'] . ' ' . $person['apellido_materno']
        ),
    ]);
}
}