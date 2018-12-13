<!doctype html>
<html lang="zh-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>微聊 - EASYSWOOLE DEMO</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/amazeui/2.7.2/css/amazeui.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/layer/2.3/skin/layer.css">
    <link rel="stylesheet" href="/css/main.css?v=120203">
    <script src="https://cdn.staticfile.org/vue/2.5.17-beta.0/vue.js"></script>
    <script src="https://cdn.staticfile.org/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/layer/2.3/layer.js"></script>
</head>
<body>
<div id="chat">
    <template>
        <div class="online_window">
            <div class="me_info">
                <div class="me_item">
                    <div class="me_avatar">
                        <img :src="'/avatar/'+currentUser.avatar+'.jpg'" alt="">
                    </div>
                    <div class="me_status">
                        <div class="me_username">
                            <i class="am-icon am-icon-pencil" @click="changeName"></i> {{currentUser.username}}
                        </div>
                        <div class="me_income">{{currentUser.intro}}</div>
                    </div>
                    <div class="times-icon"><i class="am-icon am-icon-times"></i></div>
                </div>
            </div>
            <div class="online_list">
                <div class="online_list_header">车上乘客</div>
                <div class="online_item" v-for="user in roomUser">
                    <template v-if="user">
                        <div class="online_avatar">
                            <img :src="'/avatar/'+user.avatar+'.jpg'" alt="">
                        </div>
                        <div class="online_status">
                            <div class="online_username">{{user.username}}</div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="online_count">
                <h6>车上乘客 <span>{{currentCount}}</span> 位</h6>
            </div>
        </div>
        <div class="talk_window">
            <div class="windows_top">
                <div class="windows_top_left"><i class="am-icon am-icon-list online-list"></i> 欢迎乘坐特快列车</div>
                <div class="windows_top_right">
                    <a href="https://github.com/easy-swoole/demo/tree/3.x-chat" target="_blank" style="color: #999">查看源码</a>
                </div>
            </div>
            <div class="windows_body" id="chat-window" v-scroll-bottom>
                <ul class="am-comments-list am-comments-list-flip">
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
                                <a href="#link-to-user-home">
                                    <img :src="'/avatar/'+chat.avatar+'.jpg'" alt="" class="am-comment-avatar" width="48" height="48"/>
                                </a>
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
                                                <img :src="chat.content">
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
                    <label for="text-input" style="display: none"></label>
                    <textarea name="" id="text-input" cols="30" rows="10" title=""></textarea>
                </div>
                <div class="toolbar">
                    <div class="left"><a href="http://www.easyswoole.com/" target="_blank">POWER BY EASYSWOOLE V3</a>
                    </div>
                    <div class="right">
                        <button class="send" @click="clickBtnSend">发送消息 ( Enter )</button>
                    </div>
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
            isReconnection: false,
            currentUser: {username: '-----', intro: '-----------', userFd: 0, avatar: 0},
            roomUser: {},
            roomChat: [],
            up_recv_time: 0
        },
        created: function () {
            var othis = this;
            setInterval(function(){
                if (!othis.websocketInstance || othis.websocketInstance.readyState === 3) {
                    othis.connect(othis.isReconnection);
                }
            }, 1000);
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
            $('.online-list').on('click', function () {
                $('.online_window').show();
                $('.windows_input').hide();
            });
            $('.times-icon').on('click', function () {
                $('.online_window').hide();
                $('.windows_input').show();
            })
        },
        methods: {
            /**
             *
             */
            connect: function(is_reconnection = false){
                var othis = this;
                var username = localStorage.getItem('username');
                var websocketServer = this.websocketServer;
                if (username) {
                    websocketServer += '?username=' + encodeURIComponent(username)
                }
                if (is_reconnection) {
                    websocketServer += (username ? '&' : '?') + 'is_reconnection=1'
                }
                this.websocketInstance = new WebSocket(websocketServer);
                this.websocketInstance.onopen = function (ev) {
                    // 前端循环心跳 (1min)
                    var ping_t = setInterval(function () {
                        othis.websocketInstance.send('PING');
                    }, 1000 * 30);
                    // 请求获取自己的用户信息和在线列表
                    othis.release('index', 'info');
                    othis.release('index', 'online');
                    othis.websocketInstance.onmessage = function (ev) {
                        try {
                            var data = JSON.parse(ev.data);
                            if(othis.up_recv_time + 10 * 1000 > (new Date(data.sendTime)).getTime()){
                                othis.up_recv_time = (new Date(data.sendTime)).getTime();
                                data.sendTime = null;
                            }else {
                                othis.up_recv_time = (new Date(data.sendTime)).getTime();
                            }
                            switch (data.action) {
                                case 101: {
                                    // 收到管理员消息
                                    othis.roomChat.push({
                                        type: data.type ? data.type : 'text',
                                        fd: 0,
                                        content: data.content,
                                        avatar: 99,
                                        username: '列车乘务员'
                                    });
                                    break;
                                }
                                case 103 : {
                                    // 收到用户消息
                                    var message = {
                                        type: data.type,
                                        fd: data.fromUserFd,
                                        content: data.content,
                                        avatar: othis.roomUser[data.fromUserFd].avatar,
                                        username: othis.roomUser[data.fromUserFd].username,
                                        sendTime: data.sendTime
                                    };
                                    othis.roomChat.push(message);
                                    break;
                                }
                                case 104 : {
                                    // 收到最后消息
                                    var message = {
                                        type: data.type,
                                        fd: data.fromUserFd,
                                        content: data.content,
                                        avatar: data.avatar,
                                        username: data.username,
                                        sendTime: data.sendTime
                                    };
                                    othis.roomChat.push(message);
                                    break;
                                }
                                case 201: {
                                    // 刷新自己的用户信息
                                    othis.currentUser.intro = data.intro;
                                    othis.currentUser.avatar = data.avatar;
                                    othis.currentUser.userFd = data.userFd;
                                    othis.currentUser.username = data.username;
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
                                        content: '乘客 ' + data.info.username + ' 已登车',
                                    });
                                    break;
                                }
                                case 204: {
                                    // 用户已离线
                                    var username = othis.roomUser[data.userFd].username;
                                    othis.$delete(othis.roomUser, data.userFd);
                                    othis.roomChat.push({
                                        type: 'tips',
                                        content: '乘客 ' + username + ' 下车了',
                                    });
                                    break;
                                }
                            }
                        } catch (e) {

                        }
                    };
                    othis.websocketInstance.onclose = function () {
                        clearInterval(ping_t);
                        othis.isReconnection = true;
                        othis.websocketInstance = null;
                    };
                    othis.websocketInstance.onerror = function () {
                        clearInterval(ping_t);
                        othis.isReconnection = true;
                        othis.websocketInstance = null;
                    }
                }
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
             * 发送图片消息
             * @param base64_content
             */
            broadcastImageMessage: function (base64_content) {
                this.release('broadcast', 'roomBroadcast', {content: base64_content, type: 'image'})
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
                        layer.tips('连接已断开', '.windows_input', {
                            tips: [1, '#ff4f4f'],
                            time: 2000
                        });
                    }
                } else {
                    layer.tips('请输入消息内容', '.windows_input', {
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
            currentCount() {
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