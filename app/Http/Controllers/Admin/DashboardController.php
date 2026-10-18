<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\PortfolioItem;
use App\Models\Subscriber;
use App\Models\TeamMember;
use App\Models\Video;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Vidéos publiées', 'value' => Video::published()->count(), 'icon' => 'bi-camera-reels', 'route' => 'admin.videos.index'],
                ['label' => 'Vues des vidéos', 'value' => Video::sum('views'), 'icon' => 'bi-eye', 'route' => 'admin.videos.index'],
                ['label' => 'Réalisations', 'value' => PortfolioItem::count(), 'icon' => 'bi-images', 'route' => 'admin.portfolio.index'],
                ['label' => 'Membres de l’équipe', 'value' => TeamMember::count(), 'icon' => 'bi-people', 'route' => 'admin.team.index'],
                ['label' => 'Messages non lus', 'value' => ContactMessage::whereNull('read_at')->count(), 'icon' => 'bi-envelope', 'route' => 'admin.messages.index'],
                ['label' => 'Abonnés newsletter', 'value' => Subscriber::count(), 'icon' => 'bi-send', 'route' => 'admin.subscribers.index'],
            ],
            'pendingVideos' => Video::where('is_published', false)->latest()->take(5)->get(),
            'latestMessages' => ContactMessage::latest()->take(5)->get(),
        ]);
    }
}
