<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function getGroup(Request $request, $group_id = null)
    {
        if ($group_id !== null) {
            $group = Group::query()->find($group_id);
            if (!$group) return ResponseController::groupNotExists();
            return ResponseController::success($group);
        }

        $groups = Group::query()->paginate($request['size']);
        return ResponseController::success($groups);
    }

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

    public function updateGroup(Request $request, $group_id)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'nullable|string',
            'count' => 'nullable|numeric',
            'size'  => 'nullable|numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        $group = Group::query()->find($group_id);
        if (!$group) return ResponseController::groupNotExists();

        $update = [];

        if ($request['name'] !== null) {
            $Group = Group::query()->firstWhere('name', $request['name']);
            if ($group['id'] !== $Group['id']) return ResponseController::groupExists();
            $update['name'] = $request['name'];
        }

        if ($request['count'] !== null) $update['count'] = $request['count'];
        if ($request['size'] !== null) $update['size'] = $request['size'];

        if (count($update) === 0) return ResponseController::paramsError();

        $group->update($update);

        return ResponseController::success();
    }

    public function removeGroup(Request $request, $group_id)
    {
        if (in_array($group_id, ['-1', '0'])) return ResponseController::groupCanNotBeRemoved('自带分组禁止删除');

        $users = User::query()->where('group_id', $group_id)->get();
        if ($users->count() > 0) return ResponseController::groupCanNotBeRemoved('用户组还存在用户');

        $group = Group::query()->find($group_id);
        if (!$group) return ResponseController::groupNotExists();

        $group->delete();

        return ResponseController::success();
    }

    public function removeGroups(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_ids.*' => 'numeric'
        ]);

        if ($validator->fails()) return ResponseController::paramsError();

        foreach ($request['group_ids'] as $group_id) {
            $group = Group::query()->find($group_id);
            if (!$group) return ResponseController::groupNotExists();
            $group->delete();
        }

        return ResponseController::success();
    }
}
