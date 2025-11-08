document.addEventListener('DOMContentLoaded',()=>{
  const api=window.eduSyncAPI; const token=localStorage.getItem('edusync_token');
  const adminCheck=document.getElementById('admin-check');
  const ensureAdmin=async()=>{ try{ const u=await api.getUser(); if(u.role!=='admin'){adminCheck.textContent='Acceso sólo para administradores.'; adminCheck.style.display='block'; throw new Error('Not admin');} }catch(e){ console.error(e); } };
  ensureAdmin();

  const attach=(form, buildPayload, url, successKey)=>{
    form.addEventListener('submit', async e=>{
      e.preventDefault(); const msg=form.querySelector('.msg'); msg.style.display='none'; msg.className='msg'; msg.textContent='';
      const btn=form.querySelector('button'); btn.disabled=true; const original=btn.textContent; btn.textContent='Procesando...';
      try{
        const payload=buildPayload(new FormData(form));
        const res=await fetch(window.eduSyncAPI.baseURL+'/'+url,{method:'POST',headers:{'Content-Type':'application/json; charset=UTF-8',Authorization:'Bearer '+token,Accept:'application/json'},body:JSON.stringify(payload)});
        const text=await res.text();
        let data;
        try { 
          data=JSON.parse(text); 
        } catch(jsonErr) { 
          console.error('Respuesta no-JSON:', text.substring(0,500)); 
          throw new Error('Error del servidor. Ver consola.'); 
        }
        if(!res.ok){ throw new Error(data.error||data.message||JSON.stringify(data.errors)||'Error'); }
        msg.textContent=(data.message||'Creado correctamente')+ (data.email? ' | '+data.email:'');
        msg.classList.add('ok'); msg.style.display='block'; form.reset();
      }catch(err){ msg.textContent=err.message||'Fallo'; msg.classList.add('err'); msg.style.display='block'; }
      finally{ btn.disabled=false; btn.textContent=original; }
    });
  };

  // Docente
  attach(document.getElementById('form-docente'), fd=>({
    name: fd.get('name'), password: fd.get('password'), especialidad: fd.get('especialidad')||null, telefono: fd.get('telefono')||null, bio: fd.get('bio')||null
  }), 'teachers', 'teacher');

  // Tutor
  attach(document.getElementById('form-tutor'), fd=>({
    name: fd.get('name'), password: fd.get('password')
  }), 'guardians', 'guardian');

  // Alumno (usa /register para crear user + student)
  attach(document.getElementById('form-alumno'), fd=>({
    name: fd.get('name'), password: fd.get('password'), telefono: fd.get('telefono')||null, fecha_nacimiento: fd.get('fecha_nacimiento')||null, direccion: fd.get('direccion')||null
  }), 'register', 'student');

  // Mostrar lista de usuarios
  const loadUsers=async()=>{
    const list=document.getElementById('users-list');
    try{
      const res=await fetch(window.eduSyncAPI.baseURL+'/users/all',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}});
      if(!res.ok) throw new Error('No se pudo cargar usuarios');
      const users=await res.json();
      if(!users || users.length===0){ list.innerHTML='<p style="color:#666;font-size:.85rem">No hay usuarios registrados.</p>'; return; }
      let html='<table style="width:100%;border-collapse:collapse;font-size:.8rem"><thead><tr style="background:#033f4e;color:#fff"><th style="padding:8px;text-align:left">ID</th><th style="padding:8px;text-align:left">Nombre</th><th style="padding:8px;text-align:left">Email</th><th style="padding:8px;text-align:left">Rol</th><th style="padding:8px;text-align:left">Creado</th></tr></thead><tbody>';
      users.forEach(u=>{ html+=`<tr style="border-bottom:1px solid #ddd"><td style="padding:8px">${u.id}</td><td style="padding:8px">${u.name}</td><td style="padding:8px"><code style="font-size:.75rem">${u.email}</code></td><td style="padding:8px"><span style="background:#e3f7ed;color:#0a7a3f;padding:3px 8px;border-radius:4px;font-size:.7rem;font-weight:600">${u.role}</span></td><td style="padding:8px;color:#666">${new Date(u.created_at).toLocaleDateString()}</td></tr>`; });
      html+='</tbody></table>';
      list.innerHTML=html;
    }catch(err){ list.innerHTML='<p style="color:#c0392b;font-size:.85rem">Error: '+err.message+'</p>'; }
  };
  document.getElementById('refresh-users').addEventListener('click',loadUsers);
  loadUsers();
});