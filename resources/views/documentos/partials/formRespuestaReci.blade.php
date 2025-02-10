<input type="hidden" id="documento_id" name="documento_id" value="{{ $documento->id }}">
<input type="hidden" id="documento_nombre" name="documento_nombre" value="{{ $documento->nombre_doc }}">
<div class="mb-3 row">
    <!-- Tipo de documento -->
    <div class="col-md-3">
        <label for="tipo" class="form-label">Tipo</label>
        <select name="tipo" id="tipo" class="form-select">
            <option value="oficio">Oficio</option>
            <option value="solicitud">Solicitud</option>
            <option value="resolucion">Resolución</option>
            <option value="acta">Acta</option>
            <option value="certificado">Certificado</option>
            <option value="reglamento">Reglamento</option>
            <option value="contrato">Contrato</option>
            <option value="informe">Informe</option>
            <option value="memorando">Memorando</option>
            <option value="certificacion">Certificación</option>
            <option value="planificacion">Planificación</option>
            <option value="otro">Otro</option>
        </select>
        @error('tipo')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <!-- Número de Oficio -->
    <div class="col-md-3">
        <label for="numero_oficio" class="form-label">Número de Oficio</label>
        <input type="text" name="numero_oficio" id="numero_oficio" class="form-control">
        @error('numero_oficio')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <!-- Fecha -->
    <div class="col-md-3">
        <label for="fecha_recibido" class="form-label">Fecha Recibido</label>
        <input type="date" name="fecha_recibido" id="fecha_recibido" class="form-control"
            value="{{ now()->toDateString() }}">
        @error('fecha_recibido')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <!-- Formato-->
    <div class="col-md-3">
        <label for="formato_documento" class="form-label">Formato</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="formato_documento" name="formato_documento"
                value="virtual">
            <label class="form-check-label" for="formato_documento">
                Marcar si es Virtual
            </label>
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="remitente" class="form-label">Remitente (Persona)</label>
    <input type="text" name="remitente" id="remitente" class="form-control">
    @error('remitente')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="entidad_id" class="form-label">Entidad Remitente</label>
    <select name="entidad_id" id="entidad_id" class="js-example-basic-single">
        <option value="">Seleccione una entidad</option>
        @foreach ($entidades as $entidad)
            <option value="{{ $entidad->id }}">{{ $entidad->nombre }}</option>
        @endforeach
    </select>
    @error('entidad_id')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="asunto" class="form-label">Asunto</label>
    <textarea name="asunto" id="asunto" class="form-control" rows="3"></textarea>
    @error('asunto')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="observaciones" class="form-label">Observaciones</label>
    <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
    @error('observaciones')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
