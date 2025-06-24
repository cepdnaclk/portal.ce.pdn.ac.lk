<?php

use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Http\Resources\TaxonomyPageResource;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use Tests\TestCase;

uses(TestCase::class)->in('Feature');

beforeEach(function () {
    Storage::fake('public');
});

test('taxonomy page creation writes html file', function () {
    $taxonomy = Taxonomy::factory()->create();
    $page = TaxonomyPage::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'slug' => 'sample-page',
        'html' => '<p>hello</p>',
    ]);

    Storage::disk('public')->assertExists('taxonomy-pages/sample-page.html');
    assertDatabaseHas('taxonomy_pages', ['slug' => 'sample-page']);
});

test('taxonomy page update regenerates html file', function () {
    $page = TaxonomyPage::factory()->create(['slug' => 'update-page']);
    Storage::disk('public')->assertExists('taxonomy-pages/update-page.html');

    $page->update(['html' => '<p>updated</p>']);

    Storage::disk('public')->assertExists('taxonomy-pages/update-page.html');
    expect(Storage::disk('public')->get('taxonomy-pages/update-page.html'))->toContain('updated');
});

test('taxonomy page deletion removes html file', function () {
    $page = TaxonomyPage::factory()->create(['slug' => 'delete-page']);
    Storage::disk('public')->assertExists('taxonomy-pages/delete-page.html');

    $page->delete();

    Storage::disk('public')->assertMissing('taxonomy-pages/delete-page.html');
    assertDatabaseMissing('taxonomy_pages', ['slug' => 'delete-page']);
});

test('taxonomy page resource serializes correctly', function () {
    $page = TaxonomyPage::factory()->create(['slug' => 'res-page']);
    $data = (new TaxonomyPageResource($page))->resolve();

    expect($data['slug'])->toBe('res-page')
        ->and($data['html'])->toBe($page->html);
});
