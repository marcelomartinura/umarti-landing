<?php
// Recibe el formulario de la landing y lo envía por email.
// CONFIGURAR: casilla que recibe los contactos.
const DESTINO   = 'CAMBIAR@umartidigital.com';
const REMITENTE = 'no-responder@umartidigital.com';

function volver($ok) {
    header('Location: /?enviado=' . ($ok ? '1' : '0') . '#contacto', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') volver(false);

// Trampa para bots: este campo está oculto para las personas.
if (!empty($_POST['sitio'])) volver(true);

$limpiar = function ($v, $max = 300) {
    $v = trim((string)($v ?? ''));
    $v = str_replace(["\r", "\n"], ' ', $v);
    return mb_substr($v, 0, $max);
};

$nombre   = $limpiar($_POST['nombre'] ?? '');
$empresa  = $limpiar($_POST['empresa'] ?? '');
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$telefono = $limpiar($_POST['telefono'] ?? '', 40);
$pais     = $limpiar($_POST['pais'] ?? '', 40);
$interes  = ($_POST['interes'] ?? '') === 'presentacion' ? 'Recibir la presentación comercial' : 'Agendar una llamada de 20 minutos';
$mensaje  = mb_substr(trim((string)($_POST['mensaje'] ?? '')), 0, 3000);

if ($nombre === '' || $empresa === '' || !$email) volver(false);

$asunto = "Nuevo contacto de consultoría: $empresa ($pais) - $interes";
$cuerpo = "Quiere: $interes\nNombre: $nombre\nEmpresa: $empresa\nEmail: $email\nWhatsApp: $telefono\nPaís: $pais\n\nMensaje:\n$mensaje\n";
$cabeceras = "From: Umarti Digital <" . REMITENTE . ">\r\n"
           . "Reply-To: $email\r\n"
           . "Content-Type: text/plain; charset=UTF-8\r\n";

$ok = mail(DESTINO, '=?UTF-8?B?' . base64_encode($asunto) . '?=', $cuerpo, $cabeceras);
volver($ok);
