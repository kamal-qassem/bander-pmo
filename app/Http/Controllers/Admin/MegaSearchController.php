<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MegaSearchController extends Controller
{
    protected $models = [
        'Account' => 'global.accounts.title',
        'Article' => 'global.articles.title',
        'Asset' => 'global.assets.title',
        'AssetsLocation' => 'global.assets-locations.title',
        'Brand' => 'global.brands.title',
        'ClientProject' => 'global.client-projects.title',
        'Contact' => 'global.contacts.title',
        'ContactCompany' => 'global.contact-companies.title',
        'ContactNote' => 'global.contact-notes.title',
        'ContentPage' => 'global.content-pages.title',
        'Department' => 'global.departments.title',
        'Expense' => 'global.expense.title',
        'FaqQuestion' => 'global.faq-questions.title',
        'Income' => 'global.income.title',
        'InternalNotification' => 'global.internal-notifications.title',
        'Invoice' => 'global.invoices.title',
        'MasterSetting' => 'global.master-settings.title',
        'MessengerMessage' => 'global.app_messages',
        'Product' => 'global.products.title',
        'PurchaseOrder' => 'global.purchase-orders.title',
        'Task' => 'global.tasks.title',
        'Warehouse' => 'global.ware-houses.title',
    ];

    public function search(Request $request)
    {

        $search = $request->input('search', false);
        $term = $search['term'];

        if (!$term) {
            abort(500);
        }

        $return = [];
        foreach ($this->models as $modelString => $translation) {
            $model = 'App\\' . $modelString;

            $query = $model::query();

            $fields = $model::$searchable;

            if ( ! empty( $fields ) ) {
                foreach ($fields as $field) {
                    $query->orWhere($field, 'LIKE', '%' . $term . '%');
                }

                $results = $query->get();

                foreach ($results as $result) {
                    $results_formated = $result->only($fields);
                    $results_formated['model'] = trans($translation);
                    $results_formated['fields'] = $fields;
                    $fields_formated = [];
                    foreach ($fields as $field) {
                        $fields_formated[$field] = title_case(str_replace('_', ' ', $field));
                    }
                    $results_formated['fields_formated'] = $fields_formated;

                    $results_formated['url'] = url('/admin/' . str_plural(snake_case($modelString)) . '/' . $result->id);

                    $return[] = $results_formated;
                }
            }
        }

        return response()->json(['results' => $return]);
    }
	
	public function getDetails( Request $request ) {
		$return = array();
		$type = $request->type;
		$id = $request->id;
		if ( 'tax' === $type ) {
			$return = \App\Tax::find( $id )->first();
		} elseif ( 'discount' === $type ) {
			$return = \App\Discount::find( $id )->first();
		}
		return response()->json(['results' => $return, 'type' => $type, 'id' => $id]);
	}
}
