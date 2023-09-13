@extends('layouts.main')

@section('title', '登陆')

@section('template')
    <div class="container">
        <el-card>
            <h1>
                <img src="favicon.ico" alt="logo">
            </h1>
            <h2> {{config("app.name")}} </h2>
            <el-form
                    ref="loginFormRef"
                    v-bind:model="loginForm"
                    v-bind:rules="loginFormRule"
                    label-width="100px"
            >
                <el-form-item label="用户名" prop="username">
                    <el-input v-model="loginForm.username"></el-input>
                </el-form-item>
                <el-form-item label="密码" prop="password">
                    <el-input v-model="loginForm.password"></el-input>
                </el-form-item>
                <el-form-item class="center">
                    <el-button
                            type="primary"
                            v-on:click="login(loginFormRef)"
                            v-bind:disabled="loginForm.pending"
                            v-bind:loading="loginForm.pending"
                    >
                        登陆
                    </el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
@endsection

@section('scripts')
    <script>
        const {createApp, ref} = Vue
        const {ElMessage} = ElementPlus

        const app = createApp({
            setup() {
                const loginForm = ref({
                    username: "",
                    password: "",
                    pending: false
                })

                const loginFormRef = ref(null)

                const loginFormRule = {
                    username: [{required: true, message: '请输入用户名', trigger: 'blur'}],
                    password: [{required: true, message: '请输入密码', trigger: 'blur'}]
                }

                const login = async (formEl) => {
                    if (!formEl) return
                    if (await formEl.validate(() => {
                    })) {
                        loginForm.value.pending = true

                        const response = await axios.post("{{route('admin.login')}}", {
                            username: loginForm.value.username,
                            password: loginForm.value.password
                        }).catch(error => {
                            const {response: {data: {message}, status}} = error
                            ElMessage.error(status === 400 ? "用户名或密码错误" : '服务器错误')
                        }) ?? "failed"

                        loginForm.value.pending = false

                        if (response !== 'failed') {
                            ElMessage.success('登陆成功')
                            setTimeout(() => location.reload(), 1000)
                        }
                    }
                }

                return {
                    loginForm,
                    loginFormRule,
                    loginFormRef,
                    login
                }
            }
        })

        app.use(ElementPlus)
        app.mount('#app')
    </script>
@endsection

@section('style')
    <style>
        .container {
            max-width: 515px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        h2 {
            margin: 0 0 15px 0;
        }
    </style>
@endsection
