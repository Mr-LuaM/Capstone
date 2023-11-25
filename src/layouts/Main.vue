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
        class="elevation-12 rounded-e"
      >
        <v-list density="comfortable" nav>
          <v-list-item
            link
            to="/admin/dashboard"
            :class="{ highlight: isRouteActive('/admin/dashboard') }"
            prepend-icon="mdi-view-dashboard"
          >
            <v-list-item-title>Dashboard</v-list-item-title>
          </v-list-item>
          <v-list-item
            link
            to="/admin/applicants"
            :class="{ highlight: isRouteActive('/admin/applicants') }"
            prepend-icon="mdi-account-group"
          >
            <v-list-item-title>Applicants</v-list-item-title>
          </v-list-item>
          <v-list-item
            link
            to="/admin/stations"
            :class="{ highlight: isRouteActive('/admin/stations') }"
            prepend-icon="mdi-map-marker"
          >
            <v-list-item-title>Stations</v-list-item-title>
          </v-list-item>
          <!-- Add more menu items for different sections -->
        </v-list>
        <template v-slot:append>
          <div class="pa-2">
            <v-btn @click="logout" block>Logout</v-btn>
          </div>
        </template>
      </v-navigation-drawer>
      <!-- Admin Page Main Content -->
      <v-main>
        <v-container fluid class="pa-7">
          <v-breadcrumbs :items="breadcrumbs">
            <template v-slot:prepend>
              <v-icon size="small" icon="$vuetify"></v-icon>
            </template>
          </v-breadcrumbs>
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
      breadcrumbs: [],
    };
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
    logout() {
      // Perform logout actions here
      // For example, clear the token from localStorage
      localStorage.removeItem("jwt_token");

      // Redirect to the login page and clear navigation history
      this.$router.push({ path: "/login" }).catch(() => {});
    },
    isRouteActive(route) {
      return this.$route.path === route;
    },
    updateBreadcrumbs() {
  const routeSegments = this.$route.path.split('/').filter(segment => segment !== '');

  this.breadcrumbs = routeSegments.map(segment => segment.charAt(0).toUpperCase() + segment.slice(1));

  console.log('Breadcrumbs:', this.breadcrumbs);
},
  },
  watch: {
    $route() {
      this.updateBreadcrumbs();
    },
  },
  created() {
    this.updateBreadcrumbs();
  },
};
</script>

<style scoped>
/* Add your styles if needed */
</style>
