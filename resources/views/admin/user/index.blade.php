@extends('layouts.admin')

@section('style')
    <style>
        #page li {
            display: inline-block;
        }

        #page .active span {
            background-color: #009688;
            color: #fff;
            border: 0px;
            height: 30px;
            border-radius: 2px;
        }

        #page .disabled span {
            color: #ccc;
        }
    </style>
@endsection

@section('content')
    <a href="{{ route('admin.user.create') }}" class="layui-btn">添加用户</a>
    <table class="layui-table">
        <colgroup>
            <col width="50">
        </colgroup>
        <thead>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>邮箱</th>
            <th>是否管理员</th>
            <th>所属角色</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->administrator==\App\Http\Models\Users::ADMIN_YES?'是':'否' }}</td>
                <td>
                    @foreach($user->roles as $role)
                        <span class="layui-badge layui-bg-green">{{ $role->name }}</span>
                    @endforeach
                </td>
                <td>
                    @if($user->status==\App\Http\Models\Users::STATUS_ENABLE)
                        <span style="color:#009688;">启用</span>
                    @else
                        <span style="color:#FF5722;">禁用</span>
                    @endif
                </td>
                <td>{{ $user->created_at }}</td>
                <td>
                    <a href="{{ route('admin.user.edit') }}?user_id={{ $user->id }}"
                       class="layui-btn layui-btn-sm">编辑</a>
                    @if($user->status==\App\Http\Models\Users::STATUS_ENABLE)
                        <button class="layui-btn layui-btn-warm layui-btn-sm" type="button"
                                onclick="changeStatus({{ $user->id }})">禁用
                        </button>
                        <a href="javascript:void(0)" onclick="resetPwd({{ $user->id }})"
                           class="layui-btn layui-btn-sm layui-btn-warm">重置密码</a>
                    @else
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button"
                                onclick="changeStatus({{ $user->id }})">启用
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div id="page" class="layui-box layui-laypage layui-laypage-default">{{ $users->links() }}</div>
@endsection

@section('script')
    <script>
        layui.use(['layer'], function () {
            var layer = layui.layer;
        });

        function changeStatus(user_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            var load = layer.load();
            $.post("{{ route('admin.user.status') }}", {user_id: user_id},
                function (data) {
                    layer.close(load);
                    if (data.code === 0) {
                        layer.msg('操作成功', {
                            offset: '15px'
                            , icon: 1
                            , time: 1000
                        }, function () {
                            window.location.reload();
                        });

                    } else {
                        layer.msg(data.msg, {
                            offset: '15px'
                            , icon: 2
                            , time: 2000
                        });
                    }
                });
        }

        function resetPwd(id) {
            layer.confirm('你确定要重置这个用户的密码吗？', {
                title: '重置密码',
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                var load = layer.load();
                $.post("{{ route('admin.user.reset') }}", {id: id},
                    function (data) {
                        layer.close(load);
                        if (data.code === 0) {
                            layer.msg(data.msg, {
                                offset: '15px'
                                , icon: 1
                                , time: 2000
                            }, function () {
                                location.href = '{{ route('admin.user.index') }}';
                            });
                        } else {
                            layer.msg(data.msg, {
                                offset: '15px'
                                , icon: 2
                                , time: 2000
                            });
                        }
                    });
            });
        }
    </script>
@endsection