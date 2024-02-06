<?php
if (false === get_transient('SyncPWN_RememberAuthor')) {
    $SyncPWN_RememberAuthor = '1';
    $SyncPWN_expires_in = 0;
} else {
    $SyncPWN_cache_token = get_transient('SyncPWN_access_token');
    looklog($SyncPWN_cache_token);
    $SyncPWN_RememberAuthor = false !== $SyncPWN_cache_token ? '0' : '1';
    if (false !== $SyncPWN_cache_token) {
        $SyncPWN_expires_in = $SyncPWN_cache_token['begin_cache'] + $SyncPWN_cache_token['expires_in'] - time();
    } else {
        $SyncPWN_expires_in = 0;
    }
}
$carrier = file_get_contents(SYNC_PAYPAL_WAYBILL_NUMBER_DIR . '/static/carriers.json');
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
    [v-cloak] {
        display: none;
    }

    .SyncPWN_admin {
        /* display: relative; */
    }

    .toast-center-center-myalert {
        margin-top: -24%;
        margin-left: 34%;
        width: 400px;
        text-align: center;
    }

    #getClientData .form-row .input-group-prepend .input-group-text {
        border-radius: .375rem 0 0 .375rem;
    }

    #getClientData .form-row .input-group-prepend .input-group-text:hover {
        cursor: pointer;
    }

    #getClientData .hint-message {
        display: inline-flex;
        justify-content: flex-start;
        flex-direction: row;
    }

    #getClientData .form-check-input:checked {
        background-color: unset;
    }

    .SyncPWN_admin-dis-none {
        display: none;
    }

    .align-items-center-left1 {
        width: 46%;
    }

    #SyncPWN_admin .align-items-center-left1 .col-auto-99 {
        margin-left: 20px;
        width: 100px;
    }

    #SyncPWN_admin .align-items-center-left1 {
        flex-wrap: nowrap;
    }

    #SyncPWN_admin .align-items-center-left1 .form-control {
        width: 430px;
    }

    #alert_msg .alert-dismissible {
        height: 50px;
        line-height: 16px;
        overflow: inherit;
    }

    #SyncPWN_admin #carrier {
        height: 33.6px;
        width: 320px;
        border-radius: 3px;
    }

    #SyncPWN_admin .align-items-center-left1 .form-text {
        margin-left: 22px;
    }
</style>

<body>
    <div v-cloak class="home" id="SyncPWN_admin" style="margin-top: 14px;">
        <!-- <h1></h1> -->
        <nav class="navbar bg-body-tertiary" style="width: 99%;">
            <div class="container-fluid">
                <div class="navbar-brand" href="#">
                    <img src="<?php echo SYNC_PAYPAL_WAYBILL_NUMBER_URL . '/static/logo.svg'; ?>" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
                    {{headline}}
                </div>
            </div>
        </nav>
        <hr style="margin-top: 14px;">
        <div id="getClientData">
            <div v-if="SyncPWN_RememberAuthor == 1" class="form-row align-items-center getClientBox">
                <div class="col-auto">
                    <label class="sr-only" for="ClientIdInput">CLIENT_ID</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div @click="changeType('ClientIdInput')" class="input-group-text"><i class="bi bi-eye-fill ClientIdInput"></i>&nbsp;C</div>
                        </div>
                        <input type="password" class="form-control" id="ClientIdInput" placeholder="您的客户端 ID">
                    </div>
                </div>
                <div class="col-auto">
                    <label class="sr-only" for="ClientSecretInput">CLIENT_SECRET</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div @click="changeType('ClientSecretInput')" class="input-group-text"><i class="bi bi-eye-fill ClientSecretInput"></i>&nbsp;P</div>
                        </div>
                        <input type="password" class="form-control" id="ClientSecretInput" placeholder="您的客户端 密钥">
                    </div>
                </div>
                <div class="col-auto hint-message">
                    <div>
                        <input class="form-check-input" type="checkbox" id="RememberMe" v-model="RememberMe">
                        <label class="form-check-label" for="RememberMe">
                            Remember Me 7 Day
                        </label>
                    </div>
                    <div class="message SyncPWN_admin-dis-none">
                        <label style="font-size: 12px;margin-left: 20px;" class="form-check-label">
                            无论您是否选择 Remember 您的信息,您的信息都永远不会进行二次展现。
                            此项仅决定:您下次访问是否需要重新登录 和 是否允许一键刷新Token。
                        </label>
                    </div>

                </div>
                <div class="col-auto">
                    <button @click="sendClient(0)" id="getClientBoxBtnS" style="width: 100%;" type="submit" class="btn btn-primary mb-2">Submit</button>
                </div>
            </div>
            <div v-else class="form-row align-items-center getClientBox">

                <label class="form-check-label">
                    Access Token Expires In({{progressbarTimeout}}s) :
                </label>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" :style="{'width':progressbar+'%'}" aria-valuemin="0" aria-valuemax="32100"></div>
                </div>
                <br>
                <div class="vstack gap-2 col-md-5 mx-auto" style="width: 100%;">
                    <button @click="sendClient(1)" id="getClientBoxBtnR" type="button" class="btn btn-secondary">Refresh Token</button>
                    <button @click="sendClient(2)" type="button" class="btn btn-outline-secondary">Remove SecretKey And Token</button>
                </div>
            </div>
        </div>
        <br>

        <hr>
        <div class="alert alert-light" role="alert">
            要为多个事务添加跟踪信息，请在请求正文中包含以下参数：可用参数请参阅 <a target="_blank" href="https://developer.paypal.com/docs/tracking/tracking-api/integrate/#link-addtrackinginformationwithtrackingnumbers">Paypal 添加包裹跟踪参数参考</a>
        </div>
        <div style="display: flex">
            <div style="width: 54%;">
                <div class="row g-3 align-items-center align-items-center-left1">
                    <div class="col-auto col-auto-99">
                        <label class="col-form-label">Required</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" placeholder="status" id="status" class="form-control" aria-describedby="status">
                    </div>
                    <div class="col-auto">
                        <span id="status_text" class="form-text">
                            必须 物料装运的状态。请参阅 <a target="_blank" href="https://developer.paypal.com/docs/tracking/reference/shipping-status/">发货状态</a>
                        </span>
                    </div>
                </div>
                <div class="row g-3 align-items-center align-items-center-left1">
                    <div class="col-auto col-auto-99">
                        <label class="col-form-label">Required</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" placeholder="transaction_id" id="transaction_id" class="form-control" aria-describedby="transaction_id">
                    </div>
                    <div class="col-auto">
                        <span id="transaction_id_text" class="form-text">
                            必须 PayPal 交易 ID。
                        </span>
                    </div>
                </div>
                <div class="row g-3 align-items-center align-items-center-left1">
                    <div class="col-auto col-auto-99">
                        <label class="col-form-label">Choosable</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" placeholder="tracking_number" id="tracking_number" class="form-control" aria-describedby="tracking_number">
                    </div>
                    <div class="col-auto">
                        <span id="tracking_number_text" class="form-text">
                            可选 货件的跟踪号。
                        </span>
                    </div>
                </div>
                <div class="row g-3 align-items-center align-items-center-left1">
                    <div class="col-auto col-auto-99">
                        <label class="col-form-label">Choosable</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" placeholder="tracking_number_type" id="tracking_number_type" class="form-control" aria-describedby="tracking_number_type">
                    </div>
                    <div class="col-auto">
                        <span id="tracking_number_type_text" class="form-text">
                            可选 跟踪号的类型。
                        </span>
                    </div>
                </div>
                <div class="row g-3 align-items-center align-items-center-left1">
                    <div class="col-auto col-auto-99">
                        <label class="col-form-label">Choosable</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" placeholder="shipment_date" id="shipment_date" class="form-control" aria-describedby="shipment_date">
                    </div>
                    <div class="col-auto">
                        <span id="shipment_date_text" class="form-text">
                            可选 发货发生的日期。
                        </span>
                    </div>
                </div>
                <div class="row g-3 align-items-center align-items-center-left1">
                    <div class="col-auto col-auto-99">
                        <label class="col-form-label">Choosable</label>
                    </div>
                    <div class="col-auto">
                        <select id="carrier" v-model="carrier" class="form-select" aria-label="Default select example">
                            <option disabled value="">Open this select menu</option>
                            <option v-for="item in carriers" :value="item.Enum">{{ item.Description }}</option>
                        </select>
                    </div>
                    <div class="col-auto" style="width: 100%;">
                        <span id="carrier_text" class="form-text">
                            可选 货件的承运方。请参阅 <a target="_blank" href="https://developer.paypal.com/docs/tracking/reference/carriers/#link-global">承运运营商</a>
                        </span>

                    </div>
                </div>
                <div v-if="carrier=='OTHER'" class="row g-3 align-items-center align-items-center-left1">
                    <div class="col-auto col-auto-99">
                        <label class="col-form-label">Choosable</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" placeholder="carrier_name_other" id="carrier_name_other" class="form-control" aria-describedby="carrier_name_other">
                    </div>
                    <div class="col-auto">
                        <span id="carrier_name_other_text" class="form-text">
                            可选 货件的承运方名称。
                        </span>
                    </div>
                </div>
                <br>
                <div class="row g-3 align-items-center align-items-center-left1" style="width: 90%;">
                    <!-- <button @click="syncPWN(1)" type="button" class="btn btn-secondary">Append Record</button> -->
                    <button @click="syncPWN(0)" id="SingleCommit" type="button" class="btn btn-outline-secondary">
                        Single Commit
                    </button>
                    <!-- <button class="btn btn-primary" type="button" disabled>
                        <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                        Loading...
                    </button> -->
                </div>
            </div>
            <div id="alert_msg" style="width: 45%;max-height: 330px;overflow: overlay;margin-left: -20px;">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>交易ID ----- / 答复：</strong> PayPal API 响应答复
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <div style="float: left;"></div>
        <!-- 有关参数的详细信息，请参阅参数。 -->
        <!-- <button @click="alertmsg" type="button" class="btn btn-primary" id="liveAlertBtn">显示警告框（alert）</button> -->
        <div id="liveAlertPlaceholder" class="toast-center-center-myalert"></div>
    </div>

    <script>
        // 将vue挂载到id为home的根元素上
        jQuery(document).ready(function($) {
            var vm = new Vue({
                el: "#SyncPWN_admin",
                data() {
                    return {
                        headline: '同步Paypal运单号',
                        SyncPWN_RememberAuthor: <?php echo $SyncPWN_RememberAuthor; ?>,
                        layerSum: 0,
                        RememberMe: true,
                        progressbar: 0,
                        progressbarType: 0,
                        progressbarTimeout: <?php echo $SyncPWN_expires_in; ?>,
                        carriers: <?php echo $carrier; ?>,
                        astrictBtn: false,
                        actTimeout: 0,
                        carrier: '',
                    };
                },
                methods: {
                    layer(message, type, timeout = 2) {
                        if (this.layerSum <= 5) {
                            const alertPlaceholder = document.getElementById('liveAlertPlaceholder')
                            const wrapper = document.createElement('div')
                            const theme = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']
                            wrapper.innerHTML = [
                                `<div class="alert alert-${type} alert-dismissible" role="alert">`,
                                `   <div>${message}</div>`,
                                '   ',
                                '</div>'
                            ].join('')

                            this.layerSum++
                            alertPlaceholder.append(wrapper)
                            setTimeout(() => {
                                var parentElement = document.getElementById('liveAlertPlaceholder');
                                if (parentElement.hasChildNodes()) {
                                    var firstChild = parentElement.firstChild;
                                    parentElement.removeChild(firstChild);
                                }
                                this.layerSum--
                            }, timeout * 1000);
                        }
                    },
                    alertmsg() {
                        const theme = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']
                        this.layer('A simple primary alert—check it out!', theme[this.layerSum], 4)
                    },
                    sendClient(type) {
                        if (this.astrictBtn) {
                            return false;
                        }
                        var field = false;
                        var domId = false;
                        var domBtn = false;
                        var domBtnText = false;
                        switch (type) {
                            case 0:
                                var ClientIdInput = document.querySelector('#getClientData #ClientIdInput');
                                var ClientSecretInput = document.querySelector('#getClientData #ClientSecretInput');
                                if (ClientIdInput !== null && ClientSecretInput !== null) {
                                    if (!ClientIdInput.value || !ClientSecretInput.value) {
                                        this.astrictBtn = true;
                                        this.layer('请检查客户端 ID/密钥 必填', 'warning', 4)
                                        setTimeout(() => {
                                            this.astrictBtn = false;
                                        }, (Math.random() * (4 - 1) + 1) * 1000);
                                        return false
                                    }
                                    var field = {
                                        'ClientIdInput': ClientIdInput.value,
                                        'ClientSecretInput': ClientSecretInput.value,
                                        'RememberMe': this.RememberMe
                                    }
                                }
                                domBtn = document.querySelector('#getClientBoxBtnS');
                                domBtnText = 'Submit';
                                domId = '#getClientBoxBtnS';
                                break;
                            case 1:
                                var field = {
                                    'RefreshToken': 'Refresh',
                                }
                                break;
                            case 2:
                                var field = {
                                    'RemoveToken': 'LogOut',
                                }
                                break;
                            default:
                                break;
                        }
                        if (field) {
                            this.astrictBtn = true;
                            if (domBtn) domBtn.innerHTML = '<span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span> Loading...';
                            this.sendAjax('sync_pwn_send_client', field);
                            if (type === 0) {
                                var interval = setInterval(() => {
                                    if (this.actTimeout > 20) {
                                        if (domBtn !== null) domBtn.innerHTML = domBtnText;
                                        clearInterval(interval);
                                        this.actTimeout = 0;
                                        this.astrictBtn = false;
                                        this.layer('请求超时', 'danger', 3)
                                    }
                                    console.log(!this.astrictBtn, domBtnText);
                                    if (!this.astrictBtn && domBtnText) {
                                        domBtn = document.querySelector('#getClientBoxBtnR')
                                        if (domBtn !== null) {
                                            domBtn.innerHTML = 'Refresh Token';
                                        } else {
                                            domBtn = document.querySelector(domId);
                                            if (domBtn !== null) domBtn.innerHTML = domBtnText;
                                        }
                                        clearInterval(interval);
                                        this.actTimeout = 0;
                                    }
                                    this.actTimeout++
                                }, 1000);
                            }
                        }
                    },
                    syncPWN(type) {
                        if (this.astrictBtn) {
                            return false;
                        }
                        // this.layer(message, type, timeout);
                        var field = false;
                        var domBtn = false;
                        var domBtnText = false;
                        switch (type) {
                            case 0:
                                var status = document.querySelector('#status');
                                var transaction_id = document.querySelector('#transaction_id');
                                let statusArr = ['SHIPPED', 'ON_HOLD', 'DELIVERED', 'CANCELLED'];
                                if (!status.value || !transaction_id.value) {
                                    this.astrictBtn = true;
                                    this.layer('必填项目不能置空', 'warning', 4)
                                    setTimeout(() => {
                                        this.astrictBtn = false;
                                    }, (Math.random() * (4 - 1) + 1) * 1000);
                                    return false
                                } else if (!statusArr.includes(status.value)) {
                                    this.astrictBtn = true;
                                    this.layer('合法物料装运状态：SHIPPED, ON_HOLD, DELIVERED, CANCELLED', 'warning', 4)
                                    setTimeout(() => {
                                        this.astrictBtn = false;
                                    }, (Math.random() * (4 - 1) + 1) * 1000);
                                    return false
                                }
                                var field = {
                                    'status': status.value,
                                    'transaction_id': transaction_id.value,
                                    'tracking_number': document.querySelector('#tracking_number').value,
                                    'tracking_number_type': document.querySelector('#tracking_number_type').value,
                                    'shipment_date': document.querySelector('#shipment_date').value,
                                    'carrier': document.querySelector('#carrier').value,
                                }
                                field['carrier_name_other'] = field['carrier'] == 'OTHER'?document.querySelector('#carrier_name_other').value:'';
                                field = Object.keys(field).reduce((acc, key) => {
                                    if (field[key] !== null && field[key] !== undefined && field[key] !== '') {
                                        acc[key] = field[key];
                                    }
                                    return acc;
                                }, {});
                                domBtn = document.querySelector('#SingleCommit');
                                domBtnText = 'Single Commit';
                                break;
                            case 1:
                                break;
                            case 2:
                                break;
                            default:
                                break;
                        }
                        if (field) {
                            // console.log('syncPWN...');
                            // console.log(domBtn.innerHTML, 99999);
                            this.astrictBtn = true;
                            if (domBtn) domBtn.innerHTML = '<span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span> Loading...';
                            this.sendAjax('sync_paypal_waybill_number', field);
                        }
                        var interval = setInterval(() => {
                            console.log(11111111);
                            if (this.actTimeout > 20) {
                                if (domBtn !== null) domBtn.innerHTML = domBtnText;
                                clearInterval(interval);
                                this.actTimeout = 0;
                                this.astrictBtn = false;
                                this.layer('请求超时', 'danger', 3)
                            }
                            if (!this.astrictBtn && domBtn !== null && domBtnText) {
                                domBtn.innerHTML = domBtnText;
                                clearInterval(interval);
                            }
                            this.actTimeout++;
                        }, 1000);
                    },
                    sendAjax(actions, field) {
                        $.post(ajaxurl, {
                            _ajax_nonce: ajaxurl,
                            action: actions,
                            res: field
                        }, (data) => { //callback
                            this.astrictBtn = false;
                            console.log('8888->', data);
                            let response = JSON.parse(data);
                            let message = response.message;
                            let type = 'light';
                            let timeout = 5;
                            if (response.code == 101) {
                                type = 'warning';
                            } else if (response.code == 102) {
                                type = 'info';
                            } else if (response.code == 105) {
                                type = 'danger';
                                let dom = document.querySelector('#alert_msg');
                            } else if (response.code == 200) {
                                type = 'info';
                                timeout = 2
                                if (response.timeout != 'undefined') {
                                    this.progressbarTimeout = response.timeout;
                                } else {
                                    this.progressbarTimeout = 0;
                                }
                                if (response.type != 'undefined') {
                                    if (response.type == '0') {
                                        this.SyncPWN_RememberAuthor = 0
                                        // 通知令牌剩余存活时长
                                        if (this.progressbarType === 0 && this.progressbarTimeout > 1) {
                                            this.progressbarType = setInterval(() => {
                                                if (this.progressbarTimeout <= 1) {
                                                    this.progressbarTimeout = 1;
                                                    clearInterval(this.progressbarType);
                                                }
                                                this.progressbar = (this.progressbarTimeout / 32100).toFixed(4) * 100;
                                                this.progressbarTimeout -= 1;
                                            }, 1000);
                                        }
                                    } else if (response.type == '1') {
                                        // 通知令牌剩余存活时长
                                        if (this.progressbarType === 0 && this.progressbarTimeout > 1) {
                                            this.progressbarType = setInterval(() => {
                                                if (this.progressbarTimeout <= 1) {
                                                    this.progressbarTimeout = 1;
                                                    clearInterval(this.progressbarType);
                                                }
                                                this.progressbar = (this.progressbarTimeout / 32100).toFixed(4) * 100;
                                                this.progressbarTimeout -= 1;
                                            }, 1000);
                                        }
                                    } else if (response.type == '2') {
                                        this.SyncPWN_RememberAuthor = 1;
                                        clearInterval(this.progressbarType);
                                        this.progressbarType = 0;
                                    }
                                }
                            } else if (response.code == 205) {
                                type = 'info';
                                if (response.transaction_id != 'undefined') {
                                    let dom = document.querySelector('#alert_msg');
                                    let innerHTML = `
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <strong>交易ID:` + response.transaction_id + ` 答复 </strong>: ` + response.message + `
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    `;
                                    dom.insertAdjacentHTML('beforeend', innerHTML);
                                    message = ''
                                    dom.scrollTop = dom.scrollHeight;
                                }
                            } else if (response.code == 401) {
                                type = 'danger';
                            } else {
                                message = '稍后重试'
                            }
                            if (message != '') {
                                this.layer(message, type, timeout);
                            }

                        });
                        return false;
                    },
                    changeType(element) {
                        var eleI = document.querySelector('#getClientData #' + element);
                        var eleC = document.querySelector('#getClientData .' + element);
                        if (eleI !== null && eleC !== null) {
                            if (eleI.type == 'password') {
                                eleI.type = 'text';
                                eleC.classList.toggle('bi-eye-slash-fill');
                                eleC.classList.remove('bi-eye-fill');
                            } else {
                                eleI.type = 'password';
                                eleC.classList.toggle('bi-eye-fill');
                                eleC.classList.remove('bi-eye-slash-fill');
                            }
                        }
                    }

                },
                mounted() {
                    // 通知令牌剩余存活时长
                    console.log(this.carriers);
                    if (this.progressbarType === 0 && this.progressbarTimeout > 1) {
                        this.progressbarType = setInterval(() => {
                            if (this.progressbarTimeout <= 1) {
                                this.progressbarTimeout = 1;
                                clearInterval(this.progressbarType);
                            }
                            this.progressbar = (this.progressbarTimeout / 32100).toFixed(4) * 100;
                            this.progressbarTimeout -= 1;
                        }, 1000);
                    }
                    console.log('这是默认执行的操作' + this.progressbarTimeout);
                },
                created() {
                    console.log('这是默认执行的操作2' + this.progressbarTimeout);
                },
                watch: {
                    // 使用对象形式，键是要观察的数据属性，值是回调函数
                    RememberMe(newValue, oldValue) {
                        var element = document.querySelector('#getClientData .getClientBox');
                        if (element !== null) {
                            if (newValue === true) {
                                document.querySelector('#getClientData .getClientBox .message').classList.toggle('SyncPWN_admin-dis-none');
                            } else {
                                document.querySelector('#getClientData .getClientBox .message').classList.remove('SyncPWN_admin-dis-none')
                            }
                        }

                    },
                    carrier(newValue, oldValue) {
                        if (newValue == 'OTHER') {
                            if (newValue === true) {
                                document.querySelector('#getClientData .getClientBox .message').classList.toggle('SyncPWN_admin-dis-none');
                            } else {
                                document.querySelector('#getClientData .getClientBox .message').classList.remove('SyncPWN_admin-dis-none')
                            }
                        }

                    },
                },
            });
            // vm.config.productionTip = false
        })
    </script>

</body>

</html>