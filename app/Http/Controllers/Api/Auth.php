<?php

namespace App\Http\Controllers\Api\Entity;

use App\Http\Controllers\Controller;

use App\Mail\MultipleAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User as mUser;
use App\Models\Company;
use App\Models\Login_attempt;

use Carbon\Carbon;

class Auth extends Controller
{
    private $_maxAttempt    = 7;
    private $_activePin     = 120; // second
    private $_compCodeSelect;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->_compCodeSelect  = $this->setConfigDatabaseConnectionSelect($request);

            return $next($request);
        });
    }

    public function company(Request $req)
    {
        if (!empty($req->input('api_gateway'))) {
            $captcha = true;
        } else {
            $captcha = $this->checkLoginAttempt($req);
        }

        if (!$captcha) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'Forbidden Access!',
                'data'      => [],
            ], 403);
        }

        $validate = Validator::make($req->all(), [
            'comp_code' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => $validate->errors(),
                'data'      => [],
            ], 200);
        }

        $get = Company::where(Company::raw("BINARY comp_code"), $req->input('comp_code'))
            ->where('comp_is_ho', 1)
            ->first();

        if (empty($get)) {
            return response()->json([
                'data'      => [],
                'status'    => 'failed',
                'captcha'   => $captcha
            ], 200);
        }

        if (!empty($req->input('api_gateway'))) {
            $responseData = ['comp_code' => $get->comp_code];
        } else {
            $this->resetLoginAttempt($captcha);

            $responseData = [
                'comp_id'   => $get->comp_id,
                'comp_code' => $get->comp_code
            ];
        }

        return response()->json([
            'data'      => $responseData,
            'status'    => 'success',
            'captcha'   => $captcha
        ], 200);
    }

    public function signIn(Request $req)
    {
        $db         = trim($req->input('_db'));
        $captcha    = $this->checkLoginAttempt($req, $db);

        if (!$captcha) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'Forbidden Access!',
                'data'      => [],
            ], 403);
        }

        $validate = Validator::make($req->all(), [
            'username'  => 'required',
            'password'  => 'required',
            '_db'       => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => $validate->errors(),
                'data'      => [],
            ], 200);
        }

        $user = new mUser;

        if ($db != 'axiasolusi' && $db != 'undefined') {
            $user->setConnection($this->_compCodeSelect);
        }

        $get = $user->join('companies', 'users.comp_id', '=', 'companies.comp_id')
            ->where($user->raw("BINARY username"), trim($req->input('username')))
            ->where('comp_code', trim($req->input('_db')))
            ->where('user_status', 1)
            ->where(function ($query) {
                return $query->whereDate('user_inactive_date', '>', Carbon::now()->format('Y-m-d'))
                    ->orWhereNull('user_inactive_date');
            })
            ->with('Company')
            ->first();

        if (empty($get) || (!Hash::check($req->input('password'), $get->password))) {
            return response()->json([
                'data'      => [],
                'status'    => 'failed',
                'captcha'   => $captcha
            ], 200);
        }

        $pin =  rand(100000, 999999);

        $row = $user->find($get->user_id);

        $row->user_pin          = $pin;
        $row->user_active_pin   = Carbon::now()->toDateTimeString();

        $row->save();

        if ($req->input('_db') != 'axiasolusi') {
            // send notification email
            Mail::to($get->user_email)->send(new MultipleAuth([
                'fullname'      => $get->user_fullname,
                'username'      => $get->username,
                'pin'           => $pin,
                'activePin'     => $this->_activePin
            ]));
            // end

            $this->resetLoginAttempt($captcha, $db);

            return response()->json([
                'data'      => [
                    'user'  => [
                        'user_id'       => $get->user_id,
                        'user_fullname' => $get->user_fullname,
                        'user_type'     => $get->user_type,
                    ],
                    'company'   => [
                        'comp_id'       => $get->company->comp_id,
                        'comp_code'     => $get->company->comp_code,
                    ]
                ],
                'status'    => 'success',
                'captcha'   => $captcha
            ], 200);
        } else {
            $this->resetLoginAttempt($captcha, $db);

            $company    = $get->company;
            $user       = $get->toArray();

            foreach ($user as $key => $value) {
                if ($key == 'company') {
                    unset($user[$key]);
                }
            }

            return response()->json([
                'data'      => [
                    'user'          => $user,
                    'company'       => $company,
                    'api_token'     => Crypt::encryptString(base64_encode(json_encode([
                        'user_id'       => $get->user_id,
                        'username'      => $get->username,
                        'comp_code'     => $db,
                        'active_date'   => Carbon::now()->toDateTimeString(),
                        'random_string' => Str::random(),
                    ])))
                ],
                'status'    => 'success',
                'captcha'   => $captcha
            ], 200);
        }
    }

    public function validatePin(Request $req)
    {
        $db         = trim($req->input('_db'));
        $captcha    = $this->checkLoginAttempt($req, $db);

        if (!$captcha) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'Forbidden Access!',
                'data'      => [],
            ], 403);
        }

        $validate = Validator::make($req->all(), [
            'username'  => 'required',
            'user_pin'  => 'required',
            '_db'       => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => $validate->errors(),
                'data'      => [],
            ], 200);
        }

        $user = new mUser;

        if ($db != 'axiasolusi' && $db != 'undefined') {
            $user->setConnection($this->_compCodeSelect);
        }

        $get = $user->join('companies', 'users.comp_id', '=', 'companies.comp_id')
            ->where([
                'username'  => trim($req->input('username')),
                'comp_code' => $db,
                'user_pin'  => trim($req->input('user_pin')),
            ])
            ->with('Company')
            ->first();

        if (empty($get)) {
            return response()->json([
                'data'      => [],
                'type'      => 1,
                'status'    => 'failed',
                'message'   => 'Your PIN is invalid, please check mailbox!',
                'captcha'   => $captcha
            ], 200);
        }

        $date   = Carbon::parse($get->user_active_pin);
        $now    = Carbon::now();

        $diff   = $date->diffInSeconds($now);

        if ($diff > $this->_activePin) {
            return response()->json([
                'data'      => [],
                'type'      => 2,
                'status'    => 'failed',
                'message'   => 'Your PIN is expired!, please re-login.',
                'captcha'   => $captcha
            ], 200);
        }

        $this->resetLoginAttempt($captcha, $db);

        $company    = $get->company;
        $user       = $get->toArray();

        foreach ($user as $key => $value) {
            if ($key == 'company') {
                unset($user[$key]);
            }
        }

        return response()->json([
            'data'      => [
                'user'          => $user,
                'company'       => $company,
                'api_token'     => Crypt::encryptString(base64_encode(json_encode([
                    'user_id'       => $get->user_id,
                    'username'      => $get->username,
                    'comp_code'     => $db,
                    'active_date'   => Carbon::now()->toDateTimeString(),
                    'random_string' => Str::random(),
                ])))
            ],
            'status'    => 'success',
            'captcha'   => $captcha
        ], 200);
    }

    public function checkLoginAttempt(Request $req, $db = 'axiasolusi')
    {
        $mLoginAttempt = new Login_attempt;

        if ($db != 'axiasolusi' && $db != 'undefined') {
            $mLoginAttempt->setConnection($this->_compCodeSelect);
        }

        if (empty($req->header('Captcha')) || empty($req->header('IPAddress')) || empty($req->header('User-Agent'))) {
            return false;
        }

        $loginAttempt = Login_attempt::where('ip_address', $req->header('IPAddress'))
            ->where('user_agent', $req->header('User-Agent'))
            ->first();

        if (empty($loginAttempt)) {
            $captcha = Str::random(32);

            Login_attempt::create([
                'ip_address'    => $req->header('IPAddress'),
                'user_agent'    => $req->header('User-Agent'),
                'captcha'       => $captcha,
                'attempt'       => 0,
            ]);

            return $captcha;
        } else {
            if ($loginAttempt->captcha == $req->header('Captcha')) {
                if ($loginAttempt->attempt >= $this->_maxAttempt) {
                    return false;
                }
                $newCaptcha = Str::random(32);

                $loginAttempt = Login_attempt::where('captcha', $loginAttempt->captcha)->update([
                    'captcha'       => $newCaptcha,
                    'attempt'       => $loginAttempt->attempt + 1,
                ]);

                return $newCaptcha;
            } else {
                return false;
            }
        }
    }

    public function resetLoginAttempt($captcha, $db = 'axiasolusi')
    {
        $mLoginAttempt = new Login_attempt;

        if ($db != 'axiasolusi' && $db != 'undefined') {
            $mLoginAttempt->setConnection($this->_compCodeSelect);
        }

        $loginAttempt = Login_attempt::where('captcha', $captcha)->first();

        if (!empty($loginAttempt)) {
            return Login_attempt::where('captcha', $loginAttempt->captcha)->update([
                'attempt'   => 0,
            ]);
        }
    }
}
