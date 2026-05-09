<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Listing;
use Illuminate\Support\Facades\Storage;

class AdminComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['user', 'listing.user'])->latest()->paginate(20);
        return view('admin.complaints.index', compact('complaints'));
    }

    public function resolve(Complaint $complaint)
    {
        $complaint->update(['status' => 'resolved']);
        return back()->with('success', 'Şikayet çözüldü olarak işaretlendi.');
    }
}
