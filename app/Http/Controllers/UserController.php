<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data   = User::get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return $this->getStatus($row->status);
                })->addColumn('gender', function ($row) {
                    return $this->getGender($row->gender);
                })
                ->make(true);
        }
        return view('users');
    }

    public function getGender($key = 1) {
        $data = [
            1 => 'Male',
            2 => 'Female'
        ];
        return $data[$key];
    }

    public function getStatus($key = 1) {
        $data = [
            1 => 'Active',
            0 => 'Inactive'
        ];
        return $data[$key];
    }
}
