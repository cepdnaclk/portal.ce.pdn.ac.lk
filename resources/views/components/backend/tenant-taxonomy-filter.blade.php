{{--
    Component: Tenant → Taxonomy Filter Script
    
    This component provides client-side filtering of taxonomy options based on the selected tenant.
    It listens for changes to a tenant dropdown and filters taxonomy options to show only those
    belonging to the selected tenant.
    
    Usage: Simply include this component at the bottom of any form that has both
           #tenant_id and #taxonomy_id selects:
           
           <x-backend.tenant-taxonomy-filter />
--}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tenantSelect = document.getElementById('tenant_id')
        const taxonomySelect = document.getElementById('taxonomy_id')
        if (!tenantSelect || !taxonomySelect) {
            return
        }
        const syncTaxonomyOptions = () => {
            const tenantId = tenantSelect.value
            Array.from(taxonomySelect.options).forEach((option) => {
                if (!option.value) {
                    option.hidden = false
                    return
                }
                const matchesTenant = !tenantId || option.dataset.tenant === tenantId
                option.hidden = !matchesTenant
                if (!matchesTenant && option.selected) {
                    option.selected = false
                }
            })
        }
        tenantSelect.addEventListener('change', syncTaxonomyOptions)
        syncTaxonomyOptions()
    })
</script>
