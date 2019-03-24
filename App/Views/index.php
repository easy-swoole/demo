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
    <template>
        <div class="online_window">
            <div class="online_intro">
                Seeking same kinds and have a nice trip
            </div>
            <div class="online_count">
                {{currentCount}} rangers here right now
            </div>
        </div>
        <div class="talk_window">
            <div class="windows_body" id="chat-window">
                <ul class="am-comments-list am-comments-list-flip" v-scroll-bottom>
                    <template v-for="chat in roomChat">
                        <template v-if="chat.type === 'tips'">
                            <div class="chat-tips">
                                <span class="am-badge am-badge-primary am-radius">{{chat.content}}</span></div>
                        </template>
                        <template v-else>
                            <div v-if="chat.sendTime" class="chat-tips">
                                <span class="am-radius" style="color: #666666">{{chat.sendTime}}</span>
                            </div>
                            <article class="am-comment" :class="{ 'am-comment-flip' : chat.fd == currentUser.userFd }">
                                <div class="am-comment-main">
                                    <header class="am-comment-hd">
                                        <div class="am-comment-meta">
                                            <a href="#link-to-user" class="am-comment-author">{{chat.username}}</a>
                                        </div>
                                    </header>
                                    <div class="am-comment-bd">
                                        <div class="bd-content">
                                            <template v-if="chat.type === 'text'">
                                                {{chat.content}}
                                            </template>
                                            <template v-else-if="chat.type === 'image'">
                                                <img :src="chat.content" width="100%">
                                            </template>
                                            <template v-else>
                                                {{chat.content}}
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </template>
                </ul>
            </div>
            
            <div class="windows_input">
                <div class="input-box">
                    <textarea name="" id="text-input" cols="30" rows="10" class="emojiable-input"></textarea>
                </div>
                <div class="input-btns">
                    <button id="emoji-button"></button>
                    <button class="send" @click="clickBtnSend">Send</button>
                </div>
            </div>
        </div>
    </template>
</div>
<script>
    var Vm = new Vue({
        el: '#chat',
        data: {
            websocketServer: "<?= $server ?>",
            websocketInstance: undefined,
            Reconnect: false,
            ReconnectTimer: null,
            HeartBeatTimer: null,
            ReconnectBox: null,
            currentUser: {username: 'Niror', userFd: 0, msgCnt: 0},
            roomUser: {},
            roomChat: [],
            up_recv_time: 0
        },
        created: function () {
            this.connect();
        },
        mounted: function () {
            var othis = this;
            var textInput = $('#text-input');
            textInput.on('keydown', function (ev) {
                if (ev.keyCode == 13 && ev.shiftKey) {
                    textInput.val(textInput.val() + "\n");
                    return false;
                } else if (ev.keyCode == 13) {
                    othis.clickBtnSend();
                    ev.preventDefault();
                    return false;
                }
            });
        },
        methods: {
            connect: function () {
                var othis = this;
                var username = localStorage.getItem('username');
                var websocketServer = this.websocketServer;
                if (username) {
                    websocketServer += '?username=' + encodeURIComponent(username)
                }
                this.websocketInstance = new WebSocket(websocketServer);
                this.websocketInstance.onopen = function (ev) {
                    // 断线重连处理
                    if (othis.ReconnectBox) {
                        layer.close(othis.ReconnectBox);
                        othis.ReconnectBox = null;
                        clearInterval(othis.ReconnectTimer);
                    }
                    // 前端循环心跳 (1min)
                    othis.HeartBeatTimer = setInterval(function () {
                        othis.websocketInstance.send('PING');
                    }, 1000 * 10);
                    // 请求获取自己的用户信息和在线列表
                    othis.release('index', 'info');
                    othis.release('index', 'online');
                    othis.OnlineNirorTimer = setInterval(function(){
                        othis.release('index', 'online')
                    }, 1000);
                    
                    othis.websocketInstance.onmessage = function (ev) {
                        try {
                            var data = JSON.parse(ev.data);
                            if (data.sendTime) {
                                if (othis.up_recv_time + 10 * 1000 > (new Date(data.sendTime)).getTime()) {
                                    othis.up_recv_time = (new Date(data.sendTime)).getTime();
                                    data.sendTime = null;
                                } else {
                                    othis.up_recv_time = (new Date(data.sendTime)).getTime();
                                }
                            }
                            switch (data.action) {
                                case 101: {
                                    // 收到管理员消息
                                    othis.roomChat.push({
                                        type: data.type ? data.type : 'text',
                                        fd: 0,
                                        content: data.content,
                                        username: 'ADMIN'
                                    });
                                    break;
                                }
                                case 103 : {
                                    // 收到用户消息
                                    var broadcastMsg = {
                                        type: data.type,
                                        fd: data.fromUserFd,
                                        content: data.content,
                                        username: othis.roomUser[data.fromUserFd].username,
                                        sendTime: data.sendTime
                                    };
                                    othis.roomChat.push(broadcastMsg);
                                    break;
                                }
                                case 104 : {
                                    // 收到最后消息
                                    var lastMsg = {
                                        type: data.type,
                                        fd: data.fromUserFd,
                                        content: data.content,
                                        username: data.username,
                                        sendTime: data.sendTime
                                    };
                                    othis.roomChat.push(lastMsg);
                                    break;
                                }
                                case 201: {
                                    // 刷新自己的用户信息
                                    othis.currentUser.userFd = data.userFd;
                                    othis.currentUser.username = data.username;
                                    othis.currentUser.msgCnt = data.msgCnt;
                                    break;
                                }
                                case 202: {
                                    // 刷新当前的在线列表
                                    othis.roomUser = data.list;
                                    break;
                                }
                                case 203: {
                                    // 新用户上线
                                    othis.$set(othis.roomUser, data.info.userFd, data.info);
                                    othis.roomChat.push({
                                        type: 'tips',
                                        content: 'Niror ' + data.info.username + ' is comming',
                                    });
                                    break;
                                }
                            }
                        } catch (e) {

                        }
                    };
                    othis.websocketInstance.onclose = function (ev) {
                        othis.doReconnect();
                    };
                    othis.websocketInstance.onerror = function (ev) {
                        othis.doReconnect();
                    }
                }
            },
            doReconnect: function () {
                var othis = this;
                clearInterval(othis.HeartBeatTimer);
                othis.ReconnectBox = layer.msg('Disconnected，reconnecting...', {
                    scrollbar: false,
                    shade: 0.3,
                    shadeClose: false,
                    time: 0,
                    offset: 't'
                });
                othis.ReconnectTimer = setInterval(function () {
                    othis.connect();
                }, 1000)
            },
            /**
             * 向服务器发送消息
             * @param controller 请求控制器
             * @param action 请求操作方法
             * @param params 携带参数
             */
            release: function (controller, action, params) {
                controller = controller || 'index';
                action = action || 'action';
                params = params || {};
                var message = {controller: controller, action: action, params: params}
                this.websocketInstance.send(JSON.stringify(message))
            },
            /**
             * 发送文本消息
             * @param content
             */
            broadcastTextMessage: function (content) {
                this.release('broadcast', 'roomBroadcast', {content: content, type: 'text'})
            },
            /**
             * 点击发送按钮
             * @return void
             */
            clickBtnSend: function () {
                var textInput = $('#text-input');
                var content = textInput.val();
                if (content.trim() !== '') {
                    if (this.websocketInstance && this.websocketInstance.readyState === 1) {
                        this.broadcastTextMessage(content);
                        textInput.val('');
                    } else {
                        layer.tips('Disconnected', '.windows_input', {
                            tips: [1, '#ff4f4f'],
                            time: 2000
                        });
                    }
                } else {
                    layer.tips('no more empty msg plz~', '.windows_input', {
                        tips: [1, '#3595CC'],
                        time: 2000
                    });
                }
            },
            changeName: function () {
                layer.prompt({title: '拒绝吃瓜，秀出你的昵称', formType: 0}, function (username, index) {
                    if (username) {
                        localStorage.setItem('username', username);
                        window.location.reload();
                    }
                    layer.close(index);
                });

            }
        },
        computed: {
            currentCount: function () {
                return Object.getOwnPropertyNames(this.roomUser).length - 1;
            }
        },
        directives: {
            scrollBottom: {
                componentUpdated: function (el) {
                    el.scrollTop = el.scrollHeight
                }
            }
        }
    });
</script>
</body>
</html>