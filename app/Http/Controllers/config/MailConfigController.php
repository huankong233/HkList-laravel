<?php

namespace App\Http\Controllers\config;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MailConfigController extends Controller
{
    public function getMailConfig(Request $request)
    {
        $config = config('mail');

        return ResponseController::success([
            'switch'       => $config['switch'],
            'host'         => $config['mailers']['smtp']['host'],
            'port'         => $config['mailers']['smtp']['port'],
            'username'     => $config['mailers']['smtp']['username'],
            'password'     => $config['mailers']['smtp']['password'],
            'encryption'   => $config['mailers']['smtp']['encryption'],
            'from_address' => $config['from']['address'],
            'from_name'    => $config['from']['name'],
            'to_address'   => $config['to']['address'],
            'to_name'      => $config['to']['name']
        ]);
    }

    public function sendTestMail(Request $request)
    {
        try {
            Mail::raw('亲爱的 ' . config('mail.to.name') . ':\n\t这是一封来自' . config('app.name') . '的连通性测试邮件,请查收!', function ($message) {
                $message->to(config('mail.to.address'))->subject('测试邮件');
            });
        } catch (Exception $e) {
            return ResponseController::sendMailFailed($e->getMessage());
        }

        return ResponseController::success();
    }

    public function updateMailConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'switch'       => 'nullable|bool',
            'host'         => 'nullable|string',
            'port'         => 'nullable|numeric',
            'username'     => 'nullable|string',
            'password'     => 'nullable|string',
            'encryption'   => ['nullable', Rule::in(['tls', 'ssl'])],
            'from_address' => 'nullable|string',
            'from_name'    => 'nullable|string',
            'to_address'   => 'nullable|string',
            'to_name'      => 'nullable|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $update = [];

        if ($request['switch'] !== null) $update['MAIL_SWITCH'] = $request['switch'];
        if ($request['host'] !== null) $update['MAIL_HOST'] = $request['host'];
        if ($request['port'] !== null) $update['MAIL_PORT'] = $request['port'];
        if ($request['username'] !== null) $update['MAIL_USERNAME'] = $request['username'];
        if ($request['password'] !== null) $update['MAIL_PASSWORD'] = $request['password'];
        if ($request['encryption'] !== null) $update['MAIL_ENCRYPTION'] = $request['encryption'];
        if ($request['from_address'] !== null) $update['MAIL_FROM_ADDRESS'] = $request['from_address'];
        if ($request['from_name'] !== null) $update['MAIL_FROM_NAME'] = $request['from_name'];
        if ($request['to_address'] !== null) $update['MAIL_TO_ADDRESS'] = $request['to_address'];
        if ($request['to_name'] !== null) $update['MAIL_TO_NAME'] = $request['to_name'];

        if (count($update) === 0) ResponseController::paramsError();

        updateEnv($update);

        return ResponseController::success();
    }
}
