<?php

namespace App\Domains\Taxonomy\Validators;

use App\Domains\Taxonomy\Models\Taxonomy;
use Illuminate\Support\Facades\Validator;

class TaxonomyTermMetadataValidator
{
    public function validate(array $metadata, Taxonomy $taxonomy): void
    {
        Validator::validate($metadata, $this->rules($taxonomy));
    }

    public function rules(Taxonomy $taxonomy): array
    {
        $rules = [];
        foreach ($taxonomy->properties as $property) {
            $rules[$property['code']] = $this->ruleForType($property['data_type']);
        }
        return $rules;
    }

    protected function ruleForType(string $type): string
    {
        switch ($type) {
            case 'string':
                return 'nullable|string';
            case 'email':
                return 'nullable|email';
            case 'integer':
                return 'nullable|integer';
            case 'float':
                return 'nullable|numeric';
            case 'boolean':
                return 'nullable|boolean';
            case 'date':
            case 'datetime':
                return 'nullable|date';
            case 'url':
                return 'nullable|url';
            case 'file':
                return 'nullable|integer|exists:taxonomy_files,id';
            case 'page':
                return 'nullable|integer|exists:taxonomy_pages,id';
            case 'list':
                return 'nullable|integer|exists:taxonomy_lists,id';
            case 'taxonomy_term':
                return 'nullable|integer|exists:taxonomy_terms,id';
            default:
                return 'nullable';
        }
    }
}
