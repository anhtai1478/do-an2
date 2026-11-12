<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = History::with('user')->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $histories = $query->paginate(10)->withQueryString();

        return view('admin.histories.index', compact('histories'));
    }

    public function destroy(History $history): RedirectResponse
    {
        $history->delete();

        return redirect()->route('admin.histories.index')->with('success', 'Đã xóa bản ghi lịch sử mua hàng.');
    }
}

