<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::all();
        return view('pages.pengaturan.index-pengaturan', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'tarif_denda' => 'required|numeric|min:0',
        ]);

        Pengaturan::where('key', 'tarif_denda')->update([
            'value' => $request->tarif_denda,
        ]);

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Update Tarif Denda menjadi Rp ' . number_format($request->tarif_denda, 0, ',', '.') . ' per hari',
            'modul'     => 'Pengaturan',
            'timestamp' => now(),
        ]);

        return redirect()->route('pengaturan.index')->with('success', 'Tarif denda berhasil diupdate!');
    }
}
