@extends('layouts.main')

@section('title', '登陆')

@section('template')
    <div class="container">
        <h1>
            <img src="https://x.imgs.ovh/x/2023/08/19/64df97c347088.png" alt="logo">
        </h1>
        <h2> {{config("app.name")}} </h2>
        <div>
            <form method="post" @@submit.prevent="onSubmit">
                <div class="form-group">
                    <div class="form-field">
                        <label>用户名</label>
                        <input name="username" v-model="username" required>
                    </div>

                    <div class="form-field">
                        <label>密码</label>
                        <input name="password" v-model="password" required>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" :disabled="pending">登陆</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const {createApp, ref} = Vue
        const {ElMessage} = ElementPlus

        const app = createApp({
            setup() {
                const username = ref("")
                const password = ref("")
                const pending = ref(false)

                const onSubmit = async () => {
                    pending.value = true
                    const response = await axios.post("{{route("admin.login")}}", {
                        username: username.value,
                        password: password.value
                    }).catch(error => {
                        const {response: {status}} = error
                        ElMessage.error(status === 400 ? '用户名或密码错误' : '服务器错误')
                    }) ?? 'failed'
                    pending.value = false

                    if (response !== 'failed') {
                        location.reload()
                    }
                }

                return {
                    onSubmit,
                    pending,
                    username,
                    password
                }
            }
        })

        app.use(ElementPlus)
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
