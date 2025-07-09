<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index()
{
    $currentUser = Auth::user();
    $pegawai = $currentUser->pegawai;
    $nama_departemen = $pegawai->departemen->nama_departemen ?? 'N/A'; // Perbaikan dari $currentPegawai

    // Ambil semua user dengan relasi pegawai, departemen, dan jabatan
    $users = User::with(['pegawai.departemen', 'pegawai.jabatan'])->get();

    // Ambil pegawai yang belum memiliki user
    $pegawaiTanpaUser = Pegawai::whereDoesntHave('user')
        ->with(['departemen', 'jabatan'])
        ->get();

    return view('admin.user.index', compact(
        'users',
        'pegawaiTanpaUser',
        'nama_departemen',
        'pegawai'
    ));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        $nama_departemen = $pegawai->departemen->nama_departemen ?? 'N/A';
        $pegawaiTanpaUser = Pegawai::whereDoesntHave('user')
            ->with(['departemen', 'jabatan'])
            ->get();

        return view('admin.user.create', compact('pegawaiTanpaUser','nama_departemen','pegawai'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:user',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:kepala_yayasan,pegawai,hrd',
            'id_pegawai' => 'required|exists:pegawai,id_pegawai|unique:user,id_pegawai'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'id_pegawai' => $request->id_pegawai,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        
        $user = User::with('pegawai')->findOrFail($id);
        $pegawai = $user->pegawai;
        $nama_departemen = $pegawai->departemen->nama_departemen ?? 'N/A';
        return view('admin.user.edit', compact('user','pegawai','nama_departemen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:user,username,' . $id . ',id_user',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,pegawai',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus user.'
            ], 500);
        }
    }

    /**
     * Create user for specific pegawai
     */
    public function createForPegawai(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pegawai' => 'required|exists:pegawai,id_pegawai|unique:user,id_pegawai'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai sudah memiliki user atau tidak ditemukan.'
            ], 400);
        }

        try {
            $pegawai = Pegawai::findOrFail($request->id_pegawai);
            
            // Generate username from email (part before @)
            $username = explode('@', $pegawai->email)[0];
            
            // Check if username already exists, if so add number
            $originalUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $originalUsername . $counter;
                $counter++;
            }

            // Default password: pegawai123
            $password = 'pegawai123';

            // Default role: pegawai
            $role = 'pegawai';

            User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'role' => $role,
                'id_pegawai' => $request->id_pegawai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat dengan username: ' . $username
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat user.'
            ], 500);
        }
    }

    /**
     * Create users for multiple pegawai
     */
    public function createMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pegawai' => 'required|array|min:1',
            'id_pegawai.*' => 'required|exists:pegawai,id_pegawai|unique:user,id_pegawai'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Beberapa pegawai sudah memiliki user atau tidak ditemukan.'
            ], 400);
        }

        try {
            $createdCount = 0;
            $pegawaiList = Pegawai::whereIn('id_pegawai', $request->id_pegawai)->get();

            foreach ($pegawaiList as $pegawai) {
                // Generate username from email (part before @)
                $username = explode('@', $pegawai->email)[0];
                
                // Check if username already exists, if so add number
                $originalUsername = $username;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }

                // Default password: pegawai123
                $password = 'pegawai123';

                // Default role: pegawai
                $role = 'pegawai';

                User::create([
                    'username' => $username,
                    'password' => Hash::make($password),
                    'role' => $role,
                    'id_pegawai' => $pegawai->id_pegawai,
                ]);

                $createdCount++;
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil membuat ' . $createdCount . ' user.',
                'created' => $createdCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat user.'
            ], 500);
        }
    }
}