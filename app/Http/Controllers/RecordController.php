<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'ip'         => 'required|string',
            'fs_id'      => 'required|numeric',
            'user_id'    => 'required|numeric',
            'account_id' => 'required|numeric',
            'size'       => 'required|numeric',
            'ua'         => 'required|string',
            'url'        => 'required|string'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        Record::query()->create($data);

        return ResponseController::success();
    }

    public function removeRecord(Request $request, $record_id)
    {
        $record = Record::query()->find($record_id);
        if (!$record) return ResponseController::recordNotExists();
        $record->delete();
        return ResponseController::success();
    }
}
