<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class HistorialDocumentosExport
{
    public function export()
    {
        $filePath = storage_path('app/public/historial_documentos.xlsx');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ✅ Agregar título centrado
        $sheet->setCellValue('A1', 'Reporte de Historial de Documentos');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ✅ Encabezados
        $headers = ['Nombre Documento', 'Remitente/Destino', 'Usuario', 'Estado Anterior', 'Estado Nuevo', 'Fecha Cambio', 'Observaciones', 'Tipo Documento'];
        $sheet->fromArray($headers, null, 'A3');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A3:H3')->applyFromArray($headerStyle);

        // ✅ Obtener los datos de historial_documentos con relaciones
        $historial = DB::table('historial_documentos')
            ->leftJoin('documentos_recibidos', 'historial_documentos.id_documento', '=', 'documentos_recibidos.id')
            ->leftJoin('documentos_emitidos', 'historial_documentos.id_documento', '=', 'documentos_emitidos.id')
            ->leftJoin('users', 'historial_documentos.id_usuario', '=', 'users.id')
            ->select(
                DB::raw("COALESCE(documentos_recibidos.nombre_doc, documentos_emitidos.nombre_doc) as nombre_documento"),
                DB::raw("CASE 
                            WHEN historial_documentos.origen = 'emitido' THEN documentos_emitidos.destino
                            ELSE documentos_recibidos.remitente
                         END as remitente_destino"),
                DB::raw("CONCAT(users.nombre, ' ', users.apellido) as usuario"),
                'historial_documentos.estado_anterior',
                'historial_documentos.estado_nuevo',
                'historial_documentos.fecha_cambio',
                'historial_documentos.observaciones',
                DB::raw("CASE 
                            WHEN historial_documentos.origen = 'emitido' THEN 'Emitido'
                            ELSE 'Recibido'
                         END as tipo_documento")
            )->get();

        // ✅ Insertar datos en el archivo Excel
        $rowIndex = 4;
        foreach ($historial as $item) {
            $sheet->fromArray([
                $item->nombre_documento ?? 'N/A',
                $item->remitente_destino ?? 'N/A',
                $item->usuario ?? 'N/A',
                $item->estado_anterior,
                $item->estado_nuevo,
                $item->fecha_cambio,
                $item->observaciones,
                $item->tipo_documento
            ], null, "A{$rowIndex}");

            // ✅ Ajustar altura de la fila para expandir texto
            $sheet->getRowDimension($rowIndex)->setRowHeight(-1);

            $rowIndex++;
        }

        // ✅ Aplicar filtros al encabezado
        $sheet->setAutoFilter("A3:H3");

        // ✅ Ajustar automáticamente el ancho de las columnas
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ✅ Permitir que el texto en la columna "Observaciones" se expanda verticalmente
        $sheet->getStyle("G4:G{$rowIndex}")->getAlignment()->setWrapText(true);

        // ✅ Guardar archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // ✅ Descargar y eliminar después
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}