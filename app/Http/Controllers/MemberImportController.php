<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; 
use App\Models\Member; 
use App\Imports\MemberImport;
class MemberImportController extends Controller
{
   public function import(Request $request)
    {
        // ตรวจสอบว่าไฟล์ถูกอัปโหลดมาหรือไม่
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,ods'
        ]);

        // ประมวลผลการนำเข้าไฟล์
        try {
            Excel::import(new MemberImport, $request->file('file'));
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
