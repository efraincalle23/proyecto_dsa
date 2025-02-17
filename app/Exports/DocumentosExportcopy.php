<?php

namespace App\Exports;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Style\Style;
use Illuminate\Support\Facades\DB;

class DocumentosExportcopy
{
    public function export1()
    {
        $filePath = storage_path('app/public/documentos.xlsx');

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        $headerStyle = (new Style())->setFontBold();

        $headerRow = WriterEntityFactory::createRowFromArray([
            'Número de Oficio',
            'Asunto',
            'Fecha Recibido',
            'Formato',
            'Destino',
            'Observaciones',
            'Entidad'
        ], $headerStyle);

        $writer->addRow($headerRow);

        // Obtener los datos con el nombre de la entidad
        $documentos = DB::table('documentos_emitidos')
            ->join('entidades', 'documentos_emitidos.entidad_id', '=', 'entidades.id')
            ->select(
                'documentos_emitidos.numero_oficio',
                'documentos_emitidos.asunto',
                'documentos_emitidos.fecha_recibido',
                'documentos_emitidos.formato_documento',
                'documentos_emitidos.destino',
                'documentos_emitidos.observaciones',
                'entidades.nombre as entidad_nombre' // Aquí se obtiene el nombre de la entidad
            )
            ->get();

        foreach ($documentos as $doc) {
            $row = WriterEntityFactory::createRowFromArray([
                $doc->numero_oficio,
                $doc->asunto,
                $doc->fecha_recibido,
                $doc->formato_documento,
                $doc->destino,
                $doc->observaciones,
                $doc->entidad_nombre // Se muestra el nombre de la entidad
            ]);
            $writer->addRow($row);
        }

        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function export()
    {
        $filePath = storage_path('app/public/documentos.xlsx');

        // Crear escritor de Excel
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        // Aplicar estilo en negrita a los encabezados
        //$headerStyle = (new Style())->setFontBold();

        // Estilo para encabezado (negrita, fondo gris, alineado)
        $headerStyle = (new Style())
            ->setFontBold()
            ->setBackgroundColor("E0E0E0")
            ->setShouldWrapText(true);

        // Agregar un título grande antes del encabezado
        $titleRow = WriterEntityFactory::createRowFromArray([
            "Reporte de Documentos Emitidos y Recibidos"
        ], (new Style())->setFontBold()->setFontSize(14));

        $writer->addRow($titleRow);
        $writer->addRow(WriterEntityFactory::createRowFromArray([])); // Espacio vacío

        // Definir encabezados
        $headerRow = WriterEntityFactory::createRowFromArray([
            'Número de Oficio',
            'Asunto',
            'Fecha Recibido',
            'Formato',
            'Destino',
            'Observaciones',
            'Entidad',
            'Tipo Documento'
        ], $headerStyle);

        $writer->addRow($headerRow);

        // Consulta optimizada para obtener documentos emitidos y recibidos
        $emitidos = DB::table('documentos_emitidos')
            ->leftJoin('entidades', 'documentos_emitidos.entidad_id', '=', 'entidades.id')
            ->select(
                'documentos_emitidos.nombre_doc',
                'documentos_emitidos.asunto',
                'documentos_emitidos.fecha_recibido',
                'documentos_emitidos.formato_documento',
                'documentos_emitidos.destino',
                'documentos_emitidos.observaciones',
                'entidades.nombre as entidad_nombre',
                DB::raw("'Emitido' as tipo_documento")
            );

        $recibidos = DB::table('documentos_recibidos')
            ->leftJoin('entidades', 'documentos_recibidos.entidad_id', '=', 'entidades.id')
            ->select(
                'documentos_recibidos.nombre_doc',
                'documentos_recibidos.asunto',
                'documentos_recibidos.fecha_recibido',
                'documentos_recibidos.formato_documento',
                'documentos_recibidos.remitente',
                'documentos_recibidos.observaciones',
                'entidades.nombre as entidad_nombre',
                DB::raw("'Recibido' as tipo_documento")
            );

        $documentos = $emitidos->union($recibidos)->get();

        // Agregar datos al archivo
        foreach ($documentos as $doc) {
            $row = WriterEntityFactory::createRowFromArray([
                $doc->nombre_doc,
                $doc->asunto,
                $doc->fecha_recibido,
                $doc->formato_documento,
                $doc->destino,
                $doc->observaciones,
                $doc->entidad_nombre ?? 'N/A', // Evitar valores nulos
                $doc->tipo_documento
            ]);
            $writer->addRow($row);
        }

        $writer->close();

        // Descargar y eliminar el archivo después de enviarlo
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}