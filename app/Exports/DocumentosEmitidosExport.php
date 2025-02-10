<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DocumentosEmitidosExport
{
    public function export()
    {
        $filePath = storage_path('app/public/documentos_emitidos.xlsx');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ✅ Agregar título centrado
        $sheet->setCellValue('A1', 'Reporte de Documentos Emitidos');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ✅ Encabezados
        $headers = ['Número de Oficio', 'Asunto', 'Fecha Emitido', 'Formato', 'Destino', 'Observaciones', 'Entidad', 'Tipo Documento'];
        $sheet->fromArray($headers, null, 'A3');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A3:H3')->applyFromArray($headerStyle);

        // ✅ Obtener solo documentos emitidos
        $documentos = DB::table('documentos_emitidos')
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
            )->get();

        // ✅ Insertar datos en el archivo Excel
        $rowIndex = 4;
        foreach ($documentos as $doc) {
            $sheet->fromArray([
                $doc->nombre_doc,
                $doc->asunto,
                $doc->fecha_recibido,
                $doc->formato_documento,
                $doc->destino ?? 'N/A',
                $doc->observaciones,
                $doc->entidad_nombre ?? 'N/A',
                $doc->tipo_documento
            ], null, "A{$rowIndex}");

            // ✅ Ajustar altura de la fila para que el texto de "Asunto" se expanda
            $sheet->getRowDimension($rowIndex)->setRowHeight(-1);

            $rowIndex++;
        }

        // ✅ Aplicar filtros al encabezado
        $sheet->setAutoFilter("A3:H3");

        // ✅ Ajustar automáticamente el ancho de las columnas
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ✅ Permitir que el texto en la columna "Asunto" se expanda verticalmente
        $sheet->getStyle("B4:B{$rowIndex}")->getAlignment()->setWrapText(true);

        // ✅ Guardar archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // ✅ Descargar y eliminar después
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}