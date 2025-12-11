<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sites\SiteStoreRequest;
use App\Services\Sites\CustomerSiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SiteController extends Controller
{

    public function index(Request $request, CustomerSiteService $customerSiteService) {
        return Inertia::render('site/Index', [
            'sites' => $customerSiteService->sites(),
            'pages' => fn() => $customerSiteService->pages($request->get('site_ids', []))
        ]);
    }

    public function show(Request $request, CustomerSiteService $customerSiteService, $siteId) {
        return Inertia::render('site/Show', [
            'site' => $customerSiteService->sites($siteId)
        ]);
    }

    public function create(Request $request, CustomerSiteService $customerSiteService) {
        return Inertia::render('site/Create', [
            'teams' => fn () => auth()->user()->teams()->get()
        ]);
    }

    public function store(SiteStoreRequest $request, CustomerSiteService $customerSiteService) {
        $customerSiteService->new([
            'team_id' => $request->get('team_id'),
            'display_name' => $request->get('display_name'),
        ]);

        return Redirect::route('sites.index');
    }

}
