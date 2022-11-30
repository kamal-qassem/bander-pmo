@if($editor_enabled)

@if($codemirror_enabled)
    <script src="{{ url('js/cdn-js-files/codemirror/codemirror.min.js') }}"></script>
    <script src="{{ url('js/cdn-js-files/codemirror/xml.min.js') }}"></script>
@endif

<script src="{{ url('js/cdn-js-files/summernote/summernote.min.js') }}"></script>
@if($editor_locale)
    <script src="{{ url('js/cdn-js-files/summernote/lang/summernote-en.min.js') }}"></script>
@endif
<script>


    $(function() {

        var options = $.extend(true, {lang: '{{$editor_locale}}' {!! $codemirror_enabled ? ", codemirror: {theme: '{$codemirror_theme}', mode: 'text/html', htmlMode: true, lineWrapping: true}" : ''  !!} } , {!! $editor_options !!});

        $("textarea.summernote-editor").summernote(options);

        $("label[for=content]").click(function () {
            $("#content").summernote("focus");
        });
    });


</script>
@endif