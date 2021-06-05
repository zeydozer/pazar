<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Cookie;
use Mail;

class LoginC extends Controller
{
    public function index(Request $r)
    {
        if ($r->isMethod('post'))
        {
            $user = User::where('mail', $r->get('mail'))->where('pass', md5($r->get('pass')))->where('del', 0)->first();
            
            if ($user)
            {
                Cookie::queue(Cookie::forever('user', $user));

                return [true, 'Yönlendiriliyorsunuz...'];
            }
    
            else return [false, 'Bilgiler hatalı! <br> Kontrol ederek tekrar deneyin.'];
        }

        else return view('login', ['title' => 'Giriş Yap']);
    }

    public function logout()
    {
        if (Cookie::get('user'))
        
            Cookie::queue(Cookie::forget('user'));
        
        return redirect('login');
    }

    public function token(Request $r)
    {
        $token = $r->has('token') ? $r->get('token') : null;

        if ($token)
        {
            $control = User::where('token', $token)->count();

            if ($control == 0) return redirect('token');
        }

        if ($r->isMethod('post'))
        {
            if ($token)
            {
                $user = User::where('mail', $r->get('mail'))->where('token', $token)->first();

                if (is_null($user)) return [false, 'E-posta adresi hatalı!'];

                if ($r->get('pass') != $r->get('repeat'))

                    return [false, 'Parola doğrulama hatalı!'];

                $user->pass = md5($r->get('pass'));
                
                $user->token = null;

                $data = ['subj' => 'refresh', 'name' => $user->name, 'pass' => $r->get('pass')];

                Mail::send(['html' => 'mail'], $data, function($msg) use ($user)
                {
                    $msg->to($user->mail, $user->isim)->subject('Parola Yenile');
                    
                    $msg->from('web@noone.com.tr', 'Ruberu | Pazaryeri Entegrasyon');
                });

                $user->save();

                return [true, 'Parolanız başarıyla değiştirildi.'];
            }

            else
            {
                $user = User::where('mail', $r->get('mail'))->first();

                if (is_null($user)) return [false, 'E-Posta adresi hatalı!'];

                $user->token = md5(mt_rand());

                $data = ['subj' => 'reset', 'name' => $user->name, 'token' => $user->token];

                Mail::send(['html' => 'mail'], $data, function($msg) use ($user)
                {
                    $msg->to($user->mail, $user->isim)->subject('Parola Sıfırla');
                    
                    $msg->from('web@noone.com.tr', 'Ruberu | Pazaryeri Entegrasyon');
                });

                $user->save();

                return [true, 'Parola yenileme bağlantısı e-posta adresinize gönderildi.'];
            }
        }

        else return view('login', ['title' => $token ? 'Parola Yenile' : 'Parola Sıfırla']);
    }
}
