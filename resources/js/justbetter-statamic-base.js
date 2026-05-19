import PackagesIndex from './pages/Packages/Index.vue';

Statamic.booting(() => {
    Statamic.$inertia.register('statamic-base::Packages/Index', PackagesIndex);
});
