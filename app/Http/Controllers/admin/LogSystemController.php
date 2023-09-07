<?php

namespace App\Http\Controllers\admin;

use DataTables;
use App\Models\admin\Log;
use App\Models\admin\User;
use App\Models\admin\Module;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogSystemController extends Controller
{
    private static $module = "log_system";

    public function index(){
        //Check permission
        if (!isAllowed(static::$module, "view")) {
            abort(403);
        }

        return view('administrator.logs.index');
    }

    public function getData(Request $request)
    {
        $data = Log::query()->with('user');

        if ($request->user || $request->module) {
            if ($request->user != "") {
                $user = $request->user;
                $data->where("user_id", $user);
            }
            
            if ($request->module != "") {
                $module = $request->module ;
                $data->where("module", $module);
            }
            $data->get();
        }
        // dd($request->module);


        return DataTables::of($data)
            ->make(true);
    }

    public function getDetail($id){

        $data = Log::with('user')->find($id);
        if (!$data) {
            return abort(404);
        }
        return response()->json([
            'data' => $data,
        ]);
    }
    
    public function getDataModule(Request $request)
    {
        $data_module = Module::query();

        return DataTables::of($data_module)
            ->make(true);
    }

    public function getDataUser(Request $request)
    {
        $data_user = User::query()->with('user_group');

        return DataTables::of($data_user)
            ->make(true);
    }

    public function clearLogs()
    {
        //Check permission
        if (!isAllowed(static::$module, "clear")) {
            abort(403);
        }
        try {
            // Hapus semua data log
            Log::truncate();

            return redirect()->route('admin.logSystems')->with('success', 'Semua data log berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.logSystems')->with('error', 'Terjadi kesalahan saat menghapus data log: ' . $e->getMessage());
        }
    }
}
