document.addEventListener('DOMContentLoaded', async function() {
  const api = window.eduSyncAPI;
  try {
    const user = api.user || {}; document.getElementById('studentName').textContent = user.name || 'Alumno';
    const [coursesResp, assignmentsResp, gradesResp] = await Promise.all([
      api.getMyCourses().catch(()=>({data:[]})),
      api.getMyAssignments().catch(()=>({data:{data:[]}})),
      api.getMyGrades().catch(()=>({data:{data:[]}}))
    ]);
    const courses = coursesResp.data || [];
    const assignments = (assignmentsResp.data && assignmentsResp.data.data) ? assignmentsResp.data.data : [];
    const grades = (gradesResp.data && gradesResp.data.data) ? gradesResp.data.data : [];
    document.getElementById('activeCourses').textContent = courses.length;
    const now = new Date();
    const pending = assignments.filter(a => new Date(a.due_date) > now);
    document.getElementById('pendingTasks').textContent = pending.length;
    const average = grades.length ? (grades.reduce((s,g)=> s + (parseFloat(g.score)||0),0) / grades.length).toFixed(2) : 'N/A';
    document.getElementById('averageGrade').textContent = average;
    const list = document.getElementById('coursesList'); list.innerHTML='';
    courses.forEach(c=>{ const div=document.createElement('div'); div.className='course-card'; div.innerHTML=`<h4>${c.nombre}</h4><p>Código: ${c.codigo_curso}</p><p>Tareas próximas: ${c.active_assignments}</p><p>Próxima entrega: ${c.next_due_date ? new Date(c.next_due_date).toLocaleDateString('es-ES') : '—'}</p>`; list.appendChild(div); });
    const tbody = document.querySelector('#tasksTable tbody'); tbody.innerHTML='';
    pending.slice(0,10).forEach(t=>{ const tr=document.createElement('tr'); tr.innerHTML=`<td>${t.course?.nombre||'Curso'}</td><td>${t.title}</td><td>${new Date(t.due_date).toLocaleDateString('es-ES')}</td><td class="status-pending">Pendiente</td>`; tbody.appendChild(tr); });
  } catch (e) {
    document.getElementById('studentName').textContent = 'Error al cargar datos';
    console.error(e);
  }
});