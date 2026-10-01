<div>
    <label for="patient_document" class="block font-medium text-sm text-gray-700">
        Paciente (DNI) *
    </label>

    <div class="mt-1 flex gap-2">
        <input id="patient_document"
               type="text"
               maxlength="8"
               inputmode="numeric"
               pattern="\d{8}"
               oninput="this.value = this.value.replace(/\D/g, '').slice(0, 8)"
               value="{{ old('patient_document', $selectedPatient?->document) }}"
               placeholder="Ingrese DNI de 8 dígitos"
               class="block w-full border-gray-300 rounded-md shadow-sm"
               required>

        <button type="button"
                id="find-patient"
                class="inline-flex items-center gap-2 bg-sky-700 hover:bg-sky-800 text-white font-semibold px-4 py-2 rounded-md whitespace-nowrap">
            Buscar
        </button>
    </div>

    <input id="patient_id"
           name="patient_id"
           type="hidden"
           value="{{ old('patient_id', $selectedPatient?->id) }}">

    <div id="patient-result" class="mt-2 text-sm">
        @if ($selectedPatient)
            <p class="text-green-700">
                Paciente seleccionado:
                <strong>{{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}</strong>
            </p>
        @endif
    </div>

    @error('patient_id')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

    <div>
        <label for="doctor_id" class="block font-medium text-sm text-gray-700">
            Doctor responsable *
        </label>

        <select id="doctor_id"
                name="doctor_id"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                required>
            <option value="">Seleccione un doctor</option>

            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}"
                    @selected(old('doctor_id', $appointment->doctor_id ?? '') == $doctor->id)>
                    {{ $doctor->name }}
                </option>
            @endforeach
        </select>

        @error('doctor_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="appointment_type" class="block font-medium text-sm text-gray-700">
            Tipo de atención *
        </label>

        <select id="appointment_type"
                name="appointment_type"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                required>
            <option value="reserved"
                @selected(old('appointment_type', $appointment->appointment_type ?? 'reserved') === 'reserved')}>
                Cita reservada
            </option>

            <option value="immediate"
                @selected(old('appointment_type', $appointment->appointment_type ?? '') === 'immediate')}>
                Atención inmediata
            </option>
        </select>

        @error('appointment_type')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="scheduled_at" class="block font-medium text-sm text-gray-700">
            Fecha y hora *
        </label>

        <input id="scheduled_at"
               name="scheduled_at"
               type="datetime-local"
               min="{{ now()->format('Y-m-d\TH:i') }}"
               value="{{ old('scheduled_at', isset($appointment) && $appointment->scheduled_at ? $appointment->scheduled_at->format('Y-m-d\TH:i') : '') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
               required>

        @error('scheduled_at')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="reason" class="block font-medium text-sm text-gray-700">

   <div>
    <label for="duration_minutes" class="block font-medium text-sm text-gray-700">
        Duración estimada *
    </label>

    <select id="duration_minutes"
            name="duration_minutes"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
            required>
        <option value="15"
            @selected(old('duration_minutes', $appointment->duration_minutes ?? 30) == 15)>
            15 minutos
        </option>

        <option value="30"
            @selected(old('duration_minutes', $appointment->duration_minutes ?? 30) == 30)>
            30 minutos
        </option>

        <option value="45"
            @selected(old('duration_minutes', $appointment->duration_minutes ?? 30) == 45)>
            45 minutos
        </option>

        <option value="60"
            @selected(old('duration_minutes', $appointment->duration_minutes ?? 30) == 60)>
            1 hora
        </option>

        <option value="90"
            @selected(old('duration_minutes', $appointment->duration_minutes ?? 30) == 90)>
            1 hora y 30 minutos
        </option>

        <option value="120"
            @selected(old('duration_minutes', $appointment->duration_minutes ?? 30) == 120)>
            2 horas
        </option>
    </select>

    @error('duration_minutes')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>     
            Motivo de la cita
        </label>

        <input id="reason"
               name="reason"
               type="text"
               value="{{ old('reason', $appointment->reason ?? '') }}"
               placeholder="Ejemplo: dolor dental, limpieza, control..."
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

        @error('reason')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    @if (isset($appointment))
        <div>
            <label for="status" class="block font-medium text-sm text-gray-700">
                Estado *
            </label>

            <select id="status"
                    name="status"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                    required>
                <option value="scheduled" @selected(old('status', $appointment->status) === 'scheduled')>
                    Reservada
                </option>
                <option value="attended" @selected(old('status', $appointment->status) === 'attended')>
                    Atendida
                </option>
                <option value="cancelled" @selected(old('status', $appointment->status) === 'cancelled')>
                    Cancelada
                </option>
                <option value="no_show" @selected(old('status', $appointment->status) === 'no_show')>
                    No asistió
                </option>
            </select>

            @error('status')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div class="md:col-span-2">
        <label for="notes" class="block font-medium text-sm text-gray-700">
            Observaciones
        </label>

        <textarea id="notes"
                  name="notes"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes', $appointment->notes ?? '') }}</textarea>

        @error('notes')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
    const patientDocumentInput = document.getElementById('patient_document');
    const patientIdInput = document.getElementById('patient_id');
    const findPatientButton = document.getElementById('find-patient');
    const patientResult = document.getElementById('patient-result');

    function clearSelectedPatient() {
        patientIdInput.value = '';
        patientResult.textContent = '';
    }

    patientDocumentInput.addEventListener('input', clearSelectedPatient);

    findPatientButton.addEventListener('click', async () => {
        const dni = patientDocumentInput.value.trim();

        clearSelectedPatient();

        if (!/^\d{8}$/.test(dni)) {
            patientResult.textContent = 'Ingresa un DNI válido de 8 dígitos.';
            patientResult.className = 'mt-2 text-sm text-red-600';
            return;
        }

        findPatientButton.disabled = true;
        findPatientButton.textContent = 'Buscando...';

        try {
            const response = await fetch('{{ route('appointments.find-patient') }}', {
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
                throw new Error(result.message || 'No se encontró el paciente.');
            }

            patientIdInput.value = result.id;
            patientResult.textContent = `Paciente seleccionado: ${result.full_name}`;
            patientResult.className = 'mt-2 text-sm text-green-700 font-medium';
        } catch (error) {
            patientResult.textContent = error.message;
            patientResult.className = 'mt-2 text-sm text-red-600';
        } finally {
            findPatientButton.disabled = false;
            findPatientButton.textContent = 'Buscar';
        }
    });
</script>