'use client';

import { useEffect, useState } from 'react';
import { operationalPlansApi, executionEventsApi } from '@/lib/api';
import { requireAuth } from '@/lib/auth';
import { Navbar } from '@/components/navbar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { FileText, Plus, Clock, User, Activity, CheckCircle } from 'lucide-react';
import type { OperationalPlan, ExecutionEvent } from '@/types';

export default function TeamCDashboard() {
  const [plans, setPlans] = useState<OperationalPlan[]>([]);
  const [selectedPlanVersion, setSelectedPlanVersion] = useState<any>(null);
  const [executionEvents, setExecutionEvents] = useState<ExecutionEvent[]>([]);
  const [loading, setLoading] = useState(true);
  const [showEventForm, setShowEventForm] = useState(false);
  const [eventFormData, setEventFormData] = useState({
    event_type: '',
    notes: '',
  });
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    if (!requireAuth('team_c')) {
      return;
    }
    loadPlans();
  }, []);

  useEffect(() => {
    if (selectedPlanVersion) {
      loadExecutionEvents();
    }
  }, [selectedPlanVersion]);

  const loadPlans = async () => {
    try {
      setLoading(true);
      const response = await operationalPlansApi.getAll();
      const data = response?.data || response;
      setPlans(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error('Failed to load plans:', error);
      setPlans([]);
    } finally {
      setLoading(false);
    }
  };

  const loadExecutionEvents = async () => {
    try {
      const response = await executionEventsApi.getAll({
        operational_plan_version_id: selectedPlanVersion.id,
      });
      const data = response?.data || response;
      setExecutionEvents(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error('Failed to load execution events:', error);
      setExecutionEvents([]);
    }
  };

  const handleRecordEvent = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);

    try {
      await executionEventsApi.create({
        operational_plan_version_id: selectedPlanVersion.id,
        event_type: eventFormData.event_type,
        event_data: { notes: eventFormData.notes, timestamp: new Date().toISOString() },
      });

      setEventFormData({ event_type: '', notes: '' });
      setShowEventForm(false);
      await loadExecutionEvents();
    } catch (error: any) {
      alert(error.message || 'Failed to record event');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="max-w-7xl mx-auto p-8">
        <div className="mb-8">
          <div className="flex items-center gap-3 mb-2">
            <div className="p-3 bg-purple-100 rounded-full">
              <Activity className="w-6 h-6 text-purple-600" />
            </div>
            <h1 className="text-3xl font-bold text-gray-900">Team C - Execution Events</h1>
          </div>
          <p className="text-gray-600 ml-16">Record and monitor execution events for operational plans</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Operational Plans List */}
          <div className="lg:col-span-1">
            <h2 className="text-2xl font-semibold mb-4">Operational Plans</h2>
            {loading ? (
              <Card>
                <CardContent className="pt-6">
                  <div className="text-center py-12 text-gray-500">
                    Loading plans...
                  </div>
                </CardContent>
              </Card>
            ) : !Array.isArray(plans) || plans.length === 0 ? (
              <Card>
                <CardContent className="pt-6">
                  <div className="text-center py-12">
                    <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <p className="text-gray-500">No operational plans yet</p>
                  </div>
                </CardContent>
              </Card>
            ) : (
              <div className="space-y-3">
                {plans.map((plan) => (
                  <Card key={plan.id} className="border-l-4 border-l-purple-500 bg-white shadow-sm hover:shadow-md transition-shadow">
                    <CardHeader className="pb-3">
                      <div className="flex items-center gap-2">
                        <div className="p-1.5 bg-purple-100 rounded-lg">
                          <FileText className="w-4 h-4 text-purple-600" />
                        </div>
                        <CardTitle className="text-sm text-gray-900">
                          Plan #{plan.id}
                        </CardTitle>
                      </div>
                      <CardDescription className="text-xs ml-8">
                        {plan.planning_request_item?.route?.name || 'N/A'}
                      </CardDescription>
                    </CardHeader>
                    <CardContent>
                      {plan.versions && plan.versions.length > 0 ? (
                        <div className="space-y-2">
                          {plan.versions
                            .sort((a, b) => b.version - a.version)
                            .map((version) => (
                              <Button
                                key={version.id}
                                onClick={() => setSelectedPlanVersion(version)}
                                variant={selectedPlanVersion?.id === version.id ? 'default' : 'outline'}
                                size="sm"
                                className={`w-full justify-start ${
                                  selectedPlanVersion?.id === version.id
                                    ? 'bg-purple-600 hover:bg-purple-700'
                                    : 'hover:bg-purple-50 hover:text-purple-600 hover:border-purple-600'
                                }`}
                              >
                                <span className="text-xs">v{version.version}</span>
                                {version.is_active && (
                                  <Badge className="ml-2 bg-purple-100 text-purple-800 text-xs">Active</Badge>
                                )}
                              </Button>
                            ))}
                        </div>
                      ) : (
                        <p className="text-xs text-gray-500">No versions</p>
                      )}
                    </CardContent>
                  </Card>
                ))}
              </div>
            )}
          </div>

          {/* Execution Events */}
          <div className="lg:col-span-2">
            {selectedPlanVersion ? (
              <>
                <div className="flex justify-between items-center mb-4">
                  <div>
                    <h2 className="text-2xl font-semibold">Execution Events</h2>
                    <p className="text-sm text-gray-600">
                      Plan #{selectedPlanVersion.operational_plan_id} - Version {selectedPlanVersion.version}
                    </p>
                  </div>
                  <Button
                    onClick={() => setShowEventForm(!showEventForm)}
                    className="bg-purple-600 hover:bg-purple-700"
                  >
                    {showEventForm ? (
                      <>Cancel</>
                    ) : (
                      <><Plus className="h-4 w-4 mr-2" /> Record Event</>
                    )}
                  </Button>
                </div>

                {showEventForm && (
                  <Card className="mb-6 bg-white shadow-lg border-l-4 border-l-purple-500">
                    <CardHeader className="bg-purple-50 border-b border-purple-100">
                      <div className="flex items-center gap-2">
                        <div className="p-2 bg-purple-100 rounded-lg">
                          <Plus className="w-5 h-5 text-purple-600" />
                        </div>
                        <div>
                          <CardTitle className="text-purple-900">Record Execution Event</CardTitle>
                          <CardDescription>Log execution data for this plan version</CardDescription>
                        </div>
                      </div>
                    </CardHeader>
                    <CardContent className="pt-6">
                      <form onSubmit={handleRecordEvent}>
                        <div className="space-y-4">
                          <div className="space-y-2">
                            <Label htmlFor="event-type" className="text-blue-600 font-medium">Event Type</Label>
                            <Input
                              id="event-type"
                              type="text"
                              value={eventFormData.event_type}
                              onChange={(e) => setEventFormData({ ...eventFormData, event_type: e.target.value })}
                              className="border-2 border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all text-blue-600"
                              placeholder="e.g., departure, arrival, delay, completion"
                              required
                            />
                          </div>

                          <div className="space-y-2">
                            <Label htmlFor="event-notes" className="text-blue-600 font-medium">Notes</Label>
                            <textarea
                              id="event-notes"
                              value={eventFormData.notes}
                              onChange={(e) => setEventFormData({ ...eventFormData, notes: e.target.value })}
                              className="w-full p-2 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all text-blue-600"
                              rows={3}
                              placeholder="Add any notes about this event..."
                            />
                          </div>

                          <div className="relative my-6">
                            <div className="absolute inset-0 flex items-center">
                              <div className="w-full border-t border-gray-200"></div>
                            </div>
                            <div className="relative flex justify-center text-sm">
                              <span className="px-2 bg-white text-gray-500">Ready to record?</span>
                            </div>
                          </div>

                          <div className="flex gap-4">
                            <Button
                              type="button"
                              onClick={() => setShowEventForm(false)}
                              variant="outline"
                              className="flex-1 border-2 border-gray-300 hover:bg-gray-50 transition-all"
                            >
                              Cancel
                            </Button>
                            <Button
                              type="submit"
                              disabled={submitting}
                              className="flex-1 bg-purple-600 hover:bg-purple-700 transition-all shadow-md hover:shadow-lg"
                            >
                              <Activity className="h-4 w-4 mr-2" />
                              {submitting ? 'Recording...' : 'Record Event'}
                            </Button>
                          </div>
                        </div>
                      </form>
                    </CardContent>
                  </Card>
                )}

                <div className="space-y-3">
                  {executionEvents.length === 0 ? (
                    <Card>
                      <CardContent className="pt-6">
                        <div className="text-center py-12">
                          <Activity className="w-12 h-12 mx-auto text-gray-400 mb-4" />
                          <p className="text-gray-500">No execution events recorded yet</p>
                          <p className="text-sm text-gray-400 mt-2">Click "Record Event" to add your first event</p>
                        </div>
                      </CardContent>
                    </Card>
                  ) : (
                    executionEvents.map((event) => (
                      <Card key={event.id} className="border-l-4 border-l-purple-400 bg-gradient-to-r from-purple-50 to-white shadow-md">
                        <CardContent className="pt-4">
                          <div className="flex items-start gap-3">
                            <div className="p-2 bg-purple-100 rounded-lg">
                              <Activity className="w-5 h-5 text-purple-600" />
                            </div>
                            <div className="flex-1">
                              <div className="flex items-center justify-between mb-2">
                                <h4 className="font-semibold text-gray-900">{event.event_type}</h4>
                                <Badge className="bg-purple-100 text-purple-800 text-xs">
                                  <CheckCircle className="w-3 h-3 mr-1" />
                                  Recorded
                                </Badge>
                              </div>

                              {event.event_data?.notes && (
                                <div className="mb-2 p-3 bg-purple-50 rounded border border-purple-200">
                                  <p className="text-sm text-gray-700">{event.event_data.notes}</p>
                                </div>
                              )}

                              <div className="flex items-center gap-4 text-sm text-gray-600">
                                <div className="flex items-center gap-1">
                                  <Clock className="w-3 h-3" />
                                  <span>{new Date(event.recorded_at).toLocaleString()}</span>
                                </div>
                                <div className="flex items-center gap-1">
                                  <User className="w-3 h-3" />
                                  <span>{event.recorder?.name || 'Unknown'}</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </CardContent>
                      </Card>
                    ))
                  )}
                </div>
              </>
            ) : (
              <Card>
                <CardContent className="pt-6">
                  <div className="text-center py-20">
                    <FileText className="w-16 h-16 mx-auto text-gray-300 mb-4" />
                    <p className="text-gray-500 text-lg mb-2">Select a plan version</p>
                    <p className="text-sm text-gray-400">Choose a plan version from the left to view and record execution events</p>
                  </div>
                </CardContent>
              </Card>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
