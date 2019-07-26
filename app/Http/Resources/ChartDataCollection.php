<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ChartDataCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $result = [];
        //$csvAll = [];

        foreach ($this as $chart) {
            $csv = [];
            $lines = file(public_path() . $chart->file, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $key => $value) {
                if($key === 0) continue;
                $csv[$key] = str_getcsv($value);
                // $csvAll[] = str_getcsv($value);
            }

            $result[] = [
                'id' => intval($chart->id),
                'fileName' => basename($chart->file_name),
                'csv' => $csv,
                'ch_left' => intval($chart->ch_left),
                'ch_top' => intval($chart->ch_top),
                'ch_width' => intval($chart->ch_width),
                'ch_height' => intval($chart->ch_height),
                'visible' => intval($chart->visible),
                'mode' => $chart->mode,
            ];
        }
/*
        array_unshift($result, [
            'id' => -1,
            'fileName' => 'All files',
            'csv' => $csvAll,
            'ch_left' => 100,
            'ch_top' => 100,
            'ch_width' => 400,
            'ch_height' => 300,
            'visible' => 0,
            'mode' => 'Linear',
        ]);*/

        return $result;
    }
}
