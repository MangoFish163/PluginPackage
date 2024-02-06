<!-- <link href="https://cdn.bootcdn.net/ajax/libs/layui/2.8.17/css/layui.css" rel="stylesheet"> -->
<!-- <script src="https://cdn.bootcdn.net/ajax/libs/layui/2.8.17/layui.min.js"></script> -->
<style>
    .countrylist--item {
        /* overflow: hidden; */
        text-align: center;
        line-height: 40px;
        background-color: #f7f9fa;
        border-radius: 2px;
        color: #6c7073;
        cursor: pointer;
        display: inline-block;
        font-size: 14px;
        height: 48px;
        margin-bottom: 8px;
        margin-right: 9px;
        padding: 0 16px;
        transition: all .3s ease;
        width: 250px;
        border: 1px solid saddlebrown 30%;
    }

    .countrylist--desc {
        color: #3b3e40;
        float: left;
        margin: 0;
        padding: 0;
    }

    .countrylist--item .countrylist--lang {
        color: #9fa3a6;
        float: right;
        font-size: 13px;
    }

    #selectYouCountry {
        background: #fff;
        margin-left: auto;
        margin-right: auto;
        max-height: 80vh;
        overflow-y: scroll;
        transform: translateY(-30px);
        width: 960px;
        z-index: 1003;
        overflow-x: hidden;
        /* 禁用水平滚动条 */
    }

    #selectYouCountryBox .icon {
        width: 30px;
        height: 26px;
        margin-top: -6px;
        display: inline-block;
        vertical-align: middle;
    }
    #selectYouCountryBox{
        border: none;
    }
</style>
<?php
// 引入DOM节点(图标)
$HaiTaoSkipToLagArr = array('en', 'de', 'es', 'fr', 'it', 'ja', 'zh-TW');
$ToLags = explode('/', $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]);
$ToLag = in_array($ToLags[1], $HaiTaoSkipToLagArr) ? strtoupper($ToLags[1]) : 'LANG';
?>
<div id="selectYouCountryBox" style="cursor:pointer;" class="layui-btn layui-btn-primary" lay-on="get-country-page-custom">
    <svg t="1705484620277" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4246" width="200" height="200">
        <path d="M870.912 103.936H152.576c-47.616 0-86.528 38.912-86.528 86.528v526.848c0 47.616 38.912 86.528 86.528 86.528h224.256l111.616 111.616c6.656 6.656 14.848 9.728 23.552 9.728s16.896-3.072 23.552-9.728l111.616-111.616h224.256c47.616 0 86.528-38.912 86.528-86.528V190.464c-0.512-47.616-38.912-86.528-87.04-86.528z m19.968 613.376c0 10.752-8.704 19.968-19.968 19.968H640c-10.24-2.048-21.504 1.024-29.696 9.216L512 844.8l-98.304-98.304c-8.192-8.192-19.456-11.264-29.696-9.216H152.576c-10.752 0-19.968-8.704-19.968-19.968V190.464c0-10.752 8.704-19.968 19.968-19.968h718.336c11.264 0 19.968 8.704 19.968 19.968v526.848z" fill="#3E3A39" p-id="4247"></path>
        <path d="M248.32 258.56c-16.896 0-30.72 13.312-31.744 30.208v317.44c0 17.408 14.336 31.744 31.744 31.744 17.408 0 31.744-14.336 31.744-31.744v-317.44c-1.024-16.896-14.848-30.208-31.744-30.208z" fill="#FA7268" p-id="4248"></path>
        <path d="M515.072 378.368c-9.216-7.168-20.992-12.288-34.304-14.848-13.312-3.072-28.672-4.096-45.568-4.096-14.336 0-27.136 1.536-37.888 4.096-10.752 2.56-20.48 6.144-28.672 10.24-8.192 4.096-15.36 9.216-20.992 14.336-4.608 4.608-8.704 9.728-11.776 14.336-4.608 5.632-7.68 12.8-7.68 20.48 0 17.408 14.336 31.744 31.744 31.744 15.36 0 28.16-10.752 31.232-25.088l0.512-0.512c2.56-2.56 5.632-5.12 9.216-7.168 4.096-2.048 8.704-4.096 13.824-5.12 5.632-1.536 11.776-2.048 19.456-2.048s14.848 0.512 20.992 2.048c5.632 1.024 10.24 3.072 13.824 5.632 3.584 2.56 6.144 5.632 8.192 9.728 2.048 4.096 3.072 10.24 3.072 16.896v4.608l-48.64 4.608c-14.336 1.536-27.648 3.584-39.424 7.168-12.288 3.584-23.552 8.192-32.768 14.848-9.216 6.656-16.896 15.872-22.016 26.624s-8.192 24.064-8.192 39.424c0 11.776 2.048 22.528 5.632 32.768 3.584 9.728 9.216 18.944 16.384 26.112 7.168 7.168 15.872 13.312 26.112 16.896 10.24 4.096 22.016 6.144 35.328 6.144 15.872 0 30.72-3.584 44.544-10.24 8.704-4.608 17.408-9.728 25.6-15.872v9.728c0 16.896 13.824 30.72 30.72 30.72s30.72-13.824 30.72-30.72V450.048c0-16.896-2.56-31.232-7.168-43.008-5.12-11.776-12.288-21.504-22.016-28.672z m-34.816 167.424c-4.096 3.072-8.192 6.656-12.288 9.216-5.12 3.584-10.24 6.656-14.848 9.216-5.12 2.56-9.728 4.608-14.336 6.144-4.608 1.536-8.704 2.048-12.8 2.048-10.752 0-18.432-2.048-24.064-6.144-5.12-3.584-7.168-11.264-7.168-21.504 0-6.144 1.024-11.776 3.584-15.872 2.56-4.096 5.632-7.68 10.24-10.752 5.12-3.072 11.264-5.632 18.432-7.168 7.68-2.048 16.896-3.072 27.136-4.096l26.624-2.56v41.472zM799.232 384.512c-6.144-8.192-14.336-14.848-24.576-18.944-9.728-4.096-22.016-6.144-36.352-6.144-8.192 0-16.384 1.024-24.576 3.584-8.192 2.56-15.872 5.632-23.552 9.216-7.68 4.096-14.336 8.704-21.504 13.824l-6.144 4.608v-5.632 4.608c0-17.408-14.336-31.232-31.744-31.232s-31.744 13.824-31.744 31.232v-4.608 226.304c0 17.408 14.336 31.744 31.744 31.744s31.744-14.336 31.744-31.744V451.584c4.608-4.096 9.216-7.68 14.336-11.776 5.632-4.096 11.264-8.192 16.896-11.264 5.632-3.072 10.752-5.632 16.384-7.68 5.12-1.536 9.728-2.56 13.824-2.56 6.656 0 11.776 1.024 15.36 2.56 3.072 1.536 5.632 4.096 7.68 8.192 2.048 4.608 3.584 10.24 4.096 17.92 0.512 8.192 1.024 17.92 1.024 29.696v134.656c0 17.408 14.336 31.744 31.744 31.744s31.744-14.336 31.744-31.744V453.12c0-14.336-1.024-27.136-3.584-38.4-2.048-11.776-6.656-22.016-12.8-30.208z" fill="#3E3A39" p-id="4249"></path>
    </svg>
    <span style="font-size: 24px;" class="selectYouCountryText"><?php echo $ToLag; ?></span>
</div>
<script>
    layui.use(function() {
        var $ = layui.$;
        var layer = layui.layer;
        var util = layui.util;
        var form = layui.form;
        util.on('lay-on', {
            'get-country-page-custom': function() {
                layer.open({
                    type: 1,
                    resize: false,
                    shadeClose: true,
                    title: 'Select Your Country/Region',
                    content: `<div id="selectYouCountry"><br><br>
        <fieldset class="layui-elem-field">
            <legend>North America</legend>
            <div class="layui-field-box">
                <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">United States</span><span class="countrylist--lang">English</span></li>
                </a>
                <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Canada</span><span class="countrylist--lang">English</span></li>
                    </a>
            </div>
        </fieldset>
        <fieldset class="layui-elem-field">
            <legend>Europe</legend>
            <div class="layui-field-box">
                <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Europe</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'de\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Deutschland</span><span class="countrylist--lang">Deutsch</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">United Kingdom</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'es\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">España</span><span class="countrylist--lang">Español</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'fr\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">France</span><span class="countrylist--lang">Français</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'it\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Italia</span><span class="countrylist--lang">Italiano</span></li>
                    </a>
            </div>
        </fieldset>
        <fieldset class="layui-elem-field">
            <legend>Asia Pacific</legend>
            <div class="layui-field-box">
                <a href="#" onclick="HaiTaoSkipTo(\'ja\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">日本</span><span class="countrylist--lang">日本語</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'zh-TW\')" class="countrylist--item ga-data active">
                    <li><span class="countrylist--desc">香港</span><span class="countrylist--lang">繁體中文</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'zh-TW\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">澳門</span><span class="countrylist--lang">繁體中文</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Malaysia</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Singapore</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">New Zealand</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Australia</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Philippines</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Myanmar</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">Indonesia</span><span class="countrylist--lang">English</span></li>
                    </a>
                    
                    <a href="#" onclick="HaiTaoSkipTo(\'en\')" class="countrylist--item ga-data ">
                    <li><span class="countrylist--desc">대한민국</span><span class="countrylist--lang">한국어</span></li>
                    </a>
            </div>
        </fieldset></div>`,
                    success: function() {
                        // 对弹层中的表单进行初始化渲染
                        form.render();
                    }
                });
            }
        })
    });
    var selectYouCountryText = document.getElementsByClassName('selectYouCountryText')[0];
    if (selectYouCountryText != undefined) {
        let Countrys = ['en', 'de', 'es', 'fr', 'it', 'ja', 'zh-TW'];
        let url = document.location.toString();
        let arrUrl = url.split('/')[3];
        if (Countrys.indexOf(arrUrl) == -1) {
            selectYouCountryText.innerText = 'LANG';
        } else {
            selectYouCountryText.innerText = arrUrl.toUpperCase();
        }
    }

    function HaiTaoSkipTo(lag) {
        location.href = 'https://' + document.domain + '/' + lag;
    }
</script>