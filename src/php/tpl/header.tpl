<nav class="navbar navbar-expand-lg navbar-light bg-light border-nav sticky" style="margin-bottom: 10px" id="nav">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
	    <form class="d-flex justify-content-center input-group" onsubmit="return false">
            <input id="search_param" class="form-control px-3 input-search" type="search" autocomplete="off" placeholder="Поиск" aria-label="Искать на Eather">
            <button id="search" class="btn btn-success button-search" onclick="search_product()"><i class="bi bi-search"></i></button>
        </form>
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item" id="mainEl">
                    <center>
                        <a class="nav-link active" aria-current="page" href="index.php">
                            <h4 style="margin: 0;"><i class="bi bi-house"></i></h4>
                            <t class="nav-text">Главная</t>
                        </a>
                    </center>
                </li>
                <li class="nav-item" id="personalEl">
                    <center>
                        <a id="personal-btn" class="nav-link active" aria-current="page" data-bs-toggle="modal" data-bs-target="#authModal" href="#">
                            <h4 style="margin: 0;"><i class="bi bi-person-circle" id="personalIcon"></i></h4>
                            <t class="nav-text" id="personalText">Профиль</t>
                        </a>
                    </center>
                </li>
                <li class="nav-item" id="ordersEl">
                    <center>
                        <a class="nav-link active" href="orders.php">
                            <h4 style="margin: 0;"><i class="bi bi-box-seam"></i></h4>
                            <t class="nav-text">Заказы</t>
                        </a>
                    </center>
                </li>
                <li class="nav-item" id="cartEl">
                    <center>
                        <a class="nav-link active" href="cart.php">
                            <h4 style="margin: 0;"><i class="bi bi-basket"></i></h4>
                            <t class="nav-text">Корзина</t>
                        </a>
                    </center>
                </li>
                <li class="nav-item" id="favoritesEl">
                    <center>
                        <a class="nav-link active" href="favorites.php">
                            <h4 style="margin: 0;"><i class="bi bi-heart"></i></h4>
                            <t class="nav-text">Избранное</t>
                        </a>
                    </center>
                </li>
                <li class="nav-item" id="supportEl">
                    <center>
                        <a class="nav-link active" aria-current="page" onclick="supportBtn()" href="#">
                            <h4 style="margin: 0;"><i class="bi bi-patch-question"></i></h4>
                            <t class="nav-text">Поддержка</t>
                        </a>
                    </center>
                </li>
            </ul>
        </div>
    </div>
</nav>