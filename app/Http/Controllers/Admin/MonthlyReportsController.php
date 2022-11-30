<?php
namespace App\Http\Controllers\Admin;

use App\Income;
use App\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MonthlyReportsController extends Controller
{
    public function index(Request $r)
    {
        $from    = Carbon::parse(sprintf(
            '%s-%s-01',
            $r->query('y', Carbon::now()->year),
            $r->query('m', Carbon::now()->month)
        ));
        $to      = clone $from;
        $to->day = $to->daysInMonth;

        $exp_q = Expense::with('expense_category')
            ->whereBetween('entry_date', [$from, $to]);

        $inc_q = Income::with('income_category')
            ->whereBetween('entry_date', [$from, $to]);

        $exp_total = $exp_q->sum('amount');
        $inc_total = $inc_q->sum('amount');
        $exp_group = $exp_q->orderBy('amount', 'desc')->get()->groupBy('expense_category_id');
        $inc_group = $inc_q->orderBy('amount', 'desc')->get()->groupBy('income_category_id');
        $profit    = $inc_total - $exp_total;

        $exp_summary = [];
        foreach ($exp_group as $exp) {
            foreach ($exp as $line) {
                if (! isset($exp_summary[$line->expense_category->name])) {
                    $exp_summary[$line->expense_category->name] = [
                        'name'   => $line->expense_category->name,
                        'amount' => 0,
                    ];
                }
                $exp_summary[$line->expense_category->name]['amount'] += $line->amount;
            }
        }

        $inc_summary = [];
        foreach ($inc_group as $inc) {
            foreach ($inc as $line) {
                if (! isset($inc_summary[$line->income_category->name])) {
                    $inc_summary[$line->income_category->name] = [
                        'name'   => $line->income_category->name,
                        'amount' => 0,
                    ];
                }
                $inc_summary[$line->income_category->name]['amount'] += $line->amount;
            }
        }

        // Income Graph
        $date_from = $from;
        $date_to = $to;
               
        $reportTitle = 'Income Report';
        $reportLabel = 'SUM';
        $chartType   = 'bar';

        $results = Income::whereBetween('entry_date', [$from, $to])->get()->sortBy('entry_date')->groupBy(function ($entry) {
            if ($entry->entry_date instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->entry_date)->format('Y-m-d');
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), $entry->entry_date)->format('Y-m-d');
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', $entry->entry_date)->format('Y-m-d');
            }        })->map(function ($entries, $group) {
            return $entries->sum('amount');
        });

        // Expense Graph
        $reportTitle_expense = 'Expense Report';
        $reportLabel_expense = 'SUM';
        $chartType_expense   = 'bar';

        $results_expense = Expense::whereBetween('entry_date', [$from, $to])->get()->sortBy('entry_date')->groupBy(function ($entry) {
            if ($entry->entry_date instanceof \Carbon\Carbon) {
                return \Carbon\Carbon::parse($entry->entry_date)->format('Y-m-d');
            }
            try {
               return \Carbon\Carbon::createFromFormat(config('app.date_format'), digiDate( $entry->entry_date))->format('Y-m-d');
            } catch (\Exception $e) {
                 return \Carbon\Carbon::createFromFormat(config('app.date_format') . ' H:i:s', digiDate( $entry->entry_date))->format('Y-m-d');
            }        })->map(function ($entries, $group) {
            return $entries->sum('amount');
        });

        // Income Graph Pie
        $reportTitle_income_pie = 'Income Report Pie';
        $reportLabel_income_pie = 'SUM';
        $chartType_income_pie   = 'pie';

        $results_income_pie = Income::whereBetween('entry_date', [$from, $to])->get()->sortBy('entry_date')->groupBy(function ($entry) {
                    return $entry->income_category->name;
                   })->map(function ($entries, $group) {
            return $entries->sum('amount');
        });

        // Expense Graph Pie
        $reportTitle_expense_pie = 'Expense Report Pie';
        $reportLabel_expense_pie = 'SUM';
        $chartType_expense_pie   = 'pie';

        $results_expense_pie = Expense::whereBetween('entry_date', [$from, $to])->get()->sortBy('entry_date')->groupBy(function ($entry) {
                    return $entry->expense_category->name;
                   })->map(function ($entries, $group) {
            return $entries->sum('amount');
        });

        return view('admin.monthly_reports.index', compact(
            'exp_summary',
            'inc_summary',
            'exp_total',
            'inc_total',
            'profit',

            'reportTitle', 'results', 'chartType', 'reportLabel',
            'reportTitle_expense', 'results_expense', 'chartType_expense', 'reportLabel_expense',
            'reportTitle_income_pie', 'results_income_pie', 'chartType_income_pie', 'reportLabel_income_pie',
            'reportTitle_expense_pie', 'results_expense_pie', 'chartType_expense_pie', 'reportLabel_expense_pie'
        ));
    }
}