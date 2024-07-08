<div class="container rounded bg-white mt-5 mb-5" style="background-color: #f0f0ec">
    <div class="row"> <div class="col-md-3 border-right">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                <img class="avatar" src="photos/%avatar%" id="avatar" aria-current="page" data-bs-toggle="modal" data-bs-target="#avatarModal" alt="">
                <span class="font-weight-bold">%username%</span>
                <span class="text-black-50">%login%</span>
                <span> </span>
            </div>
        </div>
        <div class="col-md-5 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Управление аккаунтом</h4>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label class="labels" for="login">Логин аккаунта</label>
                        <input type="text" class="form-control" id="login" placeholder="Введите логин..." value="%login%">
                    </div>
                    <div class="col-md-6">
                        <label class="labels" for="name">Отображаемое имя</label>
                        <input type="text" class="form-control" id="name" placeholder="Введите имя..." value="%username%">
                    </div>
                </div>
                <form action="/" class="row mt-2">
                    <div class="col-md-6">
                        <label class="labels" for="password_1">Пароль</label>
                        <input type="password" id="password_1" class="form-control" placeholder="Введите новый пароль..." value="" autocomplete="false">
                    </div>
                    <div class="col-md-6">
                        <label class="labels" for="password_2">Подтверждение пароля</label>
                        <input type="password" id="password_2" class="form-control" value="" placeholder="Введите подтверждение пароля..." autocomplete="false">
                    </div>
                </form>
                <button id="view-pass" class="btn btn-primary" type="button" style="width: 100%; margin: 5px auto auto;">Показать пароль</button>
                <div class="col-md-12">
                    %2fa%
                </div>
                <!--<div class="row mt-3">
                    <div class="col-md-12">
                        <label class="labels">Адрес</label>
                        <input type="text" class="form-control" placeholder="Введите адрес..." value="">
                    </div>

                    <div class="col-md-12">
                        <label class="labels">Education</label>
                        <input type="text" class="form-control" placeholder="education" value="">
                    </div>
                </div>-->
                <div class="row mt-3">

                </div>
                <div class="mt-5 text-center">
                    <button class="btn btn-success profile-button" id="saveChanges" type="button">Сохранить изменения</button>
                    <button class="btn btn-danger" id="quitAccount" type="button">Выйти из аккаунта</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center experience">
                    <h4>Информация об аккаунте</h4>
                </div>
                <br>
                <div class="col-md-12">
                    <h5>ID аккаунта</h5>
                    <h6 class="form-control" id="account_id">%account_id%</h6>
                </div>
                <br>
                <div class="col-md-12">
                    <h5>ID кошелька</h5>
                    <h6 class="form-control" id="wallet_id">%wallet_id%</h6>
                </div>
                <div class="col-md-12" style="margin-top: 25px">
                    <h5 class="">Авторизованные устройства</h5>
                    <h6 class="form-control" id="devices">
                        %devices%
                    </h6>
                    <button class="btn btn-danger" disabled type="button" style="width: 100%">Завершить все сеансы</button>
                </div>
            </div>
        </div>
    </div>
</div>