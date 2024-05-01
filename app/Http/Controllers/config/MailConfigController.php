<?php

namespace App\Http\Controllers\config;

use App\Mail\TestMail;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Mail;

class MailConfigController extends Controller
{
    public function getMailConfig(Request $request)
    {
        return ResponseController::success(['mailConfig' => config("mail")]);
    }

    public function sendTestMail(Request $request)
    {
        try {
            Mail::to(config("mail.to.address"))->send(new TestMail());
        } catch (Exception $e) {
            return ResponseController::sendMailFailed($e->getMessage());
        }

        return ResponseController::success();
    }

    public function updateMailConfig(Request $request)
    {
        $update = [];

        if ($request['switch']) $update['MAIL_SWITCH'] = $request['switch'];
        if ($request['host']) $update['MAIL_HOST'] = $request['host'];
        if ($request['port']) $update['MAIL_PORT'] = $request['port'];
        if ($request['username']) $update['MAIL_USERNAME'] = $request['username'];
        if ($request['password']) $update['MAIL_PASSWORD'] = $request['password'];
        if ($request['encryption']) {
            if (!in_array($request['encryption'], ['ssl', 'tls'])) return ResponseController::paramsError();
            $update['MAIL_ENCRYPTION'] = $request['encryption'];
        }
        if ($request['fromAddress']) $update['MAIL_FROM_ADDRESS'] = $request['fromAddress'];
        if ($request['fromName']) $update['MAIL_FROM_NAME'] = $request['fromName'];
        if ($request['toAddress']) $update['MAIL_TO_ADDRESS'] = $request['toAddress'];
        if ($request['toName']) $update['MAIL_TO_NAME'] = $request['toName'];

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }
}
