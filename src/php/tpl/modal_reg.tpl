<div class="modal fade" id="regModal" tabindex="-1" aria-labelledby="regModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regModalLabel">Регистрация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="reg_login_input" class="col-form-label">Логин:</label>
                        <input type="text" class="form-control" autocomplete="off" id="reg_login_input">
                    </div>
                    <div class="mb-3">
                        <label for="reg_password_input" class="col-form-label">Пароль:</label>
                        <input type="password" class="form-control" autocomplete="off" id="reg_password_input">
                    </div>
                    <div class="mb-3">
                        <label for="reg_password_2step_input" class="col-form-label">Подтверждение пароля:</label>
                        <input type="password" class="form-control" autocomplete="off" id="reg_password_2step_input">
                        <i class="bi bi-eye" style="font-size:24px; cursor: pointer" onclick="eye_Reg()"></i>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" id="reg-btn" onclick="regButton()" class="btn btn-primary">Войти</button>
            </div>
        </div>
    </div>
</div>