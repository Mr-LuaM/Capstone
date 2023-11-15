<template>
  <v-app>
    <v-main>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <v-flex xs12 sm8 md6 lg4>
            <!-- Login -->
            <v-card>
              <v-card-title class="text-center">
                <v-img
                  src="https://your-logo-url.com"
                  <!--
                  Replace
                  with
                  your
                  logo
                  URL
                  --
                >
                  alt="Logo" max-height="50" ></v-img
                >
                <div class="mb-4">Welcome to Vuexy! 👋</div>
              </v-card-title>

              <v-card-text>
                <v-form id="formAuthentication" @submit.prevent="submitForm">
                  <v-text-field
                    v-model="email"
                    label="Email or Username"
                    outlined
                    autofocus
                  ></v-text-field>

                  <v-text-field
                    v-model="password"
                    :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="showPassword ? 'text' : 'password'"
                    label="Password"
                    outlined
                    @click:append="showPassword = !showPassword"
                  ></v-text-field>

                  <v-checkbox
                    v-model="rememberMe"
                    label="Remember Me"
                  ></v-checkbox>

                  <v-btn color="primary" block @click="submitForm"
                    >Sign in</v-btn
                  >
                </v-form>
              </v-card-text>

              <v-card-actions>
                <div class="flex-grow-1 text-center">
                  <span>New on our platform?</span>
                  <v-btn color="primary" text @click="redirectToRegistration"
                    >Create an account</v-btn
                  >
                </div>
              </v-card-actions>
            </v-card>

            <v-divider class="my-4">or</v-divider>

            <v-flex xs12 sm8 md6 lg4 class="d-flex justify-center">
              <v-btn class="me-3" icon>
                <v-icon size="fs-5" color="blue darken-2">mdi-facebook</v-icon>
              </v-btn>
              <v-btn class="me-3" icon>
                <v-icon size="fs-5" color="red accent-4">mdi-google</v-icon>
              </v-btn>
              <v-btn icon>
                <v-icon size="fs-5" color="blue lighten-2">mdi-twitter</v-icon>
              </v-btn>
            </v-flex>
          </v-flex>
        </v-layout>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: "HelloWorld",

  data: () => ({
    ecosystem: [
      {
        text: "vuetify-loader",
        href: "https://github.com/vuetifyjs/vuetify-loader/tree/next",
      },
      {
        text: "github",
        href: "https://github.com/vuetifyjs/vuetify/tree/next",
      },
      {
        text: "awesome-vuetify",
        href: "https://github.com/vuetifyjs/awesome-vuetify",
      },
    ],
    importantLinks: [
      {
        text: "Chat",
        href: "https://community.vuetifyjs.com",
      },
      {
        text: "Made with Vuetify",
        href: "https://madewithvuejs.com/vuetify",
      },
      {
        text: "Twitter",
        href: "https://twitter.com/vuetifyjs",
      },
      {
        text: "Articles",
        href: "https://medium.com/vuetify",
      },
    ],
    whatsNext: [
      {
        text: "Explore components",
        href: "https://vuetifyjs.com",
      },
      {
        text: "Roadmap",
        href: "https://vuetifyjs.com/introduction/roadmap/",
      },
      {
        text: "Frequently Asked Questions",
        href: "https://vuetifyjs.com/getting-started/frequently-asked-questions",
      },
    ],
  }),
};
</script>
