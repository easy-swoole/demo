<!doctype html>
<html lang="zh-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>微聊 - EASYSWOOLE DEMO</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/amazeui/2.7.2/css/amazeui.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/layer/2.3/skin/layer.css">
    <link rel="stylesheet" href="/css/main.css">
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
                        <div class="me_username">{{currentUser.username}}</div>
                        <div class="me_income">{{currentUser.intro}}</div>
                    </div>
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
                <div class="windows_top_left">欢迎乘坐特快列车</div>
                <div class="windows_top_right">DEMO VERSION</div>
            </div>
            <div class="windows_body" id="chat-window" v-scroll-bottom>
                <ul class="am-comments-list am-comments-list-flip">
                    <template v-for="chat in roomChat">
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
                                        {{chat.content}}
                                    </div>
                                </div>
                            </div>
                        </article>
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
            currentUser: {username: '-----', intro: '-----------', userFd: 0, avatar: 0},
            roomUser: {},
            roomChat: []
        },
        created: function () {
            var othis = this;
            this.websocketInstance = new WebSocket(this.websocketServer);
            this.websocketInstance.onopen = function (ev) {
                // 请求获取自己的用户信息和在线列表
                othis.release('index', 'info')
                othis.release('index', 'online')
                othis.websocketInstance.onmessage = function (ev) {
                    try {
                        var data = JSON.parse(ev.data);
                        switch (data.action) {
                            case 103 : {
                                // 收到用户消息
                                var message = {
                                    fd: data.fromUserFd,
                                    content: data.content,
                                    avatar: othis.roomUser[data.fromUserFd].avatar,
                                    username: othis.roomUser[data.fromUserFd].username
                                };
                                othis.roomChat.push(message)
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
                                if (data.info.userFd !== othis.currentUser.userFd) {
                                    othis.$set(othis.roomUser, data.info.userFd, data.info)
                                }
                                break;
                            }
                            case 204: {
                                // 用户已离线
                                othis.$delete(othis.roomUser, data.userFd)
                                break;
                            }
                        }
                    } catch (e) {

                    }
                }
            }
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
                    textInput.val('');
                    ev.preventDefault();
                    return false;
                }
            })
        },
        methods: {
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
                this.release('broadcast', 'roomBroadcast', {content: content})
            },
            /**
             * 点击发送按钮
             * @return void
             */
            clickBtnSend: function () {
                var content = $('#text-input').val();
                if (content.trim() !== '') {
                    this.broadcastTextMessage(content)
                } else {
                    layer.tips('请输入消息内容', '.windows_input', {
                        tips: [1, '#3595CC'],
                        time: 2000
                    });
                }
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