<?php

namespace Cc\Labems\Controllers;

use Cc\Labems\Exceptions\ErrException as Exception;
use Cc\Labems\Facades\Attacent;
use Cc\Labems\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    public function getSysInfo()
    {
        return [
            'attachmentUrl' => Attacent::url('/'),
            'routeList' => Auth::user()->routeList(),
        ];
    }

    public function sysInfo(Request $request)
    {
        return succ($this->getSysInfo());
    }

    public function changePassword(Request $request)
    {
        $password = head($this->getInput(['password' => 'required|min:6|confirmed']));
        $user = Auth::user();
        $user->password = Hash::make($password);
        $user->save();
        $token = Auth::newToken($user);
        return succ()->setJWTHeader($token);
    }

    public function login(Request $request)
    {
        $credentials = $this->getInput(['username', 'password']);
        $token = Auth::attempt($credentials, true);
        if ($token) {
            return succ($this->getSysInfo())->setJWTHeader($token);
        }
        return err('username or password is incorrect');
    }

    public function logout(Request $request)
    {
        if (config('jwt.blacklist_enabled')) {
            try {
                Auth::logout();
            } catch (\Exception $e) {
            }
        }
        return succ();
    }

    public function getInput($rule, $message = [])
    {
        $rule = collect($rule)->mapWithKeys(function ($item, $key) {
            if (is_int($key)) {
                return [$item => 'required'];
            }
            return [$key => $item];
        })->toArray();
        $validator = Validator::make(request()->all(), $rule, $message);
        if ($validator->fails()) {
            throw new Exception(implode("\n", $validator->errors()->all()));
        }
        return request()->only(array_keys($rule));
    }
}
