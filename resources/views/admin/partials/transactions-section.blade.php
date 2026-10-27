@php
    $txnsList = $transactions ?? \App\Models\Transaction::orderByDesc('transacted_at')->paginate(10);
    $mAcc = $merchantAccount ?? \App\Models\Setting::getValue('merchant_account', '01812-XXXXXX');
    $cRate = $chargesRate ?? \App\Models\Setting::getValue('charges_rate', '1.80% + ৳5.00');
    $aBal = $availableBalance ?? \App\Models\Setting::getValue('available_balance', '125,482.36');
@endphp

<!-- Top 3-Column Summary Card (Exact Match to Screenshot 8) -->
<div class="sb-summary-strip">
    <div class="sb-summary-item">
        <div class="sb-summary-icon">
            <i class="bi bi-shop"></i>
        </div>
        <div>
            <div class="sb-summary-label">Merchant Account Number</div>
            <div class="sb-summary-value">
                <span id="merchantAccText">{{ $mAcc }}</span>
                <i class="bi bi-copy" style="font-size: 14px; color: #64748B; cursor: pointer;" onclick="navigator.clipboard.writeText('{{ $mAcc }}')" title="Copy"></i>
            </div>
        </div>
    </div>

    <div class="sb-summary-item">
        <div class="sb-summary-icon">
            <i class="bi bi-percent"></i>
        </div>
        <div>
            <div class="sb-summary-label">Charges / Rates</div>
            <div class="sb-summary-value">{{ $cRate }}</div>
            <div style="font-size: 11px; color: #64748B; margin-top: 1px;">Per transaction</div>
        </div>
    </div>

    <div class="sb-summary-item">
        <div class="sb-summary-icon">
            <i class="bi bi-wallet2"></i>
        </div>
        <div>
            <div class="sb-summary-label">Available Balance</div>
            <div class="sb-summary-value">
                <span>&#2547; {{ $aBal }}</span>
                <a href="{{ request()->url() }}" style="color: #64748B; text-decoration: none;"><i class="bi bi-arrow-repeat" style="font-size: 15px; cursor: pointer;" title="Refresh Balance"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar Header -->
<form action="{{ request()->url() }}" method="GET" style="display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
    <div>
        <h3 style="font-size: 24px; font-weight: 800; color: #0B2545; margin: 0 0 4px 0;">Recent Transactions</h3>
        <p style="font-size: 13px; color: #64748B; margin: 0;">View and manage your latest bKash transactions. You can search, filter and refresh the data as needed.</p>
    </div>

    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <div class="dropdown">
            <button type="button" data-bs-toggle="dropdown" style="height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px; font-size: 13px; font-weight: 500; color: #1E293B; display: inline-flex; align-items: center; gap: 10px;">
                <i class="bi bi-calendar3" style="color: #475569;"></i>
                <span>{{ request('type') ? 'Type: ' . request('type') : '12 Oct 2025 - 19 Oct 2025' }}</span>
                <i class="bi bi-chevron-down" style="font-size: 11px; color: #64748B;"></i>
            </button>
            <ul class="dropdown-menu shadow-sm border-0" style="font-size: 13px; border-radius: 10px;">
                <li><a class="dropdown-item" href="{{ request()->url() }}">All Transactions</a></li>
                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Payment']) }}">Payment Only</a></li>
                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Settlement']) }}">Settlement Only</a></li>
            </ul>
        </div>

        <div style="position: relative; width: 265px;">
            <i class="bi bi-search" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 14px;"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Txn ID, Wallet or Mobile..." style="width: 100%; height: 42px; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 9px; padding: 0 14px 0 36px; font-size: 13px; color: #0F172A; outline: none;">
        </div>

        <a href="{{ request()->url() }}" style="height: 42px; background: #D8125B; color: #FFFFFF; border: none; border-radius: 9px; padding: 0 18px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; text-decoration: none; box-shadow: 0 4px 10px rgba(216, 18, 91, 0.2);">
            <i class="bi bi-arrow-clockwise" style="font-size: 15px;"></i>
            <span>Refresh Data</span>
        </a>
    </div>
</form>

<!-- Data Table Card -->
<div class="sb-table-card">
    <div style="overflow-x: auto;">
        <table class="sb-data-table">
            <thead>
                <tr>
                    <th>Date Time <i class="bi bi-chevron-expand"></i></th>
                    <th>From Wallet Number <i class="bi bi-chevron-expand"></i></th>
                    <th>Transaction Amount <i class="bi bi-chevron-expand"></i></th>
                    <th>Coupon Amount <i class="bi bi-chevron-expand"></i></th>
                    <th>Amount after<br>Coupon Discount <i class="bi bi-chevron-expand"></i></th>
                    <th>Cashback Amount <i class="bi bi-chevron-expand"></i></th>
                    <th>Transaction Type <i class="bi bi-chevron-expand"></i></th>
                    <th>Channel <i class="bi bi-chevron-expand"></i></th>
                    <th>Transaction ID <i class="bi bi-chevron-expand"></i></th>
                </tr>
            </thead>
            <tbody>
                @forelse($txnsList as $txn)
                    <tr>
                        <td>{{ $txn->transacted_at->format('d M Y H:i:s') }}</td>
                        <td>{{ $txn->wallet_number }}</td>
                        <td class="fw-bold">&#2547; {{ number_format($txn->amount, 2) }}</td>
                        <td>&#2547; {{ number_format($txn->coupon_amount, 2) }}</td>
                        <td class="fw-bold">&#2547; {{ number_format($txn->net_amount, 2) }}</td>
                        <td>&#2547; {{ number_format($txn->cashback_amount, 2) }}</td>
                        <td>
                            <span class="sb-badge-pill {{ $txn->type === 'Settlement' ? 'sb-badge-green' : 'sb-badge-blue' }}">
                                {{ $txn->type }}
                            </span>
                        </td>
                        <td>{{ $txn->channel }}</td>
                        <td>{{ $txn->transaction_id }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No matching transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="sb-table-footer">
        <div>Showing {{ $txnsList->firstItem() ?? 0 }} to {{ $txnsList->lastItem() ?? 0 }} of 125 transactions</div>
        <div class="sb-pagination">
            <a href="{{ $txnsList->previousPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-left"></i></a>
            <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="sb-page-btn active">1</a>
            <a href="#" class="sb-page-btn">2</a>
            <a href="#" class="sb-page-btn">3</a>
            <a href="#" class="sb-page-btn">4</a>
            <a href="#" class="sb-page-btn">5</a>
            <span style="padding: 0 4px; color: #94A3B8;">...</span>
            <a href="#" class="sb-page-btn">13</a>
            <a href="{{ $txnsList->nextPageUrl() ?? '#' }}" class="sb-page-btn"><i class="bi bi-chevron-right"></i></a>
        </div>
    </div>
</div>
