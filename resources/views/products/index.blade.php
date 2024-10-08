@extends('layouts.app')

@section('title', 'Product List')

@section('content')
<h2>Add Product</h2>
        <div class="mb-4 form-container">
            @include('products._form')
        </div>
    <div class="container mt-4">
        <h1>Product List</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="productTable">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Category</th>
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .table-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #productTable {
            border-collapse: collapse;
            width: 100%;
        }

        #productTable thead {
            background-color: #343a40;
            color: #ffffff;
        }

        #productTable tbody tr {
            transition: background-color 0.3s;
        }

        #productTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        #productTable td {
            padding: 12px;
            border: 1px solid #dee2e6;
        }

        .form-container {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 10px;
            width: 100%;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #343a40;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .btn {
            border-radius: 4px;
            padding: 10px 15px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#productForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.status === 200) {
                            $('#productTable tbody').append(`
                                <tr>
                                    <td>${response.data.id}</td>
                                    <td>${response.data.name}</td>
                                    <td>$${response.data.price.toFixed(2)}</td>
                                    <td>${response.data.quantity}</td>
                                    <td>${response.data.category.name}</td>
                                    <td>
                                        <a href="/products/${response.data.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="/products/${response.data.id}" method="POST" style="display:inline;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            `);
                            $('#productForm')[0].reset();
                            alert(response.msg);
                        } else {
                            alert('Error: ' + response.msg);
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        for (let key in errors) {
                            errorMessage += errors[key][0] + '\n';
                        }
                        alert('Validation errors:\n' + errorMessage);
                    }
                });
            });
        });
    </script>
@endsection
