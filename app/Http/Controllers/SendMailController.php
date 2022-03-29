<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class SendMailController extends Controller
{
    public function sendEmail(Request $request){
        $email =$request->email;
        $data =array(
            'name' => $request->name,
            'email_body'=>$request->email_body
        );
        Mail::send('email_template', $data, function($mail) use($email) {
            $mail->to($email, 'no-reply')
                    ->subject("Sample Email Laravel");
            $mail->from('rizalmifta22@gmail.com', 'TESTING');
        });

        // Cek kegagalan
        if (Mail::failures()) {
            return "Gagal mengirim Email";
        }
        return "Email berhasil dikirim!";
    }
}
