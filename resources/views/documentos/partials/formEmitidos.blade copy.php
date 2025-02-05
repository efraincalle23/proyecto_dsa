<div class="mb-3 row">
    <!-- Tipo -->
    <div class="form-group col-md-3">
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" class="form-control" required onchange="toggleElements()">
            <option value="oficio" {{ old('tipo', $documento->tipo ?? '') == 'oficio' ? 'selected' : '' }}>Oficio
            </option>
            <option value="oficio_circular"
                {{ old('tipo', $documento->tipo ?? '') == 'oficio_circular' ? 'selected' : '' }}>Oficio Circular
            </option>
            <option value="solicitud" {{ old('tipo', $documento->tipo ?? '') == 'solicitud' ? 'selected' : '' }}>
                Solicitud</option>
            <option value="otro" {{ old('tipo', $documento->tipo ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
        </select>
        @error('tipo')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <!-- Número de Oficio (generado automáticamente, deshabilitado para edición) -->
    <div class="form-group col-md-3">
        <label for="numero_oficio">Número de Oficio</label>
        <input type="text" id="numero_oficio" name="numero_oficio" class="form-control"
            value="{{ isset($documento) ? $documento->numero_oficio : $numeroOficio }}" readonly>
    </div>
    <div class="form-group col-md-3">
        <label for="fecha_recibido">Fecha de Emisión</label>
        <input type="date" id="fecha_recibido" name="fecha_recibido" class="form-control"
            value="{{ old('fecha_recibido', $documento->fecha_emision ?? now()->toDateString()) }}" required>
        @error('fecha_recibido')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <!-- Formato del Documento -->
    <div class="col-md-3">
        <label for="formato_documento" class="form-label">Formato</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="formato_documento" name="formato_documento"
                value="virtual"
                {{ old('formato_documento', $documento->formato_documento ?? '') == 'virtual' ? 'checked' : '' }}>
            <label class="form-check-label" for="formato_documento">Marcar si es Virtual</label>
        </div>
    </div>
</div>

<!-- Destino -->
<div class="mb-3" id="destino-container">
    <label for="destino">Dirigido a</label>
    <input type="text" id="destino" name="destino" class="form-control"
        value="{{ old('destino', $documento->destino ?? '') }}">
    @error('destino')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Entidad -->
<div class="mb-3" id="entidad-container">
    <label for="entidad_id">Entidad Receptora</label>
    <select id="entidad_id" name="entidad_id" class="form-control">
        @foreach ($entidades as $entidad)
            <option value="{{ $entidad->id }}"
                {{ old('entidad_id', $documento->entidad_id ?? '') == $entidad->id ? 'selected' : '' }}>
                {{ $entidad->nombre }}</option>
        @endforeach
    </select>
    @error('entidad_id')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<!-- Asunto -->
<div class="mb-3">
    <label for="asunto">Asunto</label>
    <textarea id="asunto" name="asunto" class="form-control" required>{{ old('asunto', $documento->asunto ?? '') }}</textarea>
    @error('asunto')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Observaciones -->
<div class="mb-3">
    <label for="observaciones">Observaciones</label>
    <textarea id="observaciones" name="observaciones" class="form-control">{{ old('observaciones', $documento->observaciones ?? '') }}</textarea>
    @error('observaciones')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Respuesta A -->
<div class="mb-3">
    <label for="Respuesta_A" hidden>Respuesta A</label>
    <select id="Respuesta_A" name="Respuesta_A" class="form-control" hidden>
        <option value="">-- Seleccione un documento recibido --</option>
        @foreach ($documentosRecibidos as $doc)
            <option value="{{ $doc->id }}"
                {{ old('Respuesta_A', $documento->Respuesta_A ?? '') == $doc->id ? 'selected' : '' }}>
                {{ $doc->numero_oficio }} - {{ $doc->asunto }}
            </option>
        @endforeach
    </select>
    @error('Respuesta_A')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Checkboxes -->
<div class="mb-3" id="checkbox-container" style="display: none;">
    <label class="form-label">Seleccionar Destinos</label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="decanatos" name="destinos[]" value="Decanatos">
        <label class="form-check-label" for="decanatos">Decanatos</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="direcciones" name="destinos[]"
            value="Direcciones de Escuelas">
        <label class="form-check-label" for="direcciones">Direcciones de Escuelas</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="departamentos" name="destinos[]"
            value="Departamentos Académicos">
        <label class="form-check-label" for="departamentos">Departamentos Académicos</label>
    </div>
</div>

<script>
    function toggleElements() {
        var tipo = document.getElementById("tipo").value;
        var checkboxes = document.getElementById("checkbox-container");
        var destino = document.getElementById("destino-container");
        var entidad = document.getElementById("entidad-container");

        if (tipo === "oficio_circular") {
            checkboxes.style.display = "block";
            destino.style.display = "none";
            entidad.style.display = "none";
        } else {
            checkboxes.style.display = "none";
            destino.style.display = "block";
            entidad.style.display = "block";
        }
    }

    // Aseguramos que el evento se ejecute correctamente al cargar la página
    window.onload = toggleElements;
</script>
