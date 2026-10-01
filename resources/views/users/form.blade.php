<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
        <label for="name" class="block font-medium text-sm text-gray-700">
            Nombre completo *
        </label>

        <input id="name"
               name="name"
               type="text"
               value="{{ old('name', $user->name ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
               required>

        @error('name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="role_id" class="block font-medium text-sm text-gray-700">
            Rol *
        </label>

        <select id="role_id"
                name="role_id"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                required>
            <option value="">Seleccione un rol</option>

            @foreach ($roles as $role)
                <option value="{{ $role->id }}"
                    @selected(old('role_id', $user->role_id ?? '') == $role->id)>
                    @switch($role->name)
                        @case('reception') Recepción @break
                        @case('doctor') Doctor externo @break
                        @case('administrator') Administradora @break
                    @endswitch
                </option>
            @endforeach
        </select>

        @error('role_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block font-medium text-sm text-gray-700">
            Correo electrónico *
        </label>

        <input id="email"
               name="email"
               type="email"
               value="{{ old('email', $user->email ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
               required>

        @error('email')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone" class="block font-medium text-sm text-gray-700">
            Teléfono *
        </label>

        <input id="phone"
               name="phone"
               type="text"
               maxlength="9"
               inputmode="numeric"
               pattern="\d{9}"
               oninput="this.value = this.value.replace(/\D/g, '').slice(0, 9)"
               value="{{ old('phone', $user->phone ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

        @error('phone')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block font-medium text-sm text-gray-700">
            Contraseña {{ isset($user) ? '(dejar vacío para no cambiar)' : '*' }}
        </label>

        <input id="password"
               name="password"
               type="password"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
               @if (!isset($user)) required @endif>

        @error('password')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block font-medium text-sm text-gray-700">
            Confirmar contraseña {{ isset($user) ? '' : '*' }}
        </label>

        <input id="password_confirmation"
               name="password_confirmation"
               type="password"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
               @if (!isset($user)) required @endif>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center">
            <input type="checkbox"
                   name="active"
                   value="1"
                   class="rounded border-gray-300 text-blue-600 shadow-sm"
                   @checked(old('active', $user->active ?? true))>

            <span class="ml-2 text-sm text-gray-700">
                Usuario activo
            </span>
        </label>

        @error('active')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>