<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Roadmaps</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div v-if="roadmapStore.myRoadmaps.length === 0" class="text-center text-gray-500">
              У вас пока нет роадмапов.
            </div>
            <ul v-else class="space-y-4">
              <li v-for="roadmap in roadmapStore.myRoadmaps" :key="roadmap.id" class="p-4 border rounded cursor-pointer" @click="openViewer(roadmap.id)">
                <h3 class="font-bold">{{ roadmap.title }}</h3>
                <p class="text-sm text-gray-600">{{ roadmap.description }}</p>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useRoadmapStore } from '@/stores/roadmap';
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const roadmapStore = useRoadmapStore();

onMounted(() => {
  roadmapStore.fetchMine();
});

function openViewer(id) {
  router.visit(`/roadmaps/${id}`);
}
</script>

<style scoped></style>