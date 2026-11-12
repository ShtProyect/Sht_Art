<?php
require 'config/database.php'; require 'includes/auth.php'; require 'includes/csrf.php'; require_admin(); require_once __DIR__ . '/includes/media.php';
$msg=''; $err='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $action=$_POST['action']??'';
 try {
  if ($action==='save_artwork') {
   $id=max(0,(int)($_POST['artwork_id']??0));
   $title=trim((string)($_POST['titulo']??''));
   $description=trim((string)($_POST['descripcion_obra']??''));
   if ($title==='' || strlen($title)>200) throw new RuntimeException('Indica un título de hasta 100 caracteres.');
   $oldFile=null;
   if ($id) {
    $q=$pdo->prepare('SELECT archivo FROM dibujos WHERE id=?'); $q->execute([$id]);
    $oldFile=$q->fetchColumn();
    if ($oldFile===false) throw new RuntimeException('El dibujo ya no existe.');
   }
   $hasFile=isset($_FILES['archivo_obra']) && ($_FILES['archivo_obra']['name']??'')!=='';
   if (!$id && !$hasFile) throw new RuntimeException('Selecciona una imagen para el dibujo.');
   $mime=null; $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
   if ($hasFile) {
    $f=$_FILES['archivo_obra'];
    if ($f['error']!==UPLOAD_ERR_OK || $f['size']<1 || $f['size']>8*1024*1024 || !is_uploaded_file($f['tmp_name'])) {
     throw new RuntimeException('El archivo debe ser una imagen de máximo 8 MB.');
    }
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset($types[$mime])) throw new RuntimeException('Usa JPG, PNG, WEBP o GIF.');
   }
   $newAbsolute=null; $pdo->beginTransaction();
   try {
    if (!$id) {
     $q=$pdo->prepare('INSERT INTO dibujos(titulo,descripcion,archivo,subido_por) VALUES(?,?,?,?)');
     $q->execute([$title,$description,'pendiente',(int)$_SESSION['user_id']]);
     $id=(int)$pdo->lastInsertId();
    }
    $storedFile=$oldFile;
    if ($hasFile) {
     $dir=__DIR__.'/uploads/artworks';
     if (!is_dir($dir) && !mkdir($dir,0755,true) && !is_dir($dir)) throw new RuntimeException('No se pudo crear el directorio de imágenes.');
     $ascii=function_exists('iconv') ? iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$title) : $title;
     if ($ascii===false) $ascii=$title;
     $slug=trim(strtolower((string)preg_replace('/[^a-zA-Z0-9]+/','-',$ascii)),'-');
     $slug=substr($slug?:'dibujo',0,48);
     $newName='obra-'.$id.'-'.$slug.'-'.bin2hex(random_bytes(6)).'.'.$types[$mime];
     $newAbsolute=$dir.'/'.$newName;
     if (!move_uploaded_file($f['tmp_name'],$newAbsolute)) throw new RuntimeException('No se pudo guardar la imagen.');
     $storedFile='artworks/'.$newName;
    }
    $q=$pdo->prepare('UPDATE dibujos SET titulo=?,descripcion=?,archivo=? WHERE id=?');
    $q->execute([$title,$description,$storedFile,$id]);
    $pdo->commit();
   } catch(Throwable $uploadError) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if ($newAbsolute!==null && is_file($newAbsolute)) unlink($newAbsolute);
    throw $uploadError;
   }
   // Solo borrar el archivo anterior después de confirmar el cambio en la base de datos.
   if ($hasFile && $oldFile!==null && $oldFile!==$storedFile) {
    $oldPath=artwork_storage_path((string)$oldFile);
    if ($oldPath!==null && is_file($oldPath)) unlink($oldPath);
   }
   $msg='Dibujo #'.$id.' guardado. Visible en la galería de inicio.';
  } elseif ($action==='delete_artwork') {
   $id=(int)($_POST['artwork_id']??0); $q=$pdo->prepare('SELECT archivo FROM dibujos WHERE id=?'); $q->execute([$id]); $file=$q->fetchColumn();
   if (!$file) throw new RuntimeException('El dibujo no existe.');
   $pdo->prepare('DELETE FROM dibujos WHERE id=?')->execute([$id]);
   { $path=artwork_storage_path((string)$file); if ($path!==null && is_file($path)) unlink($path); }
   $msg='Dibujo eliminado de la galería.';
  } elseif ($action==='save_product') {
   $id=(int)($_POST['id']??0); $name=trim($_POST['nombre']??''); $desc=trim($_POST['descripcion']??'');
   $cat=trim($_POST['categoria']??'Print'); $price=(int)($_POST['precio']??0); $stock=(int)($_POST['stock']??0);
   $image=trim($_POST['imagen_actual']??'images/demo/obra-01.svg');
   if ($name==='' || $price<1 || $stock<0) throw new RuntimeException('Revisa nombre, precio y stock.');
   if (!empty($_FILES['imagen']['name'])) {
    $f=$_FILES['imagen']; $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    if ($f['error']!==UPLOAD_ERR_OK || $f['size']>5*1024*1024 || !isset($types[$mime])) throw new RuntimeException('Imagen inválida. Usa JPG, PNG, WEBP o GIF de máximo 5 MB.');
    if (!is_dir(__DIR__.'/uploads/products')) mkdir(__DIR__.'/uploads/products',0755,true);
    $filename=bin2hex(random_bytes(16)).'.'.$types[$mime];
    if (!move_uploaded_file($f['tmp_name'],__DIR__.'/uploads/products/'.$filename)) throw new RuntimeException('No se pudo guardar la imagen.');
    $image='uploads/products/'.$filename;
   }
   if ($id) { $s=$pdo->prepare("UPDATE productos SET nombre=?,descripcion=?,categoria=?,precio=?,stock=?,imagen=? WHERE id=?"); $s->execute([$name,$desc,$cat,$price,$stock,$image,$id]); }
   else { $s=$pdo->prepare("INSERT INTO productos(nombre,descripcion,categoria,precio,stock,imagen) VALUES(?,?,?,?,?,?)"); $s->execute([$name,$desc,$cat,$price,$stock,$image]); }
   $msg='Producto guardado correctamente.';
  } elseif ($action==='toggle_product') {
   $pdo->prepare("UPDATE productos SET activo=1-activo WHERE id=?")->execute([(int)$_POST['id']]); $msg='Estado del producto actualizado.';
  } elseif ($action==='delete_product') {
   $pdo->prepare("DELETE FROM productos WHERE id=?")->execute([(int)$_POST['id']]); $msg='Producto eliminado.';
  } elseif ($action==='order_status') {
   $valid=['pendiente','pagado','preparando','enviado','entregado','cancelado']; $status=$_POST['estado']??'';
   if (!in_array($status,$valid,true)) throw new RuntimeException('Estado no válido.');
   $pdo->prepare("UPDATE pedidos SET estado=? WHERE id=?")->execute([$status,(int)$_POST['id']]); $msg='Pedido actualizado.';
  }
 } catch(Throwable $e) { $err=$e instanceof RuntimeException?$e->getMessage():'No se pudo completar la operación.'; }
}
$edit=null;
if(isset($_GET['edit_artwork'])) { $s=$pdo->prepare('SELECT * FROM dibujos WHERE id=?'); $s->execute([(int)$_GET['edit_artwork']]); $edit_artwork=$s->fetch(); } else { $edit_artwork=null; }
$artworks=$pdo->query('SELECT * FROM dibujos ORDER BY created_at DESC')->fetchAll();
if(isset($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM productos WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }
$products=$pdo->query("SELECT * FROM productos ORDER BY created_at DESC")->fetchAll();
$orders=$pdo->query("SELECT * FROM pedidos ORDER BY created_at DESC LIMIT 100")->fetchAll();
$stats=[
 'products'=>(int)$pdo->query("SELECT COUNT(*) FROM productos WHERE activo=1")->fetchColumn(),
 'orders'=>(int)$pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn(),
 'pending'=>(int)$pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado='pendiente'")->fetchColumn(),
 'sales'=>(int)$pdo->query("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE estado IN ('pagado','preparando','enviado','entregado')")->fetchColumn()
];
?><!doctype html><html lang="es" data-theme="dark"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Administración — SHT</title><link rel="stylesheet" href="css/style.css"><link rel="stylesheet" href="css/tienda.css"><link rel="stylesheet" href="css/admin.css"></head><body>
<header class="site-header admin-header"><a class="brand" href="index.php"><span class="brand-mark">S</span><span>SHT<span class="brand-dot">.</span></span></a><button class="menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="navegacion-principal">☰</button><nav class="main-nav" id="navegacion-principal" aria-label="Navegación de administración"><a href="index.php">Ver portafolio</a><a href="tienda.php">Ver tienda</a><a href="cambiar_password.php">Cambiar contraseña</a></nav><div class="header-actions"><span class="admin-user"><?=h($_SESSION['username'])?></span><a class="login-btn" href="logout.php">Cerrar sesión</a></div></header>
<main class="admin-wrap"><div class="admin-title"><div><p class="eyebrow">GESTIÓN DE LA TIENDA</p><h1>Panel de <em>administración</em></h1><p class="admin-sub">Administra el catálogo, controla el inventario y actualiza los pedidos.</p></div></div>
<?php if($msg):?><p class="admin-alert success"><?=h($msg)?></p><?php endif;?><?php if($err):?><p class="admin-alert error"><?=h($err)?></p><?php endif;?>
<section class="stats-grid"><article class="stat"><small>Productos activos</small><strong><?= $stats['products']?></strong></article><article class="stat"><small>Dibujos publicados</small><strong><?=count($artworks)?></strong></article><article class="stat"><small>Pedidos recibidos</small><strong><?=$stats['orders']?></strong></article><article class="stat"><small>Pedidos pendientes</small><strong><?=$stats['pending']?></strong></article><article class="stat"><small>Ventas confirmadas</small><strong><?=clp($stats['sales'])?></strong></article></section>
<section class="admin-section" id="productos"><div class="admin-section-head"><div><p class="eyebrow">01 — CATÁLOGO DE LA TIENDA</p><h2><?= $edit?'Editar producto':'Agregar producto'?></h2></div><a class="text-link" href="admin.php#productos">Nuevo producto ↗</a></div>
<form class="product-form" method="post" enctype="multipart/form-data"><?=csrf_field()?><input type="hidden" name="action" value="save_product"><input type="hidden" name="id" value="<?=h($edit['id']??0)?>"><input type="hidden" name="imagen_actual" value="<?=h($edit['imagen']??'images/demo/obra-01.svg')?>">
<label>Nombre<input name="nombre" required maxlength="140" value="<?=h($edit['nombre']??'')?>"></label><label>Categoría<select name="categoria"><?php foreach(['Obra original','Print','Stickers','Pines','Otros'] as $c):?><option <?=($edit['categoria']??'Print')===$c?'selected':''?>><?=h($c)?></option><?php endforeach;?></select></label><label>Precio (CLP)<input name="precio" type="number" min="1" step="1" required value="<?=h($edit['precio']??'')?>"></label><label>Stock disponible<input name="stock" type="number" min="0" required value="<?=h($edit['stock']??0)?>"></label><label class="wide">Descripción<textarea name="descripcion" rows="3"><?=h($edit['descripcion']??'')?></textarea></label><label class="wide">Imagen del producto<input type="file" name="imagen" accept="image/jpeg,image/png,image/webp,image/gif"><small>Opcional. JPG, PNG, WEBP o GIF, máximo 5 MB. Si no seleccionas una imagen se conserva la actual.</small></label><div class="wide form-actions"><button class="button button-primary" type="submit">Guardar producto</button><?php if($edit):?><a class="text-link" href="admin.php#productos">Cancelar edición</a><?php endif;?></div></form>
<div class="table-scroll"><table class="admin-table"><thead><tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th>Acciones</th></tr></thead><tbody><?php foreach($products as $p):?><tr><td><div class="product-cell"><img src="<?=h(product_public_url((string)$p['imagen']))?>" alt=""><span><?=h($p['nombre'])?></span></div></td><td><?=h($p['categoria'])?></td><td><?=clp($p['precio'])?></td><td><?=$p['stock']?></td><td><?=$p['activo']?'Activo':'Oculto'?></td><td class="actions"><a href="admin.php?edit=<?=$p['id']?>#productos">Editar</a><form method="post"><?=csrf_field()?><input type="hidden" name="action" value="toggle_product"><input type="hidden" name="id" value="<?=$p['id']?>"><button> <?=$p['activo']?'Ocultar':'Publicar'?> </button></form><form method="post" onsubmit="return confirm('¿Eliminar este producto?')"><?=csrf_field()?><input type="hidden" name="action" value="delete_product"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="danger">Eliminar</button></form></td></tr><?php endforeach;?></tbody></table></div></section>
<section class="admin-section" id="dibujos"><div class="admin-section-head"><div><p class="eyebrow">02 — PORTAFOLIO</p><h2><?= $edit_artwork?'Editar dibujo':'Agregar dibujo a la portada'?></h2><p class="admin-sub">Los dibujos publicados aquí aparecen automáticamente en la galería de la página de inicio.</p></div><a class="text-link" href="admin.php#dibujos">Nuevo dibujo ↗</a></div>
<form class="product-form" method="post" enctype="multipart/form-data"><?=csrf_field()?><input type="hidden" name="action" value="save_artwork"><input type="hidden" name="artwork_id" value="<?=h($edit_artwork['id']??0)?>">
<label class="wide">Título del dibujo<input name="titulo" required maxlength="100" value="<?=h($edit_artwork['titulo']??'')?>"></label><label class="wide">Descripción<textarea name="descripcion_obra" rows="3"><?=h($edit_artwork['descripcion']??'')?></textarea></label><label class="wide">Imagen del dibujo<input type="file" name="archivo_obra" accept="image/jpeg,image/png,image/webp,image/gif" <?=$edit_artwork?'':'required'?>><small>JPG, PNG, WEBP o GIF, máximo 8 MB. Al editar, puedes dejarlo vacío para conservar la imagen actual.</small></label><?php if($edit_artwork):?><div class="wide"><small>Imagen actual:</small><br><img class="artwork-preview" src="<?=h(artwork_public_url((string)$edit_artwork['archivo']))?>" alt="Imagen actual"></div><?php endif;?><div class="wide form-actions"><button class="button button-primary" type="submit">Guardar dibujo</button><?php if($edit_artwork):?><a class="text-link" href="admin.php#dibujos">Cancelar edición</a><?php endif;?></div></form>
<div class="table-scroll"><table class="admin-table"><thead><tr><th>ID</th><th>Dibujo</th><th>Descripción</th><th>Fecha de publicación</th><th>Acciones</th></tr></thead><tbody><?php if(!$artworks):?><tr><td colspan="5">Todavía no hay dibujos publicados.</td></tr><?php endif;?><?php foreach($artworks as $a):?><tr><td>#<?= (int)$a['id'] ?></td><td><div class="product-cell"><img src="<?=h(artwork_public_url((string)$a['archivo']))?>" alt=""><span><?=h($a['titulo'])?></span></div></td><td><?=h($a['descripcion'])?></td><td><?=date('d/m/Y',strtotime($a['created_at']))?></td><td class="actions"><a href="admin.php?edit_artwork=<?=$a['id']?>#dibujos">Editar</a><form method="post" onsubmit="return confirm('¿Eliminar este dibujo de la página de inicio?')"><?=csrf_field()?><input type="hidden" name="action" value="delete_artwork"><input type="hidden" name="artwork_id" value="<?=$a['id']?>"><button class="danger">Eliminar</button></form></td></tr><?php endforeach;?></tbody></table></div></section>
<section class="admin-section" id="pedidos"><div class="admin-section-head"><div><p class="eyebrow">02 — VENTAS</p><h2>Pedidos recibidos</h2></div></div>
<div class="table-scroll"><table class="admin-table"><thead><tr><th>N.º</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Pago</th><th>Estado / actualizar</th></tr></thead><tbody><?php if(!$orders):?><tr><td colspan="6">Todavía no hay pedidos.</td></tr><?php endif;?><?php foreach($orders as $o):?><tr><td>#<?=$o['id']?></td><td><?=h($o['nombre_cliente'])?><small class="cell-sub"><?=h($o['email'])?></small></td><td><?=date('d/m/Y H:i',strtotime($o['created_at']))?></td><td><?=clp($o['total'])?></td><td><?=h($o['metodo_pago'])?></td><td><form class="status-form" method="post"><?=csrf_field()?><input type="hidden" name="action" value="order_status"><input type="hidden" name="id" value="<?=$o['id']?>"><select name="estado"><?php foreach(['pendiente','pagado','preparando','enviado','entregado','cancelado'] as $st):?><option value="<?=$st?>" <?=$o['estado']===$st?'selected':''?>><?=ucfirst($st)?></option><?php endforeach;?></select><button type="submit">Guardar</button></form></td></tr><?php endforeach;?></tbody></table></div></section>
</main><footer class="site-footer"><a class="brand" href="index.php"><span class="brand-mark">S</span><span>SHT.</span></a><p>Administración SHT.</p><a class="back-top" href="#">VOLVER ARRIBA ↑</a></footer><script src="js/app.js"></script></body></html>
