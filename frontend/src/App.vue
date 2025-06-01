<template>
  <div class="app-container" :class="{ 'no-header': !showHeader }">
    <Header v-if="showHeader" />
    <div class="main-content">
      <RouterView />
    </div>
  </div>
</template>

<script>
import { RouterView } from 'vue-router'
import Header from './components/Header.vue'
import axios from 'axios';

export default {
  components: {
    RouterView,
    Header
  },
  data() {
    return {
      showHeader: true
    }
  },
  watch: {
    '$route'(to) {
      this.showHeader = !['/', '/login', '/register', '/ForgotPass'].includes(to.path);
    }
  },
  created() {
    this.showHeader = !['/', '/login', '/register', '/ForgotPass'].includes(this.$route.path);
  }
}
</script>

<style>
.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding-top: 70px; 
}

.no-header .main-content {
  padding-top: 0;
}

html {
  scroll-behavior: smooth;
}
</style>
