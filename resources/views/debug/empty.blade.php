<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="referrer" content="always">

        <title>Empty Page</title>

        <style type="text/css" media="screen">
            #editor {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
            }
        </style>

    </head>
    <body>

        <div id="editor">function foo(items) {
            var x = "All this is syntax highlighted";
            return x;
            }</div>

        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.min.js"
            integrity="sha512-GoORoNnxst42zE3rYPj4bNBm0Q6ZRXKNH2D9nEmNvVF/z24ywVnijAWVi/09iBiVDQVf3UlZHpzhAJIdd9BXqw=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
            type="text/javascript"
            charset="utf-8"
        ></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/theme-monokai.min.js"
            integrity="sha512-S4i/WUGRs22+8rjUVu4kBjfNuBNp8GVsgcK2lbaFdws4q6TF3Nd00LxqnHhuxS9iVDfNcUh0h6OxFUMP5DBD+g=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
            type="text/javascript"
            charset="utf-8"
        ></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-sh.min.js"
            integrity="sha512-e1lzPcRUUhfM9oRrV0pgJs+rAJMA1OGXUYSxlX2UZwaO/GvqlL5ZUKwE2lNf5I/Wq6S6ua0U4GWaRrC2J9AXIw=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
            type="text/javascript"
            charset="utf-8"
        ></script>

        <script>
            var editor = ace.edit("editor");
            editor.setTheme("ace/theme/monokai");
            editor.session.setMode("ace/mode/sh");
        </script>

    </body>
</html>
