<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>登入 - {{ env('APP_NAME','Laravel') }}</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/js/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/css/login.css" media="all">
</head>
<body>

<div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;">

    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2>{{ env('APP_NAME','Laravel') }}</h2>
            <p>欢迎登陆后台管理系统</p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
            <form action="">
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-username"
                           for="LAY-user-login-username"></label>
                    <input type="text" name="email" id="LAY-user-login-username" lay-verify="required|email"
                           placeholder="登录邮箱" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-password"
                           for="LAY-user-login-password"></label>
                    <input type="password" name="password" id="LAY-user-login-password" lay-verify="required"
                           placeholder="密码" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <div class="layui-row">
                        <div class="layui-col-xs7">
                            <label class="layadmin-user-login-icon layui-icon layui-icon-vercode"
                                   for="LAY-user-login-vercode"></label>
                            <input type="text" name="captcha" id="LAY-user-login-vercode" lay-verify="required"
                                   placeholder="图形验证码" class="layui-input">
                        </div>
                        <div class="layui-col-xs5">
                            <div style="margin-left: 10px;">
                                <img src="{{ captcha_src('math') }}" alt=""
                                     onclick="this.src='/captcha/math?'+Math.random()" id="captcha_img">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <button type="button" class="layui-btn layui-btn-fluid" lay-submit
                            lay-filter="LAY-user-login-submit">登 入
                    </button>
                </div>
                {{--<div class="layui-form-item" style="margin-bottom: 20px;">--}}
                    {{--<a href="forget.html" class="layadmin-user-jump-change layadmin-link"--}}
                       {{--style="margin-top: 7px;">忘记密码？</a>--}}
                {{--</div>--}}
            </form>
        </div>
    </div>

    <div class="layui-trans layadmin-user-login-footer">

        <p>© 2019 <a href="http://www.layui.com/" target="_blank">Laravel RBAC Permission Admin</a></p>
        <p>
            <span><a href="http://www.layui.com" target="_blank">Layui</a></span>
            <span><a href="https://learnku.com/laravel" target="_blank">Laravel-China</a></span>
            <span><a href="https://github.com/gedongdong/laravel_rbac_permission" target="_blank">Github</a></span>
        </p>
    </div>

</div>

<script src="/js/layui/layui.js"></script>
<script src="/js/jquery1.12.1.js"></script>
<script>
    layui.use(['element', 'form', 'layer'], function () {
        var element = layui.element;
        var form = layui.form;
        var layer = layui.layer;

        form.on('submit(LAY-user-login-submit)', function (data) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            $.post("{{ route('admin.login.post.white') }}", data.field,
                function (data) {
                    if (data.code === 0) {
                        layer.msg('登入成功', {
                            offset: '15px'
                            , icon: 1
                            , time: 1000
                        }, function () {
                            location.href = '{{ route('admin.index.white') }}';
                        });

                    } else {
                        layer.msg(data.msg, {
                            offset: '15px'
                            , icon: 2
                            , time: 2000
                        });
                        $('#captcha_img').attr('src', '/captcha/math?' + Math.random());
                    }
                });
        });
    });
</script>
</body>
</html>