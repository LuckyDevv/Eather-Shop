<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <center><h7><a class="nav-link active" aria-current="page" href="index.php"><h3><i class="bi bi-house"></i></h3> Главная</a></h7></center>
                </li>
                <li class="nav-item">
                    <center><h7><a id="personal-btn" class="nav-link active" aria-current="page" data-bs-toggle="modal" data-bs-target="#authModal" href="#"><h3><i class="bi bi-person-circle"></i></h3> Личный кабинет</a></h7></center>
                </li>
                <li class="nav-item">
                    <center><h7><a class="nav-link active" aria-current="page" href="#"><h3><i class="bi bi-patch-question"></i></h3> Поддержка</a></h7></center>
                </li>
            </ul>
        </div>
        <form class="d-flex" onsubmit="return false">
            <input id="search_param" class="form-control me-2 px-3" type="search" placeholder="Поиск" aria-label="Искать на Eather">
            <button id="search" class="btn btn-outline-success" onclick="vtik()"><i class="bi bi-search"></i></button>
        </form>
    </div>
</nav>