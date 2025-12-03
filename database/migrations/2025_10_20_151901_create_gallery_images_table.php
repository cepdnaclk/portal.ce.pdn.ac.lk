<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleryImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable'); // Creates imageable_id and imageable_type columns with index
            $table->string('filename');
            $table->string('original_filename');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // In bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->string('credit')->nullable();
            $table->unsignedInteger('order')->default(0)->index();
            $table->boolean('is_cover')->default(false)->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery_images');
    }
}
