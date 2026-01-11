export interface User {
  id: number;
  name: string;
  email: string;
  role: 'team_a' | 'team_b' | 'team_c';
}

export interface Route {
  id: number;
  name: string;
  identifier: string;
  description?: string;
}

export interface PlanningRequestItem {
  id?: number;
  route_id: number;
  route?: Route;
  capacity: number;
  start_date: string;
  end_date: string;
}

export interface PlanningRequest {
  id: number;
  created_by: number;
  creator?: User;
  status: 'draft' | 'submitted';
  submitted_at?: string;
  items: PlanningRequestItem[];
  created_at: string;
  updated_at: string;
}

export interface Resource {
  id: number;
  type: 'vehicle' | 'worker';
  name: string;
  details?: any;
  is_active: boolean;
}

export interface PlanVersionResource {
  id: number;
  resource_id: number;
  resource?: Resource;
  capacity: number;
  is_permanent: boolean;
  notes?: string;
}

export interface OperationalPlanVersion {
  id: number;
  operational_plan_id: number;
  version: number;
  is_active: boolean;
  valid_from: string;
  valid_to: string;
  notes?: string;
  resources?: PlanVersionResource[];
  created_by: number;
  creator?: User;
  created_at: string;
}

export interface OperationalPlan {
  id: number;
  planning_request_item_id: number;
  planning_request_item?: PlanningRequestItem;
  versions?: OperationalPlanVersion[];
  active_version?: OperationalPlanVersion;
  created_by: number;
  creator?: User;
  created_at: string;
}

export interface ExecutionEvent {
  id: number;
  operational_plan_version_id: number;
  event_type: string;
  event_data?: any;
  recorded_by: number;
  recorder?: User;
  recorded_at: string;
}
