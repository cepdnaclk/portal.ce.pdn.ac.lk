<?php

namespace App\Domains\Gallery\Jobs;

use App\Domains\Gallery\Models\GalleryImage;
use App\Domains\Gallery\Services\GalleryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessGalleryImage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $galleryImage;

    /**
     * Create a new job instance.
     *
     * @param GalleryImage $galleryImage
     */
    public function __construct(GalleryImage $galleryImage)
    {
        $this->galleryImage = $galleryImage;
    }

    /**
     * Execute the job.
     *
     * @param GalleryService $galleryService
     * @return void
     */
    public function handle(GalleryService $galleryService)
    {
        $galleryService->generateImageSizes($this->galleryImage);
    }
}
