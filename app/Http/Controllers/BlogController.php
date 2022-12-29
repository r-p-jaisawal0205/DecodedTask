<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Blog;
use Yajra\DataTables\DataTables;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $id     = Auth::id();
            if($id == 1) {
                $data   = Blog::with('user')->get();
            } else {
                $data   = Blog::with('user')->where(['user_id' => $id])->get();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->user->name;
                })->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('blog.edit',$row->id).'" class="edit btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<a title="Delete User" href="javascript:void(0);" onclick="$(`#delete-form-'.$row->id.'`).submit();" class="delete btn btn-danger btn-sm">Delete</a><form id="delete-form-'.$row->id.'" onsubmit="return confirm(`Are you sure to delete?`);" action="'. route('blog.destroy',$row->id) .'" method="post" style="display: none;">'. method_field("DELETE") .csrf_field() .'</form>';

                    return $btn;
                })
                ->make(true);
        }
        return view('list_blogs');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        return view('add_blog',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string']
        ]);

        if ($validation->fails()) {
            return redirect('blog/create')->withErrors($validation)->withInput();
        }

        $res = Blog::create([
            'title'         => $request->title,
            'description'   => $request->description,
            'user_id'       => Auth::id(),
        ]);

        if($res) {
            return redirect('blog')->with('success','Blog created.');
        }
        return redirect('blog')->with('error', 'Something went wrong.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Blog::where(['id' => $id])->first();
        return view('add_blog', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string']
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }
        $res = Blog::find($id);

        if ($res) {
            $res->title=$request->title;
            $res->description=$request->description;
            if($res->save()) {
                return redirect('blog')->with('success', 'Blog updated.');
            }
        }
        return redirect('blog')->with('error', 'Something went wrong.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Blog::find($id);

        if($res) {
            if($res->delete()){
                return redirect('blog')->with('success', 'Blog Deleted.');
            }
        }
        return redirect('blog')->with('error', 'Something went wrong.');
    }
}
