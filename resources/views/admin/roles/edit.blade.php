@extends('layouts.admin')

@section('content')
    <a href="{{ route('admin.roles.index') }}" class="layui-btn layui-btn-primary layui-btn-sm">返回</a>
    <hr>
    <form class="layui-form" action="" style="width: 900px;">
        <div class="layui-form-item" style="width: 400px;">
            <label class="layui-form-label">名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" required lay-verify="required" placeholder="请输入角色名称"
                       autocomplete="off" class="layui-input"
                       @if($role)
                       value="{{ $role->name }}"
                        @endif
                >
                @if($role)
                    <input type="hidden" name="id" value="{{ $role->id }}">
                @endif
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">权限组</label>
            <div class="layui-input-block">
                @foreach($permissions as $permission)
                    <input type="checkbox" name="permission[]" value="{{ $permission->id }}"
                           title="{{ $permission->name }}"
                           @if($permission_ids && in_array($permission->id,$permission_ids))
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
                location.href = '{{ route('admin.roles.index') }}';
            });
            @endif

            //监听提交
            form.on('submit(formDemo)', function (data) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                var load = layer.load();
                $.post("{{ route('admin.roles.update') }}", data.field,
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
            });

        });
    </script>
@endsection