<?php

namespace App\Domains\Taxonomy\Services;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Services\BaseService;

/**
 * Class TaxonomyService.
 */
class TaxonomyService extends BaseService
{
    /**
     * TaxonomyService constructor.
     *
     * @param  Taxonomy  $taxonomy
     */
    public function __construct(Taxonomy $taxonomy)
    {
        $this->model = $taxonomy;
    }
}
