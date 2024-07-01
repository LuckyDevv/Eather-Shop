<?php
function getTemplate(string $template): false|string
{
    ob_start();
    include('../tpl/' . $template . ".tpl");
    return ob_get_clean();
}
switch($_POST['type'] ?? 'null'){
    case 'get_modals':
        die(json_encode([
            'auth' => (string) getTemplate('modal_auth'),
            'reg' => (string) getTemplate('modal_reg')],
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
    case 'get_header':
        die(json_encode([
            'header' => (string) getTemplate('header')],
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
    case 'get_header_mobile':
        die(json_encode([
            'header' => (string) getTemplate('header_mobile'),
            'search' => (string) getTemplate('search')],
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
}
die();