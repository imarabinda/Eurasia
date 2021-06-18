<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Code</th>
        <th>Category</th>
        <th>Type</th>
        <th>Fabric Type</th>
        <th>Fabric Color</th>
        <th>Size</th>
        <th>Welted Edges Color</th>
        <th>Number of Stitches</th>
        <th>Rate</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->code }}</td>
            <td>{{ $item->product_category_name }}</td>
            <td>{{ $item->product_type_name }}</td>
            <td>{{ $item->fabric_type_name }}</td>
            <td>{{ $item->fabric_color_name }}</td>
            <td>{{ $item->size_height_width }}</td>
            <td>{{ $item->welted_edges_color_name }}</td>
            <td>{{ $item->number_of_stitches }}</td>
            <td>{{ $item->rate }}</td>
            
        </tr>
    @endforeach
    </tbody>
</table>