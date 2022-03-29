<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use File;
use App\Event;
use Validator;


class EventController extends Controller
{
    public function getData()
    {
     $data = Event::get();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="" class="btn-delete btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/event/'.$row->picture.'" alt="event" title="event" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }
    public function index()
    {
        return view('event.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('event.create');
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
        'date' => 'required',
        // 'link' => 'required|max:191',
        'description' => 'max:191',
        'picture' => 'required|max:2048|mimes:png,jpg,jpeg',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
        if(!empty($request->picture)){
             $file = $request->file('picture');
             $extension = strtolower($file->getClientOriginalExtension());
             $filename = uniqid() . '.' . $extension;
             
            $request->picture->move(public_path('images/event'), $filename);
            //  Storage::put('public/images/event/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Event::create(
           [
                 'name'=>request('name'),
                 'date'=>request('date'),
                 'picture'=>$filename,
                 'description' => request('description'),
                 
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
        // return view('event.edit');
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
        'name_edit' => 'required|max:191',
        'date_edit' => 'required',
        'picture_edit' => 'max:2048|mimes:png,jpg,jpeg',
        'description_edit' => 'max:191',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Event::find($id);
        if(!empty($request->picture_edit)){
          $image_path = public_path().'/images/event/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture_edit');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
          $request->picture_edit->move(public_path('images/event'), $filename);
          // Storage::put('public/images/event/' . $filename, File::get($file));
          // Storage::delete('public/images/event/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name_edit;
        $data->date   = $request->date_edit;
        $data->description   = $request->description_edit;
        // // $data->link   = $request->link_edit;
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
        $data = Event::find($id);
       
        $image_path = public_path().'/images/event/'.$data->picture;
        if(file_exists($image_path)){
         unlink($image_path);
         }
        $data->delete();
    }
}
                                                             