<?php

namespace Tests\Feature\Backend\TaxonomyFile;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use App\Domains\Auth\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaxonomyFileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_admin_can_access_taxonomy_file_listing_page()
    {
        $this->loginAsAdmin();
        $response = $this->get(route('dashboard.taxonomy-files.index'));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_file_creation_page()
    {
        $this->loginAsAdmin();
        $response = $this->get(route('dashboard.taxonomy-files.create'));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_file_edit_page()
    {
        $this->loginAsAdmin();
        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-files.edit', $taxonomyFile));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_file_view_page()
    {
        $this->loginAsAdmin();
        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-files.view', $taxonomyFile));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_file_delete_confirmation_page()
    {
        $this->loginAsAdmin();
        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-files.delete', $taxonomyFile));
        $response->assertOk();
    }

    // /** @test */
    // public function test_taxonomy_file_store_validation()
    // {
    //     $this->loginAsAdmin();

    //     // Test required fields: file_name is required if file is not present
    //     $response = $this->post(route('dashboard.taxonomy-files.store'), []);
    //     $response->assertSessionHasErrors(['file_name']);

    //     // Test taxonomy_id exists
    //     $response = $this->post(route('dashboard.taxonomy-files.store'), ['taxonomy_id' => 999]);
    //     $response->assertSessionHasErrors(['taxonomy_id']);

    //     // Test file_name unique
    //     TaxonomyFile::factory()->create(['file_name' => 'existing_file.pdf']);
    //     $response = $this->post(route('dashboard.taxonomy-files.store'), ['file_name' => 'existing_file.pdf']);
    //     $response->assertSessionHasErrors(['file_name']);

    //     // Test file max size (e.g., 10MB = 10240 KB)
    //     $response = $this->post(route('dashboard.taxonomy-files.store'), [
    //         'file' => UploadedFile::fake()->create('large_file.pdf', 10241) // 10MB + 1KB
    //     ]);
    //     $response->assertSessionHasErrors(['file']);

    //     // Test file mimes
    //     $response = $this->post(route('dashboard.taxonomy-files.store'), [
    //         'file' => UploadedFile::fake()->create('invalid_file.txt', 100)
    //     ]);
    //     $response->assertSessionHasErrors(['file']);
    // }

    // /** @test */
    // public function test_taxonomy_file_update_validation()
    // {
    //     $this->loginAsAdmin();
    //     $taxonomyFile = TaxonomyFile::factory()->create();

    //     // Test file_name required
    //     $response = $this->put(route('dashboard.taxonomy-files.update', $taxonomyFile), ['file_name' => '']);
    //     $response->assertSessionHasErrors(['file_name']);

    //     // Test taxonomy_id exists
    //     $response = $this->put(route('dashboard.taxonomy-files.update', $taxonomyFile), ['taxonomy_id' => 999]);
    //     $response->assertSessionHasErrors(['taxonomy_id']);

    //     // Test file_name unique (ignoring self)
    //     $otherTaxonomyFile = TaxonomyFile::factory()->create(['file_name' => 'other_file.pdf']);
    //     $response = $this->put(route('dashboard.taxonomy-files.update', $taxonomyFile), ['file_name' => 'other_file.pdf']);
    //     $response->assertSessionHasErrors(['file_name']);

    //     // Test file max size
    //     $response = $this->put(route('dashboard.taxonomy-files.update', $taxonomyFile), [
    //         'file' => UploadedFile::fake()->create('large_file.pdf', 10241) // 10MB + 1KB
    //     ]);
    //     $response->assertSessionHasErrors(['file']);

    //     // Test file mimes
    //     $response = $this->put(route('dashboard.taxonomy-files.update', $taxonomyFile), [
    //         'file' => UploadedFile::fake()->create('invalid_file.txt', 100)
    //     ]);
    //     $response->assertSessionHasErrors(['file']);
    // }

    /** @test */
    public function test_admin_can_create_taxonomy_file()
    {
        $this->loginAsAdmin();
        $adminUser = auth()->user();

        Storage::fake('public'); // Fake the public disk

        $taxonomy = Taxonomy::factory()->create();
        $fileName = 'new_document.pdf';
        $file = UploadedFile::fake()->create($fileName, 1000);

        $data = [
            'file_name' => $fileName, // Providing file_name explicitly
            'taxonomy_id' => $taxonomy->id,
            'file' => $file,
        ];

        $response = $this->post(route('dashboard.taxonomy-files.store'), $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('taxonomy_files', [
            'file_name' => str_replace('_', '-', $fileName), // Ensure consistent formatting
        ]);
    }

    /** @test */
    public function test_admin_can_view_taxonomy_file()
    {
        $this->loginAsAdmin();
        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-files.view', $taxonomyFile));

        $response->assertStatus(200);
        $response->assertSee($taxonomyFile->file_name);
    }

    // /** @test */
    // public function test_admin_can_download_taxonomy_file()
    // {
    //     $this->loginAsAdmin();
    //     Storage::fake('public');

    //     $fileNameWithExtension = 'test_download.pdf';
    //     $fileNameWithoutExtension = 'test_download';
    //     $filePath = 'taxonomy_files/' . $fileNameWithExtension;

    //     Storage::disk('public')->put($filePath, 'dummy content');

    //     TaxonomyFile::factory()->create([
    //         'file_name' => $fileNameWithoutExtension,
    //         'file_path' => $filePath,
    //     ]);

    //     $response = $this->get(route('download.taxonomy-file', [
    //         'file_name' => $fileNameWithoutExtension,
    //         'extension' => "pdf",
    //     ]));

    //     $response->assertStatus(200);
    //     $response->assertHeader('content-disposition', "attachment; filename={$fileNameWithExtension}");
    // }

    // /** @test */
    // public function test_admin_can_update_taxonomy_file_content()
    // {
    //     $this->loginAsAdmin();
    //     $adminUser = auth()->user();
    //     Storage::fake('public');

    //     $originalFileName = 'original.pdf';
    //     $originalFilePath = 'taxonomy_files/' . $originalFileName;
    //     Storage::disk('public')->put($originalFilePath, 'original content');

    //     $taxonomyFile = TaxonomyFile::factory()->create([
    //         'file_name' => $originalFileName,
    //         'file_path' => $originalFilePath,
    //         'created_by' => $adminUser->id,
    //     ]);

    //     $updatedFile = UploadedFile::fake()->create('updated_document.pdf', 2000);
    //     $updatedFileName = $updatedFile->getClientOriginalName();

    //     $response = $this->put(route('dashboard.taxonomy-files.update', $taxonomyFile), [
    //         'file_name' => $originalFileName,
    //         'file' => $updatedFile,
    //     ]);

    //     $response->assertStatus(302);

    //     $taxonomyFile->refresh();

    //     $this->assertEquals("taxonomy_files/{$updatedFileName}", $taxonomyFile->file_path);
    //     $this->assertEquals($updatedFileName, $taxonomyFile->file_name);
    //     $this->assertEquals($adminUser->id, $taxonomyFile->updated_by);

    //     Storage::disk('public')->assertExists("taxonomy_files/{$updatedFileName}");
    //     Storage::disk('public')->assertMissing($originalFilePath);
    // }

    // /** @test */
    // public function test_admin_can_update_taxonomy_file_name_and_metadata()
    // {
    //     $this->loginAsAdmin();
    //     $adminUser = auth()->user();
    //     Storage::fake('public');

    //     $originalFileName = 'original_name.pdf';
    //     $originalFilePath = 'taxonomy_files/' . $originalFileName;
    //     Storage::disk('public')->put($originalFilePath, 'some content');

    //     $taxonomy = Taxonomy::factory()->create();
    //     $taxonomyFile = TaxonomyFile::factory()->create([
    //         'file_name' => $originalFileName,
    //         'file_path' => $originalFilePath,
    //         'taxonomy_id' => $taxonomy->id,
    //         'created_by' => $adminUser->id,
    //     ]);

    //     $response = $this->put(route('dashboard.taxonomy-files.update', $taxonomyFile), [
    //         'file_name' => 'updated_name',
    //         'taxonomy_id' => $taxonomy->id,
    //     ]);

    //     $response->assertStatus(302);

    //     $taxonomyFile->refresh();

    //     $this->assertEquals('taxonomy_files/updated_name.pdf', $taxonomyFile->file_path);
    //     $this->assertEquals($adminUser->id, $taxonomyFile->updated_by);
    // }

    /** @test */
    public function test_admin_can_delete_taxonomy_file()
    {
        $this->loginAsAdmin();
        Storage::fake('public');

        $fileName = 'file_to_delete.pdf';
        $filePath = 'taxonomy_files/' . $fileName;

        // Create a dummy file on the faked disk
        Storage::disk('public')->put($filePath, 'dummy content');

        // Create a TaxonomyFile record
        $taxonomyFile = TaxonomyFile::factory()->create([
            'file_name' => $fileName,
            'file_path' => $filePath,
        ]);

        // Make sure it's in the DB and on disk before deleting
        $this->assertDatabaseHas('taxonomy_files', ['id' => $taxonomyFile->id]);
        Storage::disk('public')->assertExists($filePath);

        // Make the DELETE request
        $response = $this->delete(route('dashboard.taxonomy-files.destroy', $taxonomyFile));

        // Assert the response
        $response->assertStatus(302); // Should redirect after successful deletion

        // Assert the file is missing from the database
        $this->assertDatabaseMissing('taxonomy_files', ['id' => $taxonomyFile->id]);

        // Assert the physical file is missing from storage
        Storage::disk('public')->assertMissing($filePath);
    }

    /** @test */
    public function test_guest_cannot_access_taxonomy_file_routes()
    {
        // Test a representative route
        $response = $this->get(route('dashboard.taxonomy-files.create'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->get(route('dashboard.taxonomy-files.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // Attempting to perform an action
        $response = $this->post(route('dashboard.taxonomy-files.store', []));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_non_admin_user_cannot_access_taxonomy_file_management_pages_and_actions()
    {
        $user = User::factory()->create(); // Default user is not an admin
        $this->actingAs($user);

        // Attempt to access GET routes
        $response = $this->get(route('dashboard.taxonomy-files.create'));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-files.index'));
        $response->assertStatus(302);

        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-files.edit', $taxonomyFile));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-files.view', $taxonomyFile));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-files.delete', $taxonomyFile));
        $response->assertStatus(302);

        // Attempt to perform actions (POST, PUT, DELETE)
        $response = $this->post(route('dashboard.taxonomy-files.store'), ['file_name' => 'test.pdf']);
        $response->assertStatus(302);

        $response = $this->put(route('dashboard.taxonomy-files.update', $taxonomyFile), ['file_name' => 'updated.pdf']);
        $response->assertStatus(302);

        $response = $this->delete(route('dashboard.taxonomy-files.destroy', $taxonomyFile));
        $response->assertStatus(302);

        // Also test download attempt
        Storage::fake('public');
        $fileName = 'protected_file.pdf';
        $filePath = 'taxonomy_files/' . $fileName;
        Storage::disk('public')->put($filePath, 'dummy content');
        $taxonomyFileForDownload = TaxonomyFile::factory()->create([
            'file_name' => 'protected_file',
            'file_path' => $filePath,
        ]);

        // Anyone should be able to download this file
        $response = $this->get(route('download.taxonomy-file', [
            'file_name' => $taxonomyFileForDownload->file_name,
            'extension' => 'pdf',
        ]));
        $response->assertStatus(200);
    }
}
