import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
Livewire.start();

window.Chart = Chart;
