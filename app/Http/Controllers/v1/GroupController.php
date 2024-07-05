<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function getGroups(Request $request)
    {
        $groups = Group::query()->paginate($request["size"]);
        return ResponseController::success($groups);
    }

    public function addGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"  => "required|string",
            "count" => "required|numeric",
            "size"  => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $group = Group::query()->firstWhere("name", $request["name"]);
        if ($group) return ResponseController::groupExists();

        Group::query()->create([
            "name"  => $request["name"],
            "count" => $request["count"],
            "size"  => $request["size"]
        ]);

        return ResponseController::success();
    }

    public function updateGroup(Request $request, $group_id)
    {
        $validator = Validator::make($request->all(), [
            "name"  => "required|string",
            "count" => "required|numeric",
            "size"  => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $group = Group::query()->find($group_id);
        if (!$group) return ResponseController::groupNotExists();

        $Group = Group::query()->firstWhere("name", $request["name"]);
        if ($Group && $group["id"] !== $Group["id"]) return ResponseController::groupExists();

        $group->update([
            "name"  => $request["name"],
            "count" => $request["count"],
            "size"  => $request["size"]
        ]);

        return ResponseController::success();
    }

    public function removeGroups(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "group_ids"   => "required|array",
            "group_ids.*" => "required|numeric"
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        if (in_array(["-1", "0"], $request["group_ids"])) return ResponseController::groupCanNotBeRemoved("自带分组禁止删除");

        Group::query()->whereIn("id", $request["group_ids"])->delete();

        return ResponseController::success();
    }
}
