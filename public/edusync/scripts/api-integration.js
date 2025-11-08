// API Integration (migrated) - same host usage
const API_CONFIG = { BASE_URL: window.location.origin + '/api', TIMEOUT: 10000 };
class EduSyncAPI {
  constructor(){ this.token=localStorage.getItem('edusync_token'); this.user=JSON.parse(localStorage.getItem('edusync_user')||'{}'); this.baseURL=API_CONFIG.BASE_URL; }
  async request(endpoint, options={}){
    const url = `${this.baseURL}${endpoint}`; const controller=new AbortController(); const t=setTimeout(()=>controller.abort(),API_CONFIG.TIMEOUT);
    const final={ headers:{'Content-Type':'application/json','Accept':'application/json',...(this.token?{Authorization:`Bearer ${this.token}`}:{})}, signal:controller.signal, ...options };
    try { const resp=await fetch(url, final); let data=null; try{ data=await resp.json(); }catch(_){} if(!resp.ok){ const e=new Error((data&&data.error)||`HTTP ${resp.status}`); e.status=resp.status; e.details=data; throw e; } return data; } catch(err){ if(err.name==='AbortError') err=new Error('Timeout de solicitud'); if(err.status===401) this.handleAuthError(); throw err; } finally { clearTimeout(t); }
  }
  handleAuthError(){ localStorage.removeItem('edusync_token'); localStorage.removeItem('edusync_user'); window.location.href='login.html'; }
  async login(credentials){ const r=await this.request('/login',{method:'POST',body:JSON.stringify(credentials)}); if(r.token){ this.token=r.token; localStorage.setItem('edusync_token',r.token);} if(r.user){ this.user=r.user; localStorage.setItem('edusync_user',JSON.stringify(r.user)); } return r; }
  async register(data){ const r=await this.request('/register',{method:'POST',body:JSON.stringify(data)}); if(r.token){ this.token=r.token; localStorage.setItem('edusync_token',r.token);} if(r.user){ this.user=r.user; localStorage.setItem('edusync_user',JSON.stringify(r.user)); } return r; }
  async verifyToken(){ if(!this.token) return false; try{ const u=await this.request('/user'); if(u){ this.user=u; localStorage.setItem('edusync_user',JSON.stringify(u)); } return !!u; }catch(_){ return false; } }
  async getMyAssignments(){ return await this.request('/my/assignments'); }
  async getMyGrades(){ return await this.request('/my/grades'); }
  async getMyCourses(){ return await this.request('/my/courses'); }
  async getCourses(){ return await this.request('/courses'); }
  async logout(){ try{ await this.request('/logout',{method:'POST'}); }catch(_){} finally{ localStorage.removeItem('edusync_token'); localStorage.removeItem('edusync_user'); this.token=null; this.user={}; } }
  // Admin endpoints
  async createTeacher(payload){ return await this.request('/teachers',{method:'POST',body:JSON.stringify(payload)}); }
  async createGuardian(payload){ return await this.request('/guardians',{method:'POST',body:JSON.stringify(payload)}); }
  async listTeachers(){ return await this.request('/teachers'); }
  async listGuardians(){ return await this.request('/guardians'); }
  async listStudents(){ return await this.request('/students'); }
}
window.eduSyncAPI=new EduSyncAPI();
