<!doctype html>
<html lang="zh-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.staticfile.org/amazeui/2.7.2/css/amazeui.min.css">
    <link rel="stylesheet" href="/css/login.css?v=190527">
    <script src="https://cdn.staticfile.org/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/layer/2.3/layer.js"></script>
    <title>注册微聊 - EASYSWOOLE CHAT DEMO</title>
</head>
<body>

<!-- 登录框体 -->
<div class="block-box">
    <div class="main-title">注册微聊账号</div>
    <div class="sub-title">EASYSWOOLE CHAT DEMO</div>
    <form class="am-form">
        <div class="am-form-group am-input-group">
            <span class="am-input-group-label"><i class="am-icon-at am-icon-fw"></i></span>
            <input id="email" type="email" name="email" class="am-form-field" placeholder="输入电子邮件">
        </div>
        <div class="am-form-group am-input-group">
            <span class="am-input-group-label"><i class="am-icon-lock am-icon-fw"></i></span>
            <input id="password" type="password" name="password" class="am-form-field" placeholder="输入登录密码">
        </div>
        <div class="am-form-group am-input-group">
            <span class="am-input-group-label"><i class="am-icon-lock am-icon-fw"></i></span>
            <input id="repassword" type="password" name="repassword" class="am-form-field" placeholder="再次输入密码">
        </div>
        <?php if ($EnableMailCheck): ?>
            <div class="am-form-group am-input-group">
                <span class="am-input-group-label"><i class="am-icon-code am-icon-fw"></i></span>
                <input type="text" id="validate" name="validate" class="am-form-field" placeholder="输入邮件验证码">
                <span class="am-input-group-label" id="sendValidate">发送邮件</span>
            </div>
        <?php endif; ?>
        <div class="am-form-group">
            <a id="submit" type="button" class="am-btn am-btn-primary am-btn-block">注册账号</a>
        </div>
        <div class="am-form-group" style="text-align: center;margin-top: 30px;">
            <a href="/login">已有微聊账号 立刻开始畅聊</a>
        </div>
    </form>
</div>

<script>
    $(function () {
        $('#submit').on('click', function (ev) {
            console.warn('xxx')
        })
    })

    $('#submit').on('click', function (ev) {

        var email = $('#email').val();
        var password = $('#password').val();
        var repassword = $('#repassword').val();

        if (email == '') {
            alert('登录账号不能为空 /(ㄒoㄒ)/~~')
        }

        if (password == '') {
            alert('登录密码不能为空 /(ㄒoㄒ)/~~')
        }

        if (repassword == '') {
            alert('再输一次密码哟 /(ㄒoㄒ)/~~')
        }

        // 两次密码输入是否一样
        if (password !== repassword) {
            alert('骚年，两次输入的密码不一样哟 /(ㄒoㄒ)/~~')
        }

        var data = {
            email   : email,
            password: password
        }

        <?php if ($EnableMailCheck): ?>

        // 需要邮件验证码
        var validate = $('#validate').val();
        if (validate == '') {
            alert('请把验证码填好 /(ㄒoㄒ)/~~')
        }
        data.validate = validate;


        <?php endif; ?>


        return false;
    })

    <?php if ($EnableMailCheck): ?>

    // 注册验证码发送按钮
    var validate = $('#sendValidate').on('click', function () {

        var email = $('#email').val();
        if (email == '') {
            alert('请先填写邮箱账号 /(ㄒoㄒ)/~~')
        }

        // 发验证码
        $.ajax({
            url   : '/register/sendValidateCode',
            data  : {email: email},
            method: 'POST',
            success(res) {
                alert(res.data.msg);
            },
            error(err) {
                alert('请求失败')
            }
        })

    });

    <?php endif; ?>
</script>

</body>
</html>