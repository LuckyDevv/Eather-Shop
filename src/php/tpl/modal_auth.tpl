<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authModalLabel">Вход в аккаунт</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="login_input" class="col-form-label">Логин:</label>
                        <input type="text" class="form-control" id="login_input">
                    </div>
                    <div class="mb-3">
                        <label for="password_input" class="col-form-label">Пароль:</label>
                        <input type="password" class="form-control" id="password_input">
                        <i class="bi bi-eye" style="font-size:24px; cursor: pointer" onclick="eye_Auth()"></i>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#regModal">Зарегестрироваться</button>
                <button type="button" class="btn btn-primary" onclick="authButton()">Войти</button>
            </div>
        </div>
    </div>
</div>