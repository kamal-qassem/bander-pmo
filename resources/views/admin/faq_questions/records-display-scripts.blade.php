<script>
        @can('faq_question_delete_multi')
            window.route_mass_crud_entries_destroy = '{{ route('admin.faq_questions.mass_destroy') }}';
        @endcan

</script>