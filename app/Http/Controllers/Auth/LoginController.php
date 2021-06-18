<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Get the path the user should be redirected to.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo()
    {
        return route('dashboard.index');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    

    
    public function login(Request $request)
    {  
        $input = $request->all();
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
        ]);
        
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
        $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);    
            return $this->sendLockoutResponse($request);
        }
        
        $fieldType = filter_var($request->name, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        if(Auth::attempt(array($fieldType => $input['name'], 'password' => $input['password'],'is_active'=>1),$request->filled('remember')))
        {
            return $this->sendLoginResponse($request);
        }

            $this->incrementLoginAttempts($request);
            return $this->sendFailedLoginResponse($request);
    }


    public function authenticated(Request $request, $user){
            $user->last_session = session()->getId();
            $user->save();
    }


    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->route('login');
    }

    public function loggedOut($request){

    }
}
