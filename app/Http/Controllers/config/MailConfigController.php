<?php

namespace App\Http\Controllers\config;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MailConfigController extends Controller
{
    public function getMailConfig(Request $request)
    {
        $config = config("mail");

        return ResponseController::success([
            "switch"       => $config["switch"],
            "host"         => $config["mailers"]["smtp"]["host"],
            "port"         => $config["mailers"]["smtp"]["port"],
            "username"     => $config["mailers"]["smtp"]["username"],
            "password"     => $config["mailers"]["smtp"]["password"],
            "encryption"   => $config["mailers"]["smtp"]["encryption"],
            "from_address" => $config["from"]["address"],
            "from_name"    => $config["from"]["name"],
            "to_address"   => $config["to"]["address"],
            "to_name"      => $config["to"]["name"]
        ]);
    }

    public function updateMailConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "switch"       => "required|bool",
            "host"         => "required|string",
            "port"         => "required|numeric",
            "username"     => "required|string",
            "password"     => "required|string",
            "encryption"   => ["required", Rule::in(["tls", "ssl"])],
            "from_address" => "required|string",
            "from_name"    => "required|string",
            "to_address"   => "required|string",
            "to_name"      => "required|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        updateEnv([
            "MAIL_SWITCH"       => $request["switch"],
            "MAIL_HOST"         => '"' . $request["host"] . '"',
            "MAIL_PORT"         => '"' . $request["port"] . '"',
            "MAIL_USERNAME"     => '"' . $request["username"] . '"',
            "MAIL_PASSWORD"     => '"' . $request["password"] . '"',
            "MAIL_ENCRYPTION"   => '"' . $request["encryption"] . '"',
            "MAIL_FROM_ADDRESS" => '"' . $request["from_address"] . '"',
            "MAIL_FROM_NAME"    => '"' . $request["from_name"] . '"',
            "MAIL_TO_ADDRESS"   => '"' . $request["to_address"] . '"',
            "MAIL_TO_NAME"      => '"' . $request["to_name"] . '"',
        ]);

        return ResponseController::success();
    }

    public function sendTestMail(Request $request)
    {
        $update     = self::updateMailConfig($request);
        $updateData = $update->getData(true);
        if ($updateData["code"] !== 200) return $update;

        try {
            Mail::raw("亲爱的 " . config("mail.to.name") . ":\n\t这是一封来自" . config("app.name") . "的连通性测试邮件,请查收!", function ($message) {
                $message->to(config("mail.to.address"))->subject("测试邮件");
            });
        } catch (Exception $e) {
            return ResponseController::sendMailFailed($e->getMessage());
        }

        return ResponseController::success();
    }
}
