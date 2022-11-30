<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Expense;
use App\Income;
use App\User;
use App\Contact;
use App\ClientProject;
use App\Task;
use App\Asset;
use App\AssetsHistory;
use App\Product;
use App\PurchaseOrder; 
use App\Role;
use Carbon\Carbon;
use DB;

class ReportsController extends Controller
{
    public function expenseReport(Request $request)
    {
         if ($request->has('date_filter')) { 
              $parts = explode(' - ' , $request->input('date_filter'));

            $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
              $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');

              
         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');
         } 
        $reportTitle = trans('others.reports.expense-report');
        $reportLabel = trans('others.reports.sum');
        $chartType   = trans('others.reports.line');

        $date_type = 'created_at';
        if ($request->has('date_type')) {
          $date_type = $request->input('date_type');
        }

        if ( 'entry_date' === $date_type ) {
          $results = Expense::where('entry_date', '>=', $date_from)->where('entry_date', '<=', $date_to)->get()->sortBy('entry_date')->groupBy(function ($entry) {
              if ($entry->entry_date instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->entry_date)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), digiDate($entry->entry_date))->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', digiDate( $entry->entry_date))->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->sum('amount');
          });
        } else {
          $results = Expense::where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to)->get()->sortBy('created_at')->groupBy(function ($entry) {
              if ($entry->created_at instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->sum('amount');
          });
        }

        $dateTypes = array(
          'created_at' => 'Created',
          'entry_date' => 'Entry Date',
        );

        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel', 'dateTypes'));
    }

    public function incomeReport(Request $request)
    {
         if ($request->has('date_filter')) { 
              $parts = explode(' - ' , $request->input('date_filter')); 
              $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
              $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');

         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');
         } 
        $reportTitle = trans('others.reports.income-report');
        $reportLabel = trans('others.reports.sum');
        $chartType   = trans('others.reports.line');

        $date_type = 'created_at';
        if ($request->has('date_type')) {
          $date_type = $request->input('date_type');
        }

        if ( 'entry_date' === $date_type ) {
          $results = Income::where('entry_date', '>=', $date_from)->where('entry_date', '<=', $date_to)->get()->sortBy('entry_date')->groupBy(function ($entry) {
              if ($entry->entry_date instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->entry_date)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->entry_date)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->entry_date)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->sum('amount');
          });
        } else {
          $results = Income::where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to)->get()->sortBy('created_at')->groupBy(function ($entry) {
              if ($entry->created_at instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->sum('amount');
          });
        }

        $dateTypes = array(
          'created_at' => 'Created',
          'entry_date' => 'Entry Date',
        );
        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel', 'dateTypes'));
    }

    public function usersReport(Request $request)
    {
        if ($request->has('date_filter')) { 
          $parts = explode(' - ' , $request->input('date_filter')); 
            $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
            $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');
         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');
         } 
        $reportTitle = trans('others.reports.users-report');
        $reportLabel = trans('others.reports.count');
        $chartType   = trans('others.reports.bar');

        $results = User::where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to)->get()->sortBy('created_at')->groupBy(function ($entry) {
            if ($entry->created_at instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
            }        })->map(function ($entries, $group) {
            return $entries->count('id');
        });

        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel'));
    }

    public function contactsProjectsReports(Request $request)
    {
         if ($request->has('date_filter')) { 
              $parts = explode(' - ' , $request->input('date_filter')); 
              $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
              $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');

         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');
         } 
        $reportTitle = trans('others.reports.client-projects-report');
        $reportLabel = trans('others.reports.count');
        $chartType   = trans('others.reports.bar');

        $date_type = 'created_at';
        if ($request->has('date_type')) {
          $date_type = $request->input('date_type');
        }

        if ( 'start_date' === $date_type ) {
          $results = ClientProject::where(DB::raw('date(start_date)'), '>=', $date_from)->where(DB::raw('date(start_date)'), '<=', $date_to)->get()->sortBy('start_date')->groupBy(function ($entry) {
              if ($entry->start_date instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->start_date)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->start_date)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::parse($entry->start_date)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        } elseif ( 'due_date' === $date_type ) {          
          $results = ClientProject::where(DB::raw('date(due_date)'), '>=', $date_from)->where(DB::raw('date(due_date)'), '<=', $date_to)->get()->sortBy('due_date')->groupBy(function ($entry) {
           
              if ($entry->due_date instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->due_date)->format(config('app.date_format'));
              }

              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->due_date)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::parse($entry->due_date)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        } else {
          $results = ClientProject::where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to)->get()->sortBy('created_at')->groupBy(function ($entry) {
              if ($entry->created_at instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        }

        $dateTypes = array(
          'start_date' => trans('global.client-projects.fields.start-date'),
          'due_date' => trans('global.client-projects.fields.due-date'),
          'created_at' => trans('custom.common.entry-date'),
        );

        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel', 'dateTypes'));
    }

    public function tasksReport(Request $request)
    {
         if ($request->has('date_filter')) { 
              $parts = explode(' - ' , $request->input('date_filter')); 
              $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
              $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');

         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');
         } 
        $reportTitle = trans('others.reports.tasks-report');
        $reportLabel = trans('others.reports.count');
        $chartType   = trans('others.reports.line');

        $date_type = 'created_at';
        if ($request->has('date_type')) {
          $date_type = $request->input('date_type');
        }

        if ( 'start_date' === $date_type ) {
          $results = Task::where(DB::raw('date(start_date)'), '>=', $date_from)->where(DB::raw('date(start_date)'), '<=', $date_to)->get()->sortBy('start_date')->groupBy(function ($entry) {
              if ($entry->start_date instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->start_date)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->start_date)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::parse($entry->start_date)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        } elseif ( 'due_date' === $date_type ) {
          $results = Task::where(DB::raw('date(due_date)'), '>=', $date_from)->where(DB::raw('date(due_date)'), '<=', $date_to)->get()->sortBy('due_date')->groupBy(function ($entry) {
              if ($entry->start_date instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->due_date)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->start_date)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::parse($entry->due_date)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        } else {
          $results = Task::where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to)->get()->sortBy('created_at')->groupBy(function ($entry) {
              if ($entry->created_at instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        }

        $dateTypes = array(
          'start_date' => trans('global.client-projects.fields.start-date'),
          'due_date' => trans('global.client-projects.fields.due-date'),
          'created_at' => trans('custom.common.entry-date'),
        );

        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel', 'dateTypes'));
    }

    public function assetsReport(Request $request)
    {
         if ($request->has('date_filter')) { 
              $parts = explode(' - ' , $request->input('date_filter')); 
             
              $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
              $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');

         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');
         } 
        $reportTitle = trans('others.reports.assets-report');
        $reportLabel = trans('others.reports.count');
        $chartType   = trans('others.reports.bar');

        $results = Asset::where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to)->get()->sortBy('created_at')->groupBy(function ($entry) {
            if ($entry->created_at instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
            }        })->map(function ($entries, $group) {
            
            return $entries->count('id');
        });

        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel'));
    }

    public function productsReport(Request $request)
    {
         if ($request->has('date_filter')) { 
              $parts = explode(' - ' , $request->input('date_filter')); 
              $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
              $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');

             
         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');
         } 
        $reportTitle = trans('others.reports.products-report');
        $reportLabel = trans('others.reports.count');
        $chartType   = trans('others.reports.line');

        $results = Product::where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to)->get()->sortBy('created_at')->groupBy(function ($entry) {
            if ($entry->created_at instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
            }        })->map(function ($entries, $group) {
            return $entries->count('id');
        });

        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel'));
    }

    public function purchaseOrdersReport(Request $request)
    {
         if ($request->has('date_filter')) { 
              $parts = explode(' - ' , $request->input('date_filter')); 
              $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
              $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');

            
         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');
         } 
        $reportTitle = trans('others.reports.purchase-orders-report');
        $reportLabel = trans('others.reports.count');
        $chartType   = trans('others.reports.bar');

        $date_type = 'created_at';
        if ($request->has('date_type')) {
          $date_type = $request->input('date_type');
        }

        if ( 'order_date' === $date_type ) {
            $results = PurchaseOrder::where('order_date', '>=', $date_from)->where('order_date', '<=', $date_to)->get()->sortBy('order_date')->groupBy(function ($entry) {
              if ($entry->created_at instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->order_date)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->order_date)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->order_date)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        } elseif ( 'order_due_date' === $date_type ) {
            $results = PurchaseOrder::where('order_due_date', '>=', $date_from)->where('order_due_date', '<=', $date_to)->get()->sortBy('order_due_date')->groupBy(function ($entry) {
              if ($entry->created_at instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->order_due_date)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->order_due_date)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->order_due_date)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        } else {
          $results = PurchaseOrder::where(DB::raw('date(created_at)'), '>=', $date_from)->where(DB::raw('date(created_at)'), '<=', $date_to)->get()->sortBy('created_at')->groupBy(function ($entry) {
              if ($entry->created_at instanceof \Carbon\Carbon) {
                  return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
              }
              try {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
              } catch (\Exception $e) {
                   return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
              }        })->map(function ($entries, $group) {
              return $entries->count('id');
          });
        }

        $dateTypes = array(
          'order_date' => trans('global.purchase-orders.fields.order-date'),
          'order_due_date' => trans('global.purchase-orders.fields.order-due-date'),
          'created_at' => trans('custom.common.entry-date'),
        );
        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel', 'dateTypes'));
    }

    public function rolesUsersReport(Request $request)
    {
         if ($request->has('date_filter')) { 
              $parts = explode(' - ' , $request->input('date_filter')); 
              
            $date_from = Carbon::createFromFormat(config('app.date_format'), $parts[0])->format('Y-m-d');
            $date_to = Carbon::createFromFormat(config('app.date_format'), $parts[1])->format('Y-m-d');
         } else { 
              $date_from = new Carbon('last Monday');
              $date_to = new Carbon('this Sunday');              
         } 
        $reportTitle = trans('others.reports.users-roles-report');
        $reportLabel = trans('others.reports.count');
      
        $chartType   = 'doughnut';
        
        $results = Role::where('type', 'role')->whereBetween('created_at', [$date_from, $date_to])->get();
        if ( $results->count() == 0 ) {
          $results = Role::where('type', 'role')->get();
        }
        $results = $results->sortBy('created_at')->groupBy(function ($entry) {

            if ($entry->created_at instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->created_at)->format(config('app.date_format'));
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->created_at)->format(config('app.date_format'));
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->created_at)->format(config('app.date_format'));
            }
            
            return $entry->title;     
          })->map(function ($entries, $group) {
            return $entries->sum(function ($entry) {
                return User::join('role_user', 'role_user.user_id', 'contacts.id')->join('roles', 'roles.id', 'role_user.role_id')->where('role_id', $entry->id)->count('contacts.id');
            });
        });
        $colors = array();
        $roles = Role::where('type', 'role')->get()->sortBy('created_at');

        if( ! empty( $roles ) ) {
          foreach ($roles as $role) {
            $colors[] = ( $role->color ) ? $role->color : '#333';
          }
        }
      
        return view('admin.reports', compact('reportTitle', 'results', 'chartType', 'reportLabel', 'colors', 'date_from', 'date_to'));
    }


}
