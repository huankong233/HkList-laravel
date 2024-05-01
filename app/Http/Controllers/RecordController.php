<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RecordController extends Controller
{
    public function getRecord(Request $request, $record_id = null)
    {
        if ($record_id !== null) {
            $record = Record::query()->find($record_id);
            if (!$record) return ResponseController::recordNotExists();
            return ResponseController::success($record);
        }

        $records = Record::query()->get();
        return ResponseController::success($records);
    }

    public static function addRecord($data)
    {
        $validator = Validator::make($data, [
            'ip'          => 'required|string',
            'action_name' => ['required', Rule::in(['getList', 'getSign', 'downloadFiles'])],
            'link'        => 'required|string',
            'md5'         => 'string',
            'size'        => 'numeric',
            'ua'          => 'string',
            'user_id'     => 'numeric',
            'account_id'  => 'numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Record::query()->create($data);

        return ResponseController::success();
    }

    public function deleteRecord(Request $request, $record_id)
    {
        $record = Record::query()->find($record_id);
        if (!$record) return ResponseController::recordNotExists();
        $record->delete();
        return ResponseController::success();
    }
}
