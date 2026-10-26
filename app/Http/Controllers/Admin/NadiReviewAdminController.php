<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NadiReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NadiReviewAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        if (!in_array($status, ['pending', 'published', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $query = NadiReview::query()->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reviews = $query->paginate(20)->withQueryString();

        return view('admin.nadi-reviews.index', compact('reviews', 'status'));
    }

    public function update(Request $request, NadiReview $review): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:published,rejected,pending'],
            'admin_reply' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->status = $data['status'];
        $review->admin_reply = trim((string) ($data['admin_reply'] ?? '')) ?: null;
        $review->published_at = $data['status'] === 'published'
            ? ($review->published_at ?? now())
            : null;
        $review->save();

        return back()->with('success', 'Ulasan berhasil diperbarui.');
    }
}
