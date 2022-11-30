<?php

namespace App\Observers;

use Auth;
use App\AccountsAction;

class AccountCrudActionObserver
{
    public function saved($model)
    {
        // dd( $model );
        if ($model->wasRecentlyCreated == true) {
            // Data was just created
            $action = 'created';
        } else {
            // Data was updated
            $action = 'updated';
        }
        if (Auth::check()) {
            AccountsAction::create([
                'user_id'      => Auth::user()->id,
                'action'       => $action,
                'action_model' => $model->getTable(),
                'action_id'    => $model->id,
                'record' => json_encode( $model->attributesToArray() ),
                'amount' => ( $model->amount ) ? $model->amount : 0,
            ]);
        }

    }

    public function deleting($model)
    {
        if (Auth::check()) {
            AccountsAction::create([
                'user_id'      => Auth::user()->id,
                'action'       => 'deleted',
                'action_model' => $model->getTable(),
                'action_id'    => $model->id,
                'record' => json_encode( $model->attributesToArray() ),
                'amount' => ( $model->amount ) ? $model->amount : 0,
            ]);
        }
    }

    public function observeincrement($model)
    {
        
        if (Auth::check()) {
            AccountsAction::create([
                'user_id'      => Auth::user()->id,
                'action'       => 'increment',
                'action_model' => $model->getTable(),
                'action_id'    => $model->id,
                'record' => json_encode( $model->attributesToArray() ),
                'amount' => ( $model->amount ) ? $model->amount : 0,
            ]);
        }

    }

    public function observedecrement($model)
    {
        
        if (Auth::check()) {
            AccountsAction::create([
                'user_id'      => Auth::user()->id,
                'action'       => 'decrement',
                'action_model' => $model->getTable(),
                'action_id'    => $model->id,
                'record' => json_encode( $model->attributesToArray() ),
                'amount' => ( $model->amount ) ? $model->amount : 0,
            ]);
        }

    }
}