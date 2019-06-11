@extends('layouts.admin')

@section('content')
    <a href="{{ route('admin.user.index') }}" class="layui-btn layui-btn-primary layui-btn-sm">返回</a>
    <hr>
    <form class="layui-form" action="" style="width: 800px;">
        <div class="layui-form-item" style="width: 500px;">
            <label class="layui-form-label">姓名</label>
            <div class="layui-input-block">
                <input type="text" name="name" required lay-verify="required|name" placeholder="请输入姓名"
                       autocomplete="off" class="layui-input"
                       @if($user)
                       value="{{ $user->name }}"
                        @endif
                >
                @if($user)
                    <input type="hidden" name="id" value="{{ $user->id }}">
                @endif
            </div>
        </div>
        <div class="layui-form-item" style="width: 500px;">
            <label class="layui-form-label">邮箱</label>
            <div class="layui-input-block">
                <input type="text" name="email" required lay-verify="required|email" placeholder="请输入邮箱"
                       autocomplete="off" class="layui-input"
                       @if($user)
                       value="{{ $user->email }}"
                        @endif
                >
            </div>
        </div>
        <div class="layui-form-item" style="width: 500px;">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-inline">
                <input type="password" name="password" required placeholder="请输入密码"
                       autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">6-20位，留空表示不修改</div>
        </div>
        <div class="layui-form-item" style="width: 500px;">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-inline">
                <input type="password" name="password_repeat" required lay-verify="same" placeholder="请输入相同的密码"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        {{--<div class="layui-form-item">--}}
            {{--<label class="layui-form-label">状态</label>--}}
            {{--<div class="layui-input-block">--}}
                {{--<input type="radio" name="status" value="1" title="启用"--}}
                       {{--@if($user && $user->status == \App\Http\Models\Users::STATUS_ENABLE)--}}
                       {{--checked--}}
                        {{--@endif--}}
                {{-->--}}
                {{--<input type="radio" name="status" value="2" title="禁用"--}}
                       {{--@if($user && $user->status == \App\Http\Models\Users::STATUS_DISABLE)--}}
                       {{--checked--}}
                        {{--@endif--}}
                {{-->--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="layui-form-item">
            <label class="layui-form-label">是否管理员</label>
            <div class="layui-input-block">
                <input type="radio" name="administrator" value="1" lay-filter="admin" title="是"
                       @if($user && $user->administrator == \App\Http\Models\Users::ADMIN_YES)
                       checked
                        @endif
                >
                <input type="radio" name="administrator" value="2" lay-filter="admin" title="否"
                       @if($user && $user->administrator == \App\Http\Models\Users::ADMIN_NO)
                       checked
                        @endif
                >
            </div>
        </div>
        <div class="layui-form-item" id="roles"
             @if($user && $user->administrator == \App\Http\Models\Users::ADMIN_YES)
             style="display: none;"
                @endif
        >
            <label class="layui-form-label">所属角色</label>
            <div class="layui-input-block">
                @foreach($roles as $role)
                    <input type="checkbox" name="roles[{{ $role->id }}]" value="{{ $role->id }}"
                           title="{{ $role->name }}"
                           @if($role_ids && in_array($role->id,$role_ids))
                           checked
                            @endif
                    >
                @endforeach
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="formDemo" type="button">立即提交</button>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        layui.use(['form', 'layer'], function () {
            var form = layui.form;
            var layer = layui.layer;

            @if($error)
            layer.msg('{{ $error }}', {
                offset: '15px'
                , icon: 2
                , time: 2000
            }, function () {
                location.href = '{{ route('admin.user.index') }}';
            });
            @endif

            form.verify({
                name: function (value, item) { //value：表单的值、item：表单的DOM对象
                    if (!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)) {
                        return '姓名不能有特殊字符';
                    }
                    if (/(^\_)|(\__)|(\_+$)/.test(value)) {
                        return '姓名首尾不能出现下划线\'_\'';
                    }
                }
                , same: function (value, item) { //value：表单的值、item：表单的DOM对象
                    if (value !== $("input[name='password']").val()) {
                        return '两次填写的密码不一致';
                    }
                }
            });

            form.on('radio(admin)', function (data) {
                if (data.value === '1') {
                    $('#roles').hide();
                } else {
                    $('#roles').show();
                }
            });

            //监听提交
            form.on('submit(formDemo)', function (data) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                var load = layer.load();
                $.post("{{ route('admin.user.update') }}", data.field,
                    function (data) {
                        layer.close(load);
                        if (data.code === 0) {
                            layer.msg('操作成功', {
                                offset: '15px'
                                , icon: 1
                                , time: 1000
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

        });
    </script>
@endsection