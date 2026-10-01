<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500">Ficha del paciente</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $patient->first_name }} {{ $patient->last_name }}
                </h2>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('patients.edit', $patient) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md">
                    Editar datos
                </a>

                <a href="{{ route('patients.index') }}"
                   class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-md">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between border-b pb-4 mb-5">
                    <h3 class="text-lg font-semibold text-gray-800">Datos personales</h3>

                    @if ($patient->clinicalHistory)
                        <span class="bg-green-100 text-green-700 text-sm font-medium px-3 py-1 rounded-full">
                            Historia clínica creada
                        </span>
                    @else
                        <span class="bg-yellow-100 text-yellow-700 text-sm font-medium px-3 py-1 rounded-full">
                            Sin historia clínica
                        </span>
                    @endif
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-5">
                    <div>
                        <dt class="text-sm text-gray-500">DNI / Documento</dt>
                        <dd class="font-medium text-gray-900">{{ $patient->document }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Fecha de nacimiento</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $patient->birth_date?->format('d/m/Y') ?? 'No registrada' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Edad</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $patient->birth_date?->age ?? 'No registrada' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Sexo</dt>
                        <dd class="font-medium text-gray-900">{{ $patient->sex ?? 'No indicado' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Teléfono</dt>
                        <dd class="font-medium text-gray-900">{{ $patient->phone ?? 'No registrado' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Correo</dt>
                        <dd class="font-medium text-gray-900">{{ $patient->email ?? 'No registrado' }}</dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm text-gray-500">Dirección</dt>
                        <dd class="font-medium text-gray-900">{{ $patient->address ?? 'No registrada' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Contacto de emergencia</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $patient->emergency_contact ?? 'No registrado' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between border-b pb-4 mb-5">
                    <h3 class="text-lg font-semibold text-gray-800">Citas del paciente</h3>
                    <span class="text-sm text-gray-500">{{ $patient->appointments->count() }} registradas</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha y hora</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse ($patient->appointments as $appointment)
                                <tr>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $appointment->scheduled_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $appointment->doctor?->name ?? 'Sin asignar' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $appointment->reason }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="capitalize text-sm font-medium text-gray-700">
                                            {{ str_replace('_', ' ', $appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                        Aún no hay citas registradas para este paciente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>