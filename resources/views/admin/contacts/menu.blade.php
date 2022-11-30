@if ( ! empty( $type_id ) && $type_id == LEADS_TYPE )
@else
<span style="float: right;">
	<?php
	$active = '';
	if ( empty( $type_id ) ) {
		$active = ' active';
	}
	?>
	<a href="{{ route('admin.contacts.index') }}" class="btn btn-success{{$active}}"><i class="fa fa-plus"></i>&nbsp;@lang('custom.menu.all')&nbsp;<span class="badge">{{\App\Contact::count('id')}}</span></a>

	<?php
	$contacts_types = \App\ContactType::where('type', 'role')->where('status', 'active')->orderBy('priority')->get();
	if ( $contacts_types->count() > 0 ) {
		foreach( $contacts_types as $contacts_type ) {
			$active = '';
			if ( ! empty( $type_id ) && $type_id == $contacts_type->id ) {
				$active = ' activecontact';
			}
		?>
		<a href="{{ route('admin.list_contacts.index', [ 'type' => 'contact_type', 'type_id' => $contacts_type->id ]) }}" class="btn btn-success{{$active}}"><i class="fa fa-plus"></i>&nbsp;{{$contacts_type->title}}&nbsp;<span class="badge">{{\App\Contact::whereHas("contact_type",
                function ($query) use( $contacts_type ) {
                    $query->where('id', $contacts_type->id);
                })->count('contacts.id')}}</span></a>
		<?php
		}
	}

	?>
</span>
@endif
