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
    $taxonomy = Taxonomy::first();
    $tenantId = $taxonomy?->tenant_id;

    if (! $taxonomy) {
      return;
    }

    // Insert dummy taxonomy file data
    TaxonomyFile::firstOrCreate([
      'file_name' => 'Sample File 1',
      'file_path' => 'taxonomy_files/sample-1.jpg',
      'taxonomy_id' => $taxonomy->id,
      'tenant_id' => $tenantId,
      'metadata' => ["file_size" => 1],
      'created_by' => 1,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    TaxonomyFile::firstOrCreate([
      'file_name' => 'Sample File 2',
      'file_path' => 'taxonomy_files/sample-2.pdf',
      'taxonomy_id' => $taxonomy->id,
      'tenant_id' => $tenantId,
      'metadata' => ["file_size" => 1],
      'created_by' => 1,
      'created_at' => now(),
      'updated_at' => now(),
    ]);
  }
}
