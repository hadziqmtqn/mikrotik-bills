<link rel="stylesheet" href="{{ asset('css/customer-service/style.css') }}">

<div class="mb-header">
    <div class="mb-invoice-title">Layanan Pelanggan</div>
    <div class="mb-invoice-meta">
        <div><strong>Invoice #:</strong> {{ 'invoiceNumber' }}</div>
        <div><strong>Date:</strong> {{ 'createdDate' }}</div>
        <div><strong>Due:</strong> {{ 'dueDate' }}</div>
    </div>
</div>

<div class="mb-content">
    <div class="mb-parties">
        <div class="mb-party">
            <h3>FROM</h3>
            <strong>{{ 'sender.name' }}</strong><br>
            {{ 'sender.address1' }}<br>
            {{ 'sender.address2' }}<br>
            {{ 'sender.tax' }}
        </div>
        <div class="mb-party" style="text-align: right;">
            <h3>TO</h3>
            <strong>{{ 'receiver.name' }}</strong><br>
            {{ 'receiver.address1' }}<br>
            {{ 'receiver.address2' }}<br>
            {{ 'receiver.tax' }}
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th style="text-align: right;">Hours</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        </thead>
        <tbody>
        {% for item in items %}
        <tr>
            <td>{{ 'item.description' }}</td>
            <td style="text-align: right;">{{ 'item.hours' }}</td>
            <td style="text-align: right;">{{ 'currency' }} {{ 'item.price' }}</td>
        </tr>
        {% endfor %}
        </tbody>
    </table>

    <table class="mb-totals">
        <tr><td>Subtotal:</td><td style="text-align: right;">{{ 'currency' }} {{ 'subTotal' }}</td></tr>
        <tr><td>Tax ({{ 'taxRate' }}%):</td><td style="text-align: right;">{{ 'currency' }} {{ 'taxAmount' }}</td></tr>
        <tr class="mb-total-row"><td>Total:</td><td style="text-align: right;">{{ 'currency' }} {{ 'total' }}</td></tr>
    </table>

    <div class="mb-notes">
        <strong style="color: #667eea;">Notes:</strong><br>
        {{ 'footerText' }}<br>
        {{ 'footerText2' }}
    </div>
</div>
