<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ config("app.name") }}</title>
    <script src="https://cdn.bootcdn.net/ajax/libs/axios/1.5.0/axios.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/vue/3.3.4/vue.global.prod.min.js"></script>
    {{--    <script src="https://cdn.bootcdn.net/ajax/libs/vue/3.3.4/vue.global.min.js"></script>--}}
    {{--    <link rel="stylesheet" href="https://unpkg.com/element-plus@2.3.12/theme-chalk/dark/css-vars.css">--}}
    <link href="https://cdn.bootcdn.net/ajax/libs/element-plus/2.3.12/index.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/css-vars.css">
    <script src="https://cdn.bootcdn.net/ajax/libs/element-plus/2.3.12/index.full.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/element-plus-icons-vue/2.1.0/global.iife.min.js"></script>
    <link rel="stylesheet" href="./assets/css/default.css">
    @yield('style')
    <script>
        axios.interceptors.response.use(function (response) {
            return response;
        }, function (error) {
            const {response: {data: {message}}} = error
            if (message === "Unauthenticated." || message === "CSRF token mismatch.") {
                alert("登陆凭证过期, 请重新刷新页面或重新登陆")
                location.href = "/"
            }
            return Promise.reject(error);
        });

        // 防止延迟过高
        axios.defaults.timeout = 600000;

        const dark = window.matchMedia('(prefers-color-scheme: dark)').matches
        if (dark) document.querySelector("html").classList.add("dark")
    </script>
</head>
