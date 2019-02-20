<table>
    <thead>
	    <tr>
	    	@if (!empty($data[0]))
		        @foreach($data[0] as $key => $value)
		            <th>{{ ucfirst($key) }}</th>
	    	    @endforeach
	    	@endif
	    </tr>
    </thead>
    <tbody>
    	@if (!empty($data))
		    @foreach($data as $row)
		        <tr>
			        @foreach ($row as $value)
			            <td>{{ $value }}</td>
			        @endforeach
	    	    </tr>
		    @endforeach
    	@endif
    </tbody>
</table>
