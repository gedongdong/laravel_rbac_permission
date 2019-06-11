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
    <a href="{{ route('admin.roles.create') }}" class="layui-btn">添加角色</a>
    <table class="layui-table">
        <colgroup>
            <col width="50">
            <col width="130">
            <col>
            <col width="110">
            <col width="115">
        </colgroup>
        <thead>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>权限组</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    @foreach($role->permissions as $permission)
                        <span class="layui-badge layui-bg-green">{{ $permission->name }}</span>
                    @endforeach
                </td>
                <td>{{ $role->created_at }}</td>
                <td style="text-align: center;">
                    <a href="{{ route('admin.roles.edit') }}?role_id={{ $role->id }}"
                       class="layui-btn layui-btn-xs">编辑</a>
                    <button class="layui-btn layui-btn-danger layui-btn-xs" type="button"
                            onclick="del({{ $role->id }})">删除
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div id="page" class="layui-box layui-laypage layui-laypage-default">{{ $roles->links() }}</div>
@endsection

@section('script')
    <script>
        layui.use(['layer'], function () {
            var layer = layui.layer;
        });

        function del(id) {
            layer.confirm('你确定要删除这个角色吗？', {
                title: '删除确认',
                btn: ['确定', '取消'] //按钮
            }, function () {
                layer.confirm('该角色下的用户将失去对应的角色权限，确定要删除吗？', {
                    title: '删除确认',
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    var load = layer.load();
                    $.post("{{ route('admin.roles.delete') }}", {id: id},
                        function (data) {
                            layer.close(load);
                            if (data.code === 0) {
                                layer.msg('操作成功', {
                                    offset: '15px'
                                    , icon: 1
                                    , time: 1000
                                }, function () {
                                    location.href = '{{ route('admin.roles.index') }}';
                                });
                            } else {
                                layer.msg(data.msg, {
                                    offset: '15px'
                                    , icon: 2
                                    , time: 2000
                                });
                            }
                        });
                })
            });
        }
    </script>
@endsection