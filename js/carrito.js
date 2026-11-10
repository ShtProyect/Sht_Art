(function(){
 const $=s=>document.querySelector(s), cfg=window.SHT_CART, drawer=$('#cart-drawer'), backdrop=$('#cart-backdrop');
 let cart=cfg.items||{}, products={};
 document.querySelectorAll('.add-to-cart').forEach(b=>products[b.dataset.id]={
  id:b.dataset.id,name:b.closest('.product-card').querySelector('h3').textContent,
  price:Number(b.closest('.product-card').querySelector('.product-price').textContent.replace(/[^0-9]/g,'')),
  image:b.closest('.product-card').querySelector('img').src
 });
 const fmt=n=>'$'+Number(n).toLocaleString('es-CL');
 async function update(action,id,qty){
  const r=await fetch('cart_api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action,id,qty,csrf:cfg.csrf})});
  const d=await r.json(); if(!r.ok) throw new Error(d.error||'No se pudo actualizar el carrito'); cart=d.cart; render();
 }
 function render(){
  const list=$('#cart-list'); list.innerHTML=''; let total=0,count=0;
  Object.entries(cart).forEach(([id,qty])=>{const p=products[id]; if(!p)return; total+=p.price*qty;count+=qty;
   const li=document.createElement('li'); li.dataset.id=id;
   const info=document.createElement('div'), strong=document.createElement('strong'), small=document.createElement('small');
   strong.textContent=p.name; small.textContent=fmt(p.price)+' c/u'; info.append(strong,small);
   const controls=document.createElement('div');controls.className='cart-qty';
   [['minus','−'],['qty',String(qty)],['plus','+'],['remove','×']].forEach(([act,label])=>{if(act==='qty'){const span=document.createElement('span');span.textContent=label;controls.append(span)}else{const b=document.createElement('button');b.type='button';b.dataset.act=act;b.textContent=label;b.setAttribute('aria-label',act);controls.append(b)}});
   li.append(info,controls);list.append(li);
  });
  $('#cart-total').textContent=fmt(total);$('#cart-count').textContent=count;$('#cart-empty').hidden=count>0;$('#checkout').style.pointerEvents=count?'auto':'none';$('#checkout').style.opacity=count?'1':'.45';
 }
 function open(v){drawer.hidden=!v;backdrop.hidden=!v;document.body.classList.toggle('cart-open',v)}
 document.querySelectorAll('.add-to-cart').forEach(b=>b.addEventListener('click',async()=>{try{await update('add',b.dataset.id);open(true)}catch(e){alert(e.message)}}));
 $('#cart-list').addEventListener('click',async e=>{const a=e.target.dataset.act;if(!a)return;const id=e.target.closest('li').dataset.id;try{if(a==='remove')await update('remove',id);else await update('set',id,(cart[id]||0)+(a==='plus'?1:-1))}catch(err){alert(err.message)}});
 $('.cart-toggle').addEventListener('click',()=>open(true));$('.cart-close').addEventListener('click',()=>open(false));backdrop.addEventListener('click',()=>open(false));document.addEventListener('keydown',e=>{if(e.key==='Escape')open(false)});
 document.querySelectorAll('.chip').forEach(c=>c.addEventListener('click',()=>{document.querySelectorAll('.chip').forEach(x=>x.classList.toggle('is-active',x===c));document.querySelectorAll('.product-card').forEach(p=>p.hidden=c.dataset.filter!=='all'&&p.dataset.type!==c.dataset.filter)}));
 render();
})();
