{{-- Load the css file to the header --}}
<script type="text/javascript">
    function loadCSS(filename) {
        var file = document.createElement("link");
        file.setAttribute("rel", "stylesheet");
        file.setAttribute("type", "text/css");
        file.setAttribute("href", filename);

        if (typeof file !== "undefined"){
            document.getElementsByTagName("head")[0].appendChild(file)
        }
    }

    loadCSS({!! '"'.url('css/cdn-styles-css/datatables/jquery.dataTables.min.css').'"' !!});
    @if($editor_enabled)
        loadCSS({!! '"'.url('css/cdn-styles-css/summernote/summernote.css').'"' !!});
        @if($include_font_awesome)
            loadCSS({!! '"'.url('css/cdn-styles-css/font-awesome-4.7.0/css/font-awesome.min.css').'"' !!});
        @endif
        @if($codemirror_enabled)
            loadCSS({!! '"'.url('css/cdn-styles-css/codemirror/codemirror.min.css').'"' !!});
            loadCSS({!! '"'.url('css/cdn-styles-css/codemirror/theme/monokai.min.css').'"' !!});
        @endif
    @endif
</script>