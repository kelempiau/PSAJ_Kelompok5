function calculateTotal() {
    const typeSelect = document.getElementById('type');
    const addon1Select = document.getElementById('addon');
    const addon2Select = document.getElementById('addon2');
    const paymentTypeSelect = document.getElementById('payment_type');
    const dpWarning = document.getElementById('dp-warning');

    // Robust check for required elements
    if (!typeSelect || !addon1Select || !addon2Select || !paymentTypeSelect) {
        console.error("Missing required elements for calculation");
        return;
    }

    const paymentType = paymentTypeSelect.value;
    const mainPrice = parseInt(typeSelect.value) || 0;
    const addon1Price = parseInt(addon1Select.value) || 0;
    const addon2Price = parseInt(addon2Select.value) || 0;

    const baseTotal = mainPrice + addon1Price + addon2Price;

    // Check if it is a DP type (dp_transfer, dp_cash)
    const isDP = paymentType.indexOf('dp') !== -1;
    const toPay = isDP ? (baseTotal / 2) : baseTotal;

    const formattedTotal = 'Rp' + baseTotal.toLocaleString('id-ID');
    const formattedToPay = 'Rp' + toPay.toLocaleString('id-ID');

    // 1. Update Top Price Display
    const totalPriceEl = document.getElementById('total-price');
    if (totalPriceEl) {
        totalPriceEl.innerText = formattedToPay;
    }

    // 2. Update Hidden Field for Server
    const hiddenTotalEl = document.getElementById('hidden-total');
    if (hiddenTotalEl) {
        hiddenTotalEl.value = formattedTotal;
    }

    // 3. DP Warning Visibility
    if (dpWarning) {
        dpWarning.style.display = isDP ? 'block' : 'none';
    }

    // 4. Populate Order Summary List
    const summaryList = document.getElementById('summary-list');
    if (summaryList) {
        summaryList.innerHTML = '';

        // Main Service Row
        if (mainPrice > 0) {
            const label = typeSelect.options[typeSelect.selectedIndex].text;
            addSummaryRow(summaryList, label, mainPrice);
        }

        // Addon 1 Row
        if (addon1Price > 0) {
            const label = addon1Select.options[addon1Select.selectedIndex].text;
            addSummaryRow(summaryList, label, addon1Price);
        }

        // Addon 2 Row
        if (addon2Price > 0) {
            const label = addon2Select.options[addon2Select.selectedIndex].text;
            addSummaryRow(summaryList, label, addon2Price);
        }

        // DP Deduction Row (If applicable)
        if (isDP) {
            const dpRow = document.createElement('li');
            dpRow.className = 'summary-item';
            dpRow.style.color = '#e67e22';
            dpRow.style.fontWeight = '600';
            dpRow.style.marginTop = '5px';
            dpRow.style.paddingTop = '5px';
            dpRow.style.borderTop = '1px dashed #eee';
            dpRow.innerHTML = `<span>Potongan DP (50%)</span><span class="price">-Rp${(baseTotal / 2).toLocaleString('id-ID')}</span>`;
            summaryList.appendChild(dpRow);
        }
    }

    // 5. Update Payment Summary Box
    const summaryPayEl = document.getElementById('summary-pay');
    if (summaryPayEl) {
        summaryPayEl.innerText = formattedToPay;
    }

    // 6. Update Bottom Note
    const dpNote = document.getElementById('dp-note');
    if (dpNote) {
        if (isDP) {
            const sisaMethod = (paymentType === 'dp_cash') ? 'Cash di Studio' : 'Transfer (Pelunasan)';
            dpNote.innerText = `* Sisa ${formattedToPay} dapat dilunasi via ${sisaMethod}.`;
        } else {
            dpNote.innerText = "";
        }
    }
}

function addSummaryRow(parent, label, price) {
    const li = document.createElement('li');
    li.className = 'summary-item';
    li.style.display = 'flex';
    li.style.justifyContent = 'space-between';
    li.style.marginBottom = '5px';

    // Clean label (e.g., "Gel Polish - Rp50.000" -> "Gel Polish")
    let cleanLabel = label.split(' - ')[0].split(' (+')[0];

    li.innerHTML = `<span style="color: #666;">${cleanLabel}</span><span class="price" style="font-weight: 600; color: #333;">Rp${price.toLocaleString('id-ID')}</span>`;
    parent.appendChild(li);
}

function showPaymentDetail() {
    const methodSelect = document.getElementById('payment');
    if (!methodSelect) return;

    const method = methodSelect.value;
    const details = {
        'bca': document.getElementById('detail-bca'),
        'qris': document.getElementById('detail-qris'),
        'gopay': document.getElementById('detail-gopay'),
        'shopeepay': document.getElementById('detail-shopeepay'),
        'dana': document.getElementById('detail-dana')
    };

    // Hide all first
    for (let key in details) {
        if (details[key]) details[key].style.display = 'none';
    }

    // Show selected
    if (details[method]) {
        details[method].style.display = 'block';
    }
}

// Ensure first run after DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        calculateTotal();
        showPaymentDetail();
    });
} else {
    calculateTotal();
    showPaymentDetail();
}

// Add event listeners as fallback to onchange attributes
window.onload = function () {
    ['type', 'addon', 'addon2', 'payment_type'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', calculateTotal);
    });

    const payEl = document.getElementById('payment');
    if (payEl) payEl.addEventListener('change', showPaymentDetail);
};