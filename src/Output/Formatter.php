<?php

namespace Nalrep\Output;

use Illuminate\Support\Collection;

class Formatter
{
    public function format($data, string $format = 'json')
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        return match ($format) {
            'json' => $this->toJson($data),
            'html' => $this->toHtml($data),
            'pdf' => $this->toPdf($data),
            default => $this->toJson($data),
        };
    }

    protected function toJson($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    protected function toHtml($data)
    {
        if (empty($data)) {
            return '<p>No results found.</p>';
        }

        if ($this->isSimple($data)) {
            return '<p class="text-lg">' . htmlspecialchars((string) $data) . '</p>';
        }

        if ($this->isList($data)) {
            $html = '<ul class="list-disc pl-5">';
            foreach ($data as $item) {
                $html .= '<li>' . htmlspecialchars((string) $item) . '</li>';
            }
            $html .= '</ul>';
            return $html;
        }

        // Default to Table
        $html = '<table class="table-auto w-full border-collapse border border-gray-200">';
        $html .= '<thead><tr>';
        
        // Normalize first row to get headers
        $firstRow = (array) (is_object($data[0]) ? $data[0] : $data[0]);
        $headers = array_keys($firstRow);
        
        foreach ($headers as $header) {
            $html .= '<th class="border border-gray-300 px-4 py-2 bg-gray-100">' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ((array) $row as $cell) {
                $html .= '<td class="border border-gray-300 px-4 py-2">' . htmlspecialchars((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    protected function isSimple($data)
    {
        return is_scalar($data) || (is_array($data) && count($data) === 1 && is_scalar(reset($data)));
    }

    protected function isList($data)
    {
        if (!is_array($data)) return false;
        // Check if it's a sequential array of scalars
        return array_is_list($data) && count($data) > 0 && is_scalar($data[0]);
    }

    protected function toPdf($data)
    {
        $html = $this->toHtml($data);
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
