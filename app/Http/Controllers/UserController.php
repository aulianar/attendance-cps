<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $login  = auth()->user()->id;
        $search = request('search');
        if ($search) {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
                ->orderBy('name')
                ->where([
                    // khusus admin
                    ['id', '!=', '1'],
                    // selain admin
                    ['id', '!=', $login]
                ])
                ->paginate(20)
                ->withQueryString();
        } else {
            $users = User::where([
                ['id', '!=', '1'],
                ['id', '!=', $login]
            ])
                ->orderBy('name')
                ->paginate(10);
        }
        return view('user.index', compact('users'));
    }
    public function makeadmin(User $user)
    {
        $user->timestamps = false;
        $user->is_admin   = true;
        $user->save();
        return back()->with('success', 'Make admin successfully!');
    }

    public function removeadmin(User $user)
    {
        if ($user->id != 1) {
            $user->timestamps = false;
            $user->is_admin   = false;
            $user->save();
            return back()->with('success', 'Remove admin successfully!');
        } else {
            return redirect()->route('user.index');
        }
    }
    public function destroy(User $user)
    {
        if ($user->id != 1) {
            $user->delete();
            return back()->with('success', 'Delete user successfully!');
        } else {
            return redirect()->route('user.index')->with('danger', 'Delete user failed!');
        }
    }

}