<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $user = User::create($validated);
        $this->audit($request, $user, 'user.created');

        return back()->withFragment('users')->with('success', 'Le compte a été créé.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate($this->rules($user));
        if (empty($validated['password'])) unset($validated['password']);
        if ($user->id === $request->user()->id) $validated['active'] = true;
        $user->update($validated);
        $this->audit($request, $user, 'user.updated');

        return back()->withFragment('users')->with('success', 'Le compte a été mis à jour.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->id === $request->user()->id, 422, 'Vous ne pouvez pas supprimer votre propre compte.');
        if ($user->role === 'admin') {
            abort_if(User::query()->where('role', 'admin')->where('active', true)->count() <= 1, 422, 'Au moins un administrateur actif est requis.');
        }
        $this->audit($request, $user, 'user.deleted');
        $user->delete();

        return back()->withFragment('users')->with('success', 'Le compte a été supprimé.');
    }

    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user?->id)],
            'role' => ['required', Rule::in(['admin', 'direction', 'communication_owner', 'viewer'])],
            'active' => ['required', 'boolean'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:12', 'confirmed'],
        ];
    }

    private function audit(Request $request, User $subject, string $action): void
    {
        AuditLog::create([
            'user_id' => $request->user()->id, 'action' => $action,
            'subject_type' => User::class, 'subject_id' => (string) $subject->id,
            'context' => ['email' => $subject->email], 'ip_address' => $request->ip(),
        ]);
    }
}
