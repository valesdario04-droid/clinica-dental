<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Usuarios
            </h2>

            <a href="{{ route('users.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                + Nuevo usuario
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

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-4 py-3">{{ $user->name }}</td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">{{ $user->phone ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @switch($user->role?->name)
                                            @case('reception') Recepción @break
                                            @case('doctor') Doctor @break
                                            @case('administrator') Administradora @break
                                            @default Sin rol
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($user->active)
                                            <span class="text-green-700 font-medium">Activo</span>
                                        @else
                                            <span class="text-red-700 font-medium">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="text-blue-600 hover:underline">
                                            Editar
                                        </a>

                                        @if ($user->active && $user->id !== auth()->id())
                                           <button type="button"
                                             data-deactivate-user
        data-deactivate-url="{{ route('users.destroy', $user) }}"
        data-user-name="{{ $user->name }}"
        class="ml-3 text-red-600 hover:text-red-800 hover:underline font-medium">
    Desactivar
</button> 
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        No hay usuarios registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
    <div id="deactivate-modal"
     class="hidden fixed inset-0 z-50 items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="deactivate-modal-title">

    <div id="deactivate-backdrop" class="absolute inset-0 bg-gray-900/60"></div>

    <div class="relative w-full max-w-md bg-white rounded-xl shadow-xl p-6">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 text-xl font-bold">
                !
            </div>

            <div>
                <h3 id="deactivate-modal-title" class="text-lg font-semibold text-gray-900">
                    Desactivar usuario
                </h3>

                <p class="mt-2 text-sm text-gray-600">
                    Vas a desactivar la cuenta de
                    <strong id="deactivate-user-name" class="text-gray-900"></strong>.
                </p>

                <p class="mt-2 text-sm text-gray-500">
                    No podrá iniciar sesión ni realizar registros. Su información y sus registros anteriores se conservarán.
                </p>
            </div>
        </div>

        <form id="deactivate-form" method="POST" class="mt-6 flex justify-end gap-3">
            @csrf
            @method('DELETE')

            <button type="button"
                    id="close-deactivate-modal"
                    class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold px-4 py-2 rounded-md">
                Volver
            </button>

            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-md">
                Sí, desactivar
            </button>
        </form>
    </div>
</div>

<script>
    const deactivateModal = document.getElementById('deactivate-modal');
    const deactivateForm = document.getElementById('deactivate-form');

    document.querySelectorAll('[data-deactivate-user]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('deactivate-user-name').textContent =
                button.dataset.userName;

            deactivateForm.action = button.dataset.deactivateUrl;

            deactivateModal.classList.remove('hidden');
            deactivateModal.classList.add('flex');
        });
    });

    function closeDeactivateModal() {
        deactivateModal.classList.add('hidden');
        deactivateModal.classList.remove('flex');
    }

    document.getElementById('close-deactivate-modal')
        .addEventListener('click', closeDeactivateModal);

    document.getElementById('deactivate-backdrop')
        .addEventListener('click', closeDeactivateModal);
</script>
</x-app-layout>