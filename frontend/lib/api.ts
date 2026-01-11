const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://51.210.149.249:8080/api';

async function fetchApi(endpoint: string, options: RequestInit = {}) {
  // Get auth token from localStorage (if available)
  const token = typeof window !== 'undefined' ? localStorage.getItem('auth_token') : null;

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
      ...options.headers,
    },
  });

  if (!response.ok) {
    // Handle 401 Unauthorized - redirect to login
    if (response.status === 401 && typeof window !== 'undefined') {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      window.location.href = '/login';
      return;
    }

    const error = await response.json().catch(() => ({ error: 'An error occurred' }));
    throw new Error(error.error || 'An error occurred');
  }

  return response.json();
}

// Routes API
export const routesApi = {
  getAll: () => fetchApi('/routes'),
  create: (data: any) => fetchApi('/routes', { method: 'POST', body: JSON.stringify(data) }),
};

// Resources API
export const resourcesApi = {
  getAll: (params?: { type?: string; active_only?: boolean }) => {
    const query = new URLSearchParams();
    if (params?.type) query.append('type', params.type);
    if (params?.active_only) query.append('active_only', '1');
    return fetchApi(`/resources?${query}`);
  },
  create: (data: any) => fetchApi('/resources', { method: 'POST', body: JSON.stringify(data) }),
};

// Planning Requests API (Team A)
export const planningRequestsApi = {
  getAll: (params?: { status?: string }) => {
    const query = new URLSearchParams();
    if (params?.status) query.append('status', params.status);
    return fetchApi(`/planning-requests?${query}`);
  },
  getSubmitted: () => fetchApi('/planning-requests/submitted'),
  getDraft: () => fetchApi('/planning-requests/draft'),
  getOne: (id: number) => fetchApi(`/planning-requests/${id}`),
  create: (data: any) => fetchApi('/planning-requests', { method: 'POST', body: JSON.stringify(data) }),
  update: (id: number, data: any) => fetchApi(`/planning-requests/${id}`, { method: 'PUT', body: JSON.stringify(data) }),
  delete: (id: number) => fetchApi(`/planning-requests/${id}`, { method: 'DELETE' }),
  submit: (id: number) => fetchApi(`/planning-requests/${id}/submit`, { method: 'POST' }),
};

// Operational Plans API (Team B)
export const operationalPlansApi = {
  getAll: () => fetchApi('/operational-plans'),
  getOne: (id: number) => fetchApi(`/operational-plans/${id}`),
  create: (data: { planning_request_item_id: number; version?: any }) => fetchApi('/operational-plans', { method: 'POST', body: JSON.stringify(data) }),
  createVersion: (planId: number, data: any) => fetchApi(`/operational-plans/${planId}/versions`, { method: 'POST', body: JSON.stringify(data) }),
  activateVersion: (versionId: number) => fetchApi(`/operational-plan-versions/${versionId}/activate`, { method: 'POST' }),
  getActivePlans: () => fetchApi('/operational-plans/active'),
};

// Execution Events API (Team C)
export const executionEventsApi = {
  getAll: (params?: { event_type?: string; operational_plan_version_id?: number }) => {
    const query = new URLSearchParams();
    if (params?.event_type) query.append('event_type', params.event_type);
    if (params?.operational_plan_version_id) query.append('operational_plan_version_id', String(params.operational_plan_version_id));
    return fetchApi(`/execution-events?${query}`);
  },
  create: (data: any) => fetchApi('/execution-events', { method: 'POST', body: JSON.stringify(data) }),
};
