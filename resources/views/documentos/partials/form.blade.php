<div class="mb-3 row">
    <!-- Tipo de documento -->
    <div class="col-md-3">
        <label for="tipo" class="form-label">Tipo</label>
        <select name="tipo" id="tipo" class="form-select">
            <option value="oficio" {{ old('tipo', $documento->tipo ?? '') == 'oficio' ? 'selected' : '' }}>Oficio
            </option>
            <option value="solicitud" {{ old('tipo', $documento->tipo ?? '') == 'solicitud' ? 'selected' : '' }}>
                Solicitud</option>
            <option value="resolucion" {{ old('tipo', $documento->tipo ?? '') == 'resolucion' ? 'selected' : '' }}>
                Resolución</option>
            <option value="acta" {{ old('tipo', $documento->tipo ?? '') == 'acta' ? 'selected' : '' }}>Acta</option>
            <option value="certificado" {{ old('tipo', $documento->tipo ?? '') == 'certificado' ? 'selected' : '' }}>
                Certificado</option>
            <option value="reglamento" {{ old('tipo', $documento->tipo ?? '') == 'reglamento' ? 'selected' : '' }}>
                Reglamento</option>
            <option value="contrato" {{ old('tipo', $documento->tipo ?? '') == 'contrato' ? 'selected' : '' }}>Contrato
            </option>
            <option value="informe" {{ old('tipo', $documento->tipo ?? '') == 'informe' ? 'selected' : '' }}>Informe
            </option>
            <option value="memorando" {{ old('tipo', $documento->tipo ?? '') == 'memorando' ? 'selected' : '' }}>
                Memorando</option>
            <option value="certificacion"
                {{ old('tipo', $documento->tipo ?? '') == 'certificacion' ? 'selected' : '' }}>Certificación</option>
            <option value="planificacion"
                {{ old('tipo', $documento->tipo ?? '') == 'planificacion' ? 'selected' : '' }}>Planificación</option>
            <option value="otro" {{ old('tipo', $documento->tipo ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
        </select>
        @error('tipo')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <!-- Número de Oficio -->
    <div class="col-md-3">
        <label for="numero_oficio" class="form-label">Número de Oficio</label>
        <input type="text" name="numero_oficio" id="numero_oficio" class="form-control"
            value="{{ old('numero_oficio', $documento->numero_oficio ?? '') }}">
        @error('numero_oficio')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <!-- Fecha -->
    <div class="col-md-3">
        <label for="fecha_recibido" class="form-label">Fecha Recibido</label>
        <input type="date" name="fecha_recibido" id="fecha_recibido" class="form-control"
            value="{{ old('fecha_recibido', $documento->fecha_recibido ?? now()->toDateString()) }}">
        @error('fecha_recibido')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <!-- Formato-->
    <!-- Formato (Checkbox para Virtual) -->
    <!-- Formato-->
    <div class="col-md-3">
        <label for="formato_documento" class="form-label">Formato</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="formato_documento" name="formato_documento"
                value="virtual"
                {{ old('formato_documento', $documento->formato_documento ?? '') == 'virtual' ? 'checked' : '' }}>
            <label class="form-check-label" for="formato_documento">
                Marcar si es Virtual
            </label>
        </div>
    </div>

</div>

<div class="mb-3">
    <label for="remitente" class="form-label">Remitente (Persona)</label>
    <input type="text" name="remitente" id="remitente" class="form-control"
        value="{{ old('remitente', $documento->remitente ?? '') }}">
    @error('remitente')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<!-- Entidad -->
<div class="mb-3 position-relative entidad-container">
    <label for="entidad_search">Entidad Receptora</label>
    <input type="text" class="form-control entidad_search" placeholder="Busca una entidad...">
    <input type="hidden" class="entidad_id" name="entidad_id">

    <!-- Dropdown de resultados -->
    <div class="dropdown-menu w-100 entidad_dropdown" style="display: none; max-height: 200px; overflow-y: auto;"></div>

    @error('entidad_id')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>



<div class="mb-3">
    <label for="asunto" class="form-label">Asunto</label>
    <textarea name="asunto" id="asunto" class="form-control" rows="3">{{ old('asunto', $documento->asunto ?? '') }}</textarea>
    @error('asunto')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="observaciones" class="form-label">Observaciones</label>
    <textarea name="observaciones" id="observaciones" class="form-control">{{ old('observaciones', $documento->observaciones ?? '') }}</textarea>
    @error('observaciones')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
