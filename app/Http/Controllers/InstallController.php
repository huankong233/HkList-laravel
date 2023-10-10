<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Database\QueryException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstallController extends Controller
{
    public function doInstall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "db_connection"      => "required",
                "app_url"            => "required",
                "admin_route_prefix" => "required",
            ]);

            if ($validator->fails()) return ResponseController::response(400, "参数不合法");

            $dbConfig = config('database');
            switch ($request['db_connection']) {
                case 'mysql':
                    $validator = Validator::make($request->all(), [
                        "db_host"     => "required",
                        "db_port"     => "required",
                        "db_database" => "required",
                        "db_username" => "required",
                        "db_password" => "required"
                    ]);

                    if ($validator->fails()) return ResponseController::response(400, "参数不合法");

                    $mysqlDB = [
                        'host'     => $request['db_host'],
                        'port'     => $request['db_port'],
                        'database' => $request['db_database'],
                        'username' => $request['db_username'],
                        'password' => $request['db_password'],
                    ];

                    $dbConfig['default']              = 'mysql';
                    $dbConfig['connections']['mysql'] = array_merge($dbConfig['connections']['mysql'], $mysqlDB);
                    config(['database' => $dbConfig]);

                    $postData = [
                        'app_name'           => $request['app_name'],
                        'app_url'            => $request['app_url'],
                        'admin_route_prefix' => $request['admin_route_prefix'],
                        'db_connection'      => $request['db_connection'],
                        'db_host'            => $request['db_host'],
                        'db_port'            => $request['db_port'],
                        'db_database'        => $request['db_database'],
                        'db_username'        => $request['db_username'],
                        'db_password'        => $request['db_password']
                    ];
                    break;
                case 'sqlite':
                    if (file_exists(database_path('database.sqlite'))) unlink(database_path('database.sqlite'));
                    file_put_contents(database_path('database.sqlite'), '');

                    $sqliteDB                          = [
                        'database' => database_path('database.sqlite')
                    ];
                    $dbConfig['default']               = 'sqlite';
                    $dbConfig['connections']['sqlite'] = array_merge($dbConfig['connections']['sqlite'], $sqliteDB);
                    config(['database' => $dbConfig]);

                    $postData = [
                        'app_name'           => $request['app_name'],
                        'app_url'            => $request['app_url'],
                        'admin_route_prefix' => $request['admin_route_prefix'],
                        'db_connection'      => $request['db_connection'],
                        'db_host'            => "",
                        'db_port'            => "",
                        'db_database'        => database_path('database.sqlite'),
                        'db_username'        => "",
                        'db_password'        => ""
                    ];
                    break;
                default:
                    return ResponseController::response(400, '数据库类型错误');
            }

            DB::purge();
            // db测试
            DB::connection()->select('select 1 limit 1');

            // 临时写入key
            $postData['app_key'] = 'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')));

            AdminController::modifyEnv([
                "APP_NAME"           => $postData['app_name'],
                "APP_KEY"            => $postData['app_key'],
                "APP_URL"            => $postData['app_url'],
                "DB_CONNECTION"      => $postData['db_connection'],
                "DB_HOST"            => $postData['db_host'],
                "DB_PORT"            => $postData['db_port'],
                "DB_DATABASE"        => $postData['db_database'],
                "DB_USERNAME"        => $postData['db_username'],
                "DB_PASSWORD"        => $postData['db_password'],
                "ADMIN_ROUTE_PREFIX" => $postData['admin_route_prefix']
            ]);

            // 导入sql
            $installSql = database_path('sql' . DIRECTORY_SEPARATOR . $request['db_connection'] . '.sql');
            DB::unprepared(file_get_contents($installSql));

            // 添加用户
            User::insert([
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'is_admin' => 1,
            ]);

            // 写入安装锁
            $installLock = base_path() . DIRECTORY_SEPARATOR . 'install.lock';
            file_put_contents($installLock, 'install ok');
            return ResponseController::response(200, '安装成功');
        } catch (QueryException $exception) {
            return ResponseController::response(400, '数据库配置错误 :' . $exception->getMessage());
        } catch (Exception $exception) {
            return ResponseController::response(500, '异常错误 :' . $exception->getMessage());
        }
    }
}
