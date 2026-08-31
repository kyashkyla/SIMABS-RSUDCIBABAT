<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user()?->load('employee');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    ...$user->toArray(),
                    'photo' => $user->employee?->photo
                        ? Storage::url($user->employee->photo)
                        : null,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'notifications' => function () use ($request) {
                $user = $request->user();

                if (!$user || $user->role !== 'admin') {
                    return null;
                }

                return [
                    'unreadCount' => $user->unreadNotifications()->count(),
                    'latest' => $user->notifications()
                        ->latest()
                        ->take(8)
                        ->get()
                        ->map(fn ($n) => [
                            'id' => $n->id,
                            'title' => $n->data['title'] ?? '',
                            'message' => $n->data['message'] ?? '',
                            'url' => $n->data['url'] ?? null,
                            'read' => !is_null($n->read_at),
                            'time' => $n->created_at->diffForHumans(),
                        ]),
                ];
            },
        ];
    }
}