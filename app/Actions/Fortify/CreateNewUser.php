<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Facades\Mail;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'numeric', 'min:10'],
            'dob' => ['required', 'date'],
            'gender' => ['required'],
            // 'profile_photo_path' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'password' => $this->passwordRules()
        ])->validate();
        
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'mobile' => $input['mobile'],
            'dob' => $input['dob'],
            'gender' => $input['gender'],
            'password' => Hash::make($input['password']),
        ]);

        if(request()->hasFile('profile_photo_path')) {
            $imgtext = request()->file('profile_photo_path')->getClientOriginalName();
            request()->file('profile_photo_path')->storeAs('userprofile',$imgtext,'');
            $user->update(['profile_photo_path' => $imgtext]);
        }

        if($user) {
            Mail::to($user->email)->send(new UserWelcomeMail($input['email'],$input['password']));
        }

        return $user;
    }

}
