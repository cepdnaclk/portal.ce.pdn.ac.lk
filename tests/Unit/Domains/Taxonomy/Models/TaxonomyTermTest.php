<?php

namespace Tests\Unit\Domains\Taxonomy\Models;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\ContentManagement\Models\Article;
use App\Domains\Tenant\Models\Tenant;
use App\Http\Resources\ArticleResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TaxonomyTermTest extends TestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();

    $urlGeneratorMock = \Mockery::mock(\Illuminate\Routing\UrlGenerator::class);
    $urlGeneratorMock
      ->shouldReceive('route')
      ->with('download.taxonomy-file', \Mockery::any(), \Mockery::any()) // Matches name, parameters, and absolute flag
      ->andReturnUsing(function ($name, $parameters, $absolute) {
        if (isset($parameters['file_name']) && isset($parameters['extension'])) {
          return 'http://localhost/download/taxonomy-files/' . $parameters['file_name'] . '.' . $parameters['extension'];
        }
        return 'http://localhost/mocked-route-for-' . $name; // Fallback for debugging
      })
      ->byDefault(); // Allow other route calls to be potentially handled or added if needed

    // If other UrlGenerator methods are called by the helper or internals, they might need mocking.
    // For example, asset() or current(). For now, this is focused on route().
    // $urlGeneratorMock->shouldIgnoreMissing(); // Use if many other UrlGenerator methods are called and are not relevant

    $this->app->instance('url', $urlGeneratorMock);

    // The getRoutes() issue from before was likely due to mocking Route facade directly.
    // If it reappears, it would be on the 'router' service.
    // For now, the RouteNotFoundException is the primary concern.
  }

  public function test_get_formatted_metadata_attribute_handles_datetime_type()
  {
    // 1. Arrange
    $taxonomy = Taxonomy::factory()->create([
      'properties' => [
        ['code' => 'event_start_time', 'name' => 'Event Start Time', 'data_type' => 'datetime'],
        ['code' => 'event_end_time', 'name' => 'Event End Time', 'data_type' => 'datetime'],
      ]
    ]);

    $validDateTimeString = '2023-10-26 14:30:00';
    $expectedISO8601DateTime = date(DATE_ATOM, strtotime($validDateTimeString));

    $term = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [
        ['code' => 'event_start_time', 'value' => $validDateTimeString],
        ['code' => 'event_end_time', 'value' => null], // This should be filtered out by the model's logic
      ]
    ]);

    // 2. Act
    $formattedMetadata = $term->getFormattedMetadataAttribute();

    // 3. Assert
    $this->assertArrayHasKey('event_start_time', $formattedMetadata);
    $this->assertEquals($expectedISO8601DateTime, $formattedMetadata['event_start_time']);
    $this->assertArrayNotHasKey('event_end_time', $formattedMetadata); // Due to null value
  }

  public function test_get_formatted_metadata_attribute_handles_various_datetime_inputs()
  {
    $taxonomy = Taxonomy::factory()->create([
      'properties' => [
        ['code' => 'meeting_time', 'name' => 'Meeting Time', 'data_type' => 'datetime']
      ]
    ]);

    // Test with a string that strtotime can parse
    $inputDateTime = 'tomorrow 10am';
    $expectedOutput = date(DATE_ATOM, strtotime($inputDateTime));
    $term1 = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [['code' => 'meeting_time', 'value' => $inputDateTime]]
    ]);
    $formattedMetadata1 = $term1->getFormattedMetadataAttribute();
    $this->assertEquals($expectedOutput, $formattedMetadata1['meeting_time']);

    // Test with a Unix timestamp (as string, as metadata values are often strings)
    $inputTimestamp = (string)time();
    $expectedOutputTimestamp = date(DATE_ATOM, (int)$inputTimestamp);
    $term2 = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [['code' => 'meeting_time', 'value' => $inputTimestamp]]
    ]);
    $formattedMetadata2 = $term2->getFormattedMetadataAttribute();
    $this->assertEquals($expectedOutputTimestamp, $formattedMetadata2['meeting_time']);

    // Test with an already ISO8601 formatted string
    $inputISO = '2024-01-01T12:00:00+00:00';
    $expectedOutputISO = date(DATE_ATOM, strtotime($inputISO)); // strtotime will parse and date will reformat consistently
    $term3 = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [['code' => 'meeting_time', 'value' => $inputISO]]
    ]);
    $formattedMetadata3 = $term3->getFormattedMetadataAttribute();
    $this->assertEquals($expectedOutputISO, $formattedMetadata3['meeting_time']);
  }

  public function test_get_formatted_metadata_attribute_handles_other_data_types_as_is()
  {
    // 1. Arrange
    $taxonomy = Taxonomy::factory()->create([
      'properties' => [
        ['code' => 'project_name', 'name' => 'Project Name', 'data_type' => 'string'],
        ['code' => 'task_count', 'name' => 'Task Count', 'data_type' => 'integer'],
        ['code' => 'is_active', 'name' => 'Is Active', 'data_type' => 'boolean'],
        ['code' => 'completion_rate', 'name' => 'Completion Rate', 'data_type' => 'float'],
        ['code' => 'contact_email', 'name' => 'Contact Email', 'data_type' => 'email'],
        ['code' => 'project_url', 'name' => 'Project URL', 'data_type' => 'url'],
        ['code' => 'description', 'name' => 'Description', 'data_type' => 'string'], // Property not in term metadata
      ]
    ]);

    $term = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [
        ['code' => 'project_name', 'value' => 'Omega Project'],
        ['code' => 'task_count', 'value' => 150],
        ['code' => 'is_active', 'value' => true],
        ['code' => 'completion_rate', 'value' => 0.75],
        ['code' => 'contact_email', 'value' => 'contact@example.com'],
        ['code' => 'project_url', 'value' => 'http://example.com/omega'],
      ]
    ]);

    // 2. Act
    $formattedMetadata = $term->getFormattedMetadataAttribute();

    // 3. Assert
    $this->assertArrayHasKey('project_name', $formattedMetadata);
    $this->assertEquals('Omega Project', $formattedMetadata['project_name']);

    $this->assertArrayHasKey('task_count', $formattedMetadata);
    $this->assertEquals(150, $formattedMetadata['task_count']);

    $this->assertArrayHasKey('is_active', $formattedMetadata);
    $this->assertEquals(true, $formattedMetadata['is_active']);

    $this->assertArrayHasKey('completion_rate', $formattedMetadata);
    $this->assertEquals(0.75, $formattedMetadata['completion_rate']);

    $this->assertArrayHasKey('contact_email', $formattedMetadata);
    $this->assertEquals('contact@example.com', $formattedMetadata['contact_email']);

    $this->assertArrayHasKey('project_url', $formattedMetadata);
    $this->assertEquals('http://example.com/omega', $formattedMetadata['project_url']);

    $this->assertArrayNotHasKey('description', $formattedMetadata); // Should not be present

    // Ensure only expected keys are present
    $expectedKeys = ['project_name', 'task_count', 'is_active', 'completion_rate', 'contact_email', 'project_url'];
    $this->assertEqualsCanonicalizing($expectedKeys, array_keys($formattedMetadata));
  }

  public function test_get_formatted_metadata_attribute_filters_null_values_early()
  {
    // 1. Arrange
    $taxonomy = Taxonomy::factory()->create([
      'properties' => [
        ['code' => 'project_lead', 'name' => 'Project Lead', 'data_type' => 'string'],
        ['code' => 'project_document', 'name' => 'Project Document', 'data_type' => 'file'], // Will have null value
        ['code' => 'start_date', 'name' => 'Start Date', 'data_type' => 'datetime'], // Will have null value
        ['code' => 'notes', 'name' => 'Notes', 'data_type' => 'string'], // Will have null value
        ['code' => 'active_task_id', 'name' => 'Active Task ID', 'data_type' => 'integer'],
      ]
    ]);

    // We need a dummy TaxonomyFile for the 'file' type property if it were non-null,
    // but since its value will be null, it won't be processed.
    // However, the 'properties' lookup in the model still happens.

    $term = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [
        ['code' => 'project_lead', 'value' => 'Alice Wonderland'],
        ['code' => 'project_document', 'value' => null],
        ['code' => 'start_date', 'value' => null],
        ['code' => 'notes', 'value' => null],
        ['code' => 'active_task_id', 'value' => 123],
      ]
    ]);

    // 2. Act
    $formattedMetadata = $term->getFormattedMetadataAttribute();

    // 3. Assert
    $this->assertArrayHasKey('project_lead', $formattedMetadata);
    $this->assertEquals('Alice Wonderland', $formattedMetadata['project_lead']);

    $this->assertArrayHasKey('active_task_id', $formattedMetadata);
    $this->assertEquals(123, $formattedMetadata['active_task_id']);

    $this->assertArrayNotHasKey('project_document', $formattedMetadata);
    $this->assertArrayNotHasKey('start_date', $formattedMetadata);
    $this->assertArrayNotHasKey('notes', $formattedMetadata);

    // Ensure only expected keys are present
    $expectedKeys = ['project_lead', 'active_task_id'];
    $this->assertEqualsCanonicalizing($expectedKeys, array_keys($formattedMetadata));
    $this->assertCount(2, $formattedMetadata);
  }

  public function test_get_formatted_metadata_attribute_handles_empty_or_null_metadata()
  {
    // 1. Arrange
    $taxonomy = Taxonomy::factory()->create(); // Basic taxonomy

    // Scenario 1: Metadata is an empty array
    $termWithEmptyMetadataArray = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => []
    ]);

    // Scenario 2: Metadata is null
    // Need to use state() or direct assignment if factory doesn't easily set null for JSON casted attribute.
    // Or, more simply, create and then update/set.
    $termWithNullMetadata = TaxonomyTerm::factory()->make([ // make() then set to avoid DB cast issues if any
      'taxonomy_id' => $taxonomy->id,
    ]);
    $termWithNullMetadata->metadata = null; // Directly set to null
    // If saving is required for getFormattedMetadataAttribute to work (it shouldn't be, it's an accessor)
    // $termWithNullMetadata->save();


    // 2. Act
    $formattedMetadataEmptyArray = $termWithEmptyMetadataArray->getFormattedMetadataAttribute();
    $formattedMetadataNull = $termWithNullMetadata->getFormattedMetadataAttribute();

    // 3. Assert
    $this->assertIsArray($formattedMetadataEmptyArray);
    $this->assertEmpty($formattedMetadataEmptyArray);

    $this->assertIsArray($formattedMetadataNull);
    $this->assertEmpty($formattedMetadataNull);
  }

  public function test_get_formatted_metadata_attribute_handles_file_type()
  {
    // 1. Arrange
    $taxonomy = Taxonomy::factory()->create([
      'properties' => [
        ['code' => 'document_file', 'name' => 'Document File', 'data_type' => 'file']
      ]
    ]);

    $taxonomyFile = TaxonomyFile::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'file_name' => 'test-document', // Factory might append .pdf, or model accessor for file_name might handle it
      'file_path' => 'uploads/test-document.pdf' // getFileExtension derives from this
    ]);

    $term = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [
        ['code' => 'document_file', 'value' => $taxonomyFile->id]
      ]
    ]);

    // Clear cache for this specific key if it was set by factory or previous interactions
    $fileCacheKey = 'taxonomy_' . $taxonomy->id . '_file_' . $taxonomyFile->id;
    Cache::forget($fileCacheKey);

    // 2. Act
    $formattedMetadata = $term->getFormattedMetadataAttribute();

    // 3. Assert
    $this->assertArrayHasKey('document_file', $formattedMetadata);
    // The TaxonomyFile model's file_name setter slugs the name and ensures extension.
    // getFileExtension() gets extension from file_path.
    // Given file_name = 'test-document' and file_path = 'uploads/test-document.pdf',
    // the extension should be 'pdf'.
    // The route mock concatenates these as file_name + '.' + extension.
    $expectedUrl = 'http://localhost/download/taxonomy-files/test-document.pdf';
    $this->assertEquals($expectedUrl, $formattedMetadata['document_file']);
  }

  public function test_get_formatted_metadata_attribute_handles_missing_file()
  {
    // 1. Arrange
    $taxonomy = Taxonomy::factory()->create([
      'properties' => [
        ['code' => 'document_file', 'name' => 'Document File', 'data_type' => 'file']
      ]
    ]);

    $nonExistentFileId = 999; // An ID that is unlikely to exist

    $term = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [
        ['code' => 'document_file', 'value' => $nonExistentFileId]
      ]
    ]);

    // Clear cache for this specific key
    $fileCacheKey = 'taxonomy_' . $taxonomy->id . '_file_' . $nonExistentFileId;
    Cache::forget($fileCacheKey);

    // 2. Act
    $formattedMetadata = $term->getFormattedMetadataAttribute();

    // 3. Assert
    $this->assertArrayNotHasKey('document_file', $formattedMetadata);
    $this->assertEmpty($formattedMetadata); // Expect empty array as the only property was a missing file
  }

  public function test_get_formatted_metadata_attribute_handles_article_type()
  {
    $taxonomy = Taxonomy::factory()->create([
      'properties' => [
        ['code' => 'featured_article', 'name' => 'Featured Article', 'data_type' => 'article'],
      ],
    ]);

    $tenant = Tenant::factory()->create(['slug' => 'sample-tenant.test']);
    $article = Article::factory()->create(['tenant_id' => $tenant->id, 'title' => 'Sample Article']);

    $term = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [
        ['code' => 'featured_article', 'value' => $article->id],
      ],
    ]);

    $cacheKey = 'taxonomy_' . (int)$taxonomy->id . '_article_' . (int)$article->id;
    Cache::forget($cacheKey);

    $formattedMetadata = $term->getFormattedMetadataAttribute();

    $this->assertArrayHasKey('featured_article', $formattedMetadata);
    $this->assertInstanceOf(ArticleResource::class, $formattedMetadata['featured_article']);

    $payload = $formattedMetadata['featured_article']->resolve();
    $this->assertSame($article->id, $payload['id']);
    $this->assertSame('Sample Article', $payload['title']);
  }

  public function test_get_formatted_metadata_attribute_skips_missing_article()
  {
    $taxonomy = Taxonomy::factory()->create([
      'properties' => [
        ['code' => 'featured_article', 'name' => 'Featured Article', 'data_type' => 'article'],
      ],
    ]);

    $missingArticleId = 123456;
    $term = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'metadata' => [
        ['code' => 'featured_article', 'value' => $missingArticleId],
      ],
    ]);

    $cacheKey = 'taxonomy_' . (int)$taxonomy->id . '_article_' . (int)$missingArticleId;
    Cache::forget($cacheKey);

    $formattedMetadata = $term->getFormattedMetadataAttribute();

    $this->assertArrayNotHasKey('featured_article', $formattedMetadata);
  }
}
