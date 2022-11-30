<script>
    $("#selectbtn-widgets").click(function(){
        $("#selectall-widgets > option").prop("selected","selected");
        $("#selectall-widgets").trigger("change");
    });
    $("#deselectbtn-widgets").click(function(){
        $("#selectall-widgets > option").prop("selected","");
        $("#selectall-widgets").trigger("change");
    });
</script>   