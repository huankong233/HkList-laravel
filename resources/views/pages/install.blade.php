@extends('layouts.main')

@section('title', '安装')

@section('template')
    <div class="container">
        <h1>
            <img src="https://x.imgs.ovh/x/2023/08/19/64df97c347088.png" alt="logo">
        </h1>
        <h2> 就是加速 </h2>
        <div>
            <p>每个梦想的路上，一起前行！</p>
            <form method="post" v-on:submit.prevent="onSubmit">
                <div id="error" v-show="error">
                    <span v-html="message"></span>
                </div>
                <div id="success" v-show="success">
                    <span v-html="message"></span>
                </div>
                <div class="form-group">
                    <div class="form-field">
                        <label>MySQL 数据库地址</label>
                        <input name="db_host" v-model="db_host" required>
                    </div>

                    <div class="form-field">
                        <label>MySQL 端口</label>
                        <input type="number" name="db_port" v-model="db_port" required>
                    </div>
                    <div class="form-field">
                        <label>MySQL 数据库名</label>
                        <input name="db_database" v-model="db_database" required>
                    </div>

                    <div class="form-field">
                        <label>MySQL 用户名</label>
                        <input name="db_username" v-model="db_username" required>
                    </div>

                    <div class="form-field">
                        <label>MySQL 密码</label>
                        <input name="db_password" v-model="db_password" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-field">
                        <label>网站名称</label>
                        <input type="title" v-model="title" name="title" required>
                    </div>

                    <div class="form-field">
                        <label>网站url</label>
                        <input name="app_url" v-model="app_url" required placeholder="例如：http://94list.org"/>
                    </div>

                    <div class="form-field">
                        <label>后台登录路径</label>
                        <input name="admin_path" v-model="admin_path" required placeholder="例如：/admin">
                    </div>

                </div>

                <div class="form-buttons">
                    <button type="submit" v-bind:disabled="pending || success">点击安装</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const {createApp, ref} = Vue

        const app = createApp({
            setup() {
                const db_host = ref('localhost')
                const db_port = ref("3306")
                const db_database = ref('94list')
                const db_username = ref('root')
                const db_password = ref('')
                const title = ref('94list')
                const app_url = ref('http://localhost')
                const admin_path = ref('/admin')
                const success = ref(false)
                const error = ref(false)
                const pending = ref(false)
                const message = ref("")

                const onSubmit = async () => {
                    error.value = false
                    success.value = false
                    pending.value = true

                    const response = await axios.post("/api/do_install", {
                        db_host: db_host.value,
                        db_port: db_port.value,
                        db_database: db_database.value,
                        db_username: db_username.value,
                        db_password: db_password.value,
                        title: title.value,
                        app_url: app_url.value,
                        admin_path: admin_path.value,
                    }).catch(err => {
                        error.value = true
                        const {response: {data}} = err
                        message.value = data.message ?? 'failed'
                    }) ?? 'failed'
                    pending.value = false

                    if (response !== 'failed') {
                        const {data} = response
                        success.value = true
                        if (data.message === 'success') {
                            message.value = [
                                "安装成功!",
                                "您的后台登录账号密码均为:admin,请及时登录修改!<br>",
                                `<a class="btn" href="/" style="background: #bc1818;margin-right: 20px">访问首页</a><a class="btn" href="${admin_path.value}" style="background:#18bc9c">访问后台</a>`
                            ].join("<br>")
                        }
                    }
                }

                return {
                    onSubmit,
                    pending,
                    success,
                    error,
                    message,
                    db_host,
                    db_port,
                    db_database,
                    db_username,
                    db_password,
                    title,
                    app_url,
                    admin_path
                }
            }
        })

        app.mount('#app')
    </script>
@endsection

@section('style')
    <style>
        img[alt='logo'] {
            width: 100%;
        }
    </style>
@endsection
