<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <meta name="renderer" content="webkit|ie-comp|ie-stand">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
  <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>跳转提示</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            height: 100%;
            color: #232323;
        }
        #error {
            display: block;
            width: 550px;
            background-color: #fff;
            padding: 30px;
            border-radius: 3px 3px 0 0;
            position: fixed;
            left: 0;
            top: -50px;
            right: 0;
            bottom: 0;
            margin: auto;
            height: 380px;
            border-bottom: 4px #f2a59a solid;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, .15);
            box-sizing: border-box;
            text-align: center;
        }
        .icon {
            margin: 10px auto 15px;
        }
        .msg {
            font-size: 14px;
            color: #888;
        }
        .waitclass {
            margin: 25px auto 0;
            padding: 10px 0;
            width: 220px;
            font-size: 14px;
            text-align: center;
            background-color: #dedede;
            border-radius: 3px;
        }
        .waitclass a {
            text-decoration: none;
            color: #ffffff;
        }
    </style>
</head>
<body>


</head>
<body>
<div id="error"{if $code=='1'} style="border-color: #2d6dcc !important;"{/if}>
<div class="icon">
    {if $code=='1'}
 <svg t="1616749699510" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2966" xmlns:xlink="http://www.w3.org/1999/xlink" width="110" height="110"><defs><style type="text/css"></style></defs><path d="M121.792 529.984c-11.008-9.728-12.416-26.944-3.072-38.528l24.512-30.464c9.28-11.52 27.264-15.232 40.384-8.128l210.624 114.176c12.992 7.04 32.768 5.248 44.16-4.096l476.544-389.504c11.392-9.28 28.416-7.936 38.272 3.2l-0.832-0.896c9.728 11.072 10.112 29.376 0.896 40.768l-478.848 590.72c-9.28 11.392-25.728 12.736-36.672 3.008L121.792 529.984z" p-id="2967" fill="#2d6dcc"></path></svg>

    {else}
   <svg t="1616749252802" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3554" xmlns:xlink="http://www.w3.org/1999/xlink" width="110" height="110"><defs><style type="text/css"></style></defs><path d="M983.8 312.7C958 251.7 921 197 874 150c-47-47-101.7-84-162.7-109.7C648.2 13.5 581.1 0 512 0S375.8 13.5 312.7 40.3C251.7 66 197 103 150 150c-47 47-84 101.7-109.7 162.7C13.5 375.8 0 442.9 0 512s13.5 136.2 40.3 199.3C66 772.3 103 827 150 874c47 47 101.8 83.9 162.7 109.7 63.1 26.7 130.2 40.3 199.3 40.3s136.2-13.5 199.3-40.3C772.3 958 827 921 874 874c47-47 83.9-101.8 109.7-162.7 26.7-63.1 40.3-130.2 40.3-199.3s-13.5-136.2-40.2-199.3zM512 952C269.4 952 72 754.6 72 512S269.4 72 512 72s440 197.4 440 440-197.4 440-440 440z" p-id="3555" fill="#f2a59a"></path><path d="M664.7 359.3c-14.1-14.1-36.9-14.1-50.9 0L512 461.1 410.2 359.3c-14.1-14.1-36.9-14.1-50.9 0-14.1 14.1-14.1 36.9 0 50.9L461.1 512 359.3 613.8c-14.1 14.1-14.1 36.9 0 50.9 7 7 16.2 10.5 25.5 10.5s18.4-3.5 25.5-10.5L512 562.9l101.8 101.8c7 7 16.2 10.5 25.5 10.5s18.4-3.5 25.5-10.5c14.1-14.1 14.1-36.9 0-50.9L562.9 512l101.8-101.8c14.1-14.1 14.1-36.9 0-50.9z" p-id="3556" fill="#f2a59a"></path></svg>
    {/if}
</div>

<div class="msg"><?php echo(strip_tags($msg));?></div>
<div class="waitclass"><a id="href" href="{$url}">等待 <span id="wait">{$wait}</span> 秒后自动跳转</a></div>
</div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
</body>
</html>
