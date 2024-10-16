<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\TaxonomyTerms\Models\TaxonomyTerms;


class TaxonomyTermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        TaxonomyTerms::factory(10)->create(); // Create 10 records

    }
}
