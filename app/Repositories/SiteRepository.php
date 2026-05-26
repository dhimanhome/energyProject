<?php

namespace App\Repositories;

use App\Models\Site;

class SiteRepository
{
    public function active()
    {
        return Site::query()->where('status', 'active')->orderBy('site_name')->get();
    }
}
