<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useMasterStore } from '@/stores/masterStore'
import NavbarComponent from '@/components/NavbarComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'

const masterStore = useMasterStore()
const route = useRoute()

const hideChrome = computed(() => !!route.meta.hideChrome)

onMounted(() => {
  masterStore.fetchMasterData()
})
</script>

<template>
  <div class="d-flex flex-column min-vh-100">
    <NavbarComponent v-if="!hideChrome" />
    <main class="flex-grow-1">
      <router-view />
    </main>
    <FooterComponent v-if="!hideChrome" />
  </div>
</template>
