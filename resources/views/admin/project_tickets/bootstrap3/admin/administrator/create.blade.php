@extends($master)
@section('page', trans('ticketit::admin.administrator-create-title'))

@section('content')
    @include('ticketit::shared.header')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>{{ trans('ticketit::admin.administrator-create-title') }}</h2>
        </div>
        @if ($users->isEmpty())
            <h3 class="text-center">{{ trans('ticketit::admin.administrator-create-no-users') }}</h3>
        @else
            {!! CollectiveForm::open(['route'=> $setting->grab('admin_route').'.administrator.store', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
            <div class="panel-body">
                {{ trans('ticketit::admin.administrator-create-select-user') }}
            </div>
            <table class="table table-hover">
                <tfoot>
                    <tr>
                        <td class="text-center">
                            {!! link_to_route($setting->grab('admin_route').'.administrator.index', trans('ticketit::admin.btn-back'), null, ['class' => 'btn btn-default']) !!}
                            {!! CollectiveForm::submit(trans('ticketit::admin.btn-submit'), ['class' => 'btn btn-primary']) !!}
                        </td>
                    </tr>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="checkbox">
                                <label>
                                    <input name="administrators[]" type="checkbox" value="{{ $user->id }}" {!! $user->ticketit_admin ? "checked onclick='return false'" : "" !!}> {{ $user->name }}
                                </label>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! CollectiveForm::close() !!}
        @endif
    </div>
    {!! $users->render() !!}
@stop
