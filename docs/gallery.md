# Gallery Feature Documentation

## Overview

The Gallery feature allows administrators to attach multiple images to News and Event items. Each gallery supports:
- Multiple image uploads
- Image reordering via drag-and-drop
- Cover image selection
- Metadata (alt text, caption, credit)
- Automatic image resizing
- JSON API exposure

## Architecture

### Database Schema

The `gallery_images` table uses a polymorphic relationship to support both News and Events:

```
gallery_images
├── id (primary key)
├── imageable_type (morphs)
├── imageable_id (morphs)
├── filename
├── original_filename
├── disk
├── path
├── mime_type
├── file_size
├── width
├── height
├── alt_text
├── caption
├── credit
├── order
├── is_cover
├── deleted_at (soft deletes)
└── timestamps
```

### Models

- **GalleryImage**: Core model representing a gallery image
- **News**: Has polymorphic relationship with GalleryImage
- **Event**: Has polymorphic relationship with GalleryImage

## Configuration

Gallery configuration is stored in `config/gallery.php` and can be customized via environment variables:

```env
GALLERY_ENABLED=true
GALLERY_DISK=public
GALLERY_MAX_IMAGES=12
GALLERY_MAX_FILE_SIZE=10240
GALLERY_QUEUE_PROCESSING=false
```

### Configuration Options

| Option | Description | Default |
|--------|-------------|---------|
| `enabled` | Enable/disable gallery feature | `true` |
| `disk` | Storage disk (local, public, s3) | `public` |
| `max_images` | Maximum images per item | `12` |
| `max_file_size` | Max file size in KB | `10240` (10MB) |
| `allowed_mimes` | Allowed MIME types | `['image/jpeg']` |
| `min_width` | Minimum image width | `200` |
| `min_height` | Minimum image height | `200` |
| `queue_processing` | Queue image processing | `false` |

### Image Sizes

Generated sizes are configured in `config/gallery.php`:

```php
'sizes' => [
    'thumb' => [
        'width' => 400,
        'height' => 400,
        'aspect_ratio' => true,
    ],
    'medium' => [
        'width' => 800,
        'height' => 800,
        'aspect_ratio' => true,
    ],
],
```

## Admin Usage

### Accessing Gallery Management

1. Navigate to News or Events in the admin dashboard
2. Edit an existing News/Event item
3. Click the "Manage Gallery" button at the bottom of the edit form
4. You'll be redirected to the Gallery Management page

### Uploading Images

1. On the Gallery Management page, select one or multiple JPEG images
2. Click "Upload Images"
3. Images will be uploaded and automatically processed
4. The first uploaded image is automatically set as the cover

### Managing Images

#### Update Metadata
- Edit alt text (required for accessibility)
- Add caption (optional, supports basic HTML tags)
- Add credit/photographer name (optional)
- Click "Save" to update

#### Set Cover Image
- Click "Set as Cover" on any image to make it the primary image
- Only one image can be the cover at a time

#### Reorder Images
- Drag images by the footer handle
- Order is automatically saved
- Images appear in the API in this order

#### Delete Images
- Click the "Delete" button on any image
- Confirm deletion
- If the deleted image was the cover, the first remaining image becomes the cover

### Constraints

- Only JPEG images are allowed for security
- Maximum file size: 10 MB per image
- Minimum dimensions: 200x200 pixels
- Maximum 12 images per News/Event item (configurable)

## API Endpoints

### News API

#### List News with Gallery
```
GET /api/news/v1/
```

Response includes gallery array with all images ordered by `order` field.

#### Get Single News with Gallery
```
GET /api/news/v1/{id}
```

### Events API

#### List Events with Gallery
```
GET /api/events/v1/
```

#### Get Single Event with Gallery
```
GET /api/events/v1/{id}
```

### Response Format

```json
{
  "id": 1,
  "title": "Sample News",
  "description": "...",
  "gallery": [
    {
      "id": 1,
      "filename": "abc123.jpg",
      "original_filename": "photo.jpg",
      "alt_text": "Description",
      "caption": "Photo caption",
      "credit": "Photographer Name",
      "width": 1920,
      "height": 1080,
      "file_size": 2048576,
      "mime_type": "image/jpeg",
      "is_cover": true,
      "order": 0,
      "urls": {
        "original": "https://example.com/storage/gallery/abc123.jpg",
        "thumb": "https://example.com/storage/gallery/abc123_thumb.jpg",
        "medium": "https://example.com/storage/gallery/abc123_medium.jpg"
      },
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

## Frontend Implementation

### Displaying Gallery Images

```blade
@if(config('gallery.enabled') && $news->gallery->count() > 0)
    <div class="gallery">
        @foreach($news->gallery as $image)
            <figure>
                <img 
                    src="{{ $image->getSizeUrl('medium') }}"
                    srcset="{{ $image->getSizeUrl('thumb') }} 400w, 
                            {{ $image->getSizeUrl('medium') }} 800w"
                    sizes="(min-width: 1024px) 800px, 100vw"
                    alt="{{ $image->alt_text }}"
                    loading="lazy"
                    decoding="async"
                    width="{{ $image->width }}"
                    height="{{ $image->height }}">
                @if($image->caption)
                    <figcaption>{{ $image->caption }}</figcaption>
                @endif
            </figure>
        @endforeach
    </div>
@endif
```

### Display Cover Image Only

```blade
@if(config('gallery.enabled') && $news->coverImage)
    <img src="{{ $news->coverImage->getSizeUrl('medium') }}" 
         alt="{{ $news->coverImage->alt_text }}">
@endif
```

## Development

### Running Tests

```bash
# All gallery tests
php artisan test --filter=Gallery

# Feature tests only
php artisan test tests/Feature/Backend/Gallery/

# Unit tests only
php artisan test tests/Unit/Services/GalleryServiceTest.php
```

### Migrations

Run migrations to create the gallery_images table:

```bash
php artisan migrate
```

### Seeders

For local development, seed demo gallery images:

```bash
php artisan db:seed --class=GallerySeeder
```

## Security

### Validation

- **MIME Type Checking**: Uses `finfo_file()` to verify actual file content, not just extension
- **File Size Limits**: Enforced at upload time
- **Dimension Validation**: Minimum dimensions checked server-side
- **Input Sanitization**: Alt text, caption, and credit fields are sanitized

### Rate Limiting

Gallery upload routes should be rate-limited. Add to `RouteServiceProvider.php`:

```php
RateLimiter::for('gallery-uploads', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
});
```

Then apply to routes:

```php
Route::post('news/{news}/gallery/upload', ...)
    ->middleware('throttle:gallery-uploads');
```

### Authorization

- Gallery management is protected by the same permissions as News/Event editing
- Only users with `user.access.editor.news` or `user.access.editor.events` can manage galleries

## Troubleshooting

### Images Not Appearing

1. Check storage link exists:
   ```bash
   php artisan storage:link
   ```

2. Verify disk configuration in `.env`:
   ```env
   GALLERY_DISK=public
   ```

3. Check file permissions on `storage/app/public/gallery`

### Upload Fails

1. Check PHP upload limits in `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

2. Verify storage disk has sufficient space

3. Check Laravel logs in `storage/logs/`

### Queue Processing Issues

If using `GALLERY_QUEUE_PROCESSING=true`:

1. Ensure queue worker is running:
   ```bash
   php artisan queue:work
   ```

2. Check failed jobs:
   ```bash
   php artisan queue:failed
   ```

### 404 on Image URLs

1. Run storage link command:
   ```bash
   php artisan storage:link
   ```

2. Verify `.htaccess` or nginx config allows access to storage

3. Check `APP_URL` in `.env` matches your domain

## Performance Considerations

### Caching

- Image URLs include version parameter for cache busting
- Set long TTL in headers (configured via `gallery.cache_ttl`)
- Consider CDN for production

### Optimization

- Images are automatically resized to configured sizes
- Use `lazy` loading attribute on `<img>` tags
- Implement `srcset` for responsive images
- Consider WebP format for modern browsers (future enhancement)

### Database

- Gallery images are indexed on `imageable_type`, `imageable_id`, `order`, and `is_cover`
- Use eager loading to prevent N+1 queries:
  ```php
  News::with('gallery')->get();
  ```

## Future Enhancements

- [ ] Support for additional image formats (PNG, WebP)
- [ ] Automatic WebP conversion
- [ ] Image cropping UI
- [ ] Bulk operations
- [ ] CDN integration
- [ ] EXIF data preservation options
- [ ] Watermarking
- [ ] Image optimization tools integration
