layer(message, type, timeout = 2) {
    const alertPlaceholder = document.getElementById('liveAlertPlaceholder')
    const wrapper = document.createElement('div')
    const theme = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible" role="alert">`,
        `   <div>${message}</div>`,
        '   ',
        '</div>'
    ].join('')

    alertPlaceholder.append(wrapper)
    setTimeout(() => {
        var parentElement = document.getElementById('liveAlertPlaceholder');
        if (parentElement.hasChildNodes()) {
            var firstChild = parentElement.firstChild;
            parentElement.removeChild(firstChild);
        }
    }, timeout * 1000);
    const alert = (message, type) => {

    }
},
alertmsg() {
    this.layer('A simple primary alert—check it out!', 'primary', 4)
    this.layer('A simple secondary alert—check it out!', 'secondary', 4)
    this.layer('A simple success alert—check it out!', 'success', 4)
    this.layer('A simple danger alert—check it out!', 'danger', 4)
    this.layer('A simple warning alert—check it out!', 'warning', 4)
    this.layer('A simple info alert—check it out!', 'info', 4)
    this.layer('A simple light alert—check it out!', 'light', 4)
    this.layer('A simple dark alert—check it out!', 'dark', 4)
}