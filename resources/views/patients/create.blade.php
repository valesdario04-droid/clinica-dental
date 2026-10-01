<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registrar paciente
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('patients.store') }}">
                    @csrf

                    @include('patients.form')

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('patients.index') }}"
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                            Guardar paciente
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>