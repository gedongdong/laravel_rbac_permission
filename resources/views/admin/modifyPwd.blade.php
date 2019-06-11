@extends('layouts.admin')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">修改密码</div>
        <div class="layui-card-body">
            <form class="layui-form" action="">
                <div class="layui-form" lay-filter="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">当前密码</label>
                        <div class="layui-input-inline">
                            <input type="password" name="oldPassword" lay-verify="required" lay-verType="tips"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">新密码</label>
                        <div class="layui-input-inline">
                            <input type="password" name="password" lay-verify="required|pass" lay-verType="tips"
                                   autocomplete="off" id="LAY_password" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">6到20个字符</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">确认新密码</label>
                        <div class="layui-input-inline">
                            <input type="password" name="password_repeat" lay-verify="required|same" lay-verType="tips"
                                   autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="formDemo" type="button">确认修改</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form', 'layer'], function () {
            var form = layui.form;
            var layer = layui.layer;

            form.verify({
                same: function (value, item) { //value：表单的值、item：表单的DOM对象
                    if (value !== $("input[name='password']").val()) {
                        return '两次填写的密码不一致';
                    }
                }

                //我们既支持上述函数式的方式，也支持下述数组的形式
                //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
                , pass: [
                    /^[\S]{6,20}$/
                    , '密码必须6到20位，且不能出现空格'
                ]
            });

            //监听提交
            form.on('submit(formDemo)', function (data) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                var load = layer.load();
                $.post("{{ route('admin.new_pwd.white') }}", data.field,
                    function (data) {
                        layer.close(load);
                        console.log(data);
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