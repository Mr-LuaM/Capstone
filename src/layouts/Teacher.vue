<template>
  <v-app>
    <!-- Admin Page Header -->
    <v-app-bar
      app
      class="rounded-b-m sticky"
      color="primary"
      style="
        background: linear-gradient(
          90deg,
          hsla(210, 100%, 20%, 1) 85%,
          hsla(350, 85%, 42%, 1) 91%
        );
      "
    >
      <v-app-bar-nav-icon @click="toggleSidebar"></v-app-bar-nav-icon>
      <v-toolbar-title class="ml-2"> Teacher</v-toolbar-title>
      <v-spacer></v-spacer>
      <NotifVue />
      <ThemeSwitcher />
      <AvatarMenu />
    </v-app-bar>

    <!-- Admin Page Content -->
    <v-row>
      <!-- Admin Page Sidebar -->

      <v-navigation-drawer
        app
        v-model="sidebarOpen"
        class="elevation-12 rounded-e"
      >
        <v-img
          width="300"
          :aspect-ratio="1"
          src="@/assets/img/logo/logo.jpg"
          cover
        ></v-img>

        <v-list density="comfortable" nav>
          <v-list-item
            link
            to="/teacher/dashboard"
            :class="{ highlight: isRouteActive('/teacher/dashboard') }"
            prepend-icon="mdi-view-dashboard"
          >
            <v-list-item-title> Dashboard</v-list-item-title>
          </v-list-item>
          <v-list-item
            link
            to="/teacher/students"
            :class="{ highlight: isRouteActive('/teacher/students') }"
            prepend-icon="mdi-human-greeting"
          >
            <v-list-item-title> Students</v-list-item-title>
          </v-list-item>
          <v-list-item
            link
            to="/teacher/exams"
            :class="{ highlight: isRouteActive('/teacher/exams') }"
            prepend-icon="mdi-pencil"
          >
            <v-list-item-title>Manage Exams</v-list-item-title>
          </v-list-item>
          <v-list-item
            link
            to="/teacher/schedule"
            :class="{ highlight: isRouteActive('/teacher/schedule') }"
            prepend-icon="mdi-calendar"
          >
            <v-list-item-title>Schedule</v-list-item-title>
          </v-list-item>

          <v-list-item
            link
            to="/teacher/announcements"
            :class="{ highlight: isRouteActive('/teacher/announcements') }"
            prepend-icon="mdi-post"
          >
            <v-list-item-title>Announcements</v-list-item-title>
          </v-list-item>

          <v-container>
            <v-divider></v-divider>
          </v-container>
          <v-list-item
            link
            to="/teacher/edit-accounts"
            :class="{ highlight: isRouteActive('/teacher/edit-accounts') }"
            prepend-icon="mdi-account-edit"
          >
            <v-list-item-title>Profile</v-list-item-title>
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
          <!-- Main content of the station page goes here -->
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
      sidebarOpen: false, // Initially close the sidebar
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

      // Clear user information from Vuex store
      this.$store.dispatch("logout");

      // Redirect to the login page and clear navigation history
      this.$router.push({ path: "/login" }).catch(() => {});
    },
    isRouteActive(route) {
      return this.$route.path === route;
    },
    updateBreadcrumbs() {
      const routeSegments = this.$route.path
        .split("/")
        .filter((segment) => segment !== "");

      this.breadcrumbs = routeSegments.map(
        (segment) => segment.charAt(0).toUpperCase() + segment.slice(1)
      );

      console.log("Breadcrumbs:", this.breadcrumbs);
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
