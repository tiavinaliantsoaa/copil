import { Chart, ArcElement, BarController, BarElement, CategoryScale, DoughnutController, Legend, LinearScale, Tooltip } from 'chart.js';
import {
    Archive, BriefcaseBusiness, CalendarDays, CalendarPlus, ChartNoAxesCombined,
    ChevronLeft, ChevronRight, CircleAlert, CircleCheck, Cpu,
    Eye, FileDown, FileText, GraduationCap, Images, LayoutDashboard, ListChecks,
    LockKeyhole, LockOpen, LogIn, LogOut, Maximize, MonitorPlay, PanelsTopLeft,
    Plus, Presentation, Save, Trash2, TrendingUp, Upload, UserPlus, Users,
    UserX, WandSparkles, X, createIcons,
} from 'lucide';

const appIcons = {
    Archive, BriefcaseBusiness, CalendarDays, CalendarPlus, ChartNoAxesCombined,
    ChevronLeft, ChevronRight, CircleAlert, CircleCheck, Cpu, Eye, FileDown,
    FileText, GraduationCap, Images, LayoutDashboard, ListChecks, LockKeyhole,
    LockOpen, LogIn, LogOut, Maximize, MonitorPlay, PanelsTopLeft, Plus,
    Presentation, Save, Trash2, TrendingUp, Upload, UserPlus, Users, UserX,
    WandSparkles, X,
};

Chart.register(ArcElement, BarController, BarElement, CategoryScale, DoughnutController, Legend, LinearScale, Tooltip);

const platformColors = ['#1877F2', '#E4405F', '#111111', '#0A66C2', '#34A853', '#EA4335', '#F4B400'];

function initialiseNavigation() {
    const buttons = [...document.querySelectorAll('[data-nav]')];
    const panels = [...document.querySelectorAll('[data-panel]')];
    const mobile = document.querySelector('[data-mobile-nav]');
    if (!buttons.length || !panels.length) return;

    const show = (id) => {
        if (!document.querySelector(`[data-panel="${id}"]`)) id = 'dashboard';
        panels.forEach((panel) => panel.hidden = panel.dataset.panel !== id);
        buttons.forEach((button) => button.classList.toggle('active', button.dataset.nav === id));
        if (mobile) mobile.value = id;
        history.replaceState(null, '', `#${id}`);
        window.scrollTo({ top: 0, behavior: 'instant' });
    };
    buttons.forEach((button) => button.addEventListener('click', () => show(button.dataset.nav)));
    mobile?.addEventListener('change', () => show(mobile.value));
    show(location.hash.slice(1) || 'dashboard');
}

function initialisePlatformTabs() {
    document.querySelectorAll('[data-platform-tabs]').forEach((group) => {
        const buttons = [...group.querySelectorAll('[data-platform-tab]')];
        const target = group.dataset.platformTabs;
        const panels = [...document.querySelectorAll(`[data-platform-group="${target}"]`)]
        const show = (id) => {
            buttons.forEach((button) => button.classList.toggle('active', button.dataset.platformTab === id));
            panels.forEach((panel) => panel.hidden = panel.dataset.platformId !== id);
        };
        buttons.forEach((button) => button.addEventListener('click', () => show(button.dataset.platformTab)));
        if (buttons[0]) show(buttons[0].dataset.platformTab);
    });
}

function chartOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, padding: 18 } } },
    };
}

function initialiseCharts() {
    const data = window.escmCharts;
    if (!data) return;
    const leads = document.getElementById('leadsChart');
    if (leads) new Chart(leads, {
        type: 'doughnut',
        data: { labels: data.platformLabels, datasets: [{ data: data.leads, backgroundColor: platformColors, borderColor: '#ffffff', borderWidth: 3 }] },
        options: { ...chartOptions(), cutout: '62%' },
    });
    const comparison = document.getElementById('comparisonChart');
    if (comparison) new Chart(comparison, {
        type: 'bar',
        data: { labels: data.platformLabels, datasets: [
            { label: data.currentLabel, data: data.leads, backgroundColor: '#d9252a', borderRadius: 4 },
            { label: data.previousLabel, data: data.previousLeads, backgroundColor: '#c8cdd2', borderRadius: 4 },
        ] },
        options: { ...chartOptions(), scales: { y: { beginAtZero: true, grid: { color: '#eef0f2' } }, x: { grid: { display: false } } } },
    });
    const training = document.getElementById('trainingChart');
    if (training) new Chart(training, {
        type: 'doughnut',
        data: { labels: data.trainingLabels, datasets: [{ data: data.trainingLeads, backgroundColor: platformColors.slice(0, 5), borderColor: '#ffffff', borderWidth: 3 }] },
        options: { ...chartOptions(), cutout: '58%' },
    });
}

function initialiseLists() {
    document.querySelectorAll('[data-add-list]').forEach((button) => {
        button.addEventListener('click', () => {
            const list = document.querySelector(`[data-list="${button.dataset.addList}"]`);
            const template = document.getElementById(button.dataset.template);
            if (!list || !template) return;
            const index = Date.now();
            const uuid = crypto.randomUUID();
            const fragment = template.content.cloneNode(true);
            fragment.querySelectorAll('[name]').forEach((input) => input.name = input.name.replaceAll('__INDEX__', index).replaceAll('__UUID__', uuid));
            fragment.querySelectorAll('[data-category-template]').forEach((node) => node.value = node.value.replaceAll('__UUID__', uuid));
            list.appendChild(fragment);
            createIcons({ icons: appIcons });
        });
    });
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-remove-item]');
        if (button) button.closest('[data-list-item]')?.remove();
    });
}

function initialiseFilePreviews() {
    document.addEventListener('change', (event) => {
        if (!event.target.matches('[data-file-preview]')) return;
        const output = event.target.closest('form')?.querySelector('[data-preview-output]');
        const file = event.target.files?.[0];
        if (!output || !file) return;
        output.innerHTML = '';
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = 'Aperçu du fichier sélectionné';
            img.className = 'mt-3 aspect-video w-full rounded-md object-cover';
            output.appendChild(img);
        } else {
            output.textContent = `${file.name} · ${(file.size / 1024 / 1024).toFixed(1)} Mo`;
            output.className = 'mt-3 text-xs font-semibold text-zinc-600';
        }
    });
}

function initialisePeriodSelector() {
    const select = document.querySelector('[data-period-select]');
    select?.addEventListener('change', () => {
        window.location.href = `${select.dataset.url}?period=${encodeURIComponent(select.value)}`;
    });
}

function initialisePresentation() {
    const slides = [...document.querySelectorAll('[data-slide]')];
    if (!slides.length) return;
    const counter = document.querySelector('[data-slide-counter]');
    let current = 0;
    const show = (index) => {
        current = Math.max(0, Math.min(slides.length - 1, index));
        slides.forEach((slide, i) => slide.classList.toggle('active', i === current));
        if (counter) counter.textContent = `${current + 1} / ${slides.length}`;
    };
    document.querySelector('[data-slide-prev]')?.addEventListener('click', () => show(current - 1));
    document.querySelector('[data-slide-next]')?.addEventListener('click', () => show(current + 1));
    document.querySelector('[data-fullscreen]')?.addEventListener('click', () => document.documentElement.requestFullscreen?.());
    document.addEventListener('keydown', (event) => {
        if (['ArrowRight', 'PageDown', ' '].includes(event.key)) show(current + 1);
        if (['ArrowLeft', 'PageUp'].includes(event.key)) show(current - 1);
        if (event.key === 'Home') show(0);
        if (event.key === 'End') show(slides.length - 1);
    });
    show(0);
}

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons: appIcons });
    initialiseNavigation();
    initialisePlatformTabs();
    initialiseCharts();
    initialiseLists();
    initialiseFilePreviews();
    initialisePeriodSelector();
    initialisePresentation();
    setTimeout(() => document.querySelector('[data-flash]')?.remove(), 5000);
});
