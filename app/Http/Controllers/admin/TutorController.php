<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use File;
use App\Tutor;
use Validator;
use Cache;
class TutorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData()
    {
     $data = Tutor::all();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="btn-delete" class="delete-modal btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/tutor/'.$row->picture.'" alt="tutor" title="tutor" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }
    public function index()
    {
        $data = Tutor::all();
        return view ('tutor.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tutor.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
        'name' => 'required|max:191',
        
        
        'picture' => 'max:2048|mimes:png,jpg,jpeg',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
        if(!empty($request->picture)){
             $file = $request->file('picture');
             $extension = strtolower($file->getClientOriginalExtension());
             $filename = uniqid() . '.' . $extension;
            $request->picture->move(public_path('images/tutor'), $filename);
            //  Storage::put('public/images/mitra/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Tutor::create(
           [
                 'name'=>request('name'),
                 'picture'=>$filename,
                 'description' => request('description')
                 
           ]
         );
        return redirect()->route('tutor.index');
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data=Tutor::find($id);
        return view ('tutor.edit',compact('data'));
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
        $rules = [
        'name' => 'required|max:191',
        'picture' => 'max:2048|mimes:png,jpg,jpeg',
        
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Tutor::find($id);
        if(!empty($request->picture)){
          $image_path = public_path().'/images/tutor/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
           $request->picture->move(public_path('images/tutor'), $filename);
          // Storage::put('public/images/mitra/' . $filename, File::get($file));
          // Storage::delete('public/images/mitra/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name;
        $data->description   = $request->description;
        $data->save();

        return redirect()->route('tutor.index');
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Tutor::find($id);
        $image_path = public_path().'/images/tutor/'.$data->picture;
        if(file_exists($image_path)){
        unlink($image_path);
         }
        $data->delete();
         return redirect()->route('tutor.index');
        
    }

    public function picture($id)
    {
      // $data = Banner::find($id);
      // return Image::make(Storage::get('public/images/banner/'.$data->picture))->response();

      $data = Cache::remember('tutor'.$id, 24*60, function() use ($id) {
        return Tutor::find($id)->picture;
      });
      return Image::make(Storage::get('public/images/tutor/'.$data))->response();

    }
}
