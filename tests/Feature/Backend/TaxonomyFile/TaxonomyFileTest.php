<?php

namespace Tests\Feature\Backend\TaxonomyFile;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaxonomyFileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_admin_can_access_taxonomy_file_listing_page()
    {
        $this->loginAsAdmin();
        $response = $this->get('/dashboard/taxonomy-files');
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_file_creation_page()
    {
        $this->loginAsAdmin();
        $response = $this->get('/dashboard/taxonomy-files/create');
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_file_edit_page()
    {
        $this->loginAsAdmin();
        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get("/dashboard/taxonomy-files/{$taxonomyFile->id}/edit");
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_file_view_page()
    {
        $this->loginAsAdmin();
        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get("/dashboard/taxonomy-files/{$taxonomyFile->id}");
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_file_delete_confirmation_page()
    {
        $this->loginAsAdmin();
        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get("/dashboard/taxonomy-files/{$taxonomyFile->id}/delete");
        $response->assertOk();
    }

    /** @test */
    public function test_taxonomy_file_store_validation()
    {
        $this->loginAsAdmin();

        // Test required fields: file_name is required if file is not present
        $response = $this->post('/dashboard/taxonomy-files', []);
        $response->assertSessionHasErrors(['file_name']);

        // Test taxonomy_id exists
        $response = $this->post('/dashboard/taxonomy-files', ['taxonomy_id' => 999]);
        $response->assertSessionHasErrors(['taxonomy_id']);

        // Test file_name unique
        TaxonomyFile::factory()->create(['file_name' => 'existing_file.pdf']);
        $response = $this->post('/dashboard/taxonomy-files', ['file_name' => 'existing_file.pdf']);
        $response->assertSessionHasErrors(['file_name']);

        // Test file max size (e.g. 10MB = 10240 KB)
        $response = $this->post('/dashboard/taxonomy-files', [
            'file' => UploadedFile::fake()->create('large_file.pdf', 10241) // 10MB + 1KB
        ]);
        $response->assertSessionHasErrors(['file']);

        // Test file mimes
        $response = $this->post('/dashboard/taxonomy-files', [
            'file' => UploadedFile::fake()->create('invalid_file.txt', 100)
        ]);
        $response->assertSessionHasErrors(['file']);
    }

    /** @test */
    public function test_taxonomy_file_update_validation()
    {
        $this->loginAsAdmin();
        $taxonomyFile = TaxonomyFile::factory()->create();

        // Test file_name required
        $response = $this->put("/dashboard/taxonomy-files/{$taxonomyFile->id}", ['file_name' => '']);
        $response->assertSessionHasErrors(['file_name']);

        // Test taxonomy_id exists
        $response = $this->put("/dashboard/taxonomy-files/{$taxonomyFile->id}", ['taxonomy_id' => 999]);
        $response->assertSessionHasErrors(['taxonomy_id']);

        // Test file_name unique (ignoring self)
        $otherTaxonomyFile = TaxonomyFile::factory()->create(['file_name' => 'other_file.pdf']);
        $response = $this->put("/dashboard/taxonomy-files/{$taxonomyFile->id}", ['file_name' => 'other_file.pdf']);
        $response->assertSessionHasErrors(['file_name']);

        // Test file max size
        $response = $this->put("/dashboard/taxonomy-files/{$taxonomyFile->id}", [
            'file' => UploadedFile::fake()->create('large_file.pdf', 10241) // 10MB + 1KB
        ]);
        $response->assertSessionHasErrors(['file']);

        // Test file mimes
        $response = $this->put("/dashboard/taxonomy-files/{$taxonomyFile->id}", [
            'file' => UploadedFile::fake()->create('invalid_file.txt', 100)
        ]);
        $response->assertSessionHasErrors(['file']);
    }

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

        $response = $this->post('/dashboard/taxonomy-files', $data);

        $response->assertStatus(302); // Should redirect after successful creation

        $this->assertDatabaseHas('taxonomy_files', [
            'file_name' => $fileName,
            'taxonomy_id' => $taxonomy->id,
            'created_by' => $adminUser->id,
        ]);

        Storage::disk('public')->assertExists("taxonomy_files/{$fileName}");

        // Clean up the created file. Note: RefreshDatabase handles DB, but not usually filesystem.
        // However, Storage::fake handles its own cleanup for faked disks.
        // If not using Storage::fake or files are outside testing/disks, manual cleanup might be needed.
    }

    /** @test */
    public function test_admin_can_view_taxonomy_file()
    {
        $this->loginAsAdmin();

        $taxonomyFile = TaxonomyFile::factory()->create();

        $response = $this->get("/dashboard/taxonomy-files/{$taxonomyFile->id}");

        $response->assertStatus(200);
        $response->assertSee($taxonomyFile->file_name);
    }

    /** @test */
    public function test_admin_can_download_taxonomy_file()
    {
        $this->loginAsAdmin();
        Storage::fake('public');

        $fileNameWithExtension = 'test_download.pdf';
        $fileNameWithoutExtension = 'test_download';
        $filePath = 'taxonomy_files/' . $fileNameWithExtension;

        // Create a fake file on the faked disk
        Storage::disk('public')->put($filePath, 'dummy content');

        // Create a TaxonomyFile record
        TaxonomyFile::factory()->create([
            'file_name' => $fileNameWithoutExtension, // Storing without extension as per typical app logic
            'file_path' => $filePath,
        ]);

        $response = $this->get("/dashboard/taxonomy-files/download/{$fileNameWithoutExtension}");

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', "attachment; filename={$fileNameWithExtension}");
    }

    /** @test */
    public function test_admin_can_update_taxonomy_file_content()
    {
        $this->loginAsAdmin();
        $adminUser = auth()->user();
        Storage::fake('public');

        $originalFileName = 'original.pdf';
        $originalFilePath = 'taxonomy_files/' . $originalFileName;
        Storage::disk('public')->put($originalFilePath, 'original content');

        $taxonomyFile = TaxonomyFile::factory()->create([
            'file_name' => $originalFileName,
            'file_path' => $originalFilePath,
            'created_by' => $adminUser->id,
        ]);

        $updatedFile = UploadedFile::fake()->create('updated_document.pdf', 2000);
        $updatedFileName = $updatedFile->getClientOriginalName(); // should be 'updated_document.pdf'

        $response = $this->put("/dashboard/taxonomy-files/{$taxonomyFile->id}", [
            'file_name' => $originalFileName, // Keep original name, or let controller derive if new name is desired
            'file' => $updatedFile,
        ]);

        $response->assertStatus(302);

        $taxonomyFile->refresh();

        $this->assertEquals("taxonomy_files/{$updatedFileName}", $taxonomyFile->file_path);
        $this->assertEquals($updatedFileName, $taxonomyFile->file_name); // Assuming the name changes to the new file's name
        $this->assertEquals($adminUser->id, $taxonomyFile->updated_by);

        Storage::disk('public')->assertExists("taxonomy_files/{$updatedFileName}");
        Storage::disk('public')->assertMissing($originalFilePath); // Original physical file should be gone
    }

    /** @test */
    public function test_admin_can_update_taxonomy_file_name_and_metadata()
    {
        $this->loginAsAdmin();
        $adminUser = auth()->user();
        Storage::fake('public');

        $originalFileName = 'original_name.pdf';
        $originalBaseName = 'original_name';
        $originalFilePath = 'taxonomy_files/' . $originalFileName;
        Storage::disk('public')->put($originalFilePath, 'some content');

        $taxonomy = Taxonomy::factory()->create();
        $taxonomyFile = TaxonomyFile::factory()->create([
            'file_name' => $originalFileName,
            'file_path' => $originalFilePath,
            'taxonomy_id' => $taxonomy->id,
            'created_by' => $adminUser->id,
        ]);

        $updatedBaseName = 'updated_name';
        $updatedFileName = $updatedBaseName . '.pdf'; // Assuming extension remains or is handled by controller
        $newTaxonomy = Taxonomy::factory()->create();

        $response = $this->put("/dashboard/taxonomy-files/{$taxonomyFile->id}", [
            'file_name' => $updatedBaseName, // Update base name, controller should handle extension
            'taxonomy_id' => $newTaxonomy->id,
            // No 'file' key, so content should remain, but file on disk should be renamed
        ]);

        $response->assertStatus(302);

        $taxonomyFile->refresh();

        $this->assertEquals($updatedFileName, $taxonomyFile->file_name);
        $this->assertEquals('taxonomy_files/' . $updatedFileName, $taxonomyFile->file_path);
        $this->assertEquals($newTaxonomy->id, $taxonomyFile->taxonomy_id);
        $this->assertEquals($adminUser->id, $taxonomyFile->updated_by);

        Storage::disk('public')->assertExists('taxonomy_files/' . $updatedFileName);
        Storage::disk('public')->assertMissing($originalFilePath);
    }

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
        $response = $this->delete("/dashboard/taxonomy-files/{$taxonomyFile->id}");

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
        $response = $this->get('/dashboard/taxonomy-files/create');
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));

        $response = $this->get('/dashboard/taxonomy-files');
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));

        // Attempting to perform an action
        $response = $this->post('/dashboard/taxonomy-files', ['file_name' => 'test.pdf']);
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_non_admin_user_cannot_access_taxonomy_file_management_pages_and_actions()
    {
        $user = User::factory()->create(); // Default user is not an admin
        $this->actingAs($user);

        // Attempt to access GET routes
        $response = $this->get('/dashboard/taxonomy-files/create');
        $response->assertStatus(403);

        $response = $this->get('/dashboard/taxonomy-files'); // Listing page
        $response->assertStatus(403);

        $taxonomyFile = TaxonomyFile::factory()->create();
        $response = $this->get("/dashboard/taxonomy-files/{$taxonomyFile->id}/edit");
        $response->assertStatus(403);

        $response = $this->get("/dashboard/taxonomy-files/{$taxonomyFile->id}"); // View page
        $response->assertStatus(403);

        $response = $this->get("/dashboard/taxonomy-files/{$taxonomyFile->id}/delete"); // Delete confirmation page
        $response->assertStatus(403);

        // Attempt to perform actions (POST, PUT, DELETE)
        $response = $this->post('/dashboard/taxonomy-files', ['file_name' => 'test.pdf']);
        $response->assertStatus(403);

        $response = $this->put("/dashboard/taxonomy-files/{$taxonomyFile->id}", ['file_name' => 'updated.pdf']);
        $response->assertStatus(403);

        $response = $this->delete("/dashboard/taxonomy-files/{$taxonomyFile->id}");
        $response->assertStatus(403);

        // Also test download attempt
        Storage::fake('public');
        $fileName = 'protected_file.pdf';
        $filePath = 'taxonomy_files/' . $fileName;
        Storage::disk('public')->put($filePath, 'dummy content');
        $taxonomyFileForDownload = TaxonomyFile::factory()->create([
            'file_name' => 'protected_file',
            'file_path' => $filePath,
        ]);
        $response = $this->get("/dashboard/taxonomy-files/download/{$taxonomyFileForDownload->file_name}");
        $response->assertStatus(403);
    }
}
