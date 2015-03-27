<script type="text/javascript">
    jQuery(document).ready(function(){
        // dynamic table
        oTable = jQuery('#{{ $id }}').DataTable({{ $options_string }});
        @foreach($hidden as $column)
            oTable.columns({{$column}}).visible(false);
        @endforeach
    // custom values are available via $values array
    });
</script>
