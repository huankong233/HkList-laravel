<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ config("app.name") }}</title>
    <script src="https://cdn.bootcdn.net/ajax/libs/axios/1.5.0/axios.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/vue/3.3.4/vue.global.min.js"></script>
    <link href="https://cdn.bootcdn.net/ajax/libs/element-plus/2.3.12/index.min.css" rel="stylesheet">
    <script src="https://cdn.bootcdn.net/ajax/libs/element-plus/2.3.12/index.full.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/element-plus-icons-vue/2.1.0/global.iife.min.js"></script>
    <link rel="stylesheet" href="./assets/css/default.css">
    @yield('style')
    <script>
        axios.interceptors.response.use(function (response) {
            return response;
        }, function (error) {
            const {response: {data: {message}}} = error
            if (message === "Unauthenticated.") {
                alert("登陆凭证过期, 请重新登陆")
                location.href = "/"
            }
            return Promise.reject(error);
        });
    </script>
</head>
