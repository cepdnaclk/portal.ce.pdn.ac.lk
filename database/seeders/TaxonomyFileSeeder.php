<?php

namespace Database\Seeders;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use Illuminate\Database\Seeder;

class TaxonomyFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert dummy taxonomy file data
        TaxonomyFile::firstOrCreate([
            'file_name' => 'Sample File 1',
            'file_path' => 'taxonomy_files/sample-1.jpg',
            'taxonomy_id' => Taxonomy::first()->id,
            'metadata' => ["file_size" => 1],
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        TaxonomyFile::firstOrCreate([
            'file_name' => 'Sample File 2',
            'file_path' => 'taxonomy_files/sample-2.pdf',
            'taxonomy_id' => Taxonomy::first()->id,
            'metadata' => ["file_size" => 1],
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
