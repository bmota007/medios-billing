@extends('layouts.admin')

@section('content')
<style>
    /* Visibility Fix for Glass Theme */
    .form-control, .form-select {
        color: #ffffff !important; 
        background-color: rgba(15, 23, 42, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* FIX: This makes the calendar/date icon white */
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }

    .form-select option {
        background-color: #1e293b;
        color: white;
    }
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }
    .text-label {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
</style>

<div class="container-fluid">
    <div class="mb-5">
        <h2 class="text-white font-bold">Create <span class="text-sky-400">Invoice</span></h2>
        <p class="text-secondary small">Generate a professional billing document for your client</p>
    </div>

    <form method="POST" action="{{ route('invoice.send') }}">
        @csrf
        <div class="row g-4">
            <div class="col-md-4">
                <div class="glass-card h-100">
                    <h5 class="text-white mb-4"><i class="fa-solid fa-user-tag text-sky-400 mr-2"></i> Client Info</h5>

                    <div class="mb-4">
                        <label class="text-label">Select Customer</label>
                        <select id="customer_select" name="customer_id" class="form-control mt-2">
                            <option value="">-- Choose --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    data-name="{{ $customer->name }}" 
                                    data-email="{{ $customer->email }}"
                                    data-street="{{ $customer->billing_address }}"
                                    data-city="{{ $customer->city }}"
                                    data-state="{{ $customer->state }}"
                                    data-zip="{{ $customer->zip }}">
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="customer_name">
                    <input type="hidden" name="customer_email">
                    <input type="hidden" name="street_address">
                    <input type="hidden" name="city_state_zip">

                    <div class="mb-4">
                        <label class="text-label">Issue Date</label>
                        <input type="date" name="invoice_date" class="form-control mt-2" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-4">
                        <label class="text-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control mt-2" required>
                    </div>

                    @if(auth()->user()->role == 'super_admin')
                    <div class="p-3 rounded bg-sky-500/10 border border-sky-500/20 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_subscription" value="1" id="isSub">
                            <label class="form-check-label text-white font-bold small" for="isSub">
                                MARK AS SaaS SUBSCRIPTION
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-md-8">
                <div class="glass-card">
                    <h5 class="text-white mb-4"><i class="fa-solid fa-list text-sky-400 mr-2"></i> Services & Items</h5>
                    
                    <table class="table table-dark border-transparent" id="items">
                        <thead class="text-secondary small uppercase">
                            <tr>
                                <th width="60%">Description</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input name="items[0][desc]" class="form-control bg-transparent border-slate-700 text-white" placeholder="Service name..." required></td>
                                <td><input type="number" name="items[0][qty]" class="form-control bg-transparent border-slate-700 text-white qty" value="1"></td>
                                <td><input type="number" name="items[0][price]" class="form-control bg-transparent border-slate-700 text-white price" placeholder="0.00" step="0.01"></td>
                                <td class="text-end text-white font-bold pt-3 lineTotal">$0.00</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- FIXED: Button visibility improved with Sky Blue --}}
<button type="button" class="btn text-white opacity-100 font-bold small p-0 mt-3" onclick="addRow()">
    <i class="fa-solid fa-plus-circle me-1 text-sky-400"></i> Add Line Item
</button>
                    </button>

                    <div class="row justify-content-end mt-5">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-secondary small uppercase">Grand Total</span>
                                <span class="text-sky-400 text-2xl font-bold font-mono">$<span id="grandTotal">0.00</span></span>
                            </div>
                            <button class="btn btn-primary w-100 py-3 font-bold" type="submit">
                                PREVIEW & GENERATE
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function money(n){ return Number(n).toFixed(2); }
function recalc(){
    let sub = 0;
    document.querySelectorAll('#items tbody tr').forEach(r => {
        let q = parseFloat(r.querySelector('.qty')?.value) || 0;
        let p = parseFloat(r.querySelector('.price')?.value) || 0;
        let t = q * p;
        r.querySelector('.lineTotal').innerText = '$' + money(t);
        sub += t;
    });
    document.getElementById('grandTotal').innerText = money(sub);
}
document.addEventListener('input', recalc);

function addRow(){
    let tbody = document.querySelector('#items tbody');
    let i = tbody.children.length;
    let tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input name="items[${i}][desc]" class="form-control bg-transparent border-slate-700 text-white" required></td>
        <td><input type="number" name="items[${i}][qty]" class="form-control bg-transparent border-slate-700 text-white qty" value="1"></td>
        <td><input type="number" name="items[${i}][price]" class="form-control bg-transparent border-slate-700 text-white price" value="0" step="0.01"></td>
        <td class="text-end text-white font-bold pt-3 lineTotal">$0.00</td>
        <td><button type="button" class="btn btn-link text-danger" onclick="this.closest('tr').remove(); recalc();">X</button></td>
    `;
    tbody.appendChild(tr);
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('customer_select').addEventListener('change', function () {
        let sel = this.options[this.selectedIndex];
        if(!sel.value) return;
        
        document.querySelector('[name="customer_name"]').value = sel.getAttribute('data-name');
        document.querySelector('[name="customer_email"]').value = sel.getAttribute('data-email');
        document.querySelector('[name="street_address"]').value = sel.getAttribute('data-street');
        
        let city = sel.getAttribute('data-city') || '';
        let state = sel.getAttribute('data-state') || '';
        let zip = sel.getAttribute('data-zip') || '';
        
        document.querySelector('[name="city_state_zip"]').value = city + ", " + state + " " + zip;
    });
});
</script>
@endsection
