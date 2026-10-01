<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
    <label for="document" class="block font-medium text-sm text-gray-700">
        Documento *
    </label>

    <div class="mt-1 flex gap-2">
        <input id="document"
               name="document"
               type="text"
               maxlength="8"
               inputmode="numeric"
               pattern="\d{8}"
               oninput="this.value = this.value.replace(/\D/g, '').slice(0, 8)"
               value="{{ old('document', $patient->document ?? '') }}"
               class="block w-full border-gray-300 rounded-md shadow-sm"
               required>

        <button type="button"
                id="lookup-dni"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 whitespace-nowrap">
            Buscar DNI
        </button>
    </div>

    <p id="dni-message" class="text-sm mt-1"></p>

    @error('document')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="birth_date" class="block font-medium text-sm text-gray-700">
        Fecha de nacimiento *
    </label>

    <input id="birth_date"
           name="birth_date"
           type="date"
           max="{{ now()->format('Y-m-d') }}"
           value="{{ old('birth_date', isset($patient) && $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '') }}"
           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
           required>

    @error('birth_date')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

    <div>
        <label for="first_name" class="block font-medium text-sm text-gray-700">
            Nombres *
        </label>

        <input id="first_name"
               name="first_name"
               type="text"
               value="{{ old('first_name', $patient->first_name ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
               required>

        @error('first_name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="last_name" class="block font-medium text-sm text-gray-700">
            Apellidos *
        </label>

        <input id="last_name"
               name="last_name"
               type="text"
               value="{{ old('last_name', $patient->last_name ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
               required>

        @error('last_name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
    <label for="sex" class="block font-medium text-sm text-gray-700">
        Sexo
    </label>

    <select id="sex"
            name="sex"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        <option value="">Seleccione una opción</option>

        @foreach (['Femenino', 'Masculino'] as $option)
            <option value="{{ $option }}"
                @selected(old('sex', $patient->sex ?? '') === $option)>
                {{ $option }}
            </option>
        @endforeach
    </select>

    @error('sex')
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
               value="{{ old('phone', $patient->phone ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>

        @error('phone')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block font-medium text-sm text-gray-700">
            Correo electrónico
        </label>

        <input id="email"
               name="email"
               type="email"
               value="{{ old('email', $patient->email ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

        @error('email')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="address" class="block font-medium text-sm text-gray-700">
            Dirección *
        </label>

        <input id="address"
               name="address"
               type="text"
               value="{{ old('address', $patient->address ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>

        @error('address')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="emergency_contact" class="block font-medium text-sm text-gray-700">
            Contacto de emergencia
        </label>

        <input id="emergency_contact"
               name="emergency_contact"
               type="text"
               maxlength="9"
               inputmode="numeric"
               pattern="\d{9}"
               oninput="this.value = this.value.replace(/\D/g, '').slice(0, 9)"
               value="{{ old('emergency_contact', $patient->emergency_contact ?? '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

        @error('emergency_contact')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
<script>
    document.getElementById('lookup-dni').addEventListener('click', async () => {
        const dniInput = document.getElementById('document');
        const message = document.getElementById('dni-message');
        const button = document.getElementById('lookup-dni');
        const dni = dniInput.value.trim();

        message.textContent = '';
        message.className = 'text-sm mt-1';

        if (!/^\d{8}$/.test(dni)) {
            message.textContent = 'Ingresa un DNI válido de 8 dígitos.';
            message.classList.add('text-red-600');
            return;
        }

        button.disabled = true;
        button.textContent = 'Consultando...';

        try {
            const response = await fetch('{{ route('patients.lookup-dni') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ dni }),
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'No se pudo consultar el DNI.');
            }

            document.getElementById('first_name').value = result.first_name;
            document.getElementById('last_name').value = result.last_name;

            message.textContent = 'Datos encontrados. Verifica la información antes de guardar.';
            message.classList.add('text-green-600');
        } catch (error) {
            message.textContent = error.message;
            message.classList.add('text-red-600');
        } finally {
            button.disabled = false;
            button.textContent = 'Buscar DNI';
        }
    });
</script>