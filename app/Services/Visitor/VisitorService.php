<?php

namespace App\Services\Visitor;

use App\Filters\Pages\IsReadyForVisit;
use App\Filters\Pages\ShouldBeActive;
use App\Models\SitePage;
use App\Models\VisitRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;

class VisitorService
{
    public function __construct() { }

    /**
     * @return Collection|SitePage[]
     */
    public function getPagesToVisit(): Collection
    {
        return SitePage::query()
            ->tap(new ShouldBeActive())
            ->tap(new IsReadyForVisit())
            ->get();
    }

    public function processVisitUpdate(SitePage $page, Response $response)
    {
        if(empty($page)) return;

        if(in_array($response->getStatusCode(), $page->expected_status)) {
            $isDown = false;
            $downAt = null;
        } else {
            $isDown = true;
            $downAt = now();
        }

        $page->update([
            'is_down' => $isDown,
            'down_at' => $downAt,
            'next_check_at' => now()->plus(seconds: $page->check_interval_seconds)
        ]);

        VisitRecord::create([
            'site_page_id' => $page->getKey(),
            'status' => $response->getStatusCode(),
            'duration' => round($response->transferStats->getTransferTime() * 1000)
        ]);
    }
}
