<div class="container">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card chat-app shadow-35-10">
                <div id="plist" class="people-list">
                    <button type="button" class="btn btn-primary" onclick="newChat();" style="width: 100%">Создать обращение</button>
                    <hr>
                    <!--<div class="input-group">
                        <label for="search_chat_id"></label>
                        <input type="text" class="form-control" id="search_chat_id" placeholder="Поиск обращений">
                        <span class="input-group-text cursor-pointer" onclick="search_ticket()"><i class="fa fa-search"></i></span>
                    </div>
                    <hr>-->
                    <ul class="list-unstyled chat-list mt-2 mb-0" id="chat_list">
                    </ul>
                </div>
                <div class="chat" id="chat_main" chat_id="0">
                    <div class="chat-header clearfix" id="chat_header">
                        <button type="button" id="back_button" class="btn btn-secondary float-right" onclick="hideChat()"><i class="bi bi-x-lg"></i></button>
                        <div class="row">
                            <div class="col-lg-6">
                                <img src="logo/logo-chat.jpg" alt="avatar">
                                <div class="chat-about">
                                    <h6 class="m-b-0">Техническая поддержка</h6>
                                    <small><i class="fa fa-circle online"></i> Отвечаем 24/7</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-history" id="chat_messages">
                        <ul class="m-b-0" id="chat_history_id">
                            <li class="clearfix">
                                <div class="message-data">
                                    <span class="message-data-time">Системное сообщение</span>
                                </div>
                                <div class="message other-message shadow-35-10">Вас приветствует техническая поддержка интернет-магазина EATHER. На данной странице вы можете задать интересующие вас вопросы касательно нашего интернет-магазина. Для этого выберите созданное ранее обращение в меню или создайте новое при помощи соответствующей кнопки.</div>
                            </li>
                        </ul>
                    </div>
                    <div class="chat-message clearfix" id="chat_input">
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>