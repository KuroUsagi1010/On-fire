<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sites\PageStoreRequest;
use App\Models\Site;
use App\Models\SitePage;
use App\Services\Sites\CustomerSiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SitePageController extends Controller
{

    public function __construct(public CustomerSiteService $customerSiteService) { }

    public function index(Request $request) {
        // TODO: implement
    }

    public function create(Request $request, Site $site) {
        return Inertia::render('sitepages/Create', [
            'site' => $site
        ]);
    }

    public function store(PageStoreRequest $request, Site $site) {
        $this->customerSiteService->newPage($site, $request->validated());

        // for now we return them to the index of site
        // TODO: return them to site/show
        return Redirect::route('sites.index');
    }

    public function show(SitePage $page) {
        $pageData = $this->customerSiteService->getPageData($page);

        return Inertia::render('sitepages/Show', [
            'site' => $pageData['site'],
            'page' => $pageData['page'],
            'records' => $pageData['records'],
            'uptime_percentage' => $pageData['uptime_percentage']
        ]);
    }
}
