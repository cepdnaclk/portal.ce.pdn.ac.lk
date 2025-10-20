<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Domains\Gallery\Models\GalleryImage;
use App\Domains\Gallery\Services\GalleryService;
use App\Http\Requests\Gallery\UploadGalleryImagesRequest;
use App\Http\Requests\Gallery\UpdateGalleryImageRequest;
use App\Http\Requests\Gallery\ReorderGalleryImagesRequest;
use App\Domains\News\Models\News;
use App\Domains\Event\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    protected $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    /**
     * Show the gallery management page for a News or Event item.
     *
     * @param mixed $imageable
     * @return \Illuminate\Contracts\View\View
     */
    public function index($imageable)
    {
        try {
            // Determine type from class name
            $type = $this->getTypeFromModel($imageable);

            $stats = $this->galleryService->getGalleryStats($imageable);

            return view('backend.gallery.index', [
                'imageable' => $imageable,
                'type' => $type,
                'stats' => $stats,
            ]);
        } catch (\Exception $ex) {
            Log::error('Failed to load gallery page', ['error' => $ex->getMessage()]);
            return abort(500);
        }
    }

    /**
     * Upload images to a gallery.
     *
     * @param UploadGalleryImagesRequest $request
     * @param mixed $imageable
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(UploadGalleryImagesRequest $request, $imageable)
    {
        try {
            // Check if max images limit reached
            $stats = $this->galleryService->getGalleryStats($imageable);
            if (!$stats['can_add_more']) {
                return response()->json([
                    'message' => 'Maximum number of images reached',
                ], 422);
            }

            $images = $this->galleryService->uploadImages(
                $imageable,
                $request->file('images'),
                $request->input('metadata', [])
            );

            return response()->json([
                'message' => 'Images uploaded successfully',
                'images' => $images,
            ], 201);
        } catch (\Exception $ex) {
            Log::error('Failed to upload gallery images', [
                'error' => $ex->getMessage(),
                'imageable_id' => $imageable->id,
            ]);

            return response()->json([
                'message' => 'Failed to upload images: ' . $ex->getMessage(),
            ], 500);
        }
    }

    /**
     * Update image metadata.
     *
     * @param UpdateGalleryImageRequest $request
     * @param int $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateGalleryImageRequest $request, int $imageId)
    {
        try {
            $image = GalleryImage::findOrFail($imageId);

            $updatedImage = $this->galleryService->updateImage($image, $request->validated());

            return response()->json([
                'message' => 'Image updated successfully',
                'image' => $updatedImage,
            ]);
        } catch (\Exception $ex) {
            Log::error('Failed to update gallery image', [
                'error' => $ex->getMessage(),
                'image_id' => $imageId,
            ]);

            return response()->json([
                'message' => 'Failed to update image',
            ], 500);
        }
    }

    /**
     * Set an image as the cover.
     *
     * @param mixed $imageable
     * @param int $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCover($imageable, int $image)
    {
        try {
            $coverImage = $this->galleryService->setCoverImage($imageable, $image);

            return response()->json([
                'message' => 'Cover image set successfully',
                'image' => $coverImage,
            ]);
        } catch (\Exception $ex) {
            Log::error('Failed to set cover image', [
                'error' => $ex->getMessage(),
                'imageable_id' => $imageable->id,
                'image_id' => $image,
            ]);

            return response()->json([
                'message' => 'Failed to set cover image',
            ], 500);
        }
    }

    /**
     * Reorder gallery images.
     *
     * @param ReorderGalleryImagesRequest $request
     * @param mixed $imageable
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(ReorderGalleryImagesRequest $request, $imageable)
    {
        try {
            $this->galleryService->reorderImages($imageable, $request->input('ordered_ids'));

            return response()->json([
                'message' => 'Images reordered successfully',
            ]);
        } catch (\Exception $ex) {
            Log::error('Failed to reorder gallery images', [
                'error' => $ex->getMessage(),
                'imageable_id' => $imageable->id,
            ]);

            return response()->json([
                'message' => 'Failed to reorder images',
            ], 500);
        }
    }

    /**
     * Delete an image from the gallery.
     *
     * @param int $imageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $imageId)
    {
        try {
            $image = GalleryImage::findOrFail($imageId);

            $this->galleryService->deleteImage($image);

            return response()->json([
                'message' => 'Image deleted successfully',
            ]);
        } catch (\Exception $ex) {
            Log::error('Failed to delete gallery image', [
                'error' => $ex->getMessage(),
                'image_id' => $imageId,
            ]);

            return response()->json([
                'message' => 'Failed to delete image',
            ], 500);
        }
    }

    /**
     * Get the type from the model class.
     *
     * @param mixed $imageable
     * @return string
     */
    protected function getTypeFromModel($imageable): string
    {
        $class = get_class($imageable);
        $shortName = class_basename($class);

        return strtolower($shortName);
    }
}
