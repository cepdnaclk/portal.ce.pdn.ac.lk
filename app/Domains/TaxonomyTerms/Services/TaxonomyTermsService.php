<?php

namespace App\Domains\TaxonomyTerms\Services;

use App\Domains\TaxonomyTerms\Models\TaxonomyTerms;
use App\Services\BaseService;

/**
 * Class TaxonomyTermsService.
 */
class TaxonomyTermsService extends BaseService
{
    /**
     * TaxonomyTermsService constructor.
     *
     * @param  TaxonomyTerms  $taxonomyTerms
     */
    public function __construct(TaxonomyTerms $taxonomyTerms)
    {
        $this->model = $taxonomyTerms;
    }
}
