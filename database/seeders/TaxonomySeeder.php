<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Taxonomy\Models\Taxonomy;
use Carbon\Carbon;

class TaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(){
        $taxonomies = [
            'intranet' => [
                'code' => 'intranet',
                'name' => 'CE Intranet',
                'description' => 'This manages the intranet links shown in https://www.ce.pdn.ac.lk/intranet',
                'properties' => [
                    ['code' => 'link', 'name' => 'Link', 'data_type' => 'url']
                ],
            ],
            'students' => [
                'code' => 'students',
                'name' => 'Students',
                'description' => 'This taxonomy manages the list of student batches in the Department',
                'properties' => [
                    ['code' => 'start_date', 'name' => 'Academic Start Date', 'data_type' => 'date'],
                    ['code' => 'end_date', 'name' => 'Academic End Date', 'data_type' => 'date'],
                    ['code' => 'profiles_page', 'name' => 'Profiles Page', 'data_type' => 'url'],
                    ['code' => 'notes', 'name' => 'Notes', 'data_type' => 'string']
                ],
            ]
        ];

        foreach ($taxonomies as $key => $taxonomy) {
            Taxonomy::create([
                'code' => $taxonomy['code'],
                'name' => $taxonomy['name'],
                'description' => $taxonomy['description'],
                'properties' => $taxonomy['properties'],
                'created_by' => 1,
                'updated_by' => 1
            ]);
        }
    }
        
}