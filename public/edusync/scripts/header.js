// Header reutilizable para todas las páginas
document.addEventListener('DOMContentLoaded',()=>{
  const api=window.eduSyncAPI; const container=document.createElement('header'); container.className='eds-header';
  const brand=document.createElement('div'); brand.className='eds-brand'; brand.innerHTML='<img src="images/logo.svg" alt="Logo"><span>EduSync</span>'; container.appendChild(brand);
  const nav=document.createElement('nav'); nav.className='eds-nav';
  const links=[
    {href:'DashboardAdmin.html',text:'Admin',roles:['admin']},
    {href:'DashboardAlumno.html',text:'Alumno',roles:['student']},
    {href:'DashboardDocente.html',text:'Docente',roles:['teacher']},
    {href:'DashboardPadres.html',text:'Padres',roles:['guardian']},
    {href:'cursos.html',text:'Cursos',roles:['student','teacher']},
    {href:'tareas.html',text:'Tareas',roles:['student']},
    {href:'calificaciones.html',text:'Calificaciones',roles:['student']},
    {href:'perfil.html',text:'Perfil',roles:['student','teacher','guardian','admin']},
    {href:'manage-users.html',text:'Crear Usuarios',roles:['admin']},
    {href:'usuarios-registrados.html',text:'👥 Ver Usuarios',roles:['admin']},
  ];
  const role=(api.user&&api.user.role)||null;
  links.filter(l=>!role || l.roles.includes(role)).forEach(l=>{ const a=document.createElement('a'); a.href=l.href; a.textContent=l.text; nav.appendChild(a); });
  container.appendChild(nav);
  const logoutBtn=document.createElement('button'); logoutBtn.className='eds-logout'; logoutBtn.textContent='Salir'; logoutBtn.addEventListener('click',async()=>{await api.logout(); location.href='login.html';}); container.appendChild(logoutBtn);
  document.body.prepend(container);
});