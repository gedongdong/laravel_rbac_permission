<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ env('APP_NAME','Laravel') }}-管理后台</title>
    <link rel="stylesheet" href="/js/layui/css/layui.css">
    @yield('style')
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">{{ env('APP_NAME','Laravel') }}-管理后台</div>
        <!-- 头部区域（可配合layui已有的水平导航） -->
        <ul class="layui-nav layui-layout-left">
            <li class="layui-nav-item"><a href="{{ route('admin.index.white') }}">控制台</a></li>
            <li class="layui-nav-item"><a href="https://github.com/gedongdong/laravel_rbac_permission">Github</a></li>
            <li class="layui-nav-item"><a href="">网站首页</a></li>
            <li class="layui-nav-item">
                <a href="javascript:;">其它系统</a>
                <dl class="layui-nav-child">
                    <dd><a href="">邮件管理</a></dd>
                    <dd><a href="">消息管理</a></dd>
                    <dd><a href="">授权管理</a></dd>
                </dl>
            </li>
        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    你好，{{ session('user')['name'] }}
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="{{ route('admin.modify_pwd.white') }}">修改密码</a></dd>
                    <dd>
                        <a href="{{ route('admin.logout.white') }}"
                           onclick="event.preventDefault();
                   document.getElementById('logout-form').submit();">
                            退出
                        </a>

                        <form id="logout-form" action="{{ route('admin.logout.white') }}" method="POST"
                              style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </dd>
                </dl>
            </li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree" lay-filter="test">
                @foreach($menu_tree as $menu)
                    <li class="layui-nav-item
                            @if(key_exists('children',$menu) && in_array($currRouteName,array_column($menu['children'],'route')))
                            layui-nav-itemed
                        @endif
                            ">
                        <a class="" href="javascript:;">{{ $menu['name'] }}</a>
                        <dl class="layui-nav-child">
                            @if(key_exists('children',$menu))
                                @foreach($menu['children'] as $child)
                                    <dd
                                            @if($currRouteName == $child['route'])
                                            class="layui-this"
                                            @endif
                                    ><a href="{{ route($child['route']) }}">{{ $child['name'] }}</a></dd>
                                @endforeach
                            @endif
                        </dl>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            @yield('content')
        </div>
    </div>

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © Laravel RBAC Permission Admin
    </div>
</div>
<script src="/js/layui/layui.js"></script>
<script src="/js/jquery1.12.1.js"></script>
<script>
    //JavaScript代码区域
    layui.use('element', function () {
        var element = layui.element;

    });
</script>
@yield('script')
</body>
</html>