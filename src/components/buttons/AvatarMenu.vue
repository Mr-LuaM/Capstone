<template>
  <v-menu min-width="200px" rounded>
    <template v-slot:activator="{ props }">
      <v-btn icon v-bind="props">
        <v-avatar
          :image="getFullImageUrl()"
          size="40"
          class="elevation-8"
        ></v-avatar>
      </v-btn>
    </template>
    <v-card>
      <v-card-text>
        <div class="mx-auto text-center">
          <v-avatar color="brown" :image="getFullImageUrl()"> </v-avatar>
          <h3>{{ user.fullName }}</h3>
          <p class="text-caption mt-1">
            {{ user.email }}
          </p>
          <p class="text-caption mt-1">
            Role: {{ getRoleName() }}
          </p>
          <v-divider class="my-3"></v-divider>
          <router-link :to="`${getParentRoute()}/edit-accounts`">
            <v-btn variant="text">Edit Account</v-btn>
          </router-link>

          <v-divider class="my-3"></v-divider>

          <v-btn variant="text" @click="logout">Logout</v-btn>
        </div>
      </v-card-text>
    </v-card>
  </v-menu>
</template>
<script>
import { mapGetters } from "vuex";

export default {
  name: "AvatarMenu",
  data() {
    return {
      backendUrl: "http://backend.test/", // Define your backend URL here
    };
  },
  computed: {
    ...mapGetters(["userName", "userEmail", "userProfile", "role"]),
    user() {
      const userName = this.userName || "";

      const initials = userName
        .split(" ")
        .map((word) => word[0])
        .join("")
        .toUpperCase();

      return {
        initials,
        fullName: userName,
        email: this.userEmail,
        profilePicture: this.getFullImageUrl(),
      };
    },
  },
  methods: {
    getFullImageUrl() {
      return this.backendUrl + this.userProfile;
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
    getParentRoute() {
      // Access the current route
      const currentRoute = this.$route;

      // Extract the parent route by removing the last segment
      const parentRoute = currentRoute.path.split("/").slice(0, -1).join("/");

      return parentRoute;
    },
    getRoleName() {
      switch (this.role) {
        case "2":
          return "Main Admin";
        case "3":
          return "Station Admin";
        case "4":
          return "Teacher";
        case "5":
          return "Student";
        default:
          return "Unknown Role";
      }
    },
  },
};
</script>
