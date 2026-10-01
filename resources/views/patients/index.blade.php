<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500">Gestión clínica</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Pacientes
                </h2>
            </div>

            <a href="{{ route('patients.create') }}"
               class="inline-flex justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md">
                + Nuevo paciente
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            Buscar paciente
                        </label>

                        <input id="search"
                               type="search"
                               name="search"
                               value="{{ $search }}"
                               placeholder="DNI, nombres o apellidos"
                               class="block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label for="registered_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de registro
                        </label>

                        <input id="registered_date"
                               type="date"
                               name="registered_date"
                               value="{{ $registeredDate }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="md:col-span-3 flex flex-wrap gap-3">
                        <button type="submit"
        class="inline-flex items-center gap-2 bg-sky-700 hover:bg-sky-800 text-white font-semibold px-5 py-2 rounded-md shadow-sm transition">
    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-5 h-5"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
    </svg>
    Buscar
</button>

<a href="{{ route('patients.index') }}"
   class="inline-flex items-center gap-2 border border-sky-700 text-sky-700 hover:bg-sky-50 font-semibold px-5 py-2 rounded-md transition">
    <span class="text-lg leading-none">🧹</span>
    Limpiar filtros
</a>
                    </div>
                </form>
            </div>

            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    {{ $patients->total() }} paciente(s) encontrado(s)
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @forelse ($patients as $patient)
                    <article class="bg-white shadow-sm sm:rounded-lg p-5 border border-transparent hover:border-blue-200 transition">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-lg">
                                {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-lg">
                                            {{ $patient->first_name }} {{ $patient->last_name }}
                                        </h3>

                                        <p class="text-sm text-gray-500">
                                            DNI: {{ $patient->document }}
                                        </p>
                                    </div>

                                    <span class="text-xs text-gray-500">
                                        Registrado: {{ $patient->created_at->format('d/m/Y') }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-500">Teléfono:</span>
                                        <span class="font-medium text-gray-800">
                                            {{ $patient->phone ?? 'No registrado' }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="text-gray-500">Edad:</span>
                                        <span class="font-medium text-gray-800">
                                            {{ $patient->birth_date?->age ?? 'No registrada' }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="text-gray-500">Sexo:</span>
                                        <span class="font-medium text-gray-800">
                                            {{ $patient->sex ?? 'No indicado' }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="text-gray-500">Correo:</span>
                                        <span class="font-medium text-gray-800 break-all">
                                            {{ $patient->email ?? 'No registrado' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-t flex flex-wrap gap-x-4 gap-y-2 text-sm font-medium">
                                    <a href="{{ route('patients.show', $patient) }}"
                                       class="text-gray-700 hover:underline">
                                        Ver ficha
                                    </a>

                                    <a href="{{ route('patients.edit', $patient) }}"
                                       class="text-blue-600 hover:underline">
                                        Editar
                                    </a>

                                    
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="lg:col-span-2 bg-white shadow-sm sm:rounded-lg p-10 text-center text-gray-500">
                        No se encontraron pacientes con esos filtros.
                    </div>
                @endforelse
            </div>

            <div>
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</x-app-layout>