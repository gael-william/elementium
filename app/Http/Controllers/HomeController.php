<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\PortfolioItem;
use App\Models\TeamMember;
use App\Models\Video;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'portfolio' => PortfolioItem::published()->get(),
            'portfolioCategories' => PortfolioItem::CATEGORIES,
            'team' => TeamMember::published()->get(),
            'videos' => Video::published()->orderByDesc('is_featured')->orderBy('position')->latest()->get(),
            'videoCategories' => Video::CATEGORIES,
            'services' => ContactMessage::SERVICES,
        ]);
    }
}
