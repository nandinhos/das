import './bootstrap';

document.addEventListener('livewire:init', () => {
    Alpine.data('currencyInput', () => ({
        format(event) {
            const digits = event.target.value.replace(/\D/g, '').slice(0, 13);
            if (!digits) {
                event.target.value = '';
                return;
            }
            const numeric = parseFloat(digits) / 100;
            if (numeric > 4800000) {
                event.target.value = (4800000).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                return;
            }
            event.target.value = numeric.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }));
});
