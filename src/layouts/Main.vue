<template>
  <v-app>
    <!-- Admin Page Header -->
    <v-app-bar app color="primary-darken-1" class="rounded-b-m">
      <v-app-bar-nav-icon @click="toggleSidebar"></v-app-bar-nav-icon>
      <v-toolbar-title class="ml-2">Admin Dashboard</v-toolbar-title>
      <v-spacer></v-spacer>

      <ThemeSwitcher />
    </v-app-bar>

    <!-- Admin Page Content -->
    <v-row>
      <!-- Admin Page Sidebar -->

      <v-navigation-drawer
        app
        v-model="sidebarOpen"
        class="elevation-12 rounded-e-lg"
      >
        <v-list density="comfortable" nav>
          <v-list-item
            link
            to="/admin/dashboard"
            :class="{ highlight: $route.path === '/admin/dashboard' }"
            prepend-icon="mdi-view-dashboard"
          >
            <v-list-item-title>Dashboard</v-list-item-title>
          </v-list-item>
          <v-list-item
            link
            to="/admin/applicants"
            :class="{ highlight: $route.path === '/admin/applicants' }"
            prepend-icon="mdi-account-group"
          >
            <v-list-item-title>Applicants</v-list-item-title>
          </v-list-item>
          <v-list-item
            link
            to="/admin/stations"
            :class="{ highlight: $route.path === '/admin/stations' }"
            prepend-icon="mdi-map-marker"
          >
            <v-list-item-title>Stations</v-list-item-title>
          </v-list-item>
          <!-- Add more menu items for different sections -->
        </v-list>
        <template v-slot:append>
          <div class="pa-2">
            <v-btn block> Logout </v-btn>
          </div>
        </template>
      </v-navigation-drawer>
      <!-- Admin Page Main Content -->
      <v-main>
        <v-container fluid>
          <!-- Main content of the admin page goes here -->
          <router-view></router-view>
        </v-container>
      </v-main>
    </v-row>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      sidebarOpen: true, // Initially open the sidebar
    };
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
  },
};
</script>

<style scoped>
.highlight {
  background-color: #c8102e; /* Add your desired highlight color */
}
</style>
