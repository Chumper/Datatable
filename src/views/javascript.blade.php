<script type="text/javascript">
    jQuery(document).ready(function(){
        // dynamic table
        oTable = jQuery('#{{ $id }}').dataTable({
            @foreach ( $options as $k => $o )
                @if (!is_numeric($k))
                    {{ json_encode($k); }}:
                @endif
                @if (is_string($o))
                    @if ( @preg_match("#^\s*function\s*\([^\)]*#", $o))
                        {{ $o }}
                    @else
                        {{ json_encode($o) }}
                    @endif
                @else
                    @if (is_array($o))
                        @include(Config::get('datatable::table.options_view'), array('options' => $o))
                    @else
                        {{ json_encode($o) }}
                    @endif
                @endif ,
            @endforeach
            @foreach ($callbacks as $k => $o)
                {{ json_encode($k) }}: {{ $o }} ,
            @endforeach
        });
    });
</script>
