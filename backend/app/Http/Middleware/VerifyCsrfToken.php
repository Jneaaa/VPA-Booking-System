<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */

    // Add your API routes here to bypass CSRF for local testing
    // Example: 'api/requisition/add-item',
    protected $except = [

            // Requisition form
            'requisition/add-item',
            'requisition/remove-item',
            'requisition/temp-upload',
            'requisition/submit',
        
            // Admin image uploads
            'admin/facilities/*/images',
            'admin/facilities/*/images/bulk',
            'admin/facilities/*/images/reorder',
            'admin/facilities/*/images/*', // DELETE still uses POST via JS sometimes
            'admin/equipment/*/images/upload',
            'admin/equipment/*/images/bulk-upload',
            'admin/equipment/*/images/reorder',
            'admin/equipment/*/images/*',
        
            // Misc image uploads
            'equipment/*/upload-image',
            'equipment/*/upload-images',
        
            // Authentication
            'admin/login',
            'login',
            'admin/logout',
        ];
}
