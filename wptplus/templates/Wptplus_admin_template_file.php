<?php
// $propertys = tongtool_get_config();
?>
<br>
<hr>
<h1>通途插件后台配置</h1><br>
<a href="./">如果你有更好的想法/建议/bug反馈 ，请联系小编</a>
<br>
<hr>
<div class="layui-tab" lay-filter="exothecium-hash">
    <ul class="layui-tab-title">
        <li class="layui-this" lay-id="leader">集成管理</li>
        <!-- <li lay-id="Tab">GtranslateTab</li>
        <li lay-id="Feishu">OrderToFeishu</li>
        <li lay-id="0">>>>开发中>>></li> -->
    </ul>
    <style>
        .longer-label {
            width: 150px !important;
        }

        .longer-input {
            width: 700px !important;
        }

        .style-course {
            width: 400px !important;
            margin-left: 10px;
        }
    </style>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <div class="layui-collapse">

                <div class="layui-colla-item">
                    <div class="layui-colla-title">多语言选择器</div>
                    <div class="layui-colla-content">
                        <?php
                        $gtranslatetab_open = get_transient('gtranslatetab_open');
                        $gtranslatetab_mu_open = get_transient('gtranslatetab_mu_open');
                        ?>
                        <form class="layui-form layui-form-pane" action="">
                            <input type="hidden" name="plugin" value="1">
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">开关-默认关</label>
                                <div class="layui-input-block">
                                    <input <?php if ($gtranslatetab_open == 1) {
                                                echo 'checked';
                                            } ?> type="checkbox" name="open" lay-skin="switch" lay-filter="switchTest" title="开关">
                                    <!-- <input type="checkbox" checked name="open" lay-skin="switch" lay-filter="switchTest" title="开关"> -->
                                </div>
                            </div>
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">mu插件检查</label>
                                <div class="layui-input-block">
                                    <input <?php if ($gtranslatetab_mu_open == 1) {
                                                echo 'checked';
                                            } ?> type="checkbox" name="mu_open" lay-skin="switch" lay-filter="switchTest" title="开关">
                                    <!-- <input type="checkbox" checked name="open" lay-skin="switch" lay-filter="switchTest" title="开关"> -->
                                </div>
                            </div>
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">使用教程</label>
                                <div class="layui-input-block">
                                    <a target="_Blank" href="https://ow6br3uczyg.feishu.cn/mindnotes/KR4VbaNplmVKHLnV0L5c49eznng?from=from_copylink">
                                        <label class="layui-form-label style-course">点击查看GtranslateTab使用教程及其注意事项</label>
                                    </a>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn" lay-submit lay-filter="save1">确认</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="layui-colla-item">
                    <div class="layui-colla-title">订单提醒推送飞书</div>
                    <div class="layui-colla-content">
                        <?php
                        $feishu_webhook = get_option('wptplus_order_to_feishu');
                        $tofeishu_open = get_transient('tofeishu_open');
                        ?>
                        <form class="layui-form layui-form-pane" action="">
                            <input type="hidden" name="plugin" value="2">
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">开关-默认关</label>
                                <div class="layui-input-block">
                                    <input <?php if ($tofeishu_open == 1) {
                                                echo 'checked';
                                            } ?> type="checkbox" name="open" lay-skin="switch" lay-filter="switchTest" title="开关">
                                </div>
                            </div>
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">使用教程</label>
                                <div class="layui-input-block">
                                    <a target="_Blank" href="https://ow6br3uczyg.feishu.cn/docx/DwMud4tswoUlYSx7lXQcBlfvnGn?from=from_copylink">
                                        <label class="layui-form-label style-course">点击查看OrderToFeishu使用教程及其注意事项</label>
                                    </a>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label longer-label">Feishu Webhook</label>
                                <div class="layui-input-block">
                                    <input <?php if (!empty($feishu_webhook)) {
                                                echo 'value="' . $feishu_webhook . '"';
                                            } ?> type="text" name="feishu_webhook" autocomplete="off" placeholder="请输入" lay-verify="required" class="layui-input longer-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn" lay-submit lay-filter="save1">确认</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="layui-colla-item">
                    <div class="layui-colla-title">自定义订单标签</div>
                    <div class="layui-colla-content">
                        <?php
                        $CustomizeSignData = customizesign_get_config();
                        $customizesign_open = get_transient('customizesign_open');
                        ?>
                        <form class="layui-form layui-form-pane" action="">
                            <input type="hidden" name="plugin" value="3">
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">开关-默认关</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" <?php if ($customizesign_open == 1) {
                                                                echo 'checked';
                                                            } ?> name="open" lay-skin="switch" lay-filter="switchTest" title="开关">
                                    <!-- <input type="checkbox" checked name="open" lay-skin="switch" lay-filter="switchTest" title="开关"> -->
                                </div>
                            </div>
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">使用教程</label>
                                <div class="layui-input-block">
                                    <a target="_Blank" href="https://ow6br3uczyg.feishu.cn/docx/OVU3dVJHOobNoax5QnjcBbyqnUd?from=from_copylink">
                                        <label class="layui-form-label style-course">点击查看CustomizeSign使用教程及其注意事项</label>
                                    </a>
                                </div>
                            </div>
                            <div class="layui-form-item">

                                <div class="layui-inline">
                                    <label class="layui-form-label">名称</label>
                                    <div class="layui-input-inline">
                                        <input id="signName" type="text" name="signName" autocomplete="off" class="layui-input">
                                    </div>
                                </div>

                                <div class="layui-inline">
                                    <label class="layui-form-label">Key</label>
                                    <div class="layui-input-inline">
                                        <input id="signKey" type="text" name="signKey" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label id='hintColorLabel' class="layui-form-label" style="background-color:orange">提示色</label>
                                <div class="layui-input-inline">
                                    <select id="signColor" name="signColor" lay-filter="hintColor">
                                        <option value="green">经典绿</option>
                                        <option value="orange" selected>经典橙</option>
                                        <option value="cyan">经典青</option>
                                        <option value="blue">经典蓝</option>
                                        <option value="black">经典黑</option>
                                        <option value="gray">经典灰</option>
                                        <option value="red">经典红</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">预览</label>
                                    <div class="layui-input-inline" style="line-height: 38px;width:700px">
                                        <span class="layui-badge layui-bg-green">演示</span>
                                        <span class="layui-badge layui-bg-orange">演示</span>
                                        <span class="layui-badge layui-bg-blue">演示</span>
                                        <span class="layui-badge layui-bg-black">演示</span>
                                        <span class="layui-badge layui-bg-gray">演示</span>
                                        <span class="layui-badge layui-bg-red">演示</span>
                                        <span class="layui-badge layui-bg-cyan">演示</span>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">搜索选择框</label>
                                    <div class="layui-input-inline">
                                        <select id="signSelect" name="signUpdate" lay-search lay-filter="signUpdate">
                                            <option value="">搜索或选择已有记录</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn" lay-submit lay-filter="save1">确认</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="layui-colla-item">
                    <div class="layui-colla-title">批量修改产品在前台商店页的排列数序</div>
                    <div class="layui-colla-content">
                        <?php
                        $BatchAlterProductSort_open = get_transient('BatchAlterProductSort_open');
                        ?>
                        <form class="layui-form layui-form-pane" action="">
                            <input type="hidden" name="plugin" value="4">
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">开关-默认关</label>
                                <div class="layui-input-block">
                                    <input <?php if ($BatchAlterProductSort_open == 1) {
                                                echo 'checked';
                                            } ?> type="checkbox" name="open" lay-skin="switch" lay-filter="switchTest" title="开关">
                                    <!-- <input type="checkbox" checked name="open" lay-skin="switch" lay-filter="switchTest" title="开关"> -->
                                </div>
                            </div>
                            <div class="layui-form-item" pane>
                                <label class="layui-form-label">使用教程</label>
                                <div class="layui-input-block">
                                    <a target="_Blank" href="https://ow6br3uczyg.feishu.cn/docx/NqkSd2Zv1oWDTsxA1bacm4iwnrg">
                                        <label class="layui-form-label style-course">点击查看使用教程及其注意事项</label>
                                    </a>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <button class="layui-btn" lay-submit lay-filter="save1">确认</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="layui-colla-item">
                    <!-- 飞书链接： 密码：ow6br3uczyg -->
                    <input type="hidden" name="plugin" value="999">
                    <div class="layui-colla-title">教程的内容</div>
                    <div class="layui-colla-content">
                        <div class="layui-form-item" pane>
                            <label class="layui-form-label">帮助</label>
                            <div class="layui-input-block">
                                <a target="_Blank" href="https://ow6br3uczyg.feishu.cn/docx/HjdldG6FNot0IQxLUMockpuTnzc">
                                    <label class="layui-form-label style-course">点击查看WPTPLUS结构</label>
                                </a>
                            </div>
                        </div>
                        <p id="developed">折叠面板的内容</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="layui-tab-item">没有需要配置的项目哦 去集成页配置</div>
        <div class="layui-tab-item">没有需要配置的项目哦 去集成页配置</div>
        <div class="layui-tab-item">开发中的内容</div> -->
    </div>
</div>
<script>
    //判断某个类是否存在
    function hasClass(element, value) {
        var cls = value || '';
        //\s 匹配任何空白字符，包括空格、制表符、换页符等等
        if (cls.replace(/\s/g, '').length == 0) {
            return false; //当没有参数返回时，返回false
        }
        return new RegExp(' ' + cls + ' ').test(' ' + element.className + ' ');
    }
    layui.use(['form'], function() {
        var form = layui.form;
        var layer = layui.layer;
        var element = layui.element;
        var hasSign = <?php echo json_encode($CustomizeSignData); ?>
        // hash 地址定位
        var hashName = 'tabid'; // hash 名称
        // 获取 lay-id 值
        var layid = location.hash.replace(new RegExp('^#' + hashName + '='), '');
        // 初始切换
        element.tabChange('exothecium-hash', layid);
        // 切换事件
        element.on('tab(exothecium-hash)', function(obj) {
            location.hash = hashName + '=' + this.getAttribute('lay-id');
        });
        // 动态渲染表单
        var signSelect = document.getElementById('signSelect');
        layui.each(hasSign, function(index, item) {
            var signSelectOption = document.createElement("option");
            signSelectOption.setAttribute("value", index);
            signSelectOption.innerText = item['signName'];
            signSelect.appendChild(signSelectOption)
        });
        form.render("select");
        // 提交事件
        jQuery(document).ready(function($) {
            form.on('select(hintColor)', function(data) {
                $('#hintColorLabel').css('background-color', data.value);
                console.log($('.layui-badge'));
                $('.layui-badge').text(getDayOfWeek());
            })

            function getDayOfWeek() {
                const days = ['星期天', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
                const date = new Date();
                const day = date.getDay();
                return days[day];
            }
            form.on('select(signUpdate)', function(data) {
                let thisSign = hasSign[data.value];
                console.log(data.value);
                if (data.value == '') {
                    $('#signName').val('');
                    $('#signKey').val('');
                    $('#signColor').val('#16b777');
                    $('#hintColorLabel').css('background-color', '#16b777');
                } else {
                    $('#signName').val(thisSign['signName']);
                    $('#signKey').val(thisSign['signKey']);
                    $('#signColor').val(thisSign['signColor']);
                    $('#signSelect').val(thisSign['signKey']);
                    $('#hintColorLabel').css('background-color', thisSign['signColor']);
                }
                form.render("select");
            })
            form.on('submit(save1)', function(data) {
                var field = data.field;
                // if (data.field.open == 'on') {
                //     data.field.open = 1
                // } else {
                //     data.field.open = 0
                // }
                let actions = [
                    'gtranslatetab_save_config',
                    'tofeishu_save_config',
                    'customizesign_save_config',
                    'BatchAlterProductSort_save_config',
                    
                    
                    
                ];
                console.log(actions[data.field['plugin'] - 1], field);
                // layer.alert(JSON.stringify(field), {
                //     title: '当前填写的字段值'
                // });
                $.post(ajaxurl, {
                    _ajax_nonce: ajaxurl,
                    action: actions[data.field['plugin'] - 1],
                    res: field
                }, function(data) { //callback
                    if (data == 200) {
                        layer.msg('添加成功,刷新后可见', {
                            icon: 1,
                            time: 2000
                        });
                    } else if (data == 201) {
                        layer.msg('停用功能', {
                            icon: 1,
                            time: 2000
                        });
                    } else if (data == 202) {
                        layer.msg('启用功能', {
                            icon: 1,
                            time: 2000
                        });
                    } else if (data == 203) {
                        layer.msg('更新记录,刷新后可见', {
                            icon: 1,
                            time: 2000
                        });
                    } else if (data == 101) {
                        layer.msg('参数错误', {
                            icon: 2,
                            time: 2000
                        });
                    } else if (data == 102) {
                        layer.msg('拒绝处理', {
                            icon: 2,
                            time: 2000
                        });
                    } else if (data == 104) {
                        layer.msg('K值不合法', {
                            icon: 2,
                            time: 2000
                        });
                    } else if (data == 105) {
                        layer.msg('禁止使用关键字作为Key,请查阅手册', {
                            icon: 2,
                            time: 2000
                        });
                    } else if (data == 204) {
                        layer.msg('移除记录,刷新后可见', {
                            icon: 1,
                            time: 2000
                        });
                    }
                });
                return false;
            });
            jQuery('#developed').text(getDayOfWeek() + "也是要开心的一天~");
        });
    });
</script>