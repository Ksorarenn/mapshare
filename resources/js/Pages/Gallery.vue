<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Галерея</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <input v-model="search" placeholder="Поиск по названию" class="border p-2 mb-4 w-full" />
            <div class="grid gap-4" :class="gridClass">
              <div v-for="roadmap in filteredRoadmaps" :key="roadmap.id" class="p-4 border rounded shadow cursor-pointer" @click="openViewer(roadmap.id)">
                <h3 class="font-bold">{{ roadmap.title }}</h3>
                <p class="text-sm text-gray-600">{{ roadmap.description }}</p>
              </div>
            </div>
            <div class="flex justify-between items-center mt-4">
          <Link href="/roadmaps/create" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Новая карта</Link>
        </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { useRoadmapStore } from '@/stores/roadmap';
import { router, Link } from '@inertiajs/vue3';

const search = ref('');
const roadmapStore = useRoadmapStore();

onMounted(() => roadmapStore.fetchPublic());

const filteredRoadmaps = computed(() => {
  if (!search.value) return roadmapStore.publicRoadmaps;
  return roadmapStore.publicRoadmaps.filter(r => r.title.toLowerCase().includes(search.value.toLowerCase()));
});

function openViewer(id) {
  router.visit(`/roadmaps/${id}`);
}

const gridClass = computed(() => (window.innerWidth < 640 ? 'grid-cols-1' : 'grid-cols-3'));
</script>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const layout = AuthenticatedLayout;

<style scoped>
.gallery { max-width: 800px; margin: 0 auto; }
</style>