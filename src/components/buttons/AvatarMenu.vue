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
          <v-divider class="my-3"></v-divider>
          <router-link to="/edit-account">
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
    ...mapGetters(["userName", "userEmail", "userProfile"]),
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
  },
};
</script>
