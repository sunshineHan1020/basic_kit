(function () {
    'use strict';
    var forms = document.querySelectorAll('.wc-form');
    for (var i = 0; i < forms.length; i++) {
        if (forms[i].getAttribute('data-wc-ready')) { continue; }
        forms[i].setAttribute('data-wc-ready', '1');
        forms[i].addEventListener('submit', function (event) {
            event.preventDefault();
            var form = this, status = form.querySelector('.wc-status'), button = form.querySelector('button[type=submit]');
            if (button.disabled) { return; }
            var data = new FormData(form), fields = form.querySelectorAll('input,textarea');
            button.disabled = true;
            for (var k = 0; k < fields.length; k++) { fields[k].disabled = true; }
            status.textContent = '저장 중입니다…'; status.setAttribute('data-error', '0');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true); xhr.timeout = 90000;
            function done(message, error) {
                button.disabled = false;
                for (var j = 0; j < fields.length; j++) { fields[j].disabled = false; }
                status.textContent = message; status.setAttribute('data-error', error ? '1' : '0');
            }
            xhr.onload = function () {
                var result;
                try { result = JSON.parse(xhr.responseText); }
                catch (e) { done('응답을 확인할 수 없습니다. 로그인 상태와 저장 결과를 새로고침하여 확인해주세요.', true); return; }
                if (xhr.status !== 200 || !result.code) { done(result.msg || '저장에 실패했습니다.', true); return; }
                if (result.revisions) {
                    for (var key in result.revisions) {
                        if (Object.prototype.hasOwnProperty.call(result.revisions, key)) {
                            form.elements['revisions[' + key + ']'].value = result.revisions[key];
                        }
                    }
                }
                if (result.revision) { form.elements.revision.value = result.revision; }
                if (result.image_url) {
                    var preview = form.querySelector('.wc-preview');
                    preview.src = result.image_url; preview.alt = form.elements.alt_text.value;
                    form.elements.image.value = '';
                }
                done(result.msg, false);
            };
            xhr.onerror = xhr.ontimeout = function () { done('연결이 끊겼습니다. 새로고침하여 저장 결과를 확인한 후 다시 시도해주세요.', true); };
            xhr.send(data);
        });
    }
}());
