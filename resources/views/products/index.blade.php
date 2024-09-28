@extends('layouts.app')

@section('title', 'Product List')

@section('content')
    <h1>Product List</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @include('products._form')

    <h2>Products</h2>
    <table class="table" id="productTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#productForm').on('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'), // Use the form's action URL
                    data: $(this).serialize(), // Serialize the form data
                    success: function (response) {
                        // Handle success response
                        if (response.status === 200) {
                            $('#productTable tbody').append(`
                                <tr>
                                    <td>${response.data.id}</td>
                                    <td>${response.data.name}</td>
                                    <td>$${response.data.price.toFixed(2)}</td>
                                    <td>${response.data.quantity}</td>
                                    <td>${response.data.category.name}</td>
                                    <td>
                                        <a href="/products/${response.data.id}/edit" class="btn btn-warning">Edit</a>
                                        <form action="/products/${response.data.id}" method="POST" style="display:inline;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            `);
                            $('#productForm')[0].reset(); // Reset the form
                            alert(response.msg); // Show success message
                        } else {
                            alert('Error: ' + response.msg);
                        }
                    },
                    error: function (xhr) {
                        // Handle error response
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        for (let key in errors) {
                            errorMessage += errors[key][0] + '\n'; // Collect error messages
                        }
                        alert('Validation errors:\n' + errorMessage);
                    }
                });
            });
        });
    </script>
@endsection
