/**
 * 高亮渲染代码块
 * @param element 渲染目标位置
 * @param template 从该元素获取即将被渲染的文本
 * @constructor
 */
var TypedCode = function (element, template) {
    var templateCode = document.getElementById(template).innerText;
    console.warn(templateCode);
    code = hljs.highlightAuto(document.getElementById(template).innerText, ['php'])
    console.warn(code)
    var options = {
        strings: ['<pre class="hljs php"><code class="php">' + code.value + '</code></pre>'],
        typeSpeed: 20,
        loop: true,
        loopCount: Infinity,
        showCursor: true,
        autoInsertCss: true,
        cursorChar: '|',
        contentType: 'html',
        backDelay: 3000
    }
    return new Typed(element, options);
}

/**
 * 初始化渲染页面汉堡包
 */
document.addEventListener('DOMContentLoaded', function () {

    // Get all "navbar-burger" elements
    var $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Check if there are any navbar burgers
    if ($navbarBurgers.length > 0) {

        // Add a click event on each of them
        $navbarBurgers.forEach(function ($el) {
            $el.addEventListener('click', function () {

                // Get the target from the "data-target" attribute
                var target  = $el.dataset.target;
                var $target = document.getElementById(target);

                // Toggle the class on both the "navbar-burger" and the "navbar-menu"
                $el.classList.toggle('is-active');
                $target.classList.toggle('is-active');

            });
        });
    }

});