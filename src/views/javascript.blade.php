<script type="text/javascript">
    jQuery(document).ready(function(){
        // dynamic table
        oTable = jQuery('#{{ $id }}').dataTable({

        @foreach ($options as $k => $o)
            {{ json_encode($k) }}: @if(!is_array($o)) @if(preg_match("/function/", $o)) {{ $o }} @else {{ json_encode($o) }}, @endif
                @else
                [{
                    @foreach ($o as $x => $r) 
                    {{ json_encode($x) }}: @if(is_array($r)) {{ json_encode($r) }}, @elseif(preg_match("/function/", $r)) {{ $r }}, @else {{ json_encode($r) }} @endif
                    @endforeach
                }],
                @endif

        @endforeach

        @foreach ($callbacks as $k => $o)
            {{ json_encode($k) }}: {{ $o }},
        @endforeach

        });
    // custom values are available via $values array
    });
</script>
