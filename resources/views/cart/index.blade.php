<x-app>
    <div class="cart-container">
        <div class="cart-header">
            <h2>Your Cart</h2>
        </div>

        @php $cart = session('cart', []); @endphp

        @if(count($cart) > 0)
            <div class="cart-items">
                @php $total = 0; @endphp

                @foreach($cart as $id => $item)
                    @php $total += $item['price'] * $item['quantity']; @endphp

                    <div class="cart-item">
                        <div class="item-image">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}">
                        </div>
                        <div class="item-details">
                            <span class="item-name">{{ $item['name'] }}</span>
                            <span class="item-price">${{ $item['price'] }} x {{ $item['quantity'] }}</span>
                        </div>
                        <div class="item-remove">
                            <a href="/cart/remove/{{ $id }}">Remove</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="cart-summary">
                <div class="total-price">
                    Total: ${{ $total }}
                </div>

                <form method="POST" target="aba_webservice"
                    action="https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase"
                    id="aba_merchant_request">
                    @csrf
                    <input type="hidden" name="hash" value="{{ $hash ?? '' }}" id="hash" />
                    <input type="hidden" name="tran_id" value="{{ $tranId ?? '' }}" id="tran_id" />
                    <input type="hidden" name="amount" value="{{ $amount ?? '' }}" id="amount" />
                    <input type="hidden" name="payment_option" value="{{ $payment_option ?? '' }}" />
                    <input type="hidden" name="merchant_id" value="{{ $merchant_id ?? '' }}" />
                    <input type="hidden" name="req_time" value="{{ $req_time ?? '' }}" />
                    <input type="hidden" name="continue_success_url" value="{{ $continue_success_url ?? '' }}" />
                    <input type="hidden" name="currency" value="{{ $currency ?? '' }}" />

                </form>

                <input type="button" id="checkout_button" value="Checkout Now">
            </div>

        @else
            <div class="empty-state">
                <div class="empty-icon">🛒</div>
                <div class="empty-text">Your cart is currently empty.</div>
                <a href="/" class="btn-continue">Continue Shopping</a>
            </div>
        @endif
    </div>

</x-app>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>

<script>
    $(document).ready(function () {
        $('#checkout_button').click(function () {
            // Append selected payment option if it exists elsewhere in your DOM
            if ($(".payment_option:checked").length > 0) {
                $('#aba_merchant_request').append($(".payment_option:checked"));
            }
            AbaPayway.checkout();
        });
    });
</script>