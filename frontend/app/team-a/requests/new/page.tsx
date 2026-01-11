'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { Route, PlanningRequestItem } from '@/types';
import { routesApi, planningRequestsApi } from '@/lib/api';
import { requireAuth } from '@/lib/auth';
import { Navbar } from '@/components/navbar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Plus, X, Save, FileText } from 'lucide-react';

export default function NewPlanningRequest() {
  const router = useRouter();
  const [routes, setRoutes] = useState<Route[]>([]);
  const [items, setItems] = useState<PlanningRequestItem[]>([
    { route_id: 0, capacity: 1, start_date: '', end_date: '' }
  ]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    // Check authentication
    if (!requireAuth('team_a')) {
      return;
    }
    loadRoutes();
  }, []);

  const loadRoutes = async () => {
    try {
      const response = await routesApi.getAll();
      // Extract the data array from the response object
      const data = response?.data || response;
      setRoutes(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error('Failed to load routes:', err);
      setRoutes([]); // Set empty array on error
    }
  };

  const addItem = () => {
    setItems([...items, { route_id: 0, capacity: 1, start_date: '', end_date: '' }]);
  };

  const removeItem = (index: number) => {
    setItems(items.filter((_, i) => i !== index));
  };

  const updateItem = (index: number, field: keyof PlanningRequestItem, value: any) => {
    const newItems = [...items];
    newItems[index] = { ...newItems[index], [field]: value };
    setItems(newItems);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await planningRequestsApi.create({ items });
      const requestId = response.data?.id || response.id;
      router.push(`/team-a/requests/${requestId}`);
    } catch (err: any) {
      setError(err.message || 'Failed to create request');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <div className="max-w-4xl mx-auto p-8">
        <div className="mb-8">
          <div className="flex items-center gap-3 mb-2">
            <div className="p-3 bg-blue-100 rounded-full">
              <Plus className="w-6 h-6 text-blue-600" />
            </div>
            <h1 className="text-3xl font-bold text-gray-900">Create New Planning Request</h1>
          </div>
          <p className="text-gray-600 ml-16">Add route items and submit for operational planning</p>
        </div>

        {error && (
          <div className="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm">
            {error}
          </div>
        )}

        <Card className="bg-white shadow-lg">
          <CardHeader className="bg-blue-50 border-b border-blue-100">
            <CardTitle className="text-blue-900">Planning Request Details</CardTitle>
            <CardDescription>Add route items to your planning request</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit}>
              <div className="space-y-6">
                {Array.isArray(items) && items.map((item, index) => (
                  <Card key={index} className="border-l-4 border-l-blue-400 bg-gradient-to-r from-blue-50 to-white shadow-md">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <div className="flex items-center gap-2">
                          <div className="p-2 bg-blue-100 rounded-lg">
                            <FileText className="w-5 h-5 text-blue-600" />
                          </div>
                          <CardTitle className="text-lg text-gray-900">Item {index + 1}</CardTitle>
                        </div>
                        {items.length > 1 && (
                          <Button
                            type="button"
                            onClick={() => removeItem(index)}
                            variant="ghost"
                            size="sm"
                            className="hover:bg-red-50 hover:text-red-600"
                          >
                            <X className="h-4 w-4" />
                          </Button>
                        )}
                      </div>
                    </CardHeader>
                    <CardContent>
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="space-y-2">
                          <Label htmlFor={`route-${index}`} className="text-blue-600 font-medium">Route</Label>
                          <select
                            id={`route-${index}`}
                            value={item.route_id}
                            onChange={(e) => updateItem(index, 'route_id', Number(e.target.value))}
                            className="w-full p-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-blue-600"
                            required
                          >
                            <option value="" className="text-blue-600">Select a route</option>
                            {Array.isArray(routes) && routes.map((route) => (
                              <option key={route.id} value={route.id} className="text-blue-600">
                                {route.name} ({route.identifier})
                              </option>
                            ))}
                          </select>
                        </div>

                        <div className="space-y-2">
                          <Label htmlFor={`capacity-${index}`} className="text-blue-600 font-medium">Capacity</Label>
                          <Input
                            id={`capacity-${index}`}
                            type="number"
                            min="1"
                            value={item.capacity}
                            onChange={(e) => updateItem(index, 'capacity', Number(e.target.value))}
                            className="border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-blue-600"
                            required
                          />
                        </div>

                        <div className="space-y-2">
                          <Label htmlFor={`start-date-${index}`} className="text-blue-600 font-medium">Start Date</Label>
                          <Input
                            id={`start-date-${index}`}
                            type="date"
                            value={item.start_date}
                            onChange={(e) => updateItem(index, 'start_date', e.target.value)}
                            className="border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-blue-600"
                            required
                          />
                        </div>

                        <div className="space-y-2">
                          <Label htmlFor={`end-date-${index}`} className="text-blue-600 font-medium">End Date</Label>
                          <Input
                            id={`end-date-${index}`}
                            type="date"
                            value={item.end_date}
                            onChange={(e) => updateItem(index, 'end_date', e.target.value)}
                            className="border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-blue-600"
                            required
                          />
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                ))}

                <Button
                  type="button"
                  onClick={addItem}
                  variant="outline"
                  className="w-full border-2 border-dashed border-blue-300 hover:bg-blue-50 hover:border-blue-500 transition-all text-blue-600 font-medium"
                >
                  <Plus className="h-4 w-4 mr-2" />
                  Add Another Item
                </Button>

                <div className="relative my-6">
                  <div className="absolute inset-0 flex items-center">
                    <div className="w-full border-t border-gray-200"></div>
                  </div>
                  <div className="relative flex justify-center text-sm">
                    <span className="px-2 bg-white text-gray-500">Ready to submit?</span>
                  </div>
                </div>

                <div className="flex gap-4">
                  <Button
                    type="button"
                    onClick={() => router.push('/team-a')}
                    variant="outline"
                    className="flex-1 border-2 border-gray-300 hover:bg-gray-50 transition-all"
                  >
                    Cancel
                  </Button>
                  <Button
                    type="submit"
                    disabled={loading}
                    className="flex-1 bg-blue-600 hover:bg-blue-700 transition-all shadow-md hover:shadow-lg"
                  >
                    <Save className="h-4 w-4 mr-2" />
                    {loading ? 'Creating...' : 'Create Request'}
                  </Button>
                </div>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
