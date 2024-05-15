<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {

        $users = User::orderBy('created_at')->paginate(10);
        return view('admin.users.list', [
            'users' => $users
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        // dd($user);
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, $id)
    {

        $validate = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,' . $id . ',id'
        ]);
        if ($validate->passes()) {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->mobile = $request->mobile;
            $user->save();
            session()->flash('success', 'User Update succesfully. ');
            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validate->errors()
            ]);
        }
    }

    public function destroy(Request $request)
    {

        $id = $request->id;
        // dd($id);
        $user = User::find($id);
        if ($user == Null) {
            session()->flash('error', 'User not found');
            return response()->json([
                'status' => false
            ]);
        }
        $user->delete($id);
        session()->flash('success', 'User deleted successfully');
        return response()->json([
            'status' => true
        ]);
    }
}
