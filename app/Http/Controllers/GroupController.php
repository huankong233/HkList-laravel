<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function addGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string',
            'count' => 'required|numeric',
            'size'  => 'required|numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $group = Group::query()->firstWhere('name', $request['name']);
        if ($group) return ResponseController::groupExists();

        Group::query()->create([
            'name'  => $request['name'],
            'count' => $request['count'],
            'size'  => $request['size']
        ]);

        return ResponseController::success();
    }

    public function updateGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required|numeric',
            'count' => 'numeric',
            'size'  => 'numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $group = Group::query()->find($request['id']);
        if (!$group) return ResponseController::groupNotExists();

        $update = [];

        if ($request['name']) {
            if (Group::query()->firstWhere('name', $request['name'])) return ResponseController::groupExists();
            $update['name'] = $request['name'];
        }

        if ($request['count']) $update['count'] = $request['count'];
        if ($request['size']) $update['count'] = $request['size'];

        $group->update($update);

        return ResponseController::success();
    }

    public function removeGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $group = Group::query()->find($request['id']);
        if (!$group) return ResponseController::groupNotExists();

        $group->delete();
        return ResponseController::success();
    }
}
