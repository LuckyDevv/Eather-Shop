<div class="modal fade" id="2faModal" tabindex="-1" aria-labelledby="2faModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="2faModalLabel">Включение 2FA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <center>
                    <h5>Сканируйте QR-код</h5>
                    <img id="2fa_qr" src="photos/loading.gif" class=""  alt=""/>
                    <h5 style="margin-top: 10px;">Или введите вручную:</h5>
                    <h5 id="secret_code_"></h5>
                </center>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" aria-current="page" data-bs-toggle="modal" data-bs-target="#2faModalNext" class="btn btn-success profile-button">Далее</button>
            </div>
        </div>
    </div>
</div>