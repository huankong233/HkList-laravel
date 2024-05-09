<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\InvCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvCodeController extends Controller
{
    public function getInvCode(Request $request, $inv_code_id = null)
    {
        if ($inv_code_id !== null) {
            $invCode = InvCode::query()->find($inv_code_id);
            if (!$invCode) return ResponseController::invCodeNotExists();
            return ResponseController::success($invCode);
        }

        $InvCodes = InvCode::query()->paginate($request['size']);
        return ResponseController::success($InvCodes);
    }

    public function addInvCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'can_count' => 'required|numeric',
            'group_id'  => 'required|numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $invCode = InvCode::query()->firstWhere('name', $request['name']);
        if ($invCode) return ResponseController::invCodeExists();

        $group = Group::query()->find($request['group_id']);
        if (!$group) return ResponseController::groupNotExists();

        InvCode::query()->create([
            'name'      => $request['name'],
            'group_id'  => $request['group_id'],
            'use_count' => 0,
            'can_count' => $request['can_count']
        ]);

        return ResponseController::success();
    }

    public function generateInvCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'can_count' => 'required|numeric',
            'count'     => 'required|numeric',
            'group_id'  => 'required|numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $group = Group::query()->find($request['group_id']);
        if (!$group) return ResponseController::groupNotExists();

        for ($i = 0; $i < $request['count']; $i++) {
            $name    = Str::random();
            $invCode = InvCode::query()->firstWhere('name', $name);
            if ($invCode) {
                $i--;
                continue;
            }

            InvCode::query()->create([
                'name'      => $name,
                'group_id'  => $request['group_id'],
                'use_count' => 0,
                'can_count' => $request['can_count']
            ]);
        }

        return ResponseController::success();
    }

    public function updateInvCode(Request $request, $inv_code_id)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'string',
            'use_count' => 'numeric',
            'group_id'  => 'numeric',
            'can_count' => 'numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $invCode = InvCode::query()->find($inv_code_id);
        if (!$invCode) return ResponseController::invCodeNotExists();

        $update = [];

        if (isset($request['name'])) {
            $InvCode = InvCode::query()->firstWhere('name', $request['name']);
            if ($InvCode && $invCode['id'] !== $InvCode['id']) return ResponseController::invCodeExists();
            $update['name'] = $request['name'];
        }

        if (isset($request['group_id'])) {
            $group = Group::query()->find($request['group_id']);
            if (!$group) return ResponseController::groupNotExists();
            $update['group_id'] = $request['group_id'];
        }

        if (isset($request['use_count'])) $update['use_count'] = $request['use_count'];
        if (isset($request['can_count'])) $update['can_count'] = $request['can_count'];

        if (count($update) === 0) return ResponseController::paramsError();

        $invCode->update($update);

        return ResponseController::success();
    }

    public function removeInvCode(Request $request, $inv_code_id)
    {
        $invCode = InvCode::query()->find($inv_code_id);
        if (!$invCode) return ResponseController::invCodeNotExists();

        $invCode->delete();

        return ResponseController::success();
    }

    public function removeInvCodes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inv_code_ids.*' => 'numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        foreach ($request['inv_code_ids'] as $inv_code_id) {
            $invCode = InvCode::query()->find($inv_code_id);
            if (!$invCode) return ResponseController::invCodeNotExists();
            $invCode->delete();
        }

        return ResponseController::success();
    }
}
