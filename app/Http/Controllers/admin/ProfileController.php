<?php

namespace App\Http\Controllers\admin;

use DataTables;
use App\Models\admin\User;
use Illuminate\Http\Request;
use App\Models\admin\Profile;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    private static $module = "profile";

    public function index($kode) {
        //Check permission
        if (auth()->user()->kode != 'daysf-01' && $kode != auth()->user()->kode) {
            abort(403);
        }
        
        // Temukan data pengguna berdasarkan kode
        $data = Profile::with('user')
        ->where('user_kode',$kode)
        ->first();
    
        // Jika data tidak ditemukan, tampilkan pesan kesalahan atau arahkan ke halaman lain
        if (!$data) {
            return redirect()->route('admin.dashboard')->with('error', 'Data pengguna tidak ditemukan.');
        }
    
        return view('administrator.profile.index', compact('data'));
    }
    

    public function getData(Request $request){
        $data = Profile::with('user')->get();

        return DataTables::of($data)
            ->make(true);
    }
    
    public function update(Request $request)
    {
        $kode = $request->kode;

        // Check permission
        if (auth()->user()->kode != 'daysf-01' && $kode != auth()->user()->kode) {
            abort(403);
        }

        $data = Profile::where('user_kode',$kode)->with('user')->first();

        if (!$data) {
            return redirect()->route('admin.profile',$kode)->with('error', 'Data tidak ditemukan.');
        }

        $request->validate([
            'email' => 'unique:users,email,' . $data->user->id,
        ]);

        // Simpan data sebelum diupdate
        $previousData = $data->toArray();

        $updates = [];

        if ($request->filled('full_name')) {
            $updates['full_name'] = $request->full_name;
        }
        if ($request->filled('no_telepon')) {
            $updates['no_telepon'] = $request->no_telepon;
        }
        if ($request->filled('pendidikan_terakhir')) {
            $updates['pendidikan_terakhir'] = $request->pendidikan_terakhir;
        }
        if ($request->filled('tempat_lahir')) {
            $updates['tempat_lahir'] = $request->tempat_lahir;
        }
        if ($request->filled('tanggal_lahir')) {
            $updates['tanggal_lahir'] = $request->tanggal_lahir;
        }
        if ($request->filled('alamat')) {
            $updates['alamat'] = $request->alamat;
        }
        
        if ($request->filled('email')) {
            $user = User::where('kode', $kode)->first();
            if ($user) {
                $user->update(['email' => $request->email]);
            } else {
                return redirect()->route('admin.profile',$kode)->with('error', 'User tidak ditemukan.');
            }
        }

        $data->update($updates);

        // Kumpulkan data yang diperbarui dalam array
        $updatedData = [];
        foreach ($updates as $key => $value) {
            $updatedData[$key] = $data->$key;
        }

        // Kirim data yang diperbarui ke fungsi createLog
        createLog(static::$module, __FUNCTION__, $kode, ['Data sebelum diupdate' => $previousData, 'Data sesudah diupdate' => ['data' => $updatedData, 'user' => $user]]);

        return redirect()->route('admin.profile',$kode)->with('success', 'Data berhasil diupdate.');
    }




    
    public function getDetail($kode){

        $data = Profile::with('user')->find($kode);

        return response()->json([
            'data' => $data,
            'status' => 'success',
            'message' => 'Sukses memuat detail user.',
        ]);
    }

    public function checkEmail(Request $request){
        if($request->ajax()){
            $users = Profile::where('email', $request->email);
            
            if(isset($request->id)){
                $users->where('id', '!=', $request->id);
            }
    
            if($users->exists()){
                return response()->json([
                    'message' => 'Email sudah dipakai',
                    'valid' => false
                ]);
            } else {
                return response()->json([
                    'valid' => true
                ]);
            }
        }
    }
}
