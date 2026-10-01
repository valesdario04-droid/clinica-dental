<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Agenda de citas
            </h2>

            <a href="{{ route('appointments.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                + Nueva cita
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="date" class="block font-medium text-sm text-gray-700">
                            Fecha de la agenda
                        </label>

                        <input id="date"
                               name="date"
                               type="date"
                               value="{{ $date }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
    <label for="patient_search" class="block font-medium text-sm text-gray-700">
        Buscar paciente
    </label>

    <input id="patient_search"
           name="patient_search"
           type="search"
           value="{{ $patientSearch }}"
           placeholder="DNI, nombre o apellido"
           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
</div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">
                            Filtrar
                        </button>

                        <a href="{{ route('appointments.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
                            Limpiar
                        </a>
                    </div>
                </form>

                <div class="mt-6 flex flex-wrap justify-between gap-3 border-t pt-5">
                    <div class="flex gap-3">
                        <a href="{{ route('appointments.index', [
                            'date' => \Carbon\Carbon::parse($date)->subDay()->toDateString(),
                            'patient_search' => $patientSearch
                        ]) }}"
                           class="border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded">
                            ← Día anterior
                        </a>

                        <a href="{{ route('appointments.index', [
                            'date' => \Carbon\Carbon::parse($date)->addDay()->toDateString(),
                            'patient_search' => $patientSearch
                        ]) }}"
                           class="border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded">
                            Día siguiente →
                        </a>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('appointments.report', [
                            'date' => $date,
                            'patient_search' => $patientSearch
                        ]) }}"
                           target="_blank"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                            Reporte imprimible
                        </a>

                        <a href="{{ route('appointments.export', [
                            'date' => $date,
                            'patient_search' => $patientSearch
                        ]) }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            Exportar Excel
                        </a>
                    </div>
                </div>

                <p class="mt-5 text-sm text-gray-600">
                    Mostrando agenda del:
                    <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>
                </p>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duración</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse ($appointments as $appointment)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $appointment->scheduled_at->format('h:i A') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $appointment->patient->last_name }},
                                        {{ $appointment->patient->first_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $appointment->doctor->name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $appointment->duration_minutes }} min
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $appointment->appointment_type === 'immediate' ? 'Inmediata' : 'Reservada' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @switch($appointment->status)
                                            @case('scheduled') Reservada @break
                                            @case('attended') Atendida @break
                                            @case('cancelled') Cancelada @break
                                            @case('no_show') No asistió @break
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('appointments.edit', $appointment) }}"
                                           class="text-blue-600 hover:underline">
                                            Editar
                                        </a>

                                        @if (!in_array($appointment->status, ['cancelled', 'attended']))
                                            <button type="button"
        data-cancel-appointment
        data-cancel-url="{{ route('appointments.destroy', $appointment) }}"
        data-patient="{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}"
        data-date="{{ $appointment->scheduled_at->format('d/m/Y h:i A') }}"
        class="ml-3 text-red-600 hover:text-red-800 hover:underline font-medium">
    Cancelar
</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        No hay citas registradas para esta fecha.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $appointments->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="cancel-modal"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="cancel-modal-title">

    <div id="cancel-backdrop" class="absolute inset-0 bg-gray-900/60"></div>

    <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl p-6">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                !
            </div>

            <div>
                <h3 id="cancel-modal-title" class="text-lg font-semibold text-gray-900">
                    Cancelar cita
                </h3>

                <p class="mt-2 text-sm text-gray-600">
                    Estás a punto de cancelar la cita de
                    <strong id="cancel-patient" class="text-gray-900"></strong>
                    programada para
                    <strong id="cancel-date" class="text-gray-900"></strong>.
                </p>

                <p class="mt-2 text-sm text-gray-500">
                    La cita se conservará en el historial con estado “Cancelada”.
                </p>
            </div>
        </div>

        <form id="cancel-form" method="POST" class="mt-6 flex justify-end gap-3">
            @csrf
            @method('DELETE')

            <button type="button"
                    id="close-cancel-modal"
                    class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold px-4 py-2 rounded-md">
                Volver
            </button>

            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-md">
                Sí, cancelar cita
            </button>
        </form>
    </div>
</div>

<script>
    const cancelModal = document.getElementById('cancel-modal');
    const cancelForm = document.getElementById('cancel-form');

    document.querySelectorAll('[data-cancel-appointment]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('cancel-patient').textContent = button.dataset.patient;
            document.getElementById('cancel-date').textContent = button.dataset.date;
            cancelForm.action = button.dataset.cancelUrl;

            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
        });
    });

    function closeCancelModal() {
        cancelModal.classList.add('hidden');
        cancelModal.classList.remove('flex');
    }

    document.getElementById('close-cancel-modal').addEventListener('click', closeCancelModal);
    document.getElementById('cancel-backdrop').addEventListener('click', closeCancelModal);
</script>
</x-app-layout>