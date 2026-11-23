<form action="{{ route('order.return') }}" method="POST">
    @csrf
    <input type="hidden" name="qr_token" value="{{ $qrToken ?? '' }}">
    
    <button type="submit" name="item_type" value="key" class="return-button">Return Key</button>
    <button type="submit" name="item_type" value="remote" class="return-button">Return Remote</button>
    <button type="submit" name="item_type" value="both" class="return-button">Return Both</button>
</form>
