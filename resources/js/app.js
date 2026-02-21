import './bootstrap';

// Importa Livewire e Alpine como um conjunto integrado (recomendado pelo Livewire 4)
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import collapse from '@alpinejs/collapse';

// Registra plugins Alpine antes de inicializar
Alpine.plugin(collapse);

// Componente de máscara de moeda (BRL) reutilizável
Alpine.data('currencyInput', () => ({
    format(event) {
        const digits = event.target.value.replace(/\D/g, '').slice(0, 13);
        if (!digits) {
            event.target.value = '';
            return;
        }
        const numeric = parseFloat(digits) / 100;
        // Limite máximo: R$ 4.800.000,00 (teto do Simples Nacional)
        if (numeric > 4800000) {
            event.target.value = (4800000).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            return;
        }
        event.target.value = numeric.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}));

// Livewire.start() inicializa Livewire e Alpine juntos, na ordem correta
Livewire.start();
