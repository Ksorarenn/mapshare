<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Создание роадмапа
      </h2>
    </template>

    <div class="w-full h-[calc(100vh-160px)] flex overflow-hidden relative bg-white border-t border-gray-200">
      
      <div class="absolute top-0 left-0 right-0 h-16 bg-gray-50/90 backdrop-blur-sm border-b border-gray-200 z-20 flex items-center justify-between px-6 shadow-sm">
        
        <div class="flex items-center space-x-3 flex-1 max-w-md mr-4">
          <label for="roadmap-title" class="text-xs font-bold text-gray-400 uppercase tracking-wider shrink-0">Название:</label>
          <input 
            id="roadmap-title"
            v-model="roadmapTitle" 
            type="text" 
            class="w-full bg-white border border-gray-200 rounded-md px-3 py-1.5 text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm" 
            placeholder="Введите название роадмапа..."
          />
        </div>
        
        <div class="flex items-center space-x-4">
          <div class="flex items-center bg-white p-1 rounded-lg border border-gray-200 space-x-1 shadow-sm">
            <button 
              v-if="isEditMode"
              @click="addNodeViaButton" 
              class="px-3 py-1.5 bg-white text-gray-700 border border-gray-200 rounded-md hover:bg-gray-50 text-sm flex items-center font-medium shadow-sm transition-colors"
              title="Добавить узел"
            >
              <span class="text-lg leading-none mr-1.5 text-indigo-600">+</span> Добавить
            </button>
            
            <button 
              v-if="isEditMode"
              @click="deleteSelectedNode" 
              :disabled="!activeSelectedNodeId"
              :class="[
                'px-3 py-1.5 text-sm rounded-md font-medium transition-colors shadow-sm',
                activeSelectedNodeId 
                  ? 'bg-red-600 text-white hover:bg-red-500 cursor-pointer' 
                  : 'bg-gray-100 text-gray-400 cursor-not-allowed'
              ]"
              title="Удалить выбранный узел"
            >
              Удалить
            </button>
          </div>

          <button 
            @click="isSidebarOpen = !isSidebarOpen"
            :disabled="!activeSelectedNodeId"
            :class="[
              'px-3 py-1.5 text-sm border rounded-md font-medium shadow-sm transition-colors',
              activeSelectedNodeId 
                ? 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' 
                : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
            ]"
          >
            {{ isSidebarOpen ? 'Скрыть панель →' : '← Показать панель' }}
          </button>

          <div class="space-x-2 border-l border-gray-200 pl-4 flex items-center">
            <button 
              v-if="isOwner" 
              @click="isEditMode = !isEditMode" 
              class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded-md font-medium transition-colors"
            >
              {{ isEditMode ? 'Режим просмотра' : 'Режим редактирования' }}
            </button>

            <button v-if="isEditMode" @click="save" class="px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 text-sm font-medium shadow-sm transition-colors">Сохранить</button>
            <button v-if="isEditMode" @click="reset" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium shadow-sm transition-colors">Сбросить</button>
          </div>
        </div>
      </div>

      <div class="flex-1 h-full bg-white relative pt-16">
        <VueFlow
          v-model:nodes="nodes"
          v-model:edges="edges"
          :fit-view="true"
          :pan-on-drag="true"
          :zoom-on-scroll="true"
          class="h-full w-full"
          @pane-dblclick="addNode"
          @pane-click="deselectNode"
          @node-click="selectNode"
          @connect="onConnect"
        >
          <template #node-default="{ id, data }">
            <Handle id="target" type="target" position="top" class="!bg-indigo-500 !w-2.5 !h-2.5" />
            
            <div class="flex items-center space-x-2.5 px-1 py-0.5">
              <div v-if="!isEditMode" class="flex items-center shrink-0">
                <input 
                  type="checkbox" 
                  :checked="completedNodes.includes(id)" 
                  @change="toggleNodeProgress(id)"
                  @click.stop
                  class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer transition-all"
                />
              </div>

              <div :class="['font-medium text-sm transition-all select-none', completedNodes.includes(id) ? 'line-through text-gray-400' : 'text-gray-900']">
                {{ data.title }}
              </div>
            </div>
            
            <Handle id="source" type="source" position="bottom" class="!bg-indigo-500 !w-2.5 !h-2.5" />
          </template>

          <Controls />
        </VueFlow>
      </div>

      <div 
        :class="[
          'h-full bg-white border-l border-gray-200 shadow-2xl transition-all duration-300 flex flex-col z-30',
          isSidebarOpen && activeSelectedNodeId ? 'w-96 opacity-100' : 'w-0 opacity-0 overflow-hidden border-l-0'
        ]"
      >
        <div v-if="selectedNode" class="p-5 flex-1 flex flex-col overflow-y-auto">
          <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h3 class="text-lg font-semibold text-gray-900 truncate pr-2">{{ selectedNode.data.title }}</h3>
            <button @click="isSidebarOpen = false" class="text-gray-400 hover:text-gray-600 text-sm">✕</button>
          </div>

          <div v-if="isEditMode" class="flex bg-gray-100 p-0.5 rounded mb-4">
            <button 
              @click="activeTab = 'edit'"
              :class="['flex-1 py-1 text-xs font-medium rounded transition-colors', activeTab === 'edit' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900']"
            >
              Редактировать
            </button>
            <button 
              @click="activeTab = 'preview'"
              :class="['flex-1 py-1 text-xs font-medium rounded transition-colors', activeTab === 'preview' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900']"
            >
              Просмотр (Markdown)
            </button>
          </div>

          <div class="flex-1 flex flex-col min-h-0">
            <div v-if="activeTab === 'edit' && isEditMode" class="space-y-4 flex-1 flex flex-col">
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Название на карте</label>
                <input 
                  v-model="selectedNode.data.title" 
                  type="text" 
                  class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                  placeholder="Название темы"
                />
              </div>

              <div class="flex-1 flex flex-col">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Содержимое (Markdown)</label>
                <textarea 
                  v-model="selectedNode.data.description" 
                  class="w-full flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none font-mono" 
                  placeholder="Используйте # Заголовок, * Списки, [Ссылка](url)..."
                ></textarea>
              </div>
            </div>

            <div 
              v-else 
              class="flex-1 border rounded p-3 bg-gray-50 overflow-y-auto markdown-body text-sm text-gray-800"
              v-html="markdownPreview"
            ></div>
          </div>
          
          <div class="pt-2 mt-2 text-xs text-gray-400 flex justify-between shrink-0">
            <span>ID узла: {{ selectedNode.id }}</span>
          </div>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { marked } from 'marked';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { VueFlow, addEdge, useVueFlow, Handle } from '@vue-flow/core';
import { Controls } from '@vue-flow/controls';
// КРИТИЧЕСКИЙ ФИКС: Импортируем базовые стили библиотеки, чтобы контейнер не ломался и растягивался на весь экран
import '@vue-flow/core/dist/style.css';
import '@vue-flow/core/dist/theme-default.css';
import '@vue-flow/controls/dist/style.css';

const props = defineProps({
  roadmap: { type: Object, default: null }
});

const page = usePage();
// Текущий залогиненный юзер
const currentUser = computed(() => page.props.auth.user); 

// Флаг: является ли текущий юзер владельцем этой карты
const isOwner = computed(() => {
  if (!props.roadmap) return true; // Если карта новая — мы её создатели
  return props.roadmap.user_id === currentUser.value.id;
});

// Переключатель режимов (если владелец захочет посмотреть на карту глазами гостя)
const isEditMode = ref(isOwner.value);

const completedNodes = ref([]); // Сюда будем загружать массив ID из таблицы UserRoadmapProgress

const { project } = useVueFlow();

marked.setOptions({
  gfm: true,
  breaks: true,
});
// ---------- Состояние ----------
// Если роадмап передан из контроллера — берем его имя, иначе дефолтное
const roadmapTitle = ref(props.roadmap ? props.roadmap.title : 'Мой новый роадмап');

// Если есть сохраненный graph_data — парсим его ноды, иначе берем стартовую ноду
const nodes = ref(
  props.roadmap && props.roadmap.graph_data?.nodes 
    ? props.roadmap.graph_data.nodes 
    : [
        {
          id: '1',
          type: 'default',
          position: { x: 100, y: 100 },
          data: { 
            title: 'Начало пути', 
            description: '# Добро пожаловать!\n\nЭто стартовая точка вашего роадмапа.' 
          },
          style: { width: 'max-content', minWidth: '100px', padding: '12px 16px', border: '2px solid #4a5568', borderRadius: '8px', background: '#fff' },
        }
      ]
);

// Если есть сохраненный graph_data — берем связи, иначе пустой массив
const edges = ref(props.roadmap && props.roadmap.graph_data?.edges ? props.roadmap.graph_data.edges : []);

const isSidebarOpen = ref(false);
const activeTab = ref('edit');
const activeSelectedNodeId = ref(null); 
const selectedNode = ref(null);

const markdownPreview = computed(() => {
  if (!selectedNode.value?.data?.description) {
    return '<p class="text-gray-400 italic">Описание отсутствует...</p>';
  }
  return marked.parse(selectedNode.value.data.description);
});
async function toggleNodeProgress(nodeId) {
  if (completedNodes.value.includes(nodeId)) {
    completedNodes.value = completedNodes.value.filter(id => id !== nodeId);
  } else {
    completedNodes.value.push(nodeId);
  }

  try {
    // Отправляем обновленный массив в твой ProgressController
    await axios.post('/progress', {
      roadmap_id: props.roadmap.id,
      completed_nodes: completedNodes.value
    });
  } catch (e) {
    console.error('Ошибка сохранения прогресса:', e);
  }
}

// При загрузке страницы подтягиваем существующий прогресс юзера
onMounted(async () => {
  if (props.roadmap && !isEditMode.value) {
    try {
      const { data } = await axios.get(`/progress/${props.roadmap.id}`);
      if (data) {
        completedNodes.value = data.completed_nodes || [];
      }
    } catch (e) {
      console.log('Прогресс для этой карты еще не создан');
    }
  }
});
// ---------- Хелперы ----------

function selectNode({ node }) {
  if (!node) return;
  
  activeSelectedNodeId.value = node.id;
  selectedNode.value = nodes.value.find(n => n.id === node.id);
  isSidebarOpen.value = true;
  activeTab.value = 'edit';
  
  nodes.value.forEach(n => {
    if (n.id === node.id) {
      n.style = { ...n.style, borderColor: '#2563eb', borderWidth: '3px' };
    } else {
      n.style = { ...n.style, borderColor: '#4a5568', borderWidth: '2px' };
    }
  });
}

function deselectNode() {
  activeSelectedNodeId.value = null;
  selectedNode.value = null;
  isSidebarOpen.value = false;

  nodes.value.forEach(n => {
    n.style = { ...n.style, borderColor: '#4a5568', borderWidth: '2px' };
  });
}

function addNode(event) {
  const position = project({
    x: event.clientX,
    y: event.clientY,
  });
  createNewNode(position);
}

function addNodeViaButton() {
  const position = { x: 250, y: 250 };
  createNewNode(position);
}

function createNewNode(position) {
  // Генерируем уникальный ID на основе текущего времени в миллисекундах
  // Приведение к string обязательно, так как Vue Flow требует строковые ID
  const id = Date.now().toString();
  
  nodes.value.push({
    id,
    type: 'default',
    position,
    data: { 
      title: `Новый шаг`, // Убрали индекс из названия, так как таймштамп в названии не нужен
      description: '* Пункт 1\n* Пункт 2' 
    },
    style: { width: 'max-content', minWidth: '100px', padding: '12px 16px', border: '2px solid #4a5568', borderRadius: '8px', background: '#fff' },
  });
}

function deleteSelectedNode() {
  if (!activeSelectedNodeId.value) return;

  nodes.value = nodes.value.filter(n => n.id !== activeSelectedNodeId.value);
  edges.value = edges.value.filter(
    e => e.source !== activeSelectedNodeId.value && e.target !== activeSelectedNodeId.value
  );

  activeSelectedNodeId.value = null;
  selectedNode.value = null;
  isSidebarOpen.value = false;
}

function onConnect(params) {
  edges.value = addEdge(params, edges.value);
}

async function save() {
  const titleToSend = roadmapTitle.value.trim() || 'Безымянный роадмап';
  const graphDataToSend = { nodes: nodes.value, edges: edges.value };

  // Если у нас открыта существующая карта (есть props.roadmap)
  if (props.roadmap && props.roadmap.id) {
    router.patch(`/roadmaps/${props.roadmap.id}`, {
      title: titleToSend,
      description: props.roadmap.description || '',
      graph_data: graphDataToSend,
    }, {
      onSuccess: () => {
        alert('Роадмап успешно обновлен!');
      },
      onError: (errors) => {
        console.error('Ошибка обновления:', errors);
      }
    });
  } else {
    // Если создаем новую карту
    router.post('/roadmaps', {
      title: titleToSend,
      description: '',
      graph_data: graphDataToSend,
    }, {
      onError: (errors) => {
        console.error('Ошибка создания:', errors);
      }
    });
  }
}

function reset() {
  roadmapTitle.value = 'Мой новый роадмап';
  nodes.value = [
    {
      id: '1',
      type: 'default',
      position: { x: 100, y: 100 },
      data: {
        title: 'Начало пути',
        description: 'Добро пожаловать в роадмап! Это стартовая точка.'
      },
      style: { 
        width: 'max-content', 
        minWidth: '100px', 
        padding: '12px 16px', 
        border: '2px solid #4a5568', 
        borderRadius: '8px', 
        background: '#fff' 
      },
    },
  ];
  edges.value = [];
  activeSelectedNodeId.value = null;
  selectedNode.value = null;
  isSidebarOpen.value = false;
}
</script>

<style scoped>
.vue-flow__node {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transition: border-color 0.15s ease, border-width 0.15s ease;
}

.vue-flow__node:hover:not(.vue-flow__node[style*="border-color: rgb(37, 99, 235)"]) {
  border-color: #818cf8 !important;
}

.markdown-body :deep(h1) { 
  font-size: 1.5rem; 
  font-weight: 700; 
  margin-bottom: 0.75rem; 
  border-bottom: 1px solid #e5e7eb; 
  padding-bottom: 0.25rem; 
}

.markdown-body :deep(h2) { 
  font-size: 1.25rem; 
  font-weight: 600; 
  margin-top: 1rem; 
  margin-bottom: 0.5rem; 
}

.markdown-body :deep(h3) { 
  font-size: 1.1rem; 
  font-weight: 600; 
  margin-top: 0.75rem; 
  margin-bottom: 0.25rem; 
}

.markdown-body :deep(p) { 
  margin-bottom: 0.75rem; 
  line-height: 1.5; 
}

.markdown-body :deep(ul) { 
  list-style-type: disc; 
  padding-left: 1.25rem; 
  margin-bottom: 0.75rem; 
}

.markdown-body :deep(ol) { 
  list-style-type: decimal; 
  padding-left: 1.25rem; 
  margin-bottom: 0.75rem; 
}

.markdown-body :deep(li) { 
  margin-bottom: 0.25rem; 
}

.markdown-body :deep(a) { 
  color: #4f46e5; 
  text-decoration: underline; 
  font-weight: 500; 
}

.markdown-body :deep(a:hover) { 
  color: #4338ca; 
}

.markdown-body :deep(code) { 
  background-color: #f3f4f6; 
  padding: 0.125rem 0.25rem; 
  border-radius: 0.25rem; 
  font-family: monospace; 
  font-size: 0.875rem; 
}
</style>