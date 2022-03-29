<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use File;
use App\Promotion;
use Validator;
use Cache;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData()
    {
     $data = Promotion::all();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="btn-delete" class="delete-modal btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/promotion/'.$row->picture.'" alt="promotion" title="promotion" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }
    public function index()
    {
        return view('promotion.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('promotion.create');
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
        
        'description' => 'required',
        
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
            $request->picture->move(public_path('images/promotion'), $filename);
            //  Storage::put('public/images/promotion/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Promotion::create(
           [
                 'name'=>request('name'),
                 'picture'=>$filename,
                 'link' => request('link'),
                 'description' => request('description')
                 
           ]
         );
        return response()->json(['success'=>'Data added successfully','data'=>$data]);
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
        return view ('promotion.edit');
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
        
        'picture_edit' => 'max:2048|mimes:png,jpg,jpeg',
        
        'description_edit'=>'required'
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Promotion::find($id);
        
        if(!empty($request->picture_edit)){
        $image_path = public_path().'/images/promotion/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture_edit');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
           $request->picture_edit->move(public_path('images/promotion'), $filename);
          // Storage::put('public/images/promotion/' . $filename, File::get($file));
          // Storage::delete('public/images/promotion/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name_edit;
        $data->description   = $request->description_edit;
        $data->link   = $request->link_edit;
        $data->save();

        return response()->json(['success'=>'Data added successfully','data'=>$data]);
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
        $data = Promotion::find($id);
        $image_path = public_path().'/images/promotion/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
        $data->delete();
    }

     public function picture($id)
    {
      // $data = Banner::find($id);
      // return Image::make(Storage::get('public/images/banner/'.$data->picture))->response();

      $data = Cache::remember('promotion'.$id, 24*60, function() use ($id) {
        return Promotion::find($id)->picture;
      });
      return Image::make(Storage::get('public/images/promotion/'.$data))->response();

    }
}
