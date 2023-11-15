<template>
  <div>
    <!-- Admin Page Header -->
    <v-app-bar app>
      <v-app-bar-nav-icon @click="toggleSidebar"></v-app-bar-nav-icon>
      <v-toolbar-title>Admin Dashboard</v-toolbar-title>
    </v-app-bar>

    <!-- Admin Page Content -->

    <!-- Sidebar Component -->

    <Sidebar :sidebarOpen.sync="sidebarOpen" />

    <!-- Admin Page Main Content -->

    <v-main>
      <!-- Main content of the admin page goes here -->
      <router-view></router-view>
    </v-main>
  </div>
</template>

<script>
import Sidebar from "./Sidebar.vue";

export default {
  components: {
    Sidebar,
  },
  data() {
    return {
      sidebarOpen: true,
    };
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
  },
};
</script>
