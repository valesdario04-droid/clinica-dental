<x-app-layout>
    <x-slot name="header">
        <h2>Panel de Administración</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Bienvenida, {{ auth()->user()->name }}</h3>
                <p class="mt-2">Tienes acceso completo a pacientes, citas, historias clínicas, servicios, usuarios y reportes.</p>
            </div>
        </div>
    </div>
</x-app-layout>