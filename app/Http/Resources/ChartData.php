<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChartData extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $csv = [];
        $lines = file(public_path() . $this[0]->file, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $key => $value) {
            if($key === 0) continue;
            $csv[$key] = str_getcsv($value);
        }

        $result[] = [
            'id' => intval($this[0]->id),
            'fileName' => basename($this[0]->file_name),
            'csv' => $csv,
            'ch_left' => intval($this[0]->ch_left),
            'ch_top' => intval($this[0]->ch_top),
            'ch_width' => intval($this[0]->ch_width),
            'ch_height' => intval($this[0]->ch_height),
            'visible' => intval($this[0]->visible),
            'mode' => $this[0]->mode,
        ];

        return $result;
    }
}
