<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DocumentosRecibidosExport
{
    public function exportfuncaba()
    {
        $filePath = storage_path('app/public/documentos_recibidos.xlsx');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ✅ Agregar título centrado
        $sheet->setCellValue('A1', 'Reporte de Documentos Recibidos');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ✅ Encabezados
        $headers = ['Número de Oficio', 'Asunto', 'Fecha Recibido', 'Formato', 'Remitente', 'Observaciones', 'Entidad', 'Tipo Documento'];
        $sheet->fromArray($headers, null, 'A3');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A3:H3')->applyFromArray($headerStyle);

        // ✅ Obtener solo documentos recibidos
        $documentos = DB::table('documentos_recibidos')
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
            )->get();

        // ✅ Insertar datos en el archivo Excel
        $rowIndex = 4;
        foreach ($documentos as $doc) {
            $sheet->fromArray([
                $doc->nombre_doc,
                $doc->asunto,
                $doc->fecha_recibido,
                $doc->formato_documento,
                $doc->remitente ?? 'N/A',
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
    public function export($user)
    {
        $filePath = storage_path('app/public/documentos_recibidos.xlsx');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ✅ Agregar título centrado
        $sheet->setCellValue('A1', 'Reporte de Documentos Recibidos');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ✅ Encabezados
        $headers = ['Número de Oficio', 'Asunto', 'Fecha Recibido', 'Formato', 'Remitente', 'Observaciones', 'Entidad', 'Tipo Documento'];
        $sheet->fromArray($headers, null, 'A3');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A3:H3')->applyFromArray($headerStyle);

        // ✅ Consulta de documentos recibidos con filtrado según el rol
        $query = DB::table('documentos_recibidos')
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

        if ($user->rol === 'Administrativo') {
            $query->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('historial_documentos')
                    ->whereRaw('historial_documentos.id_documento = documentos_recibidos.id')
                    ->where(function ($q) use ($user) {
                        $q->where('historial_documentos.id_usuario', $user->id)
                            ->orWhere('historial_documentos.destinatario', $user->id);
                    });
            });
        }

        $documentos = $query->get();

        // ✅ Insertar datos en el archivo Excel
        $rowIndex = 4;
        foreach ($documentos as $doc) {
            $sheet->fromArray([
                $doc->nombre_doc,
                $doc->asunto,
                $doc->fecha_recibido,
                $doc->formato_documento,
                $doc->remitente ?? 'N/A',
                $doc->observaciones,
                $doc->entidad_nombre ?? 'N/A',
                $doc->tipo_documento
            ], null, "A{$rowIndex}");

            $sheet->getRowDimension($rowIndex)->setRowHeight(-1);
            $rowIndex++;
        }

        // ✅ Aplicar filtros y ajustar columnas
        $sheet->setAutoFilter("A3:H3");
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle("B4:B{$rowIndex}")->getAlignment()->setWrapText(true);

        // ✅ Guardar archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}