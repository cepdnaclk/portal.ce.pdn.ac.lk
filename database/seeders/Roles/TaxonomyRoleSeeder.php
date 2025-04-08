<?php

namespace Database\Seeders\Roles;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class TaxonomyRoleSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        Role::create([
            'type' => User::TYPE_USER,
            'name' => 'Taxonomy Manager',
        ]);

        $taxonomyManagers = Permission::create([
            'type' => User::TYPE_USER,
            'name' => 'user.taxonomy',
            'description' => 'Taxonomy Permission',
        ]);
        $taxonomyManagers->children()->saveMany([
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.taxonomy.editor',
                'description' => 'Taxonomy Editor Permission',
            ]),
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.taxonomy.viewer',
                'description' => 'Taxonomy Viewer Permission',
                'sort' => 2,
            ]),
            
        ]);
        $this->enableForeignKeys();
    }
}
