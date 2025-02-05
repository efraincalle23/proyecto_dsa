<div class="mb-3 row">
    <!-- Tipo -->
    <div class="form-group col-md-3">
        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" class="form-control" required>
            <option value="oficio" {{ old('tipo', $documento->tipo ?? '') == 'oficio' ? 'selected' : '' }}>Oficio
            </option>
            <option value="solicitud" {{ old('tipo', $documento->tipo ?? '') == 'solicitud' ? 'selected' : '' }}>
                Solicitud
            </option>
            <option value="otro" {{ old('tipo', $documento->tipo ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
        </select>
        @error('tipo')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group col-md-3">
        <label for="numero_oficio">Número de Oficioss {{ $documento->id }}</label>
        <input type="text" id="numero_oficio" name="numero_oficio" class="form-control" value="{{ $numeroOficio }}"
            readonly>
    </div>

    <input type="hidden" id="documento_id" name="documento_id" value="{{ $documento->id }}">
    <input type="hidden" id="documento_nombre" name="documento_nombre" value="{{ $documento->nombre_doc }}">


    <!-- Fecha de Emisión -->
    <div class="form-group col-md-3">
        <label for="fecha_recibido">Fecha de Emisión</label>
        <input type="date" id="fecha_recibido" name="fecha_recibido" class="form-control"
            value="{{ old('fecha_recibido', $documento->fecha_emision ?? now()->toDateString()) }}" required>
        @error('fecha_recibido')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <!--FORMATO-->
    <div class="form-group col-md-3">
        <label for="formato_documento" class="form-label">Formato</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="formato_documento" name="formato_documento"
                value="virtual"
                {{ old('formato_documento', $documento->formato_documento ?? '') == 'virtual' ? 'checked' : '' }}>
            <label class="form-check-label" for="formato_documento">
                Marcar si es Virtual
            </label>
        </div>
        @error('formato_documento')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

</div>

<!-- Destino -->
<div class="mb-3">
    <label for="destino">Dirigido a</label>
    <input type="text" id="destino" name="destino" class="form-control"
        value="{{ old('destino', $documento->destino ?? '') }}" required>
    @error('destino')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Entidad -->
<div class="mb-3">
    <label for="entidad_id">Entidad Receptora</label>
    <select id="entidad_id" name="entidad_id" class="form-control" required>
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
    <textarea type="text" id="asunto" name="asunto" class="form-control" required></textarea>
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
                {{ old('Respuesta_A', $documento->id ?? '') == $doc->id ? 'selected' : '' }}>
                {{ $doc->numero_oficio }} - {{ $doc->asunto }}
            </option>
        @endforeach
    </select>
    @error('Respuesta_A')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
