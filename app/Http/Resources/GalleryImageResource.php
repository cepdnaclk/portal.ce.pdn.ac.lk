<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GalleryImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'original_filename' => $this->original_filename,
            'alt_text' => $this->alt_text,
            'caption' => $this->caption,
            'credit' => $this->credit,
            'width' => $this->width,
            'height' => $this->height,
            'file_size' => $this->file_size,
            'mime_type' => $this->mime_type,
            'is_cover' => $this->is_cover,
            'order' => $this->order,
            'urls' => $this->getAllSizes(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
