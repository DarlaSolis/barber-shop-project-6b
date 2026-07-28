<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrosExport
{
    protected Collection $appointments;

    public function __construct(Collection $appointments)
    {
        $this->appointments = $appointments;
    }

    public function download(string $filename): StreamedResponse
    {
        return new StreamedResponse(function () {
            $options = new Options();
            $options->setColumnWidth(18, 1); // Fecha
            $options->setColumnWidth(22, 2); // Cliente
            $options->setColumnWidth(22, 3); // Barbero
            $options->setColumnWidth(20, 4); // Servicio
            $options->setColumnWidth(16, 5); // Método
            $options->setColumnWidth(12, 6); // Total
            $options->setColumnWidth(16, 7); // Comisión
            $options->setColumnWidth(16, 8); // Barbero (40%)

            $writer = new Writer($options);
            $writer->openToFile('php://output');

            $headerStyle = (new Style())
                ->withFontBold(true)
                ->withFontColor(Color::WHITE)
                ->withBackgroundColor('F59E0B');

            $writer->addRow(Row::fromValuesWithStyle(
                ['Fecha', 'Cliente', 'Barbero', 'Servicio', 'Método', 'Total', 'Comisión (60%)', 'Barbero (40%)'],
                $headerStyle
            ));

            foreach ($this->appointments as $a) {
                $precio = $a->service->price ?? 0;
                $writer->addRow(Row::fromValues([
                    $a->appointment_date->format('d/m/Y H:i'),
                    $a->client->name ?? '—',
                    $a->barber->name ?? '—',
                    $a->service->name ?? '—',
                    $a->payment_method ?? '—',
                    round($precio, 2),
                    round($precio * 0.60, 2),
                    round($precio * 0.40, 2),
                ]));
            }

            $writer->close();
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
