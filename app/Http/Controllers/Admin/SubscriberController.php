<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index(): View
    {
        return view('admin.subscribers.index', [
            'subscribers' => Subscriber::latest()->paginate(50),
        ]);
    }

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'inscrit_le'], ';');
            Subscriber::orderBy('created_at')->each(
                fn ($s) => fputcsv($out, [$s->email, $s->created_at->format('Y-m-d H:i')], ';'),
            );
            fclose($out);
        }, 'abonnes-elementium.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('status', 'Abonné supprimé.');
    }
}
