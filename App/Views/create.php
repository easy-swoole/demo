<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nirvana - Happy Chat Around World</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/amazeui/2.7.2/css/amazeui.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/layer/2.3/skin/layer.css">
    <link rel="stylesheet" href="/css/main.css?v=120203">
    <script src="https://cdn.staticfile.org/vue/2.5.17-beta.0/vue.js"></script>
    <script src="https://cdn.staticfile.org/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/layer/2.3/layer.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/css/jquery.emojipicker.css">
    <script type="text/javascript" src="/script/jquery.emojipicker.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/css/jquery.emojipicker.tw.css">
    <script type="text/javascript" src="/script/jquery.emojis.js"></script>

    
</head>
<script type='text/javascript'>
    $(document).ready(function(e) {
        $('.emojiable-input').emojiPicker({ button: false });
        $('#emoji-button').click(function (e) {
            e.preventDefault();
            $('#text-input').emojiPicker('toggle');
        });
    });
</script>
<body>
<div id="chat">
    <div>
        <form action="/chat/create" method="post">
            <p>Name: <input type="text" name="name" /></p>
            <p>Subject: <input type="text" name="subject" /></p>
            <input type="submit" value="Submit" /> 
        </form>
    </div>
</div>
</body>
</html>