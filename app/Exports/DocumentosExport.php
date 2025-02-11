<?php
namespace App\Exports;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentosExport
{
    public function export($user)
    {
        $filePath = storage_path('app/public/documentos.xlsx');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ✅ Agregar título grande centrado
        $sheet->setCellValue('A1', 'Reporte de Documentos Emitidos y Recibidos');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ✅ Encabezados con estilo
        $headers = ['Número de Oficio', 'Asunto', 'Fecha Recibido', 'Formato', 'Destino', 'Observaciones', 'Entidad', 'Tipo Documento'];
        $sheet->fromArray($headers, null, 'A3');
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A3:H3')->applyFromArray($headerStyle);

        // ✅ Obtener documentos emitidos y recibidos
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

        if ($user->rol === 'Administrativo') {
            $emitidos->whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('historial_documentos')
                    ->whereRaw('historial_documentos.id_documento = documentos_emitidos.id')
                    ->where(function ($q) use ($user) {
                        $q->where('historial_documentos.id_usuario', $user->id)
                            ->orWhere('historial_documentos.destinatario', $user->id);
                    });
            });

            $recibidos->whereExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('historial_documentos')
                    ->whereRaw('historial_documentos.id_documento = documentos_recibidos.id')
                    ->where(function ($q) use ($user) {
                        $q->where('historial_documentos.id_usuario', $user->id)
                            ->orWhere('historial_documentos.destinatario', $user->id);
                    });
            });
        }
        $documentos = $emitidos->union($recibidos)->get();

        // ✅ Insertar datos en el archivo Excel
        $rowIndex = 4;
        foreach ($documentos as $doc) {
            $sheet->fromArray([
                $doc->nombre_doc,
                $doc->asunto, // Insertar todo el texto del "Asunto"
                $doc->fecha_recibido,
                $doc->formato_documento,
                $doc->destino ?? 'N/A',
                $doc->observaciones,
                $doc->entidad_nombre ?? 'N/A',
                $doc->tipo_documento
            ], null, "A{$rowIndex}");

            // ✅ Configurar la columna "Asunto" para mostrar solo una línea
            $sheet->getStyle("B{$rowIndex}")->getAlignment()->setWrapText(false); // Desactivar ajuste de texto
            $sheet->getColumnDimension('B')->setWidth(30); // Ancho fijo para la columna "Asunto"

            $rowIndex++;
        }

        // ✅ Ajustar automáticamente el ancho de las demás columnas (excepto "Asunto")
        foreach (range('A', 'H') as $col) {
            if ($col !== 'B') { // Excluir la columna "Asunto"
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // ✅ Aplicar filtros al encabezado
        $sheet->setAutoFilter("A3:H3");

        // ✅ Guardar archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // ✅ Descargar y eliminar después
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}