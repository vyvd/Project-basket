<style>

</style>
<div class="assessmentBasket text-center">
    <h4>Your Basket</h4>
    <h2 v-if="orderSummaryProcessing" class="text-center"><i class="fas fa-spin fa-spinner"></i></h2>
    <div v-else-if="cartItems.length" class="cart-items align-items-center">
        <p>{{cartItems.length}} X Module Assessment</p>
        <h5>Total to Pay - {{ totalPrice }}</h5>
    </div>
    <div v-else class="text-center col-12">
        <h5>Your Cart is Empty!</h5>
    </div>
</div>