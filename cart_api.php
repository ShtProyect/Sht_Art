<?php
require 'config/database.php'; require 'includes/auth.php'; require 'includes/csrf.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); echo json_encode(['error'=>'Método no permitido']); exit; }
$input=json_decode(file_get_contents('php://input'),true) ?: [];
if (!hash_equals($_SESSION['csrf']??'', $input['csrf']??'')) { http_response_code(403); echo json_encode(['error'=>'Sesión vencida. Recarga la página.']); exit; }
if (!isset($_SESSION['cart'])) $_SESSION['cart']=[];
$action=$input['action']??''; $id=(int)($input['id']??0);
try {
 if ($action==='add' || $action==='set') {
  $s=$pdo->prepare("SELECT id,stock,activo FROM productos WHERE id=?"); $s->execute([$id]); $p=$s->fetch();
  if (!$p || !$p['activo']) throw new RuntimeException('Producto no disponible.');
  $qty=$action==='add'?(int)($_SESSION['cart'][$id]??0)+1:max(0,min(99,(int)($input['qty']??0)));
  if ($qty>(int)$p['stock']) throw new RuntimeException('No hay stock suficiente.');
  if ($qty===0) unset($_SESSION['cart'][$id]); else $_SESSION['cart'][$id]=$qty;
 } elseif ($action==='remove') unset($_SESSION['cart'][$id]);
 elseif ($action==='clear') $_SESSION['cart']=[];
 else throw new RuntimeException('Acción no válida.');
 echo json_encode(['ok'=>true,'cart'=>$_SESSION['cart']]);
} catch(Throwable $e) { http_response_code(400); echo json_encode(['error'=>$e->getMessage()]); }
