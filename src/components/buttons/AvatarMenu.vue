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
          <v-avatar color="brown">
            <span class="text-h5">{{ user.initials }}</span>
          </v-avatar>
          <h3>{{ user.fullName }}</h3>
          <p class="text-caption mt-1">
            {{ user.email }}
          </p>
          <v-divider class="my-3"></v-divider>
          <v-btn rounded variant="text"> Edit Account </v-btn>
          <v-divider class="my-3"></v-divider>
          <v-btn rounded variant="text"> Disconnect </v-btn>
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
  },
};
</script>
