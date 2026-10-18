<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        return view('admin.team.index', [
            'members' => TeamMember::orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.team.form', ['member' => new TeamMember(['is_published' => true, 'element' => 'feu'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        TeamMember::create($this->validated($request));

        return redirect()->route('admin.team.index')->with('status', 'Membre ajouté.');
    }

    public function edit(TeamMember $member): View
    {
        return view('admin.team.form', compact('member'));
    }

    public function update(Request $request, TeamMember $member): RedirectResponse
    {
        $member->update($this->validated($request, $member));

        return redirect()->route('admin.team.index')->with('status', 'Membre mis à jour.');
    }

    public function destroy(TeamMember $member): RedirectResponse
    {
        Media::delete($member->photo);
        $member->delete();

        return redirect()->route('admin.team.index')->with('status', 'Membre supprimé.');
    }

    private function validated(Request $request, ?TeamMember $member = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'element' => ['required', Rule::in(array_keys(TeamMember::ELEMENTS))],
            'photo_file' => ['nullable', 'image', 'max:5120'],
            'position' => ['nullable', 'integer', 'min:0'],
        ];
        foreach (TeamMember::SOCIALS as $network) {
            $rules["socials.$network"] = ['nullable', 'url', 'max:300'];
        }

        $data = $request->validate($rules);

        if ($request->hasFile('photo_file')) {
            Media::delete($member?->photo);
            $data['photo'] = Media::store($request->file('photo_file'), 'team');
        }

        $data['socials'] = array_filter($data['socials'] ?? []);
        $data['position'] = $data['position'] ?? 0;
        $data['is_published'] = $request->boolean('is_published');
        unset($data['photo_file']);

        return $data;
    }
}
