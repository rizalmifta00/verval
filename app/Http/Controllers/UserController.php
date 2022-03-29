<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Auth;
Use Alert;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = Role::all();
        return view('user.index',compact('role'));
    }

    public function getData(Request $request)
     {
         $data = User::all();
         return datatables()->of($data)
         ->addColumn('action', function($row){
            $btn = '<a href="'.route('user.edit',$row->id).'" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></a>';
            $btn = $btn.'  <button id="btn-delete" class="delete-modal btn btn-danger btn-icon"><i class="icon-trash"></i></button>';
            return $btn;
         })
         ->addColumn('role', function($row){
             return implode(', ',$row->getRoleNames()->toArray());
         })
         ->addIndexColumn()
         ->rawColumns(['action'])
         ->make(true);
     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::all();
        return view('user.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(),
        [
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required',
        ]
        );

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = \Hash::make($request->password);
        $user->save();
        $user->roles()->sync($request->role);

        toast('Data created successfully!','success');
        return redirect()->route('user.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = User::find($id);
        $role = Role::all();
        return view('user.edit',compact('data','role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(),
        [
                'name' => 'required',
                'email' => 'required|unique:users,email,'.$id,
                'password' => 'nullable|confirmed|min:8',
        ]
        );

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = \Hash::make($request->password);
        }
        $user->save();
        $user->roles()->sync($request->role);
        toast('Data updated successfully!','success');
        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return response()->json(['status' => 'success', 'data'=>'success delete data']);
    }

    public function myProfile()
    {
      $data = User::find(Auth::user()->id);
      return view('user.my-profile',compact('data'));
    }

    public function updateProfile(Request $request)
    {
        $this->validate(request(),
        [
            'nama' => 'required',
            'email' => 'required|unique:users,email,'.Auth::user()->id,
        ]
        );

        $user = User::find(Auth::user()->id);

        $user->name = $request->nama;
        $user->email = $request->email;
        $user->save();
        toast('Profile information has been updated!','success');
        return redirect()->route('profile.index');
    }
    public function updatePassword(Request $request)
    {
        $data= User::find(Auth::user()->id);
        if(Hash::check($request->current_password,$data->password)){
          $data->password = Hash::make($request->password);
          $this->validate($request,
            [
              'current_password' => 'required',
              'password' => 'required|string|min:8|confirmed|max:191',
              'password_confirmation' => 'required',
            ]);
          $data->save();
          toast('Password has been changed!','success');
          return redirect()->route('profile.index');
        }
        else{
          return redirect()->route('profile.index')->with('errorPassword','Password does not match.');
        }
    }
}
