<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RecordController extends Controller
{
    public function getRecords(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "orderBy" => ["required", Rule::in(["size", "id"])]
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $records = Record::query()->orderByDesc($request["orderBy"] ?? "id")->paginate($request["size"]);
        return ResponseController::success($records);
    }

    public function getRecordsCount()
    {
        $today = Record::query()->whereDate("created_at", date("Y-m-d"))->get();
        $total = Record::query()->get();
        return ResponseController::success([
            "today" => [
                "count" => $today->count(),
                "size"  => $today->sum("size"),
            ],
            "total" => [
                "count" => $total->count(),
                "size"  => $total->sum("size"),
            ]
        ]);
    }

    public static function addRecord($data)
    {
        $validator = Validator::make($data, [
            "ip"         => "required|string",
            "fs_id"      => "required|numeric",
            "url"        => "required|string",
            "ua"         => "required|string",
            "user_id"    => "required|numeric",
            "token_id"   => "required|numeric",
            "account_id" => "required|numeric",
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Record::query()->create($data);

        return ResponseController::success();
    }

    public function removeRecords(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "record_ids"   => "required|array",
            "record_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Record::query()->whereIn("id", $request->record_ids)->delete();

        return ResponseController::success();
    }
}
