async function checkAvailability() {
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');
    const warningDiv = document.getElementById('availability-warning');

    if (!dateInput || !dateInput.value) return;

    const selectedDate = dateInput.value;

    try {
        const response = await fetch(`../api/check_availability.php?date=${selectedDate}`);
        const data = await response.json();

        if (data.success && data.unavailable_times) {
            const timeOptions = timeSelect.querySelectorAll('option');
            timeOptions.forEach(option => {
                if (option.value) {
                    if (!option.dataset.originalText) {
                        option.dataset.originalText = option.textContent;
                    }

                    const isUnavailable = data.unavailable_times.includes(option.value);
                    option.disabled = isUnavailable;

                    if (isUnavailable) {
                        option.style.color = '#ccc';
                        option.textContent = option.dataset.originalText + (data.is_past_date ? ' (Sudah Lewat)' : ' (Penuh)');
                    } else {
                        option.style.color = '';
                        option.textContent = option.dataset.originalText;
                    }
                }
            });

            if (data.is_past_date) {
                showPastDateWarning();
                timeSelect.value = '';
            } else if (timeSelect.value && data.unavailable_times.includes(timeSelect.value)) {
                showAvailabilityWarning(timeSelect.value);
                timeSelect.value = '';
            } else if (warningDiv) {
                warningDiv.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error checking availability:', error);
    }
}

async function checkTimeAvailability() {
    const dateInput = document.getElementById('date');
    const timeSelect = document.getElementById('time');

    if (!dateInput.value || !timeSelect.value) return;

    const selectedDate = dateInput.value;
    const selectedTime = timeSelect.value;

    try {
        const response = await fetch(`../api/check_availability.php?date=${selectedDate}&time=${selectedTime}`);
        const data = await response.json();

        if (data.success && !data.available) {
            showAvailabilityWarning(selectedTime);
        } else {
            hideAvailabilityWarning();
        }
    } catch (error) {
        console.error('Error checking time availability:', error);
    }
}

function showPastDateWarning() {
    let warningDiv = document.getElementById('availability-warning');

    if (!warningDiv) {
        warningDiv = createWarningDiv();
        const dateGroup = document.querySelector('.form-grid-2');
        if (dateGroup) dateGroup.appendChild(warningDiv);
    }

    warningDiv.innerHTML = `
        <span style="font-size: 1.2rem;">🚫</span>
        <span>Maaf, tanggal ini sudah terlewat. Silakan pilih tanggal hari ini atau yang akan datang.</span>
    `;
    warningDiv.style.display = 'flex';
}

function createWarningDiv() {
    const div = document.createElement('div');
    div.id = 'availability-warning';
    div.style.cssText = `
        margin-top: 10px;
        padding: 12px 15px;
        background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
        border-left: 4px solid #ef4444;
        border-radius: 8px;
        color: #dc2626;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideDown 0.3s ease-out;
    `;
    return div;
}

function showAvailabilityWarning(time) {
    let warningDiv = document.getElementById('availability-warning');

    if (!warningDiv) {
        warningDiv = createWarningDiv();
        const dateGroup = document.querySelector('.form-grid-2');
        if (dateGroup) dateGroup.appendChild(warningDiv);
    }

    const now = new Date();
    const currentHour = now.getHours();
    const currentMin = now.getMinutes();
    const [h, m] = time.split(':').map(Number);

    const dateInput = document.getElementById('date');
    const isToday = dateInput.value === new Date().toISOString().split('T')[0];

    if (isToday && (h < currentHour || (h === currentHour && m <= currentMin))) {
        warningDiv.innerHTML = `
            <span style="font-size: 1.2rem;">⏱️</span>
            <span>Maaf, jam <strong>${time}</strong> untuk hari ini sudah terlewat.</span>
        `;
    } else {
        warningDiv.innerHTML = `
            <span style="font-size: 1.2rem;">⚠️</span>
            <span>Maaf, jadwal <strong>${time}</strong> sudah direservasi. Silakan pilih jam lain.</span>
        `;
    }
    warningDiv.style.display = 'flex';
}

function hideAvailabilityWarning() {
    const warningDiv = document.getElementById('availability-warning');
    if (warningDiv) {
        warningDiv.style.display = 'none';
    }
}

function calculateTotal() {
    const typeSelect = document.getElementById('type');
    const addon1Select = document.getElementById('addon');
    const addon2Select = document.getElementById('addon2');
    const paymentTypeSelect = document.getElementById('payment_type');
    const dpWarning = document.getElementById('dp-warning');

    if (!typeSelect || !addon1Select || !addon2Select || !paymentTypeSelect) {
        console.error("Missing required elements for calculation");
        return;
    }

    const paymentType = paymentTypeSelect.value;
    const mainPrice = parseInt(typeSelect.value) || 0;
    const addon1Price = parseInt(addon1Select.value) || 0;
    const addon2Price = parseInt(addon2Select.value) || 0;

    const baseTotal = mainPrice + addon1Price + addon2Price;

    const isDP = paymentType.indexOf('dp') !== -1;
    const dpPercent = window.MIN_DP_PERCENT || 50;
    const toPay = isDP ? (baseTotal * (dpPercent / 100)) : baseTotal;

    const formattedTotal = 'Rp' + baseTotal.toLocaleString('id-ID');
    const formattedToPay = 'Rp' + toPay.toLocaleString('id-ID');

    const totalPriceEl = document.getElementById('total-price');
    if (totalPriceEl) {
        totalPriceEl.innerText = formattedToPay;
    }

    const hiddenTotalEl = document.getElementById('hidden-total');
    const hiddenServiceNameEl = document.getElementById('hidden-service-name');

    if (hiddenTotalEl) {
        hiddenTotalEl.value = formattedTotal;
    }

    if (hiddenServiceNameEl && mainPrice > 0) {
        const fullLabel = typeSelect.options[typeSelect.selectedIndex].text;
        hiddenServiceNameEl.value = fullLabel.split(' - ')[0].trim();
    }

    if (dpWarning) {
        dpWarning.style.display = isDP ? 'block' : 'none';
    }

    const summaryList = document.getElementById('summary-list');
    if (summaryList) {
        summaryList.innerHTML = '';

        if (mainPrice > 0) {
            const label = typeSelect.options[typeSelect.selectedIndex].text;
            addSummaryRow(summaryList, label, mainPrice);
        }

        if (addon1Price > 0) {
            const label = addon1Select.options[addon1Select.selectedIndex].text;
            addSummaryRow(summaryList, label, addon1Price);
        }

        if (addon2Price > 0) {
            const label = addon2Select.options[addon2Select.selectedIndex].text;
            addSummaryRow(summaryList, label, addon2Price);
        }

        if (isDP) {
            const dpRow = document.createElement('li');
            dpRow.className = 'summary-item';
            dpRow.style.color = '#e67e22';
            dpRow.style.fontWeight = '600';
            dpRow.style.marginTop = '5px';
            dpRow.style.paddingTop = '5px';
            dpRow.style.borderTop = '1px dashed #eee';
            dpRow.innerHTML = `<span>Potongan DP (${dpPercent}%)</span><span class="price">-Rp${(baseTotal * (dpPercent / 100)).toLocaleString('id-ID')}</span>`;
            summaryList.appendChild(dpRow);
        }
    }

    const summaryPayEl = document.getElementById('summary-pay');
    if (summaryPayEl) {
        summaryPayEl.innerText = formattedToPay;
    }

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

    let cleanLabel = label.split(' - ')[0].split(' (+')[0];

    li.innerHTML = `<span style="color: #666;">${cleanLabel}</span><span class="price" style="font-weight: 600; color: #333;">Rp${price.toLocaleString('id-ID')}</span>`;
    parent.appendChild(li);
}

function showPaymentDetail() {
    const methodSelect = document.getElementById('payment');
    if (!methodSelect) return;

    const selectedId = methodSelect.value;

    const allDetailDivs = document.querySelectorAll('.payment-info');

    allDetailDivs.forEach(div => {
        div.style.display = 'none';
        div.classList.remove('active');
    });

    if (!selectedId) return;

    const targetDiv = document.getElementById('detail-pm-' + selectedId);
    if (targetDiv) {
        targetDiv.style.display = 'block';
        targetDiv.classList.add('active');

        targetDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        if (allDetailDivs.length === 1) {
            allDetailDivs[0].style.display = 'block';
            allDetailDivs[0].classList.add('active');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        calculateTotal();
        showPaymentDetail();
    }, 100);
});

window.onload = function () {
    ['type', 'addon', 'addon2', 'payment_type'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', calculateTotal);
    });

    const payEl = document.getElementById('payment');
    if (payEl) payEl.addEventListener('change', showPaymentDetail);

    const dateEl = document.getElementById('date');
    if (dateEl) {
        dateEl.addEventListener('change', checkAvailability);

        if (dateEl.value) {
            checkAvailability();
        }
    }

    const timeEl = document.getElementById('time');
    if (timeEl) {
        timeEl.addEventListener('change', checkTimeAvailability);
    }

    // Prevent submitting if total is 0
    const reservasiForm = document.querySelector('.reservasi-form');
    if (reservasiForm) {
        reservasiForm.addEventListener('submit', function (e) {
            const totalStr = document.getElementById('hidden-total')?.value || 'Rp0';
            const totalVal = parseInt(totalStr.replace(/[^0-9]/g, '')) || 0;

            if (totalVal <= 0) {
                e.preventDefault();
                alert('Silakan pilih layanan terlebih dahulu ya Kak! ✨');
                return false;
            }
        });
    }
};

const style = document.createElement('style');
style.textContent = `
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    select option:disabled {
        color: #ccc !important;
        background: #f5f5f5 !important;
    }
`;
document.head.appendChild(style);
