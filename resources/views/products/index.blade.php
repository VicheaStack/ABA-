<x-app>


    <div class="products-container">
        @foreach ($products as $product)
            <div class="product-card">
                <img src="{{ $product->image }}" alt="{{ $product->name }}">
                <h4>{{ $product->name }}</h4>
                <p style="font-size: 1.2em; font-weight: bold;">${{ $product->price }}</p>
                <form action="/cart/add/{{ $product->id }}" method="POST">
                    @csrf
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        @endforeach
    </div>

    <a href="/cart" class="cart-link">View Cart</a>

</x-app>
<style>
    .products-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }

    .product-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .product-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .product-card h4 {
        margin: 10px 0;
    }

    .product-card button {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
    }

    .product-card button:hover {
        background-color: #0056b3;
    }

    .cart-link {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 4px;
    }
</style>