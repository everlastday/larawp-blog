<?php

namespace App\Http\Controllers\Backend;

use App\User;
use App\Http\Requests\UserDestroyRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate($this->limit);
        $usersCount = User::count();
        return view("backend.users.index", compact('users', 'usersCount'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();
        return view("backend.users.create", compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        User::create($request->all());

        return redirect('/backend/users')->with('message', "New User was created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view("backend.users.edit", compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $input_request = $request->all();

        foreach ($input_request as $k => $v) {
            if (empty($v)) unset($input_request[$k]);
        }

        User::findOrFail($id)->update($input_request);
        return redirect('/backend/users')->with('message', "User was updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserDestroyRequest $request, $id)
    {
        if ($id == config('cms.default_user_id')) {
            return redirect('/backend/users')->with('error-message', 'You cannot delete default user!');
        } else {
            $user = User::findOrFail($id);
            $deleteOption = $request->delete_option;
            $selectedUser = $request->selected_user;

            if ($deleteOption == 'delete') {
                $user->posts()->withTrashed()->forcedelete();
            } elseif ($deleteOption == 'attribute') {
                $user->posts()->withTrashed()->update(['author_id' => $selectedUser]);
            }

            $user->delete();
            return redirect('/backend/users')->with('message', "User was deleted successfully");
        }
    }

    public function confirm($id)
    {
        $user = User::findOrFail($id);
        $users = User::where('id', '!=', $user->id)->pluck('name', 'id');

        return view("backend.users.confirm", compact('user', 'users'));
    }

}
