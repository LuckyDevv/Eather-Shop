<div class="modal fade" id="2faModalNext" tabindex="-1" aria-labelledby="2faModalNextLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="2faModalNextLabel">Подтверждение</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <center>
                    <h5>Введите код из приложения-аутентификатора</h5>
                    <div class="otp-field mb-4">
                        <input class="otp-input" type="number" id="2fa_num1" />
                        <input class="otp-input" type="number" id="2fa_num2" disabled />
                        <input class="otp-input" type="number" id="2fa_num3" disabled />
                        <input class="otp-input" type="number" id="2fa_num4" disabled />
                        <input class="otp-input" type="number" id="2fa_num5" disabled />
                        <input class="otp-input" type="number" id="2fa_num6" disabled />
                    </div>
                </center>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" aria-current="page" data-bs-toggle="modal" data-bs-target="#2faModal" data-bs-dismiss="modal" class="btn btn-primary">Назад</button>
                <button class="btn btn-success profile-button" id="two_fa_submit">Подтвердить</button>
            </div>
        </div>
    </div>
</div>