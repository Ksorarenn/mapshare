<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ваши роадмапы</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div v-if="roadmapStore.myRoadmaps.length === 0" class="text-center text-gray-500">
              У вас пока нет роадмапов.
            </div>
            <ul v-else class="space-y-4">
              <li v-for="roadmap in roadmapStore.myRoadmaps" :key="roadmap.id" class="p-4 border rounded cursor-pointer relative group" @click="openViewer(roadmap.id)">
                <h3 class="font-bold">{{ roadmap.title }}</h3>
                  <p class="text-sm text-gray-600">{{ roadmap.description }}</p>
  
                  <button 
                  @click.stop="deleteRoadmap(roadmap.id)" 
                  class="absolute top-4 right-4 bg-red-100 text-red-600 hover:bg-red-200 p-1.5 rounded text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                  Удалить
                  </button>
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

// 1. Принимаем роадмапы напрямую от Laravel контроллера через Inertia Props
const props = defineProps({
  roadmaps: {
    type: Array,
    default: () => []
  }
});

const roadmapStore = useRoadmapStore();

onMounted(() => {
  
  // Присваиваем стору
  roadmapStore.myRoadmaps = props.roadmaps;
  
});

function openViewer(id) {
  if (!id) return;
  // Переходим на страницу просмотра/редактирования конкретного роадмапа
  router.visit(`/roadmaps/${id}`);
}

function deleteRoadmap(id) {
  if (confirm('Вы уверены, что хотите удалить этот роадмап?')) {
    router.delete(`/roadmaps/${id}`, {
      onSuccess: () => {
        // Обновляем локальный стор Pinia после удаления на сервере
        roadmapStore.myRoadmaps = roadmapStore.myRoadmaps.filter(r => r.id !== id);
      }
    });
  }
}
</script>

<style scoped></style>