import { defineStore } from 'pinia';
import axios from 'axios';

export const useRoadmapStore = defineStore('roadmap', {
  state: () => ({
    publicRoadmaps: [],
    myRoadmaps: [],
    current: null,
  }),
  actions: {
    async fetchPublic(params = {}) {
      const { data } = await axios.get('/roadmaps', { params });
      this.publicRoadmaps = data.data; // paginator data
    },
    async fetchMine() {
      const { data } = await axios.get('/user/roadmaps');
      this.myRoadmaps = data;
    },
    async fetchOne(id) {
      const { data } = await axios.get(`/roadmaps/${id}`);
      this.current = data;
    },
    async create(payload) {
      const { data } = await axios.post('/roadmaps', payload);
      this.myRoadmaps.push(data);
    },
    async update(id, payload) {
      const { data } = await axios.patch(`/roadmaps/${id}`, payload);
      const idx = this.myRoadmaps.findIndex(r => r.id === id);
      if (idx !== -1) this.myRoadmaps.splice(idx, 1, data);
    },
    async delete(id) {
      await axios.delete(`/api/roadmaps/${id}`);
      this.myRoadmaps = this.myRoadmaps.filter(r => r.id !== id);
    },
  },
});
