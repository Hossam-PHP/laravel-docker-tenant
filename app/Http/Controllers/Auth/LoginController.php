<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Hyn\Tenancy\Models\Hostname;

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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showDomainForm() {
        return view('auth.domain');
    }

    public function routeToTenant( Request $request ) {        
        $invalidSubdomains = config( 'app.invalid_subdomains' );
        $validatedData = $request->validate([
            'account' => [
                'required', 
                'string',
                Rule::notIn( $invalidSubdomains ),
                'regex:/^[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])$/'
            ],
        ]);

        $fqdn = $validatedData['account'] . '.' . config( 'app.url_base' );
        $hostExists = Hostname::where( 'fqdn', $fqdn )->exists();
        $port = $request->server('SERVER_PORT') == 8000 ? ':8000' : '';
        if ( $hostExists ) {
            return redirect( ( $request->secure() ? 'https://' : 'http://' ) . $fqdn . $port . '/login' );
        } else {
            return redirect('register')
            ->withInput();
        }
    }
}
